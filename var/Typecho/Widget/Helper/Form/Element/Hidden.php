<?php

namespace Typecho\Widget\Helper\Form\Element;

use Typecho\Widget\Helper\Form\Element;
use Typecho\Widget\Helper\Layout;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Lớp trợ giúp trường ẩn
 *
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Hidden extends Element
{
    /**
     * Chức năng ban đầu tùy chỉnh
     *
     * @return void
     */
    public function init()
    {
        /** Ẩn dòng này */
        $this->setAttribute('style', 'display:none');
    }

    /**
     * Khởi tạo mục đầu vào hiện tại
     *
     * @access public
     * @param string|null $name Tên phần tử biểu mẫu
     * @param array|null $options Tùy chọn
     * @return Layout|null
     */
    public function input(?string $name = null, ?array $options = null): ?Layout
    {
        $input = new Layout('input', ['name' => $name, 'type' => 'hidden']);
        $this->container($input);
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
        $this->input->setAttribute('value', htmlspecialchars($value));
    }
}
