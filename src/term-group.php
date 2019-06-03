<?php
/**
 * 修改保存 term meta 的行为
 */

add_action('template_redirect', 'wprs_tg_template_redirect');
// add_filter('get_the_archive_description', 'wprs_tg_archive_description');
// add_filter('term_link', 'wprs_tg_change_term_link', 10, 3);


/**
 * 获取分类法项目数组
 *
 * @return array
 */
function wprs_tg_data_primary_terms()
{
    $terms  = get_option('wprs_primary_term', []);
    $output = [];

    foreach ($terms as $term_id) {

        $term = get_term($term_id);

        if (is_wp_error($term)) {
            continue;
        }

        $output[ $term->term_id ] = $term->name;
    }

    $empty = [
        '' => sprintf('%s', __('Select a tag', 'wprs')),
    ];

    $output = $empty + $output;

    return $output;
}


/**
 * 修改分类链接
 *
 * @param $term_link
 * @param $term
 * @param $taxonomy
 *
 * @return string|\WP_Error
 */
// function wprs_tg_change_term_link($term_link, $term, $taxonomy)
// {
//
//     $term_group = $term->term_group;
//     $is_primary = get_term_meta($term->term_id, '_wprs_is_primary', true);
//
//     if ($taxonomy == 'post_tag' && $term_group > 0 && $is_primary != 'yes') {
//         $primary_term = wprs_tg_get_primary_term($term_group);
//
//         $term_link = get_term_link(get_term($primary_term));
//     }
//
//     return $term_link;
//
// }


/**
 * 301 跳转到主分类
 */
function wprs_tg_template_redirect()
{
    $term = get_queried_object();

    if (isset($term->taxonomy) && $term->taxonomy == 'post_tag') {

        // $term_group  = $term->term_group;
        $is_primary     = boolval(get_term_meta($term->term_id, '_wprs_is_primary', true));
        $parent_term_id = get_term_meta($term->term_id, '_wprs_parent_term', true);

        // 如果设置了标签组、别名不是主标签、同时设置了父级标签数据（避免跳转其他插件添加的标签组），跳转到主标签
        if ( ! $is_primary && ! empty($parent_term_id)) {
            // $primary_term_id  = wprs_tg_get_primary_term($term_group);
            $parent_term_link = get_term_link(get_term($parent_term_id));

            if ( ! is_wp_error($parent_term_link)) {
                wp_redirect($parent_term_link, '301');
            }
        }

    }
}


/**
 * 获取主分类
 *
 * @param $term_group
 *
 * @return array|object|null
 */
// function wprs_tg_get_primary_term($term_group)
// {
//     global $wpdb;
//
//     // 获取分类群组中的主分类，只有一个
//     $main_term = $wpdb->get_results(
//         "SELECT term_id FROM $wpdb->termmeta
//                     WHERE meta_key = '_wprs_is_primary'
//                         AND meta_value = 1
//                         AND term_id IN (
//                                         SELECT term_id
//                                              FROM $wpdb->terms
//                                              WHERE term_group = $term_group
//                                         )"
//     );
//
//     $main_term = wp_list_pluck($main_term, 'term_id');
//
//     return $main_term[ 0 ];
// }


/**
 * 获取群组的分类
 *
 * @param $term_group
 *
 * @return array|object|null
 */
// function wprs_tg_get_grouped_terms($term_group)
// {
//     global $wpdb;
//
//     $grouped = $wpdb->get_results(
//         "SELECT term_id FROM $wpdb->terms WHERE term_group = $term_group"
//     );
//
//     $grouped = wp_list_pluck($grouped, 'term_id');
//
//     return $grouped;
// }


/**
 * 添加同义词到分类描述下面
 *
 * @param $description
 *
 * @return string
 */
// function wprs_tg_archive_description($description)
// {
//
//     $term = get_queried_object();
//     $html = '';
//
//     if ($term->taxonomy == 'post_tag') {
//
//         $term_group = $term->term_group;
//         $is_primary = get_term_meta($term->term_id, '_wprs_is_primary', true);
//
//         if ($is_primary) {
//             $alias = wprs_tg_get_grouped_terms($term_group);
//
//             $html .= '<div class="rs-term-alias">';
//             foreach ($alias as $term_id) {
//                 $term = get_term($term_id);
//                 $html .= '<a class="rs-term-alias__item" href="' . get_term_link($term) . '">' . $term->name . '</a>';
//             }
//             $html .= '</div>';
//
//         }
//
//     }
//
//     return $description . $html;
//
// }