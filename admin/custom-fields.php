<?php if (!defined('__TYPECHO_ADMIN__')) exit; ?>
<?php
$fields = isset($post) ? $post->getFieldItems() : $page->getFieldItems();
$defaultFields = isset($post) ? $post->getDefaultFieldItems() : $page->getDefaultFieldItems();
?>
<section id="custom-field"
         class="typecho-post-option<?php if (empty($defaultFields) && empty($fields)): ?> fold<?php endif; ?>">
    <label id="custom-field-expand" class="typecho-label"><a href="##"><i
                class="i-caret-right"></i> <?php _e('Trường tùy chỉnh'); ?></a></label>
    <table class="typecho-list-table mono">
        <colgroup>
            <col width="20%"/>
            <col width="15%"/>
            <col width="55%"/>
            <col width="10%"/>
        </colgroup>
        <?php foreach ($defaultFields as $field): ?>
            <?php [$label, $input] = $field; ?>
            <tr>
                <td><?php $label->render(); ?></td>
                <td colspan="3"><?php $input->render(); ?></td>
            </tr>
        <?php endforeach; ?>
        <?php foreach ($fields as $field): ?>
            <tr>
                <td>
                    <label for="fieldname" class="sr-only"><?php _e('Tên trường'); ?></label>
                    <input type="text" name="fieldNames[]" value="<?php echo htmlspecialchars($field['name']); ?>"
                           id="fieldname" class="text-s w-100">
                </td>
                <td>
                    <label for="fieldtype" class="sr-only"><?php _e('Loại trường'); ?></label>
                    <select name="fieldTypes[]" id="fieldtype">
                        <option
                            value="str"<?php if ('str' == $field['type']): ?> selected<?php endif; ?>><?php _e('Nhân vật'); ?></option>
                        <option
                            value="int"<?php if ('int' == $field['type']): ?> selected<?php endif; ?>><?php _e('Nhân vật'); ?></option>
                        <option
                            value="float"<?php if ('float' == $field['type']): ?> selected<?php endif; ?>><?php _e('Số thập phân'); ?></option>
                        <option
                            value="json"<?php if ('json' == $field['type']): ?> selected<?php endif; ?>><?php _e('Cấu trúc JSON'); ?></option>
                    </select>
                </td>
                <td>
                    <label for="fieldvalue" class="sr-only"><?php _e('Giá trị trường'); ?></label>
                    <textarea name="fieldValues[]" id="fieldvalue" class="text-s w-100"
                              rows="2"><?php echo htmlspecialchars($field[($field['type'] == 'json' ? 'str' : $field['type']) . '_value']); ?></textarea>
                </td>
                <td>
                    <button type="button" class="btn btn-xs"><?php _e('Xóa bỏ'); ?></button>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($defaultFields) && empty($fields)): ?>
            <tr>
                <td>
                    <label for="fieldname" class="sr-only"><?php _e('Tên trường'); ?></label>
                    <input type="text" name="fieldNames[]" placeholder="<?php _e('Tên trường'); ?>" id="fieldname"
                           class="text-s w-100">
                </td>
                <td>
                    <label for="fieldtype" class="sr-only"><?php _e('Loại trường'); ?></label>
                    <select name="fieldTypes[]" id="fieldtype">
                        <option value="str"><?php _e('Nhân vật'); ?></option>
                        <option value="int"><?php _e('Số nguyên'); ?></option>
                        <option value="float"><?php _e('Số thập phân'); ?></option>
                    </select>
                </td>
                <td>
                    <label for="fieldvalue" class="sr-only"><?php _e('Giá trị trường'); ?></label>
                    <textarea name="fieldValues[]" placeholder="<?php _e('Giá trị trường'); ?>" id="fieldvalue"
                              class="text-s w-100" rows="2"></textarea>
                </td>
                <td>
                    <button type="button" class="btn btn-xs"><?php _e('Xóa bỏ'); ?></button>
                </td>
            </tr>
        <?php endif; ?>
    </table>
    <div class="description clearfix">
        <button type="button" class="btn btn-xs operate-add"><?php _e('+Thêm trường'); ?></button>
        <?php _e('Các trường tùy chỉnh có thể mở rộng chức năng của mẫu của bạn. Để biết hướng dẫn sử dụng, hãy xem <a href="https://docs.typecho.org/help/custom-fields">Tài liệu trợ giúp</a>'); ?>
    </div>
</section>
