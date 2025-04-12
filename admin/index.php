<?php
include 'common.php';
include 'header.php';
include 'menu.php';

$stat = \Widget\Stat::alloc();
$posts = \Widget\Contents\Post\Admin::alloc();
$isAllPosts = ('on' == $request->get('__typecho_all_posts') || 'on' == \Typecho\Cookie::get('__typecho_all_posts'));
?>
<div class="main">
    <div class="container typecho-dashboard">
        <?php include 'page-title.php'; ?>
        <div class="row typecho-page-main">
            <div class="col-mb-12 col-tb-6" role="complementary">
                <section class="latest-link">
                    <h3><i class="fa-duotone fa-solid fa-chart-line"></i> <?php _e('Thông tin'); ?></h3>
                    <?php _e('<ul>
                        <li><span><i class="fa-regular fa-angles-right"></i></span>
                                    Tổng <b style="color: #467B96;">%s</b> thành viên</li>
                        <li><span><i class="fa-regular fa-angles-right"></i></span>
                                    Tổng <b style="color: #467B96;">%s</b> bài viết</li>
                        <li><span><i class="fa-regular fa-angles-right"></i></span>
                                    Tổng <b style="color: #467B96;">%s</b> bình luận</li>
                        <li><span><i class="fa-regular fa-angles-right"></i></span>
                                    Tổng <b style="color: #467B96;">%s</b> danh mục</li>
                        <li><span><i class="fa-regular fa-angles-right"></i></span>
                                    Tổng <b style="color: #467B96;">%s</b> tập tin</li>
                    </ul>',
                        $stat->totalUserNum, $stat->PublishedPostsNum, $stat->publishedCommentsNum, $stat->categoriesNum, $stat->filesNum); ?>
                </section>
            </div>

            <div class="col-mb-12 col-tb-6" role="complementary">
                <section class="latest-link">
                <h3><i class="fa-duotone fa-light fa-right-left"></i> <?php _e('Truy cập nhanh'); ?></h3>
                            <?php if ($user->pass('administrator', true)): ?>
                    <ul>
                        <li>
                            <span><i class="fa-regular fa-angles-right"></i></span>
                            <a href="<?php $options->adminUrl('write-post.php'); ?>"><?php _e('Bài viết mới'); ?></a> -
                            <a href="<?php $options->adminUrl('write-page.php'); ?>"><?php _e('Tạo trang độc lập'); ?></a>
                        </li>
                        <li>
                            <span><i class="fa-regular fa-angles-right"></i></span>
                            <a href="<?php $options->adminUrl('manage-posts.php'); ?>"><?php _e('Quản lý bài viết'); ?></a> -
                            <a href="<?php $options->adminUrl('manage-pages.php'); ?>"><?php _e('Q.lý trang độc lập'); ?></a>
                        </li>
                        <li>
                            <span><i class="fa-regular fa-angles-right"></i></span>
                            <a href="<?php $options->adminUrl('options-general.php'); ?>"><?php _e('Cài đặt cơ bản'); ?></a> -  <a href="<?php $options->adminUrl('manage-comments.php'); ?>"><?php _e('Cài đặt bình luận'); ?>
                            <?php if ($user->pass('editor', true) && 'on' == $request->get('__typecho_all_comments')): ?>
                                <?php if ($stat->spamCommentsNum > 0): ?>
                            <b class="balloon"><?php $stat->spamCommentsNum(); ?></b>
                                <?php elseif ($stat->waitingCommentsNum > 0): ?>
                            <b class="balloon"><?php $stat->waitingCommentsNum(); ?></b>
                                <?php endif; ?>
                            <?php else: ?>
                            <?php if ($stat->mySpamCommentsNum > 0): ?>
                            <b class="balloon"><?php $stat->mySpamCommentsNum(); ?></b>
                                <?php elseif ($stat->myWaitingCommentsNum > 0): ?>
                            <b class="balloon"><?php $stat->myWaitingCommentsNum(); ?></b>
                            <?php endif; ?>
                            	</a> 
                            <?php endif; ?>
                        </li>
                        <li>
                            <span><i class="fa-regular fa-angles-right"></i></span>
                            <a href="<?php $options->adminUrl('themes.php'); ?>"><?php _e('Quản lý themes'); ?></a> -
                            <a href="<?php $options->adminUrl('plugins.php'); ?>"><?php _e('Quản lý plugins'); ?></a>
                        </li>
                        <li>
                            <span><i class="fa-regular fa-angles-right"></i></span>
                            <a href="<?php $options->adminUrl('options-general.php'); ?>"><?php _e('Cài đặt chung'); ?></a> -
                            <a href="<?php $options->adminUrl('manage-users.php'); ?>"><?php _e('Quản lý thành viên'); ?></a>
                        </li>
                    </ul>
                            <?php elseif ($user->pass('subscriber', true)): ?>
                    <ul>
                        <li>
                            <span><i class="fa-regular fa-angles-right"></i></span>
                            <a href="<?php $options->adminUrl('write-post.php'); ?>"><?php _e('Đăng bài viết'); ?></a>
                        </li>
                        <li>
                            <span><i class="fa-regular fa-angles-right"></i></span>
                            <a href="<?php $options->adminUrl('profile.php'); ?>"><?php _e('Cài đặt cá nhân'); ?></a>
                        </li>

                    </ul>
                            <?php endif; ?>
                </section>
            </div>

            <div class="col-mb-12 col-tb-6" role="complementary">
                <section class="latest-link">
                    <h3><i class="fa-solid fa-pen-nib"></i> <?php _e('Bài viết mới'); ?></h3>
                    <?php \Widget\Contents\Post\Recent::alloc('pageSize=10')->to($posts); ?>
                    <ul>
                        <?php if ($posts->have()): ?>
                            <?php while ($posts->next()): ?>
                                <li><span><i class="fa-regular fa-angles-right"></i></span> 
                                    <a href="/admin/write-post.php?cid=<?php $posts->cid(); ?>" class="title"><?php $posts->title(); ?></a>
                                </li>
                            <?php endwhile; ?>
                            <?php else: ?>
                                <li><em><?php _e('Chưa có bài viết nào để hiển thị'); ?></em></li>
                            <?php endif; ?>
                    </ul>
                </section>
            </div>

            <div class="col-mb-12 col-tb-6" role="complementary">
                <section class="latest-link">
                    <h3><i class="fa-duotone fa-solid fa-comment-dots"></i> <?php _e('Bình luận mới'); ?></h3>
                    <ul>
                        <?php \Widget\Comments\Recent::alloc('pageSize=10')->to($comments); ?>
                        <?php if ($comments->have()): ?>
                            <?php while ($comments->next()): ?>
                                <li><span><i class="fa-regular fa-angles-right"></i></span>
                                    <b>[</b><?php $comments->author(false); ?><b>]</b> 
                                    <a href="<?php $comments->permalink(); ?>"
                                       class="title"><?php $comments->excerpt(20, '...'); ?>
                                </a> | <b>Lúc:</b> <?php $comments->date('n-j-Y'); ?></li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li><?php _e('Chưa có bình luận nào để hiển thị'); ?></li>
                        <?php endif; ?>
                    </ul>
                </section>
            </div>
            
            <div class="col-mb-12 col-tb-6" role="complementary">
                <section class="latest-link">
                    <h3><i class="fa-duotone fa-solid fa-book"></i> <?php _e('Nhật ký'); ?></h3>
                    <div id="typecho-message">
                        <ul>
                            <li><?php _e('Đọc...'); ?></li>
                        </ul>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<?php
