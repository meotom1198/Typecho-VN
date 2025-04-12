<?php
include 'common.php';
include 'header.php';
include 'menu.php';
?>

<div class="main">
    <div class="body container">
        <?php include 'page-title.php'; ?>
        <div class="row typecho-page-main" role="main">
            <div class="col-mb-12">
                <div id="typecho-welcome" class="message">
                    <form action="<?php $options->adminUrl(); ?>" method="get">
                    <h3><?php _e('Chào mừng bạn đã tham gia vào <b> "%s"</b>. Hãy tự mình khám phá thêm các tính năng tiện ích nhé!', $options->title); ?></h3>
                    <ol>
                        <li><a class="operate-delete" href="<?php $options->adminUrl('profile.php#change-password'); ?>"><?php _e('Thay đổi mật khẩu'); ?></a></li>
                        <?php if($user->pass('contributor', true)): ?>
                        <li><a href="<?php $options->adminUrl('category.php'); ?>"><?php _e('Tạo danh mục'); ?></a></li>
                        <li><a href="<?php $options->adminUrl('write-post.php'); ?>"><?php _e('Đăng bài viết'); ?></a></li>
                        <li><a href="<?php $options->siteUrl(); ?>"><?php $user->pass('administrator', true) ? _e('Về trang chủ') : _e('Về trang chủ'); ?></a></li>
                        <?php else: ?>
                        <li><a href="<?php $options->siteUrl(); ?>"><?php _e('Về trang chủ'); ?></a></li>
                        <?php endif; ?>
                    </ol>
                    <p><button type="submit" class="btn primary"><?php _e('Hoàn thành &raquo;'); ?></button></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'copyright.php';
include 'common-js.php';
include 'footer.php';
?>
