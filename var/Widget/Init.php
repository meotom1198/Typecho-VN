<?php

namespace Widget;

use Typecho\Common;
use Typecho\Cookie;
use Typecho\Date;
use Typecho\Db;
use Typecho\I18n;
use Typecho\Plugin;
use Typecho\Response;
use Typecho\Router;
use Typecho\Widget;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Mô-đun khởi tạo
 *
 * @package Widget
 */
class Init extends Widget
{
    /**
     * Chức năng nhập, khởi tạo bộ định tuyến
     *
     * @access public
     * @return void
     * @throws Db\Exception
     */
    public function execute()
    {
        /** ngoại lệ khởi tạo */
        if (!defined('__TYPECHO_DEBUG__') || !__TYPECHO_DEBUG__) {
            set_exception_handler(function (\Throwable $exception) {
                Response::getInstance()->clean();
                ob_end_clean();

                ob_start(function ($content) {
                    Response::getInstance()->sendHeaders();
                    return $content;
                });

                if (404 == $exception->getCode()) {
                    ExceptionHandle::alloc();
                } else {
                    Common::error($exception);
                }

                exit;
            });
        }

        // init class
        define('__TYPECHO_CLASS_ALIASES__', [
            'Typecho_Plugin_Interface'    => '\Typecho\Plugin\PluginInterface',
            'Typecho_Widget_Helper_Empty' => '\Typecho\Widget\Helper\EmptyClass',
            'Typecho_Db_Adapter_Mysql'    => '\Typecho\Db\Adapter\Mysqli',
            'Widget_Abstract'             => '\Widget\Base',
            'Widget_Abstract_Contents'    => '\Widget\Base\Contents',
            'Widget_Abstract_Comments'    => '\Widget\Base\Comments',
            'Widget_Abstract_Metas'       => '\Widget\Base\Metas',
            'Widget_Abstract_Options'     => '\Widget\Base\Options',
            'Widget_Abstract_Users'       => '\Widget\Base\Users',
            'Widget_Metas_Category_List'  => '\Widget\Metas\Category\Rows',
            'Widget_Contents_Page_List'   => '\Widget\Contents\Page\Rows',
            'Widget_Plugins_List'         => '\Widget\Plugins\Rows',
            'Widget_Themes_List'          => '\Widget\Themes\Rows',
            'Widget_Interface_Do'         => '\Widget\ActionInterface',
            'Widget_Do'                   => '\Widget\Action',
            'AutoP'                       => '\Utils\AutoP',
            'PasswordHash'                => '\Utils\PasswordHash',
            'Markdown'                    => '\Utils\Markdown',
            'HyperDown'                   => '\Utils\HyperDown',
            'Helper'                      => '\Utils\Helper',
            'Upgrade'                     => '\Utils\Upgrade'
        ]);

        /** Gán giá trị cho một biến */
        $options = Options::alloc();

        /** Khởi tạo gói ngôn ngữ */
        if ($options->lang && $options->lang != 'zh_CN') {
            $dir = defined('__TYPECHO_LANG_DIR__') ? __TYPECHO_LANG_DIR__ : __TYPECHO_ROOT_DIR__ . '/usr/langs';
            I18n::setLang($dir . '/' . $options->lang . '.mo');
        }

        /** Khởi tạo thư mục tập tin sao lưu */
        if (!defined('__TYPECHO_BACKUP_DIR__')) {
            define('__TYPECHO_BACKUP_DIR__', __TYPECHO_ROOT_DIR__ . '/usr/backups');
        }

        /** khởi tạo cookie */
        Cookie::setPrefix($options->rootUrl);
        if (defined('__TYPECHO_COOKIE_OPTIONS__')) {
            Cookie::setOptions(__TYPECHO_COOKIE_OPTIONS__);
        }

        /** khởi tạo cookie */
        Router::setRoutes($options->routingTable);

        /** Khởi tạo plugin */
        Plugin::init($options->plugins);

        /** Biên nhận khởi tạo */
        $this->response->setCharset($options->charset);
        $this->response->setContentType($options->contentType);

        /** Khởi tạo múi giờ */
        Date::setTimezoneOffset($options->timezone);

        /** Bắt đầu phiên, giảm tải và chỉ bật hỗ trợ phiên cho nền */
        if ($options->installed && User::alloc()->hasLogin()) {
            @session_start();
        }
    }
}
