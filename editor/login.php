<?php
declare(strict_types=1);
require __DIR__ . '/src/bootstrap.php';

if (is_dev()) {
    redirect($_SESSION['return_to'] ?? './');
}

$_SESSION['oauth_state'] = bin2hex(random_bytes(16));
redirect(discord()->authorizeUrl($_SESSION['oauth_state']));
