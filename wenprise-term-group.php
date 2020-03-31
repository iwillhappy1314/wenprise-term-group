<?php
/*
Plugin Name:        Wenprise Term Group
Plugin URI:         https://www.wpzhiku.com/wenprise-term-group/
Description:        当网站有同义词标签时，此插件可以让同义词标签存档页面 301 跳转到主标签。
Version:            1.0.6
Author:             WordPress 智库
Author URI:         https://www.wpzhiku.com/
License:            MIT License
License URI:        http://opensource.org/licenses/MIT
*/

if (version_compare(phpversion(), '5.6.20', '<')) {

    // 显示警告信息
    if (is_admin()) {
        add_action('admin_notices', function ()
        {
            printf('<div class="error"><p>' . __('Wenprise Term Group 需要 PHP %1$s 以上版本才能运行，您当前的 PHP 版本为 %2$s， 请升级到 PHP 到 %1$s 或更新的版本， 否则插件没有任何作用。',
                    'wprs') . '</p></div>',
                '5.6.20', phpversion());
        });
    }

    return;
}

// 插件设置
// add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links)
// {
//     $url = admin_url('options-general.php?page=wenprise_term_group');
//     $url = '<a href="' . esc_url($url) . '">' . __('设置') . '</a>';
//     array_unshift($links, $url);
//
//     return $links;
// });

require_once(plugin_dir_path(__FILE__) . 'vendor/autoload.php');