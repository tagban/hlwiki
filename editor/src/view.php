<?php
declare(strict_types=1);

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $to): never
{
    header('Location: ' . $to, true, 303);
    exit;
}

function render(string $title, string $body): never
{
    $cfg = config();
    $user = $_SESSION['user'] ?? null;
    $site = e($cfg['site_name']);
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title><?= e($title) ?> - <?= $site ?> editor</title>
<?php foreach ($cfg['stylesheets'] as $css): ?>
<link rel="stylesheet" href="<?= e(site_url($css)) ?>">
<?php endforeach ?>
<link rel="stylesheet" href="editor.css">
</head>
<body>
<div id="page">
  <div id="header">
    <a href="<?= e(site_url('/')) ?>" class="logo"><?php if (!empty($cfg['logo'])): ?><img src="<?= e(site_url($cfg['logo'])) ?>" alt="<?= $site ?>"><?php else: ?><?= $site ?><?php endif ?></a>
    <span class="tagline">Editor</span>
  </div>
  <div id="topbar">
    <a href="<?= e(site_url('/')) ?>">Back to <?= $site ?></a> |
    <a href="./">Editor home</a>
    <?php if ($user): ?>
      | <span class="who"><?php if (!empty($user['avatar'])): ?><img src="https://cdn.discordapp.com/avatars/<?= e($user['id']) ?>/<?= e($user['avatar']) ?>.png?size=32" alt="" width="16" height="16"> <?php endif ?><?= e($user['name']) ?></span>
      <form method="post" action="logout.php" class="inline"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><button type="submit" class="linkbutton">Log out</button></form>
    <?php endif ?>
  </div>
  <div id="layout" class="editor-layout">
    <div id="main">
      <div id="content">
        <h1 class="title"><?= e($title) ?></h1>
        <?= $body ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>
    <?php
    exit;
}

function render_message(string $title, string $html): never
{
    render($title, $html);
}

function render_not_allowed(array $user): never
{
    $d = config()['discord'];
    $role = e($d['role_name'] ?? 'contributor');
    $invite = e($d['invite'] ?? '');
    if (!$user['member']) {
        render('Join our Discord first', "<p>Editing is open to members of our Discord server with the <strong>$role</strong> role.</p>"
            . ($invite ? "<p><a class=\"button\" href=\"$invite\">Join the Discord server</a></p>" : '')
            . '<p>Once you have joined and been given the role, <a href="login.php">log in again</a>.</p>');
    }
    render('You need the ' . ($d['role_name'] ?? 'contributor') . ' role', "<p>You're in our Discord server, but editing needs the <strong>$role</strong> role. Ask a moderator in the Discord server to add it.</p><p>Once you have it, <a href=\"login.php\">log in again</a>.</p>");
}

function render_error(Throwable $e): never
{
    error_log('flatwiki editor: ' . $e->getMessage() . ($e instanceof HttpError ? " [HTTP {$e->status}] {$e->body}" : ''));
    http_response_code(502);
    render('Something went wrong', '<p>' . e($e->getMessage()) . '</p><p>Please try again in a few minutes. If it keeps happening, let us know in the Discord server.</p>');
}
