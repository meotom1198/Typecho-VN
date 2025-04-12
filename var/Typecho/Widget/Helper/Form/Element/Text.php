<?php

namespace Typecho\Widget\Helper\Form\Element;

use Typecho\Widget\Helper\Form\Element;
use Typecho\Widget\Helper\Layout;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Lớp trợ giúp mục biểu mẫu nhập văn bản
 *
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Text extends Element
{
    /**
     * Khởi tạo mục đầu vào hiện tại
     *
     * @param string|null $name Tên phần tử biểu mẫu
     * @param array|null $options Tùy chọn
     * @return Layout|null
     */
    public function input(?string $name = null, ?array $options = null): ?Layout
    {
        $input = new Layout('input', ['id' => $name . '-0-' . self::$uniqueId,
            'name' => $name, 'type' => 'text', 'class' => 'text']);
        $this->container($input);
        $this->label->setAttribute('for', $name . '-0-' . self::$uniqueId);
        $this->inputs[] = $input;

        return $input;
    }

    /**
     * Đặt giá trị mặc định của mục biểu mẫu
     *
     * @param mixed $value Đặt giá trị mặc định của mục biểu mẫu
     */
    protected function inputValue($value)
    {
        if (isset($value)) {
            $this->input->setAttribute('value', htmlspecialchars($value));
        } else {
            $this->input->removeAttribute('value');
        }
    }
}
