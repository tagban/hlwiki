<?php
declare(strict_types=1);
require __DIR__ . '/src/bootstrap.php';

$cfg = config();
$user = current_user();

if (!$user) {
    $role = e($cfg['discord']['role_name'] ?? 'contributor');
    render('Edit the ' . $cfg['site_name'], <<<HTML
        <p>Members of our Discord server with the <strong>$role</strong> role can edit pages and add new ones here.</p>
        <p>Every edit is reviewed before it appears on the site.</p>
        <p><a class="button" href="login.php">Log in with Discord</a></p>
        <p class="note">Discord only shares your username and whether you have the $role role. The editor can't read your messages or see your other servers.</p>
        HTML);
}

$user = require_contributor();

try {
    $pending = github()->openPullRequests('edit/' . $user['id'] . '-');
} catch (HttpError $e) {
    $pending = [];
}

// Page files come from GitHub; titles come from the list the site publishes at /index.json.
$pages = [];
try {
    $titles = [];
    $special = [];
    [$status, $data] = http_request('GET', $cfg['site_url'] . '/index.json');
    foreach ($status === 200 && is_array($data) ? $data : [] as $p) {
        $titles[$p['path']] = $p['title'];
        if (!empty($p['special'])) {
            $special[$p['path']] = true;
        }
    }
    foreach (github()->contentFiles() as $path) {
        $page = substr($path, strlen('content/'));
        if (!isset($special[$page])) {
            $title = $titles[$page] ?? ucfirst(str_replace(['/index.md', '.md', '-'], ['', '', ' '], $page));
            if (str_starts_with($page, 'categories/')) {
                $title = 'Category: ' . $title;
            } elseif (str_starts_with($page, 'navboxes/')) {
                $title = 'Sidebar link box: ' . ucfirst(basename(dirname($page)));
            }
            $pages[] = ['path' => $page, 'title' => $title];
        }
    }
    usort($pages, fn ($a, $b) => strcasecmp($a['title'], $b['title']));
} catch (HttpError $e) {
    $pages = [];
}

ob_start();
?>
<p>Hi <?= e($user['name']) ?>! Pick a page to edit, or start a new one. Your edits are sent for review and appear on the site once they're approved.</p>
<p><a class="button" href="edit.php?new=1">Create a new page</a></p>

<?php if ($pending): ?>
<h2>Your edits waiting for review</h2>
<ul>
  <?php foreach ($pending as $pr): ?>
  <li><a href="<?= e($pr['html_url']) ?>"><?= e($pr['title']) ?></a> <span class="note">(sent <?= e(date('M j, Y', strtotime($pr['created_at']))) ?>)</span></li>
  <?php endforeach ?>
</ul>
<?php endif ?>

<h2>Edit a page</h2>
<?php if ($pages): ?>
<ul class="pagelist">
  <?php foreach ($pages as $p): ?>
  <li><a href="edit.php?page=<?= e(rawurlencode($p['path'])) ?>"><?= e($p['title']) ?></a></li>
  <?php endforeach ?>
</ul>
<?php else: ?>
<p>The page list could not be loaded. You can also use the <strong>Edit with Discord</strong> link at the bottom of any wiki page.</p>
<?php endif ?>
<?php
render('Editor', (string) ob_get_clean());
