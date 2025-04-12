<?php

namespace Widget\Users;

use Typecho\Common;
use Typecho\Db\Exception;
use Typecho\Plugin;
use Typecho\Widget\Helper\Form;
use Utils\PasswordHash;
use Widget\ActionInterface;
use Widget\Base\Options;
use Widget\Notice;
use Widget\Plugins\Rows;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Chỉnh sửa thành phần người dùng
 *
 * @link typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Profile extends Edit implements ActionInterface
{
    /**
     * Thực thi chức năng
     */
    public function execute()
    {
        /** Người dùng đã đăng ký trở lên */
        $this->user->pass('subscriber');
        $this->request->setParam('uid', $this->user->uid);
    }

    /**
     * Cấu trúc biểu mẫu đầu ra
     *
     * @access public
     * @return Form
     */
    public function optionsForm(): Form
    {
        /** Xây dựng bảng */
        $form = new Form($this->security->getIndex('/action/users-profile'), Form::POST_METHOD);

        /** Soạn cài đặt */
        $markdown = new Form\Element\Radio(
            'markdown',
            ['0' => _t('Tắt'), '1' => _t('Bật')],
            $this->options->markdown,
            _t('Sử dụng cú pháp Markdown khi soạn thảo'),
            _t('Việc sử dụng cú pháp <a href="https://daringfireball.net/projects/markdown/">Markdown</a> có thể giúp quá trình viết của bạn dễ dàng, nhanh chóng và trực quan hơn.')
            . '<br />' . _t('Khuyên thật lòng: nên bật')
        );
        $form->addInput($markdown);

        $xmlrpcMarkdown = new Form\Element\Radio(
            'xmlrpcMarkdown',
            ['0' => _t('Tắt'), '1' => _t('Bật')],
            $this->options->xmlrpcMarkdown,
            _t('Sử dụng Markdown trong XMLRPC'),
            _t('Đối với những người chỉnh sửa ngoại tuyến hỗ trợ đầy đủ việc viết cú pháp <a href="https://daringfireball.net/projects/markdown/">Markdown</a>, việc bật tùy chọn này sẽ ngăn việc chuyển đổi nội dung sang HTML.')
        );
        $form->addInput($xmlrpcMarkdown);

        /** Tự động lưu */
        $autoSave = new Form\Element\Radio(
            'autoSave',
            ['0' => _t('Tắt'), '1' => _t('Bật')],
            $this->options->autoSave,
            _t('Tự động lưu bài viết'),
            _t('Chức năng tự động lưu có thể bảo vệ nội dung mà bạn đã viết, tránh được những trường hợp không may xảy ra. Nên bật')
        );
        $form->addInput($autoSave);

        /** Được phép theo mặc định */
        $allow = [];
        if ($this->options->defaultAllowComment) {
            $allow[] = 'comment';
        }

        if ($this->options->defaultAllowPing) {
            $allow[] = 'ping';
        }

        if ($this->options->defaultAllowFeed) {
            $allow[] = 'feed';
        }

        $defaultAllow = new Form\Element\Checkbox(
            'defaultAllow',
            ['comment' => _t('Bình luận'), 'ping' => _t('Trích dẫn'), 'feed' => _t('Tổng hợp')],
            $allow,
            _t('Quyền mặc định'),
            _t('Đặt quyền mặc định bạn sử dụng thường xuyên.')
        );
        $form->addInput($defaultAllow);

        /** Hành động của người dùng */
        $do = new Form\Element\Hidden('do', null, 'options');
        $form->addInput($do);

        /** nút gửi */
        $submit = new Form\Element\Submit('submit', null, _t('Lưu cài đặt'));
        $submit->input->setAttribute('class', 'btn primary');
        $form->addItem($submit);

        return $form;
    }

    /**
     * Danh sách cài đặt tùy chỉnh
     *
     * @throws Plugin\Exception
     */
    public function personalFormList()
    {
        $plugins = Rows::alloc('activated=1');

        while ($plugins->next()) {
            if ($plugins->personalConfig) {
                [$pluginFileName, $className] = Plugin::portal($plugins->name, $this->options->pluginDir);

                $form = $this->personalForm($plugins->name, $className, $pluginFileName, $group);
                if ($this->user->pass($group, true)) {
                    echo '<br><section id="personal-' . $plugins->name . '">';
                    echo '<h3>' . $plugins->title . '</h3>';

                    $form->render();

                    echo '</section>';
                }
            }
        }
    }

    /**
     * Tùy chọn cài đặt tùy chỉnh đầu ra
     *
     * @access public
     * @param string $pluginName Tên plugin
     * @param string $className tên lớp
     * @param string $pluginFileName Tên tệp trình cắm
     * @param string|null $group Chức vụ người dùng
     * @throws Plugin\Exception
     */
    public function personalForm(string $pluginName, string $className, string $pluginFileName, ?string &$group)
    {
        /** Xây dựng bảng */
        $form = new Form($this->security->getIndex('/action/users-profile'), Form::POST_METHOD);
        $form->setAttribute('name', $pluginName);
        $form->setAttribute('id', $pluginName);

        require_once $pluginFileName;
        $group = call_user_func([$className, 'personalConfig'], $form);
        $group = $group ?: 'subscriber';

        $options = $this->options->personalPlugin($pluginName);

        if (!empty($options)) {
            foreach ($options as $key => $val) {
                $form->getInput($key)->value($val);
            }
        }

        $form->addItem(new Form\Element\Hidden('do', null, 'personal'));
        $form->addItem(new Form\Element\Hidden('plugin', null, $pluginName));
        $submit = new Form\Element\Submit('submit', null, _t('Lưu cài đặt'));
        $submit->input->setAttribute('class', 'btn primary');
        $form->addItem($submit);
        return $form;
    }

    /**
     * Cập nhật người dùng
     *
     * @throws Exception
     */
    public function updateProfile()
    {
        if ($this->profileForm()->validate()) {
            $this->response->goBack();
        }

        /** Nhận dữ liệu */
        $user = $this->request->from('mail', 'screenName', 'url');
        $user['screenName'] = empty($user['screenName']) ? $user['name'] : $user['screenName'];

        /** Cập nhật dữ liệu */
        $this->update($user, $this->db->sql()->where('uid = ?', $this->user->uid));

        /** Đặt điểm nổi bật */
        Notice::alloc()->highlight('user-' . $this->user->uid);

        /** Tin nhắn nhắc nhở */
        Notice::alloc()->set(_t('Tập tin của bạn đã được sửa!'), 'success');

        /** Tới trang gốc */
        $this->response->goBack();
    }

    /**
     * Tạo biểu mẫu
     *
     * @return Form
     */
    public function profileForm()
    {
        /** Xây dựng bảng */
        $form = new Form($this->security->getIndex('/action/users-profile'), Form::POST_METHOD);

        /** Biệt hiệu của người dùng */
        $screenName = new Form\Element\Text('screenName', null, null, _t('Biệt danh'), _t('Biệt danh có thể khác với tên tài khoản và được sử dụng để hiển thị thay cho tên tài khoản.')
            . '<br />' . _t('Nếu để trống, tên tài khoản sẽ được sử dụng.'));
        $form->addInput($screenName);

        /** Địa chỉ trang facebook */
        $url = new Form\Element\Text('url', null, null, _t('Địa chỉ trang Facebook'), _t('Đây là địa chỉ trang Facebook của tài khoản này, vui lòng bắt đầu bằng <code>https://facebook.com/******</code>.'));
        $form->addInput($url);

        /** Địa chỉ email */
        $mail = new Form\Element\Text('mail', null, null, _t('Email') . ' *', _t('Địa chỉ Email sẽ đóng vai trò là địa chỉ liên hệ chính của tài khoản này.')
            . '<br />' . _t('Vui lòng không sao chép địa chỉ Email hiện có trong hệ thống.'));
        $form->addInput($mail);

        /** Hành động của người dùng */
        $do = new Form\Element\Hidden('do', null, 'profile');
        $form->addInput($do);

        /** nút gửi */
        $submit = new Form\Element\Submit('submit', null, _t('Chỉnh sửa'));
        $submit->input->setAttribute('class', 'btn primary');
        $form->addItem($submit);

        $screenName->value($this->user->screenName);
        $url->value($this->user->url);
        $mail->value($this->user->mail);

        /** Thêm quy tắc vào biểu mẫu */
        $screenName->addRule([$this, 'screenNameExists'], _t('Biệt danh đã tồn tại!'));
        $screenName->addRule('xssCheck', _t('Vui lòng không sử dụng ký tự đặc biệt trong biệt danh!'));
        $url->addRule('url', _t('Địa chỉ Facebook không hợp lệ!'));
        $mail->addRule('required', _t('Bạn chưa nhập Email!'));
        $mail->addRule([$this, 'mailExists'], _t('Địa chỉ Email đã tồn tại!'));
        $mail->addRule('email', _t('Địa chỉ Email không đúng định dạng!'));

        return $form;
    }

    /**
     * Thực hiện hành động cập nhật
     *
     * @throws Exception
     */
    public function updateOptions()
    {
        $settings['autoSave'] = $this->request->autoSave ? 1 : 0;
        $settings['markdown'] = $this->request->markdown ? 1 : 0;
        $settings['xmlrpcMarkdown'] = $this->request->xmlrpcMarkdown ? 1 : 0;
        $defaultAllow = $this->request->getArray('defaultAllow');

        $settings['defaultAllowComment'] = in_array('comment', $defaultAllow) ? 1 : 0;
        $settings['defaultAllowPing'] = in_array('ping', $defaultAllow) ? 1 : 0;
        $settings['defaultAllowFeed'] = in_array('feed', $defaultAllow) ? 1 : 0;

        foreach ($settings as $name => $value) {
            if (
                $this->db->fetchObject($this->db->select(['COUNT(*)' => 'num'])
                    ->from('table.options')->where('name = ? AND user = ?', $name, $this->user->uid))->num > 0
            ) {
                Options::alloc()
                    ->update(
                        ['value' => $value],
                        $this->db->sql()->where('name = ? AND user = ?', $name, $this->user->uid)
                    );
            } else {
                Options::alloc()->insert([
                    'name'  => $name,
                    'value' => $value,
                    'user'  => $this->user->uid
                ]);
            }
        }

        Notice::alloc()->set(_t("Đã lưu cài đặt!"), 'success');
        $this->response->goBack();
    }

    /**
     * Cập nhật mật khẩu
     *
     * @throws Exception
     */
    public function updatePassword()
    {
        /** Xác minh định dạng */
        if ($this->passwordForm()->validate()) {
            $this->response->goBack();
        }

        $hasher = new PasswordHash(8, true);
        $password = $hasher->hashPassword($this->request->password);

        /** Cập nhật dữ liệu */
        $this->update(
            ['password' => $password],
            $this->db->sql()->where('uid = ?', $this->user->uid)
        );

        /** Đặt điểm nổi bật */
        Notice::alloc()->highlight('user-' . $this->user->uid);

        /** Tin nhắn nhắc nhở */
        Notice::alloc()->set(_t('Thay đổi mật khẩu thành công!'), 'success');

        /** Tới trang gốc */
        $this->response->goBack();
    }

    /**
     * Tạo biểu mẫu
     *
     * @return Form
     */
    public function passwordForm(): Form
    {
        /** Xây dựng bảng */
        $form = new Form($this->security->getIndex('/action/users-profile'), Form::POST_METHOD);

        /** Mật khẩu người dùng */
        $password = new Form\Element\Password('password', null, null, _t('Mật khẩu'), _t('Thay đổi mật khẩu cho tài khoản này.')
            . '<br />' . _t('Nên sử dụng mật khẩu khó nhớ gồm các ký tự đặc biệt, chữ cái và số để tăng tính bảo mật cho tài khoản.'));
        $password->input->setAttribute('class', 'w-60');
        $form->addInput($password);

        /** Xác nhận mật khẩu */
        $confirm = new Form\Element\Password('confirm', null, null, _t('Xác nhận mật khẩu'), _t('Hãy điền lại mật khẩu mà bạn vừa điền ở trên.'));
        $confirm->input->setAttribute('class', 'w-60');
        $form->addInput($confirm);

        /** Hành động của người dùng */
        $do = new Form\Element\Hidden('do', null, 'password');
        $form->addInput($do);

        /** nút gửi */
        $submit = new Form\Element\Submit('submit', null, _t('Lưu lại'));
        $submit->input->setAttribute('class', 'btn primary');
        $form->addItem($submit);

        $password->addRule('required', _t('Bạn chưa điền mật khẩu!'));
        $password->addRule('minLength', _t('Để tài khoản của bạn bảo mật hơn, vui lòng nhập mật khẩu có ít nhất 6 ký tự'), 6);
        $confirm->addRule('confirm', _t('Hai mật khẩu bạn điền không khớp.'), 'password');

        return $form;
    }

    /**
     * Cập nhật cài đặt cá nhân
     *
     * @throws \Typecho\Widget\Exception
     */
    public function updatePersonal()
    {
        /** Nhận tên plugin */
        $pluginName = $this->request->plugin;

        /** Nhận các plugin được kích hoạt */
        $plugins = Plugin::export();
        $activatedPlugins = $plugins['activated'];

        /** Nhận mục nhập plugin */
        [$pluginFileName, $className] = Plugin::portal(
            $this->request->plugin,
            __TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_PLUGIN_DIR__
        );
        $info = Plugin::parseInfo($pluginFileName);

        if (!$info['personalConfig'] || !isset($activatedPlugins[$pluginName])) {
            throw new \Typecho\Widget\Exception(_t('Không thể định cấu hình plugin!'), 500);
        }

        $form = $this->personalForm($pluginName, $className, $pluginFileName, $group);
        $this->user->pass($group);

        /** Mẫu xác nhận */
        if ($form->validate()) {
            $this->response->goBack();
        }

        $settings = $form->getAllRequest();
        unset($settings['do'], $settings['plugin']);
        $name = '_plugin:' . $pluginName;

        if (!$this->personalConfigHandle($className, $settings)) {
            if (
                $this->db->fetchObject($this->db->select(['COUNT(*)' => 'num'])
                    ->from('table.options')->where('name = ? AND user = ?', $name, $this->user->uid))->num > 0
            ) {
                Options::alloc()
                    ->update(
                        ['value' => serialize($settings)],
                        $this->db->sql()->where('name = ? AND user = ?', $name, $this->user->uid)
                    );
            } else {
                Options::alloc()->insert([
                    'name'  => $name,
                    'value' => serialize($settings),
                    'user'  => $this->user->uid
                ]);
            }
        }

        /** Tin nhắn nhắc nhở */
        Notice::alloc()->set(_t("Cài đặt %s đã được lưu!", $info['title']), 'success');

        /** Tới trang gốc */
        $this->response->redirect(Common::url('profile.php', $this->options->adminUrl));
    }

    /**
     * Sử dụng các chức năng của riêng bạn để xử lý thông tin cấu hình tùy chỉnh
     *
     * @access public
     * @param string $className Tên lớp
     * @param array $settings giá trị cấu hình
     * @return boolean
     */
    public function personalConfigHandle(string $className, array $settings): bool
    {
        if (method_exists($className, 'personalConfigHandle')) {
            call_user_func([$className, 'personalConfigHandle'], $settings, false);
            return true;
        }

        return false;
    }

    /**
     * Chức năng nhập
     *
     * @access public
     * @return void
     */
    public function action()
    {
        $this->security->protect();
        $this->on($this->request->is('do=profile'))->updateProfile();
        $this->on($this->request->is('do=options'))->updateOptions();
        $this->on($this->request->is('do=password'))->updatePassword();
        $this->on($this->request->is('do=personal&plugin'))->updatePersonal();
        $this->response->redirect($this->options->siteUrl);
    }
}
