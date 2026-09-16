<?php
declare(strict_types=1);
require __DIR__ . '/src/bootstrap.php';

$cfg = config();
$isPost = ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
$user = require_contributor($isPost && ($_POST['do'] ?? '') === 'submit');
$github = github();

// Images uploaded while editing wait on the server until the edit is submitted.
// Each open edit form keeps its own list, so editing in two tabs doesn't mix them up.
$formId = $isPost ? (string) preg_replace('/[^0-9a-f]/', '', (string) ($_POST['form_id'] ?? '')) : bin2hex(random_bytes(8));
if (!$isPost) {
    $_SESSION['uploads'] = array_slice($_SESSION['uploads'] ?? [], -9, null, true);
}
$uploads = $_SESSION['uploads'][$formId] ?? [];

// --- Load the form state ------------------------------------------------------

$isNew = !empty($_GET['new']) || !empty($_POST['new']);
$page = (string) ($_POST['page'] ?? $_GET['page'] ?? '');
$state = [
    'title' => '', 'category' => '', 'front' => '', 'body' => '', 'sha' => null, 'summary' => '',
];

if ($isPost) {
    require_post_with_csrf();
    foreach (['title', 'category', 'front', 'body', 'summary'] as $field) {
        $state[$field] = str_replace("\r\n", "\n", (string) ($_POST[$field] ?? ''));
    }
    $state['sha'] = ($_POST['sha'] ?? '') !== '' ? (string) $_POST['sha'] : null;
}

$path = null;
if (!$isNew) {
    $path = Page::contentPath($page);
    if ($path === null) {
        http_response_code(404);
        render_message('Page not found', '<p>That page name is not valid. <a href="./">Pick a page from the list</a>.</p>');
    }
    if (!$isPost) {
        try {
            $file = $github->file($path);
        } catch (HttpError $e) {
            render_error($e);
        }
        if ($file === null) {
            http_response_code(404);
            render_message('Page not found', '<p>There is no page called <code>' . e($page) . '</code>. <a href="./">Pick a page from the list</a>.</p>');
        }
        [$state['front'], $state['body']] = Page::split($file['text']);
        $state['sha'] = $file['sha'];
    }
    $state['title'] = Page::title($state['front']) ?: $page;
}

$errors = [];
$notice = '';
$previewHtml = null;
$action = $isPost ? (string) ($_POST['do'] ?? '') : '';

// --- Actions ------------------------------------------------------------------

if ($action === 'upload') {
    $files = $_FILES['images'] ?? null;
    $count = is_array($files['name'] ?? null) ? count($files['name']) : 0;
    $taken = array_column($uploads, 'name');
    for ($i = 0; $i < $count; $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        try {
            $image = Images::accept([
                'name' => $files['name'][$i], 'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i], 'size' => $files['size'][$i],
            ], $user['id'], $github);
            while (in_array($image['name'], $taken, true)) {
                $image['name'] = pathinfo($image['name'], PATHINFO_FILENAME) . '-' . count($taken) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
            }
            $taken[] = $image['name'];
            $uploads[$image['id']] = $image;
            $alt = ucfirst(str_replace(['_', '-'], ' ', pathinfo($image['name'], PATHINFO_FILENAME)));
            $state['body'] = rtrim($state['body']) . "\n\n![$alt](/images/{$image['name']})\n";
        } catch (InvalidArgumentException $ex) {
            $errors[] = e($ex->getMessage());
        } catch (HttpError $ex) {
            render_error($ex);
        }
    }
    $_SESSION['uploads'][$formId] = $uploads;
    if (!$errors && $count) {
        $notice = 'Image added to the end of the page. Move the line that starts with ![ to wherever you want the image to appear.';
    }
}

if (str_starts_with($action, 'remove:')) {
    $id = substr($action, 7);
    if (isset($uploads[$id])) {
        $state['body'] = (string) preg_replace('/\n*!\[[^\]]*\]\(\/images\/' . preg_quote($uploads[$id]['name'], '/') . '\)\n?/', "\n", $state['body']);
        @unlink($uploads[$id]['file']);
        unset($uploads[$id]);
        $_SESSION['uploads'][$formId] = $uploads;
    }
}

if ($action === 'preview') {
    try {
        $previewHtml = preview_html($state['body'], $uploads, $github);
    } catch (HttpError $ex) {
        $errors[] = 'The preview could not be shown right now, but you can still submit your edit.';
    }
}

