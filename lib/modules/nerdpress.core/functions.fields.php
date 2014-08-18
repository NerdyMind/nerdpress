<?php
/* Page/Post Options for NerdPress */
if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_options',
		'title' => 'Page/Post Options',
		'fields' => array (
			array (
				'key' => 'field_52ec1ea24c2c2',
				'label' => 'SEO',
				'name' => '',
				'type' => 'tab',
			),
			array (
				'key' => 'field_52ec1eea4c2c4',
				'label' => 'Title',
				'name' => 'nrd_seo_title',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => 'This overrides the default page title',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => '',
			),
			array (
				'key' => 'field_52ec1f7f28a1d',
				'label' => 'Heading',
				'name' => 'nrd_seo_heading',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => 'Overrides the page\'s H1 tag, which is typically the page name',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => '',
			),
			array (
				'key' => 'field_52ec1eb14c2c3',
				'label' => 'Meta Description',
				'name' => 'nrd_seo_desc',
				'type' => 'textarea',
				'default_value' => '',
				'placeholder' => 'Enter your description here',
				'maxlength' => 255,
				'formatting' => 'none',
			),
			array (
				'key' => 'field_52ec1eb14c3b9',
				'label' => 'Canonical URL',
				'name' => 'nrd_seo_canonical',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => 'http://',
				'maxlength' => '',
				'formatting' => 'none',
			),
			array (
				'key' => 'field_52ec1e754c2c1',
				'label' => 'Options',
				'name' => '',
				'type' => 'tab',
			),
			array (
				'key' => 'field_52e02208e18a2',
				'label' => 'Sidebar',
				'name' => 'nrd_hide_sidebar',
				'type' => 'true_false',
				'message' => 'Hide sidebar on this page',
				'default_value' => 0,
			),
			array (
				'key' => 'field_52e8123bacb28',
				'label' => 'Full Width',
				'name' => 'nrd_full_width',
				'type' => 'true_false',
				'message' => 'Make this page full browser width',
				'default_value' => 0,
			),
			array (
				'key' => 'field_52eaedbd472a5',
				'label' => 'Sitemap',
				'name' => 'nrd_hide_sitemap',
				'type' => 'true_false',
				'instructions' => 'Valid only for pages.',
				'message' => 'Exclude this from the sitemap',
				'default_value' => 0,
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'post',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'page',
					'order_no' => 0,
					'group_no' => 1,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => array (
				0 => 'custom_fields',
			),
		),
		'menu_order' => 0,
	));
}
?>