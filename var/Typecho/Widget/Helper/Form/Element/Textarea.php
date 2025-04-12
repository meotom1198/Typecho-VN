<?php

namespace Typecho\Widget\Helper\Form\Element;

use Typecho\Widget\Helper\Form\Element;
use Typecho\Widget\Helper\Layout;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Lớp trợ giúp trường văn bản nhiều dòng
 *
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Textarea extends Element
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
        $input = new Layout('textarea', ['id' => $name . '-0-' . self::$uniqueId, 'name' => $name]);
        $this->label->setAttribute('for', $name . '-0-' . self::$uniqueId);
        $this->container($input->setClose(false));
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
        $this->input->html(htmlspecialchars($value));
    }
}
