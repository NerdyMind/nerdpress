<?php
$nerdpress_config = array(
	'main_class' => 'col-sm-8',
	'sidebar_class' => 'col-sm-4',
	'widget_areas' => array(
		'Sidebar Primary' => array(
			'columns' => 4,
			'mode' => null,
		),
/*
		'Footer' => array(
			'mode' => null,
		),
*/
	),
	'breadcrumbs' => true,
	'root_relative_urls' => false,
	'nice_search' => true,
	'analytics_id' => 'UA-28670316-2',
	'analytics_demographics' => false,
	'post_excerpt_length' => 40,
	'navbar_fixed' => false,
	'navbar_fixed_position' => false,
	'pagination' => 'pager',
	'responsive' => true,
	'woocommerce_columns' => 2,
	'brand_wp' => true,
	'site_in_title' => true,
	'post_types' => '',
	'taxonomies' => '',
	'social_networks' => array(
		'Facebook' => array(
			'link' => 'http://facebook.com/NerdyMindMarketing',
			'icon' => 'fa-facebook',
		),
		'Twitter' => array(
			'link' => 'http://twitter.com/Nerdy_Mind',
			'icon' => 'fa-twitter',
		),
		'Google+' => array(
			'link' => 'https://plus.google.com/116408614390096194516',
			'icon' => 'fa-google-plus',
		),
	),
	'script_animatecss' => true,
	'script_flexslider' => false,
	'script_lightbox' => false,
	'script_vimeo' => false,
	'script_bootstrap_hover' => false,
	'script_header' => array(),
	'script_footer' => array(),
	'integrations' => array(
		'bbpress',
		'twitter',
		'woocommerce',
	),
	// Set conditions for when you don't want the sidebar:
	// List: http://codex.wordpress.org/Conditional_Tags
	// WooCommerce-specific: http://docs.woothemes.com/document/conditional-tags/
	// More info: http://roots.io/the-roots-sidebar/
	'hide_sidebar_conditions' => array(
		'is_home',
		'is_front_page',
	),
	// Set specific page templates you don't want the sidebar on:
	'hide_sidebar_templates' => array(
	),
);
global $nerdpress_config;
?>