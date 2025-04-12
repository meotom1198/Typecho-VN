<?php
include 'common.php';
include 'header.php';
include 'menu.php';

$actionUrl = $security->getTokenUrl(
    \Typecho\Router::url('do', array('action' => 'backup', 'widget' => 'Backup'),
        \Typecho\Common::url('index.php', $options->rootUrl)));

$backupFiles = \Widget\Backup::alloc()->listFiles();
?>

<div class="main">
    <div class="body container">
        <?php include 'page-title.php'; ?>
        <div class="row typecho-page-main" role="main">
            <div class="col-mb-12 col-tb-8">
                <div id="typecho-welcome">
                    <form action="<?php echo $actionUrl; ?>" method="post">
                    <h3><?php _e('Sao lưu dữ liệu'); ?></h3>
                    <ul>
                        <li><?php _e('Đây là chức năng sao lưu lại data của toàn bộ: bài viết, bình luận, danh mục, tài khoản, các theme và plugin đã kích hoạt.'); ?></li>
                        <li><?php _e('Chức năng này không sao lưu các cài đặt của <b>Typecho VN</b>, không sao lưu các file mà bạn đã tải lên <strong>trên máy chủ</strong>, các file nằm <strong>trên máy chủ</strong> cần được sao lưu thủ công!'); ?></li>
                        <li><strong class="warning"><?php _e('Nếu dữ liệu sao lưu quá lớn, bạn nên sử dụng tính năng sao lưu trong trang quản lý máy chủ để tránh gặp lỗi trong quá trình sao lưu/khôi phục.'); ?></strong></li>
                    </ul>
                    <p><button class="btn primary" type="submit"><?php _e('Sao lưu &raquo;'); ?></button></p>
                        <input tabindex="1" type="hidden" name="do" value="export">
                    </form>
                </div>
            </div>

            <div id="backup-secondary" class="col-mb-12 col-tb-4" role="form">
                <h3><?php _e('Khôi phục dữ liệu'); ?></h3>
                <ul class="typecho-option-tabs clearfix">
                    <li class="active w-50"><a href="#from-upload"><?php _e('Tải lên'); ?></a></li>
                    <li class="w-50"><a href="#from-server"><?php _e('Chọn từ máy chủ'); ?></a></li>
                </ul>

                <form action="<?php echo $actionUrl; ?>" id="from-upload" class="tab-content" method="post" enctype="multipart/form-data">
                    <ul class="typecho-option">
                        <li>
                            <input tabindex="2" id="backup-upload-file" name="file" type="file" class="file">
                        </li>
                    </ul>
                    <ul class="typecho-option typecho-option-submit">
                        <li>
                            <button tabindex="4" type="submit" class="btn primary"><?php _e('Khôi phục &raquo;'); ?></button>
                            <input type="hidden" name="do" value="import">
                        </li>
                    </ul>
                </form>

                <form action="<?php echo $actionUrl; ?>" id="from-server" class="tab-content hidden" method="post">
                    <?php if (empty($backupFiles)): ?>
                    <ul class="typecho-option">
                        <li>
                            <p class="description"><?php _e('Hãy tải file bạn dùng để khôi phục lên thư mục: <strong class="warning">%s</strong> của máy chủ. Sau đó, các tùy chọn để khôi phục dữ liệu sẽ xuất hiện ở đây.', __TYPECHO_BACKUP_DIR__); ?></p>
                        </li>
                    </ul>
                    <?php else: ?>
                    <ul class="typecho-option">
                        <li>
                            <label class="typecho-label" for="backup-select-file"><?php _e('Chọn file .dat để khôi phục'); ?></label>
                            <select tabindex="5" name="file" id="backup-select-file">
                                <?php foreach ($backupFiles as $file): ?>
                                    <option value="<?php echo $file; ?>"><?php echo $file; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </li>
                    </ul>
                    <?php endif; ?>
                    <ul class="typecho-option typecho-option-submit">
                        <li>
                            <button tabindex="7" type="submit" class="btn primary"><?php _e('Khôi phục &raquo;'); ?></button>
                            <input type="hidden" name="do" value="import">
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include 'copyright.php';
include 'common-js.php';
?>
<script>
    $('#backup-secondary .typecho-option-tabs li').click(function() {
        $('#backup-secondary .typecho-option-tabs li').removeClass('active');
        $(this).addClass('active');
        $(this).parents('#backup-secondary').find('.tab-content').addClass('hidden');

        var selected_tab = $(this).find('a').attr('href');
        $(selected_tab).removeClass('hidden');

        return false;
    });

    $('#backup-secondary form').submit(function (e) {
        if (!confirm('<?php _e('Sau khi khôi phục dữ liệu sẽ xóa toàn bộ dữ liệu hiện tại, bạn có muốn khôi phục tiếp không?'); ?>')) {
            return false;
        }
    });
</script>
<?php include 'footer.php'; ?>
