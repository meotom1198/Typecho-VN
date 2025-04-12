<?php

namespace Widget;

use Typecho\Common;
use Typecho\Cookie;
use Typecho\Db\Exception;
use Typecho\Validate;
use Utils\PasswordHash;
use Widget\Base\Users;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Đăng ký thành phần
 *
 * @author qining
 * @category typecho
 * @package Widget
 */
class Register extends Users implements ActionInterface
{
    /**
     * hàm khởi tạo
     *
     * @throws Exception
     */
    public function action()
    {
        // protect
        $this->security->protect();

        /** Nếu đã đăng nhập */
        if ($this->user->hasLogin() || !$this->options->allowRegister) {
            /** Trả lại trực tiếp */
            $this->response->redirect($this->options->index);
        }

        /** Khởi tạo lớp xác nhận */
        $validator = new Validate();
        $validator->addRule('name', 'required', _t('Bạn chưa điền tên tài khoản!'));
        $validator->addRule('name', 'minLength', _t('Tên tài khoản dài tối thiểu 2 ký tự!'), 2);
        $validator->addRule('name', 'maxLength', _t('Tên tài khoản dài tối đa 32 ký tự!'), 32);
        $validator->addRule('name', 'xssCheck', _t('Không sử dụng ký tự đặc biệt trong tên tài khoản!'));
        $validator->addRule('name', [$this, 'nameExists'], _t('Tên tài khoản đã tồn tại!'));
        $validator->addRule('mail', 'required', _t('Bạn chưa nhập Email!'));
        $validator->addRule('mail', [$this, 'mailExists'], _t('Địa chỉ Email đã tồn tại!'));
        $validator->addRule('mail', 'email', _t('Địa chỉ Email bạn vừa nhập không đúng định dạng!'));
        $validator->addRule('mail', 'maxLength', _t('Địa chỉ email dài tối đa 64 ký tự!'), 64);

        /** Nếu có mật khẩu trong yêu cầu */
        if (array_key_exists('password', $_REQUEST)) {
            $validator->addRule('password', 'required', _t('Bạn chưa điền mật khẩu!'));
            $validator->addRule('password', 'minLength', _t('Để đảm bảo an toàn cho tài khoản của bạn, vui lòng nhập mật khẩu dài tối thiểu 6 chữ số!'), 6);
            $validator->addRule('password', 'maxLength', _t('Để mật khẩu dễ nhớ, độ dài mật khẩu không được vượt quá 18 ký tự!'), 18);
            $validator->addRule('confirm', 'confirm', _t('Mật khẩu bạn vừa nhập không khớp!'), 'password');
        }

        /** Chặn các ngoại lệ xác minh */
        if ($error = $validator->run($this->request->from('name', 'password', 'mail', 'confirm'))) {
            Cookie::set('__typecho_remember_name', $this->request->name);
            Cookie::set('__typecho_remember_mail', $this->request->mail);

            /** Chặn các ngoại lệ xác minh */
            Notice::alloc()->set($error);
            $this->response->goBack();
        }

        $hasher = new PasswordHash(8, true);
        $generatedPassword = Common::randString(7);

        $dataStruct = [
            'name' => $this->request->name,
            'mail' => $this->request->mail,
            'screenName' => $this->request->name,
            'password' => $hasher->hashPassword($generatedPassword),
            'created' => $this->options->time,
            'group' => 'subscriber'
        ];

        $dataStruct = self::pluginHandle()->register($dataStruct);

        $insertId = $this->insert($dataStruct);
        $this->db->fetchRow($this->select()->where('uid = ?', $insertId)
            ->limit(1), [$this, 'push']);

        self::pluginHandle()->finishRegister($this);

        $this->user->login($this->request->name, $generatedPassword);

        Cookie::delete('__typecho_first_run');
        Cookie::delete('__typecho_remember_name');
        Cookie::delete('__typecho_remember_mail');

        Notice::alloc()->set(
            _t(
                'Bạn đã đăng ký tài khoản thành công, tài khoản: <strong>%s</strong>, mật khẩu: <strong>%s</strong>!',
                $this->screenName,
                $generatedPassword
            ),
            'success'
        );
        $this->response->redirect($this->options->adminUrl);
    }
}
