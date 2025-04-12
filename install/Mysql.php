<?php if(!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<ul class="typecho-option">
    <li>
        <label class="typecho-label" for="dbHost"><?php _e('DbHost'); ?></label>
        <input type="text" class="text" name="dbHost" id="dbHost" value="localhost"/>
        <p class="description"><?php _e('"%s"', 'localhost'); ?></p>
    </li>
</ul>

<ul class="typecho-option">
    <li>
        <label class="typecho-label" for="dbUser"><?php _e('DbUser'); ?></label>
        <input type="text" class="text" name="dbUser" id="dbUser" value="" />
        <p class="description"><?php _e('"%s"', 'root'); ?></p>
    </li>
</ul>

<ul class="typecho-option">
    <li>
        <label class="typecho-label" for="dbPassword"><?php _e('DbPassword'); ?></label>
        <input type="password" class="text" name="dbPassword" id="dbPassword" value="" />
    </li>
</ul>
<ul class="typecho-option">
    <li>
        <label class="typecho-label" for="dbDatabase"><?php _e('DbName'); ?></label>
        <input type="text" class="text" name="dbDatabase" id="dbDatabase" value="" />
        <p class="description"><?php _e('Vui lòng điền tên database'); ?></p>
    </li>

</ul>

<details>
    <summary>
        <strong><?php _e('Tùy chọn nâng cao'); ?></strong>
    </summary>
    <ul class="typecho-option">
        <li>
            <label class="typecho-label" for="dbPort"><?php _e('DbPort'); ?></label>
            <input type="text" class="text" name="dbPort" id="dbPort" value="3306"/>
            <p class="description"><?php _e('Hãy để nó ở cài đặt mặc định'); ?></p>
        </li>
    </ul>

    <ul class="typecho-option">
        <li>
            <label class="typecho-label" for="dbCharset"><?php _e('Mã hóa'); ?></label>
            <select name="dbCharset" id="dbCharset">
                <option value="utf8mb4">utf8mb4</option>
                <option value="utf8">utf8</option>
            </select>
            <p class="description"><?php _e('Việc chọn mã hóa utf8mb4 yêu cầu ít nhất phiên bản MySQL thấp nhất là 5.5.3'); ?></p>
        </li>
    </ul>

    <ul class="typecho-option">
        <li>
            <label class="typecho-label" for="dbEngine"><?php _e('Công cụ'); ?></label>
            <select name="dbEngine" id="dbEngine">
                <option value="InnoDB">InnoDB</option>
                <option value="MyISAM">MyISAM</option>
            </select>
        </li>
    </ul>

    <ul class="typecho-option">
        <li>
            <label class="typecho-label" for="dbSslCa"><?php _e('Chứng chỉ SSL'); ?></label>
            <input type="text" class="text" name="dbSslCa" id="dbSslCa"/>
            <p class="description"><?php _e('Nếu database của bạn đã bật SSL, hãy điền vào đường dẫn chứng chỉ CA. Nếu không hãy để trống'); ?></p>
        </li>
    </ul>

    <ul class="typecho-option">
        <li>
            <label class="typecho-label" for="dbSslVerify"><?php _e('Xác minh chứng chỉ SSL'); ?></label>
            <select name="dbSslVerify" id="dbSslVerify">
                <option value="on"><?php _e('Bật'); ?></option>
                <option value="off"><?php _e('Tắt'); ?></option>
            </select>
        </li>
    </ul>
</details>
