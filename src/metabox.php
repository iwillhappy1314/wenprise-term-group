<?php
/**
 * 添加分类方法自定义字段
 */

add_action('edited_post_tag', 'wprs_tg_save_taxonomy_meta', 10, 2);
add_action('create_post_tag', 'wprs_tg_save_taxonomy_meta', 10, 2);

add_action('post_tag_edit_form_fields', 'wprs_tg_taxonomy_edit_meta_field', 10, 2);
add_action('post_tag_add_form_fields', 'wprs_tg_taxonomy_add_new_meta_field', 10, 2);

/**
 * 添加 term meta 字段
 */
function wprs_tg_taxonomy_add_new_meta_field()
{
    $term_options = wprs_tg_data_primary_terms();

    ?>
    <div class="form-field">
        <label for="_wprs_is_primary">
            <input type="checkbox" name="_wprs_is_primary" id="_wprs_is_primary" value="1">
            <?php esc_attr_e('Set this tag primary tag', 'wprs'); ?>
        </label>
    </div>

    <div class="form-field">
        <label for="_wprs_parent_term">
            <?php esc_attr_e('Select a primary tag', 'wprs'); ?>
        </label>
        <select name="_wprs_parent_term" id="_wprs_parent_term">
            <?php foreach ($term_options as $k => $v): ?>
                <option value="<?= esc_attr($k); ?>">
                    <?= esc_attr($v); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
}


/**
 * 编辑 term meta 字段
 *
 * @param $term
 */

function wprs_tg_taxonomy_edit_meta_field($term)
{
    $term_id      = $term->term_id;
    $term_options = wprs_tg_data_primary_terms();

    $primary_term = get_term_meta($term_id, '_wprs_is_primary', true);
    $parent_term  = get_term_meta($term_id, '_wprs_parent_term', true);
    ?>
    <tr class="form-field">
        <th scope="row">
            <?php esc_attr_e('Set as primary', 'wprs'); ?>
        </th>
        <td>
            <label for="_wprs_is_primary">
                <input type="checkbox" name="_wprs_is_primary" id="_wprs_is_primary" value="1" <?php checked($primary_term) ?>>
                <?php esc_attr_e('Set this tag primary tag', 'wprs'); ?>
            </label>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row">
            <label for="_wprs_parent_term">
                <?php esc_attr_e('Select a primary tag', 'wprs'); ?>
            </label>
        </th>
        <td>
            <select name="_wprs_parent_term" id="_wprs_parent_term">
                <?php foreach ($term_options as $k => $v): ?>
                    <option <?php selected($parent_term, $k) ?> value="<?= esc_attr($k); ?>">
                        <?= esc_attr($v); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <?php
}


/**
 * @param $term_id
 */
function wprs_tg_save_taxonomy_meta($term_id)
{
    $term_id        = (int)$term_id;
    $is_primary     = isset($_POST[ '_wprs_is_primary' ]) ? boolval($_POST[ '_wprs_is_primary' ]) : false;
    $parent_term_id = isset($_POST[ '_wprs_parent_term' ]) ? intval($_POST[ '_wprs_parent_term' ]) : false;

    remove_action('edited_post_tag', 'wprs_tg_save_taxonomy_meta');
    remove_action('create_post_tag', 'wprs_tg_save_taxonomy_meta');

    // 添加或删除主分类标记，用于获取标签组的主标签
    if ( ! $is_primary) {
        delete_term_meta($term_id, '_wprs_is_primary');
    } else {
        update_term_meta($term_id, '_wprs_is_primary', sanitize_text_field($is_primary));
    }

    // 如果不是主标签，并且设置了父级标签，添加父级标签数据
    if ( ! $is_primary && $parent_term_id) {
        update_term_meta($term_id, '_wprs_parent_term', sanitize_text_field($parent_term_id));
    }

    // 如果没有设置或删除了父级标签，删除父级标签数据
    if (empty($parent_term_id)) {
        delete_term_meta($term_id, '_wprs_parent_term');
    }

    // 添加主标签数据
    wprs_tg_set_primary_term($term_id, $is_primary);

    // 设置标签别名, 两个数据都为空时，删除 term_group
    // wprs_tg_set_term_alias($term_id, $parent_term_id, (empty($parent_term_id) && ! $is_primary));

    add_action('edited_post_tag', 'wprs_tg_save_taxonomy_meta', 10, 2);
    add_action('create_post_tag', 'wprs_tg_save_taxonomy_meta', 10, 2);
}


/**
 * 设置分类别名
 *
 * @param int $term_id 分类 ID
 * @param     $parent_term_id
 * @param     $delete
 */
// function wprs_tg_set_term_alias(int $term_id, int $parent_term_id = 0, bool $delete = false)
// {
//     if ( ! $delete) {
//
//         if ($parent_term_id) {
//             $primary_term = get_term($parent_term_id);
//
//             wp_update_term($term_id, 'post_tag', [
//                 'alias_of' => $primary_term->slug,
//             ]);
//         }
//
//     } else {
//
//         global $wpdb;
//         $wpdb->update($wpdb->terms, ['term_group' => ''], ['term_id' => $term_id], ['%s'], ['%s']);
//
//     }
//
// }


/**
 * 设置主标签
 *
 * @param int  $term_id
 * @param bool $is_primary 为 true 时，添加主标签、否则删除
 */
function wprs_tg_set_primary_term(int $term_id, $is_primary = true)
{

    $primary_terms = get_option('wprs_primary_term', []);
    // wprs_tg_set_term_alias($term_id, $term_id);

    if ($is_primary) {

        $primary_terms [] = $term_id;

    } else {

        $primary_index = array_search($term_id, $primary_terms, true);

        if ($primary_index !== false) {
            unset($primary_terms[ $primary_index ]);
        }
    }

    update_option('wprs_primary_term', array_unique($primary_terms));

}