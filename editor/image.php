<?php
declare(strict_types=1);
require __DIR__ . '/src/bootstrap.php';

// Shows an uploaded image that hasn't been submitted yet, to the person who uploaded it.
$user = require_contributor();
$image = null;
foreach ($_SESSION['uploads'] ?? [] as $form) {
    $image = $image ?? ($form[(string) ($_GET['id'] ?? '')] ?? null);
}
if (!$image || !is_file($image['file'])) {
    http_response_code(404);
    exit;
}
header('Content-Type: ' . $image['mime']);
header('Cache-Control: private, max-age=3600');
readfile($image['file']);
