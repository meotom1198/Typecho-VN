<?php
    if (!defined('__TYPECHO_ADMIN__')) {
    exit;
    }
    $header = '
    <link rel="stylesheet" href="' . $options->adminStaticUrl('css', 'normalize.css', true) . '">
    <link rel="stylesheet" href="' . $options->adminStaticUrl('css', 'grid.css', true) . '">
    <link rel="stylesheet" href="' . $options->adminStaticUrl('css', 'style.css', true) . '">
    <link rel="shortcut icon" href="/images/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400..800&family=Comfortaa:wght@300..700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/wordpress-premium/font-awesome-pro@7ab451a50c28f3a7b9f3fa425ef7570f3bf345bc/css/all.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/wordpress-premium/font-awesome-pro@7ab451a50c28f3a7b9f3fa425ef7570f3bf345bc/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/gh/wordpress-premium/font-awesome-pro@7ab451a50c28f3a7b9f3fa425ef7570f3bf345bc/js/all.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/wordpress-premium/font-awesome-pro@7ab451a50c28f3a7b9f3fa425ef7570f3bf345bc/js/all.min.js"></script>';
    $header = \Typecho\Plugin::factory('admin/header.php')->header($header);
?>
<!DOCTYPE HTML>
<html lang="vi-VN">
    <head>
            <meta charset="<?php $options->charset(); ?>">
            <meta name="renderer" content="webkit">
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
            <title><?php _e('%s - %s', $menu->title, $options->title); ?></title>
            <meta name="robots" content="noindex, nofollow">
            <?php echo $header; ?>
        <style>
            body {
                font-family: "Baloo 2", sans-serif;
                font-optical-sizing: auto;
                font-weight: 400;
                font-style: normal;
                word-wrap: break-word;
                
            }
            h1, h2, h3, h4, h5, h6 {
                font-size: 90%;
            }
        </style>
    </head>
    <body<?php if (isset($bodyClass)) {echo ' class="' . $bodyClass . '"';} ?>>
