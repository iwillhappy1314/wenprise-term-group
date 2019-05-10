<?php
/**
 * Class SampleTest
 *
 * @package Wenprise_Theme_Helper
 */

/**
 * Sample test case.
 */
class TermGroupTest extends WP_UnitTestCase
{

    function setUp()
    {
        // Call the setup method on the parent or the factory objects won't be loaded!
        parent::setUp();

        $this->post_id = $this->factory->post->create([
            'post_type'   => 'page',
            'post_status' => 'publish',
            'post_title'  => '文章中文标题测试',
        ]);


        $this->term_id = $this->factory->term->create([
            'name'     => '分类项目中文标题测试',
            'taxonomy' => 'category',
            'slug'     => '',
        ]);

    }

}