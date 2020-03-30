<?php
/**
 * Wenprise Term Group Slug 设置
 *
 * @author Amos Lee
 */
if ( ! class_exists('Wenprise_Term_Group_Settings')):

    class Wenprise_Term_Group_Settings
    {

        private $settings_api;

        function __construct()
        {
            $this->settings_api = new \WeDevs_Settings_API;

            // add_action('admin_init', [$this, 'admin_init']);
            // add_action('admin_menu', [$this, 'admin_menu']);
            add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        }


        /**
         * 加载 JS
         *
         * @param $hook
         */
        function enqueue_scripts($hook)
        {

            if ($hook == 'edit-tags.php' || $hook == 'term.php') {
                wp_enqueue_script('wprs-tg-scripts', plugin_dir_url(__FILE__) . 'scripts.js');
            }

        }


        /**
         * 初始化
         */
        function admin_init()
        {

            // set the settings
            $this->settings_api->set_sections($this->get_settings_sections());
            $this->settings_api->set_fields($this->get_settings_fields());

            // initialize settings
            $this->settings_api->admin_init();
        }


        /**
         * 添加设置菜单
         */
        function admin_menu()
        {
            add_options_page('标签分组', '标签分组', 'delete_posts', 'wenprise_term_group', [$this, 'plugin_page']);
        }
        

        function get_settings_sections()
        {
            return [
                [
                    'id'    => 'wprs_term_group',
                    'title' => __('标签分组设置', 'wprs'),
                ],
            ];
        }


        /**
         * 设置字段
         *
         * @return array settings fields
         */
        function get_settings_fields()
        {
            return [
                'wprs_term_group' => [

                    [
                        'name'              => 'redirect',
                        'label'             => __('301 跳转到主标签', 'wprs'),
                        'desc'              => __('当标签设置了主标签时，打开标签链接自动跳转到主标签。', 'wprs'),
                        'placeholder'       => __('-', 'wprs'),
                        'default'           => '',
                        'type'              => 'checkbox',
                    ],


                    [
                        'name'              => 'related_link',
                        'label'             => __('显示相关子标签', 'wprs'),
                        'desc'              => __('在主标签描述下面显示相关子标签。', 'wprs'),
                        'placeholder'       => __('-', 'wprs'),
                        'default'           => '',
                        'type'              => 'checkbox',
                    ],


                ],
            ];
        }


        /**
         * 插件设置页面
         */
        function plugin_page()
        {
            echo '<div class="wrap">';
            $this->settings_api->show_forms();
            echo '</div>';
        }

    }

endif;

new Wenprise_Term_Group_Settings();