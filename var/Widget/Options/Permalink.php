<?php

namespace Widget\Options;

use Typecho\Common;
use Typecho\Cookie;
use Typecho\Db\Exception;
use Typecho\Http\Client;
use Typecho\Router\Parser;
use Typecho\Widget\Helper\Form;
use Widget\ActionInterface;
use Widget\Base\Options;
use Widget\Notice;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Các thành phần thiết lập cơ bản
 *
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Permalink extends Options implements ActionInterface
{
    /**
     * Kiểm tra xem pagePattern có chứa các tham số cần thiết không
     *
     * @param mixed $value
     * @return bool
     */
    public function checkPagePattern($value): bool
    {
        return strpos($value, '{slug}') !== false || strpos($value, '{cid}') !== false;
    }

    /**
     * Kiểm tra xem CategoryPattern có chứa các tham số cần thiết không
     *
     * @param mixed $value
     * @return bool
     */
    public function checkCategoryPattern($value): bool
    {
        return strpos($value, '{slug}') !== false
            || strpos($value, '{mid}') !== false
            || strpos($value, '{directory}') !== false;
    }

    /**
     * Kiểm tra xem có thể viết lại được không
     *
     * @param string $value Có nên bật viết lại hay không
     * @return bool
     */
    public function checkRewrite(string $value)
    {
        if ($value) {
            $this->user->pass('administrator');

            /** Đầu tiên yêu cầu xác minh địa chỉ từ xa trực tiếp */
            $client = Client::get();
            $hasWrote = false;

            if (!file_exists(__TYPECHO_ROOT_DIR__ . '/.htaccess') && strpos(php_sapi_name(), 'apache') !== false) {
                if (is_writeable(__TYPECHO_ROOT_DIR__)) {
                    $parsed = parse_url($this->options->siteUrl);
                    $basePath = empty($parsed['path']) ? '/' : $parsed['path'];
                    $basePath = rtrim($basePath, '/') . '/';

                    $hasWrote = file_put_contents(__TYPECHO_ROOT_DIR__ . '/.htaccess', "<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase {$basePath}
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ {$basePath}index.php/$1 [L]
</IfModule>");
                }
            }

            try {
                if ($client) {
                    /** Gửi yêu cầu viết lại địa chỉ */
                    $client->setData(['do' => 'remoteCallback'])
                        ->setHeader('User-Agent', $this->options->generator)
                        ->setHeader('X-Requested-With', 'XMLHttpRequest')
                        ->send(Common::url('/action/ajax', $this->options->siteUrl));

                    if (200 == $client->getResponseStatus() && 'OK' == $client->getResponseBody()) {
                        return true;
                    }
                }

                if (false !== $hasWrote) {
                    @unlink(__TYPECHO_ROOT_DIR__ . '/.htaccess');

                    // Để nâng cao khả năng tương thích, hãy sử dụng các quy tắc viết lại kiểu chuyển hướng của WordPress. Mặc dù hiệu quả hơi kém nhưng nó có khả năng tương thích tốt hơn với chế độ fastcgi.
                    $hasWrote = file_put_contents(__TYPECHO_ROOT_DIR__ . '/.htaccess', "# Wapvn.Top
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ /index.php/$1 [L]
</IfModule>");

                    // Xác minh lại
                    $client = Client::get();

                    if ($client) {
                        /** Gửi yêu cầu viết lại địa chỉ */
                        $client->setData(['do' => 'remoteCallback'])
                            ->setHeader('User-Agent', $this->options->generator)
                            ->setHeader('X-Requested-With', 'XMLHttpRequest')
                            ->send(Common::url('/action/ajax', $this->options->siteUrl));

                        if (200 == $client->getResponseStatus() && 'OK' == $client->getResponseBody()) {
                            return true;
                        }
                    }

                    unlink(__TYPECHO_ROOT_DIR__ . '/.htaccess');
                }
            } catch (Client\Exception $e) {
                if (false != $hasWrote) {
                    @unlink(__TYPECHO_ROOT_DIR__ . '/.htaccess');
                }
                return false;
            }

            return false;
        } elseif (file_exists(__TYPECHO_ROOT_DIR__ . '/.htaccess')) {
            @unlink(__TYPECHO_ROOT_DIR__ . '/.htaccess');
        }

        return true;
    }

    /**
     * Thực hiện hành động cập nhật
     *
     * @throws Exception
     */
    public function updatePermalinkSettings()
    {
        /** Xác minh định dạng */
        if ($this->form()->validate()) {
            Cookie::set('__typecho_form_item_postPattern', $this->request->customPattern);
            $this->response->goBack();
        }

        $patternValid = $this->checkRule($this->request->postPattern);

        /** phân tích mẫu url */
        if ('custom' == $this->request->postPattern) {
            $this->request->postPattern = '/' . ltrim($this->encodeRule($this->request->customPattern), '/');
        }

        $settings = defined('__TYPECHO_REWRITE__') ? [] : $this->request->from('rewrite');
        if (isset($this->request->postPattern) && isset($this->request->pagePattern)) {
            $routingTable = $this->options->routingTable;
            $routingTable['post']['url'] = $this->request->postPattern;
            $routingTable['page']['url'] = '/' . ltrim($this->encodeRule($this->request->pagePattern), '/');
            $routingTable['category']['url'] = '/' . ltrim($this->encodeRule($this->request->categoryPattern), '/');
            $routingTable['category_page']['url'] = rtrim($routingTable['category']['url'], '/') . '/[page:digital]/';

            if (isset($routingTable[0])) {
                unset($routingTable[0]);
            }

            $settings['routingTable'] = serialize($routingTable);
        }

        foreach ($settings as $name => $value) {
            $this->update(['value' => $value], $this->db->sql()->where('name = ?', $name));
        }

        if ($patternValid) {
            Notice::alloc()->set(_t("Đã lưu cài đặt!"), 'success');
        } else {
            Notice::alloc()->set(_t("URL tùy chỉnh đang xung đột với các URL tùy chỉnh khá ! Hãy thử lại đến khi nào không còn cảnh báo này nữa."), 'notice');
        }
        $this->response->goBack();
    }

    /**
     * Cấu trúc biểu mẫu đầu ra
     *
     * @return Form
     */
    public function form(): Form
    {
        /** Xây dựng bảng */
        $form = new Form($this->security->getRootUrl('index.php/action/options-permalink'), Form::POST_METHOD);

        if (!defined('__TYPECHO_REWRITE__')) {
            /** Có nên sử dụng chức năng ghi lại địa chỉ hay không */
            $rewrite = new Form\Element\Radio(
                'rewrite',
                ['0' => _t('Tắt'), '1' => _t('Bật')],
                $this->options->rewrite,
                _t('Rewrite URL'),
                _t('Đây là chức năng viết lại URL của bài viết, danh mục và trang độc lập, giúp loại bỏ /index.php khỏi URL của blog. ') . '<br />'
                . _t('Tính năng này làm cho các liên kết của blog thân thiện hơn với các công cụ tìm kiếm phổ biến hiện nay.')
            );

            // disable rewrite check when rewrite opened
            if (!$this->options->rewrite && !$this->request->is('enableRewriteAnyway=1')) {
                $errorStr = _t('Không thể mở tính năng Rewrite URL trên máy chủ của bạn.');

                /** Nếu là máy chủ apache thì có thể xảy ra lỗi không ghi được file .htaccess. */
                if (
                    strpos(php_sapi_name(), 'apache') !== false
                    && !file_exists(__TYPECHO_ROOT_DIR__ . '/.htaccess')
                    && !is_writeable(__TYPECHO_ROOT_DIR__)
                ) {
                    $errorStr .= '<br /><strong>' . _t('Bạn đang sử dụng máy chủ Apache nhưng Typecho VN không thể tự động tạo file .htaccess trong thư mục /public_html/')
                        . _t('Vui lòng thay đổi quyền truy cập hoặc tạo tệp .htaccess theo cách thủ công với nội dung như sau: <code># Wapvn.Top
&lt;IfModule mod_rewrite.c&gt;
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ /index.php/$1 [L]
&lt;/IfModule&gt;</code>') . '</strong>';
                }

                $errorStr .=
                    '<br /><input type="checkbox" name="enableRewriteAnyway" id="enableRewriteAnyway" value="1" />'
                    . ' <label for="enableRewriteAnyway">' . _t('Nếu bạn vẫn muốn bật tính năng này, vui lòng tích vào ô dưới đây.') . '</label>';
                $rewrite->addRule([$this, 'checkRewrite'], $errorStr);
            }

            $form->addInput($rewrite);
        }

        $patterns = [
            '/archives/[cid:digital]/'                                        => _t('Kiểu mặc định')
                . ' <code>/archives/{cid}/</code>',
            '/archives/[slug].html'                                           => _t('Kiểu Wordpress')
                . ' <code>/archives/{slug}.html</code>',
            '/[year:digital:4]/[month:digital:2]/[day:digital:2]/[slug].html' => _t('Kiểu ngày tháng năm')
                . ' <code>/{year}/{month}/{day}/{slug}.html</code>',
            '/[category]/[slug].html'                                         => _t('Kiểu URL danh mục')
                . ' <code>/blogs/{cid}-{slug}.html</code>' ,
            '/[category]/[slug].html'                                         => _t('Kiểu URL blogs')
                . ' <code>/blogs/{cid}-{slug}.html</code>'
        ];

        /** Đường dẫn bài viết tùy chỉnh */
        $postPatternValue = $this->options->routingTable['post']['url'];

        /** Thêm đường dẫn được cá nhân hóa */
        $customPatternValue = null;
        if (isset($this->request->__typecho_form_item_postPattern)) {
            $customPatternValue = $this->request->__typecho_form_item_postPattern;
            Cookie::delete('__typecho_form_item_postPattern');
        } elseif (!isset($patterns[$postPatternValue])) {
            $customPatternValue = $this->decodeRule($postPatternValue);
        }
        $patterns['custom'] = _t('Tuỳ chỉnh:') .
            ' <input type="text" class="w-50 text-s mono" name="customPattern" value="' . $customPatternValue . '" />';

        $postPattern = new Form\Element\Radio(
            'postPattern',
            $patterns,
            $postPatternValue,
            _t('Tùy chỉnh URL bài viết'),
            _t('ID bài viết <code>{cid}</code>, bài viết <code>{slug}</code>, danh mục <code>{category}</code>, danh mục con <code>{directory}</code>, năm <code>{year}</code> , tháng <code>{month}</code>, ngày <code>{day}</code>')
            . '<br />' . _t('Hãy chọn kiểu URL bài viết phù hợp để URL của blog thân thiện hơn.')
            . '<br />' . _t('Khi bạn đã thiết lập URL tùy chỉnh rồi thì hãy hạn chế thay đổi.')
        );
        if ($customPatternValue) {
            $postPattern->value('custom');
        }
        $form->addInput($postPattern->multiMode());

        /** Hậu tố trang độc lập */
        $pagePattern = new Form\Element\Text(
            'pagePattern',
            null,
            $this->decodeRule($this->options->routingTable['page']['url']),
            _t('Tùy chỉnh URL trang độc lập'),
            _t('ID trang html <code>{cid}</code>, URL trang độc lập <code>{slug}</code>')
            . '<br />' . _t('Hãy chọn ít nhất một biến để sử dụng.')
        );
        $pagePattern->input->setAttribute('class', 'mono w-60');
        $form->addInput($pagePattern->addRule([$this, 'checkPagePattern'], _t('URLtrang độc lập không chứa {cid} hoặc {slug}!')));

        /** Trang chuyên mục */
        $categoryPattern = new Form\Element\Text(
            'categoryPattern',
            null,
            $this->decodeRule($this->options->routingTable['category']['url']),
            _t('Tùy chỉnh URL danh mục'),
            _t('ID danh mục <code>{mid}</code>, URL  danh mục <code>{slug}</code>, danh mục con <code>{directory}</code>')
            . '<br />' . _t('Hãy chọn ít nhất một biến để sử dụng.')
        );
        $categoryPattern->input->setAttribute('class', 'mono w-60');
        $form->addInput($categoryPattern->addRule([$this, 'checkCategoryPattern'], _t('URL danh mục không chứa {mid} hoặc {slug}!')));

        /** nút gửi */
        $submit = new Form\Element\Submit('submit', null, _t('Lưu cài đặt'));
        $submit->input->setAttribute('class', 'btn primary');
        $form->addItem($submit);

        return $form;
    }

    /**
     * Phân tích đường dẫn tùy chỉnh
     *
     * @param string $rule Đường dẫn được giải mã
     * @return string
     */
    protected function decodeRule(string $rule): string
    {
        return preg_replace("/\[([_a-z0-9-]+)[^\]]*\]/i", "{\\1}", $rule);
    }

    /**
     * Kiểm tra xem các quy tắc có xung đột không
     *
     * @param string $value quy tắc định tuyến
     * @return boolean
     */
    public function checkRule(string $value): bool
    {
        if ('custom' != $value) {
            return true;
        }

        $routingTable = $this->options->routingTable;
        $currentTable = ['custom' => ['url' => $this->encodeRule($this->request->customPattern)]];
        $parser = new Parser($currentTable);
        $currentTable = $parser->parse();
        $regx = $currentTable['custom']['regx'];

        foreach ($routingTable as $key => $val) {
            if ('post' != $key && 'page' != $key) {
                $pathInfo = preg_replace("/\[([_a-z0-9-]+)[^\]]*\]/i", "{\\1}", $val['url']);
                $pathInfo = str_replace(
                    ['{cid}', '{slug}', '{category}', '{year}', '{month}', '{day}', '{', '}'],
                    ['123', 'hello', 'default', '2008', '08', '08', '', ''],
                    $pathInfo
                );

                if (preg_match($regx, $pathInfo)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Mã hóa đường dẫn tùy chỉnh
     *
     * @param string $rule đường dẫn được mã hóa
     * @return string
     */
    protected function encodeRule(string $rule): string
    {
        return str_replace(
            ['{cid}', '{slug}', '{category}', '{directory}', '{year}', '{month}', '{day}', '{mid}'],
            [
                '[cid:digital]', '[slug]', '[category]', '[directory:split:0]',
                '[year:digital:4]', '[month:digital:2]', '[day:digital:2]', '[mid:digital]'
            ],
            $rule
        );
    }

    /**
     * Hành động ràng buộc
     *
     * @access public
     * @return void
     */
    public function action()
    {
        $this->user->pass('administrator');
        $this->security->protect();
        $this->on($this->request->isPost())->updatePermalinkSettings();
        $this->response->redirect($this->options->adminUrl);
    }
}
