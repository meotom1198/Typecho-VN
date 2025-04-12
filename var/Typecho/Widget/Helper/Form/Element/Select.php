<?php

namespace Typecho\Widget\Helper\Form\Element;

use Typecho\Widget\Helper\Form\Element;
use Typecho\Widget\Helper\Layout;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Lớp trợ giúp hộp lựa chọn thả xuống
 *
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Select extends Element
{
    /**
     * Chọn giá trị
     *
     * @var array
     */
    private $options = [];

    /**
     * Khởi tạo mục đầu vào hiện tại
     *
     * @param string|null $name Tên phần tử biểu mẫu
     * @param array|null $options Tùy chọn
     * @return Layout|null
     */
    public function input(?string $name = null, ?array $options = null): ?Layout
    {
        $input = new Layout('select');
        $this->container($input->setAttribute('name', $name)
            ->setAttribute('id', $name . '-0-' . self::$uniqueId));
        $this->label->setAttribute('for', $name . '-0-' . self::$uniqueId);
        $this->inputs[] = $input;

        foreach ($options as $value => $label) {
            $this->options[$value] = new Layout('option');
            $input->addItem($this->options[$value]->setAttribute('value', $value)->html($label));
        }

        return $input;
    }

    /**
     * Đặt giá trị phần tử biểu mẫu
     *
     * @param mixed $value giá trị phần tử biểu mẫu
     */
    protected function inputValue($value)
    {
        foreach ($this->options as $option) {
            $option->removeAttribute('selected');
        }

        if (isset($this->options[$value])) {
            $this->options[$value]->setAttribute('selected', 'true');
        }
    }
}
