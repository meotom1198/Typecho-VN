<?php

namespace Typecho\Widget\Helper\Form\Element;

use Typecho\Widget\Helper\Form\Element;
use Typecho\Widget\Helper\Layout;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Lớp trợ giúp nút radio
 *
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Radio extends Element
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
        foreach ($options as $value => $label) {
            $this->options[$value] = new Layout('input');
            $item = $this->multiline();
            $id = $this->name . '-' . $this->filterValue($value);
            $this->inputs[] = $this->options[$value];

            $item->addItem($this->options[$value]->setAttribute('name', $this->name)
                ->setAttribute('type', 'radio')
                ->setAttribute('value', $value)
                ->setAttribute('id', $id));

            $labelItem = new Layout('label');
            $item->addItem($labelItem->setAttribute('for', $id)->html($label));
            $this->container($item);
        }

        return current($this->options) ?: null;
    }

    /**
     * Đặt giá trị phần tử biểu mẫu
     *
     * @param mixed $value giá trị phần tử biểu mẫu
     */
    protected function inputValue($value)
    {
        foreach ($this->options as $option) {
            $option->removeAttribute('checked');
        }

        if (isset($this->options[$value])) {
            $this->value = $value;
            $this->options[$value]->setAttribute('checked', 'true');
            $this->input = $this->options[$value];
        }
    }
}
