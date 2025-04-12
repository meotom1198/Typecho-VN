<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE HTML>
<html>
    <meta charset="<?php $this->options->charset(); ?>">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php $this->archiveTitle([
            'category' => _t('%s'),
            'search'   => _t('%s'),
            'tag'      => _t('%s'),
            'author'   => _t('%s')
        ], '', ' - '); ?><?php $this->options->title(); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400..800&family=Comfortaa:wght@300..700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/wordpress-premium/font-awesome-pro@7ab451a50c28f3a7b9f3fa425ef7570f3bf345bc/css/all.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/wordpress-premium/font-awesome-pro@7ab451a50c28f3a7b9f3fa425ef7570f3bf345bc/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/gh/wordpress-premium/font-awesome-pro@7ab451a50c28f3a7b9f3fa425ef7570f3bf345bc/js/all.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/wordpress-premium/font-awesome-pro@7ab451a50c28f3a7b9f3fa425ef7570f3bf345bc/js/all.min.js"></script>
    <link rel="stylesheet" href="<?php $this->options->themeUrl('normalize.css'); ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('grid.css'); ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('typechovn.css'); ?>">
    <link rel="shortcut icon" href="/images/favicon.ico" />
    <?php $this->header(); ?>
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
<body>

<header id="header" class="clearfix">
    <div class="container">
        <div class="row">
            <div class="site-name col-mb-12 col-9">
                <?php if ($this->options->logoUrl): ?>
                    <a id="logo" href="<?php $this->options->siteUrl(); ?>">
                        <img src="<?php $this->options->logoUrl() ?>" alt="<?php $this->options->title() ?>"/>
                    </a>
                <?php else: ?>
                    <a id="logo" href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title() ?></a>
                    <p class="description"><?php $this->options->description() ?></p>
                <?php endif; ?>
            </div>
            <div class="site-search col-3 kit-hidden-tb">
                <form id="search" method="post" action="<?php $this->options->siteUrl(); ?>" role="search">
                    <label for="s" class="sr-only"><?php _e('Tìm kiếm từ khóa'); ?></label>
                    <input type="text" id="s" name="s" class="text" placeholder="<?php _e('Nhập từ khóa tìm kiếm'); ?>"/>
                    <button type="submit" class="submit"><?php _e('Tìm kiếm'); ?></button>
                </form>
            </div>
            <div class="col-mb-12">
                <nav id="nav-menu" class="clearfix" role="navigation">
                    <a<?php if ($this->is('index')): ?> class="current"<?php endif; ?>
                        href="<?php $this->options->siteUrl(); ?>"><?php _e('Trang chủ'); ?></a>
                    <?php \Widget\Contents\Page\Rows::alloc()->to($pages); ?>
                    <?php while ($pages->next()): ?>
                        <a<?php if ($this->is('page', $pages->slug)): ?> class="current"<?php endif; ?>
                            href="<?php $pages->permalink(); ?>"
                            title="<?php $pages->title(); ?>"><?php $pages->title(); ?></a>
                    <?php endwhile; ?>
                </nav>
            </div>
        </div>
    </div>
</header>
<div id="body">
    <div class="container">
        <div class="row">

    
    
