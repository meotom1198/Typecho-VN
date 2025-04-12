<?php

namespace Typecho\Widget\Helper\Form\Element;

use Typecho\Widget\Helper\Form\Element;
use Typecho\Widget\Helper\Layout;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Lớp trợ giúp miền ảo
 *
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Fake extends Element
{
    /**
     * Người xây dựng
     *
     * @param string $name Tên đầu vào của biểu mẫu
     * @param mixed $value giá trị mặc định của biểu mẫu
     */
    public function __construct(string $name, $value)
    {
        $this->name = $name;
        self::$uniqueId++;

        /** Chạy chức năng ban đầu tùy chỉnh */
        $this->init();

        /** Khởi tạo các mục biểu mẫu */
        $this->input = $this->input($name);

        /** Khởi tạo giá trị biểu mẫu */
        if (null !== $value) {
            $this->value($value);
        }
    }

    /**
     * Khởi tạo mục đầu vào hiện tại
     *
     * @param string|null $name Tên phần tử biểu mẫu
     * @param array|null $options Tùy chọn
     * @return Layout|null
     */
    public function input(?string $name = null, ?array $options = null): ?Layout
    {
        $input = new Layout('input');
        $this->inputs[] = $input;
        return $input;
    }

    /**
     * Đặt giá trị mặc định của mục biểu mẫu
     *
     * @param mixed $value giá trị mặc định của mục biểu mẫu
     */
    protected function inputValue($value)
    {
        $this->input->setAttribute('value', $value);
    }
}