include 'copyright.php';
include 'common-js.php';
?>

<script>
    $(document).ready(function () {
        var ul = $('#typecho-message ul'), cache = window.sessionStorage,
            html = cache ? cache.getItem('feed') : '',
            update = cache ? cache.getItem('update') : '';

        if (!!html) {
            ul.html(html);
        } else {
            html = '';
            $.get('<?php $options->index('/action/ajax?do=feed'); ?>', function (o) {
                for (var i = 0; i < o.length; i++) {
                    var item = o[i];
                    html += '<li><span><i class="fa-regular fa-angles-right"></i> </span> <a href="' + item.link + '" class="title">' + item.title + '</a></li>';
                }

                ul.html(html);
                cache.setItem('feed', html);
            }, 'json');
        }

        function applyUpdate(update) {
            if (update.available) {
                $('<div class="update-check message error"><p>'
                    + '<?php _e('Bản Typecho VN hiện tại: %s'); ?>'.replace('%s', update.current) + '<br />'
                    + '<strong><a href="' + update.link + '" target="_blank">'
                    + '<?php _e('Bản Typecho mới nhất: %s'); ?>'.replace('%s', update.latest) + '</a></strong></p></div>')
                    .insertAfter('.typecho-page-title').effect('highlight');
            }
        }

        if (!!update) {
            applyUpdate($.parseJSON(update));
        } else {
            $.get('<?php $options->index('/action/ajax?do=checkVersion'); ?>', function (o, status, resp) {
                applyUpdate(o);
                cache.setItem('update', resp.responseText);
            }, 'json');
        }
    });

</script>
<?php include 'footer.php'; ?>