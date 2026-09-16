<?php
/**
 * flatwiki editor: members of a Discord server propose wiki edits,
 * which arrive as GitHub pull requests for a maintainer to approve.
 *
 * Every public page in this folder starts with: require __DIR__ . '/src/bootstrap.php';
 */
declare(strict_types=1);

require __DIR__ . '/http.php';
require __DIR__ . '/Discord.php';
require __DIR__ . '/GitHub.php';
require __DIR__ . '/Page.php';
require __DIR__ . '/Images.php';
require __DIR__ . '/view.php';

function config(): array
{
    static $config = null;
    if ($config === null) {
        // Secrets live outside the website: ~/editor-config/<site folder>.php. The editor runs
        // either inside a site (hlwiki.com/editor/) or as a site of its own (edit.bnet.cc).
        $editorDir = dirname(__DIR__);
        $siteDir = basename($editorDir) === 'editor' ? dirname($editorDir) : $editorDir;
        $path = getenv('FLATWIKI_EDITOR_CONFIG') ?: dirname($siteDir) . '/editor-config/' . basename($siteDir) . '.php';
        if (!is_file($path)) {
            http_response_code(503);
            exit('The editor is not set up yet.');
        }
        $config = require $path;
        $unset = fn ($v) => $v === '' || str_starts_with((string) $v, 'PASTE_');
        if (PHP_SAPI !== 'cli-server' && ($unset($config['discord']['client_secret']) || $unset($config['github']['token']))) {
            http_response_code(503);
            exit('The editor is not set up yet.');
        }
        $config['data_dir'] = $config['data_dir'] ?? dirname($path) . '/' . basename($siteDir) . '-data';
        $config['site_url'] = rtrim($config['site_url'], '/');
        $config['editor_url'] = rtrim($config['editor_url'] ?? $config['site_url'] . '/editor', '/');
        $config['stylesheets'] = $config['stylesheets'] ?? ['/css/wiki.css', '/css/site.css'];
    }
    return $config;
}

/** Local testing with `php -S` only. Never true on the real server. */
function is_dev(): bool
{
    return PHP_SAPI === 'cli-server' && !empty(config()['dev_user']);
}

function is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
}

/** "https://edit.bnet.cc/x" -> "https://edit.bnet.cc" */
function origin(string $url): string
{
    $u = parse_url($url);
    return $u['scheme'] . '://' . $u['host'] . (isset($u['port']) ? ':' . $u['port'] : '');
}

/** The editor's path on its host: "/editor/" inside a site, "/" on its own domain. */
function editor_path(): string
{
    return rtrim((string) parse_url(config()['editor_url'], PHP_URL_PATH), '/') . '/';
}

/** A link or asset on the documentation site itself. */
function site_url(string $path): string
{
    return preg_match('#^https?://#', $path) ? $path : config()['site_url'] . $path;
}

function discord(): Discord
{
    return new Discord(config()['discord'], config()['editor_url'] . '/callback.php');
}

function github(): GitHub
{
    $gh = config()['github'];
    return new GitHub($gh['token'], $gh['repo'], $gh['branch'] ?? 'main', !empty(config()['dry_run']));
}

function data_dir(string $sub): string
{
    $dir = config()['data_dir'] . '/' . $sub;
    if (!is_dir($dir)) {
        mkdir($dir, 0700, true);
    }
    return $dir;
}

// --- Request setup ----------------------------------------------------------

if (!is_dev() && !is_https()) {
    header('Location: ' . origin(config()['editor_url']) . ($_SERVER['REQUEST_URI'] ?? editor_path()), true, 301);
    exit;
}

// The pages' own stylesheets and images may live on the documentation site's domain.
$siteOrigin = origin(config()['site_url']);
header("Content-Security-Policy: default-src 'self'; img-src 'self' $siteOrigin https://cdn.discordapp.com data:; style-src 'self' $siteOrigin; script-src 'self'; frame-ancestors 'none'");
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');

ini_set('session.gc_probability', '1');
ini_set('session.gc_divisor', '100');
ini_set('session.gc_maxlifetime', '28800');
session_save_path(data_dir('sessions'));
session_name('flatwiki_editor');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => editor_path(),
    'secure' => !is_dev(),
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// --- Security helpers -------------------------------------------------------

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function require_post_with_csrf(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST'
        || !hash_equals(csrf_token(), (string) ($_POST['csrf'] ?? ''))) {
        http_response_code(400);
        render_message('Please try again', '<p>Your session expired. Go back, reload the page, and try again.</p>');
    }
}

// --- Discord login state ----------------------------------------------------

function current_user(): ?array
{
    if (is_dev() && empty($_SESSION['user'])) {
        $_SESSION['user'] = config()['dev_user'] + ['member' => true, 'allowed' => true, 'checked_at' => PHP_INT_MAX];
    }
    return $_SESSION['user'] ?? null;
}

/**
 * Returns the logged-in contributor, or shows a login/permission page and stops.
 * Membership is re-checked with Discord regularly, so people who leave the server
 * or lose the role lose access within minutes.
 */
function require_contributor(bool $recheckNow = false): array
{
    $user = current_user();
    if (!$user) {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'] ?? './';
        redirect('login.php');
    }
    if (!is_dev() && ($recheckNow || time() - $user['checked_at'] > 900)) {
        try {
            if (time() >= $user['token_expires']) {
                throw new HttpError(401, '', 'Discord login expired.');
            }
            $roles = discord()->memberRoles($user['token']);
        } catch (HttpError $e) {
            if ($e->status !== 401) {
                render_error($e);
            }
            // Expired or revoked Discord login: ask them to log in again.
            unset($_SESSION['user']);
            $_SESSION['return_to'] = ($_SERVER['REQUEST_METHOD'] ?? '') === 'GET' ? ($_SERVER['REQUEST_URI'] ?? './') : './';
            redirect('login.php');
        }
        $_SESSION['user'] = $user = update_membership($user, $roles);
    }
    if (!$user['allowed']) {
        render_not_allowed($user);
    }
    return $user;
}

function update_membership(array $user, ?array $roles): array
{
    $user['member'] = $roles !== null;
    $user['allowed'] = $roles !== null && array_intersect($roles, config()['discord']['role_ids']) !== [];
    $user['checked_at'] = time();
    return $user;
}

/** Stops people from flooding the review queue. */
function within_rate_limit(string $userId): bool
{
    $limit = (int) (config()['limits']['edits_per_hour'] ?? 10);
    $file = data_dir('ratelimit') . '/' . preg_replace('/\D/', '', $userId) . '.json';
    $recent = array_filter(
        is_file($file) ? (json_decode((string) file_get_contents($file), true) ?: []) : [],
        fn ($t) => $t > time() - 3600
    );
    if (count($recent) >= $limit) {
        return false;
    }
    $recent[] = time();
    file_put_contents($file, json_encode(array_values($recent)), LOCK_EX);
    return true;
}
