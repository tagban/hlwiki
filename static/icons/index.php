<?php
// Define the directory to scan (current directory)
$dir = './';

// Scan the directory for .png files
$files = glob($dir . "*.png");

// Sort files naturally (1, 2, 10 instead of 1, 10, 2)
natsort($files);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Icon Gallery</title>
    <style>
        body { font-family: sans-serif; background: #1a1a1a; color: white; }
        .gallery { display: flex; flex-wrap: wrap; gap: 15px; padding: 20px; }
        .icon-card { text-align: center; background: #333; padding: 10px; border-radius: 8px; }
        img { display: block; max-width: 232px; height: auto; margin-bottom: 5px; }
        span { font-size: 12px; color: #bbb; }
    </style>
</head>
<body>

    <div class="gallery">
        <?php foreach ($files as $file): ?>
            <div class="icon-card">
                <img src="<?php echo $file; ?>" alt="Icon">
                <span><?php echo basename($file); ?></span>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>