if ($action === 'submit') {
    $summary = trim($state['summary']);
    if (mb_strlen($summary) < 3 || mb_strlen($summary) > 150) {
        $errors[] = 'Please describe your change in a few words (up to 150 characters).';
    }
    if ($isNew) {
        $title = trim($state['title']);
        $slug = Page::slug($title);
        if ($title === '' || $slug === '') {
            $errors[] = 'Please give the new page a title.';
        } else {
            $path = "content/$slug.md";
            if ($github->exists($path)) {
                $errors[] = 'A page with that name already exists: <a href="edit.php?page=' . e(rawurlencode("$slug.md")) . '">edit it instead</a>.';
            }
        }
        $text = Page::join(Page::newFrontMatter($title, trim($state['category'])), $state['body']);
    } else {
        if (!Page::validFrontMatter($state['front'])) {
            $errors[] = 'The page settings must start and end with a line of three dashes (---) and include a title.';
        }
        $text = Page::join($state['front'], $state['body']);
        try {
            $original = $github->file($path);
        } catch (HttpError $ex) {
            render_error($ex);
        }
        if ($original !== null && $original['sha'] === $state['sha'] && Page::join(...Page::split($original['text'])) === $text && !$uploads) {
            $errors[] = "You haven't changed anything yet.";
        }
    }
    if (trim($state['body']) === '') {
        $errors[] = 'The page text is empty.';
    }
    if (!$errors && !within_rate_limit($user['id'])) {
        $errors[] = "You've sent a lot of edits in the last hour. Please wait a bit before sending more.";
    }

    if (!$errors) {
        $files = [['path' => $path, 'content' => $text, 'sha' => $isNew ? null : $state['sha']]];
        $imageList = [];
        foreach ($uploads as $image) {
            if (!is_file($image['file'])) {
                continue;
            }
            $files[] = ['path' => 'static/images/' . $image['name'], 'content' => (string) file_get_contents($image['file']), 'sha' => null];
            $imageList[] = '`' . $image['name'] . '`';
        }
        $pageTitle = $isNew ? trim($state['title']) : $state['title'];
        $discordName = str_replace(['`', '*', '_', '[', ']'], '', $user['name']);
        $body = "Proposed with the Discord editor by **$discordName** (Discord `@{$user['username']}`, ID `{$user['id']}`).\n\n"
            . ($isNew ? 'New page' : 'Page') . ": **" . str_replace(['*', '`'], '', $pageTitle) . "** (`$path`)"
            . ($isNew ? '' : ", live at {$cfg['site_url']}" . Page::url($path)) . "\n"
            . ($imageList ? "\nImages added: " . implode(', ', $imageList) . "\n" : '')
            . "\n> " . str_replace("\n", ' ', $summary) . "\n\n"
            . 'Check the changes under **Files changed**, then merge to publish or close to decline.';
        try {
            $url = $github->proposeEdit(
                'edit/' . $user['id'] . '-' . gmdate('YmdHis'),
                $files,
                ($isNew ? 'New page: ' : "$pageTitle: ") . $summary,
                $body,
                ['name' => $user['name'], 'email' => $user['id'] . '+' . $user['username'] . '@users.discord.invalid']
            );
        } catch (EditConflict $ex) {
            $errors[] = 'Someone else changed this page after you opened it. Copy your text somewhere safe, <a href="edit.php?page=' . e(rawurlencode($page)) . '">reload the page</a>, and add your change again.';
        } catch (HttpError $ex) {
            render_error($ex);
        }
        if (!$errors) {
            foreach ($uploads as $image) {
                @unlink($image['file']);
            }
            unset($_SESSION['uploads'][$formId]);
            $log = !empty($github->dryRunLog) ? '<h2>Dry run</h2><pre>' . e(json_encode($github->dryRunLog, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) . '</pre>' : '';
            render('Thanks for your edit!', '<p>Your change has been sent for review. It will appear on the wiki once it is approved.</p>'
                . '<p><a href="' . e($url) . '">View your edit on GitHub</a> &middot; <a href="./">Back to the editor</a> &middot; <a href="' . e(site_url(Page::url($path))) . '">Back to the page</a></p>' . $log);
        }
    }
}

function preview_html(string $body, array $uploads, GitHub $github): string
{
    $site = config()['site_url'];
    // Show the wiki's shortcodes as the images they produce.
    $md = preg_replace_callback('/\{\{<\s*(iconbox|communityicon|banner)\s+"([^"]+)"\s*>\}\}/', function ($m) use ($site) {
        $src = ['iconbox' => "/icons/Icon_{$m[2]}.png", 'communityicon' => "/ik0ns/{$m[2]}.png", 'banner' => "/banners/{$m[2]}"][$m[1]];
        return "![{$m[2]}]($site$src)";
    }, $body);
    $md = preg_replace('/\{\{<\s*\/?gallery\s*>\}\}/', '', (string) $md);
    // Images waiting to be submitted aren't on the site yet.
    foreach ($uploads as $image) {
        $md = str_replace('(/images/' . $image['name'] . ')', '(' . config()['editor_url'] . "/image.php?id={$image['id']})", $md);
    }
    $md = preg_replace('/\]\(\/(?!\/)/', "]($site/", $md);
    $html = $github->markdown((string) $md);
    // GitHub routes images through its camo proxy, with the original URL hex-encoded
    // at the end. Point images from this wiki back at the wiki itself.
    $editor = config()['editor_url'];
    return (string) preg_replace_callback('#https://camo\.githubusercontent\.com/[0-9a-f]+/([0-9a-f]+)#', function ($m) use ($site, $editor) {
        $url = (string) @hex2bin($m[1]);
        if (str_starts_with($url, "$editor/")) {
            return e(substr($url, strlen(origin($editor))));
        }
        return str_starts_with($url, "$site/") ? e($url) : $m[0];
    }, $html);
}

// --- Form -------------------------------------------------------------------------

ob_start();
?>
<?php if ($errors): ?>
<div class="errors" role="alert"><?php foreach ($errors as $error): ?><p><?= $error ?></p><?php endforeach ?></div>
<?php endif ?>
<?php if ($notice): ?><p class="notice" role="status"><?= e($notice) ?></p><?php endif ?>

<?php if ($previewHtml !== null): ?>
<div class="preview">
  <p class="preview-label">Preview (approximate; not saved yet)</p>
  <?= $previewHtml ?>
</div>
<?php endif ?>

<form method="post" action="edit.php" enctype="multipart/form-data" class="editform">
  <?php /* Pressing Enter in a text box uses the first button in the form, so make that Preview. */ ?>
  <button type="submit" name="do" value="preview" class="hidden-default" tabindex="-1" aria-hidden="true">Preview</button>
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
  <input type="hidden" name="form_id" value="<?= e($formId) ?>">
  <input type="hidden" name="page" value="<?= e($page) ?>">
  <input type="hidden" name="sha" value="<?= e($state['sha']) ?>">
  <?php if ($isNew): ?>
  <input type="hidden" name="new" value="1">
  <label for="title">Page title</label>
  <input type="text" id="title" name="title" value="<?= e($state['title']) ?>" maxlength="120" required>
  <label for="category">Category <span class="note">(optional)</span></label>
  <input type="text" id="category" name="category" value="<?= e($state['category']) ?>" maxlength="60">
  <?php else: ?>
  <input type="hidden" name="title" value="<?= e($state['title']) ?>">
  <p class="note">Editing <a href="<?= e(site_url(Page::url($path))) ?>"><?= e($state['title']) ?></a>. <a href="https://www.markdownguide.org/basic-syntax/">How to format text</a></p>
  <?php endif ?>

  <label for="body">Page text</label>
  <textarea id="body" name="body" rows="24" spellcheck="true"><?= e($state['body']) ?></textarea>

  <fieldset class="images">
    <legend>Images</legend>
    <?php if ($uploads): ?>
    <ul>
      <?php foreach ($uploads as $image): ?>
      <li><img src="image.php?id=<?= e($image['id']) ?>" alt="" height="40"> <code><?= e($image['name']) ?></code>
        <button type="submit" name="do" value="remove:<?= e($image['id']) ?>" class="linkbutton">Remove</button></li>
      <?php endforeach ?>
    </ul>
    <?php endif ?>
    <label for="images">Add PNG, JPG, GIF or WebP images (up to <?= (int) (($cfg['limits']['image_max_bytes'] ?? 5_000_000) / 1_000_000) ?> MB each)</label>
    <input type="file" id="images" name="images[]" accept="image/png,image/jpeg,image/gif,image/webp" multiple>
    <button type="submit" name="do" value="upload">Upload images</button>
  </fieldset>

  <?php if (!$isNew): ?>
  <?php /* Message and sequence pages keep most of their content in the settings, so open it for them. */ ?>
  <details<?= ($errors && !Page::validFrontMatter($state['front'])) || strlen($state['front']) > strlen($state['body']) ? ' open' : '' ?>>
    <summary>Page settings (title, categories and other fields)</summary>
    <textarea name="front" rows="8" aria-label="Page settings"><?= e($state['front']) ?></textarea>
  </details>
  <?php endif ?>

  <label for="summary">What did you change?</label>
  <input type="text" id="summary" name="summary" value="<?= e($state['summary']) ?>" maxlength="150" placeholder="For example: Added download links for HotStuff 2.0.63">

  <p class="actions">
    <button type="submit" name="do" value="preview">Preview</button>
    <button type="submit" name="do" value="submit" class="primary">Send for review</button>
  </p>
</form>
<?php
render($isNew ? 'New page' : 'Editing ' . $state['title'], (string) ob_get_clean());
