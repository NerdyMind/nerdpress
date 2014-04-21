<?php

class NerdPressConfig {
	
	function __construct() {
		if ( function_exists( 'acf_add_options_sub_page' ) ) :
		
			$nerdpress_args = array(
				'title' => 'NerdPress Settings',
				'parent' => 'options-general.php',
				'capability' => 'manage_options',
				'slug' => 'nerdpress-settings',
			);
			
			acf_add_options_sub_page( $nerdpress_args );
		endif;		

		if ( is_admin() ) add_filter( 'acf/load_field/name=hide_sidebar_templates', array( &$this, 'nerdpress_load_config_templates' ) );
		if ( is_admin() ) add_filter( 'acf/load_field/name=tax_connect', array( &$this, 'nerdpress_load_config_post_types' ) );
		add_action( 'after_setup_theme', array( &$this, 'load_fields' ) );
	}
	
	function nerdpress_load_config_templates( $field ) {
		$field['choices'] = array();
	
		$templates = get_page_templates();
		
		foreach ( $templates as $template => $data ) {
			$field['choices'][ $data ] = $template;
		}
		
		return $field;
	}

	function nerdpress_load_config_post_types( $field ) {
		$field['choices'] = array();
	
		$args = array();
		
		$post_types = get_post_types( $args, 'names' );
		
		foreach ( $post_types as $post_type ) {
			$field['choices'][ $post_type ] = $post_type;
		}
		
		return $field;
	}
	
	function load_fields() {
		if(function_exists("register_field_group"))
		{
			register_field_group(array (
				'id' => 'acf_nerdpress-settings',
				'title' => 'NerdPress Settings',
				'fields' => array (
					array (
						'key' => 'field_530e37897f365',
						'label' => 'Configuration & Layout',
						'name' => '',
						'type' => 'tab',
					),
					array (
						'key' => 'field_530e37a57f366',
						'label' => 'Main Class',
						'name' => 'main_class',
						'type' => 'text',
						'instructions' => 'This is the main container class, which uses <a href="http://getbootstrap.com/css/#grid" target="_blank">Bootstrap</a> columns.',
						'required' => 1,
						'default_value' => 'col-sm-8',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'none',
						'maxlength' => '',
					),
					array (
						'key' => 'field_530e37bb7f367',
						'label' => 'Sidebar Class',
						'name' => 'sidebar_class',
						'type' => 'text',
						'instructions' => 'This is the sidebar container class, which uses <a href="http://getbootstrap.com/css/#grid" target="_blank">Bootstrap</a> columns.',
						'required' => 1,
						'default_value' => 'col-sm-4',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
					array (
						'key' => 'field_530e37fe47872',
						'label' => 'Breadcrumbs',
						'name' => 'breadcrumbs',
						'type' => 'true_false',
						'message' => 'Enable breadcrumbs',
						'default_value' => 1,
					),
					array (
						'key' => 'field_530e383247873',
						'label' => 'Branding',
						'name' => 'brand_wp',
						'type' => 'true_false',
						'message' => 'Show NerdyMind branding in backend',
						'default_value' => 1,
					),
					array (
						'key' => 'field_530q383247873',
						'label' => 'Minify',
						'name' => 'minify_css',
						'type' => 'true_false',
						'message' => 'Minify CSS for production',
						'default_value' => 0,
					),
					array (
						'key' => 'field_530q383247960',
						'label' => 'Compiler',
						'name' => 'use_compiler',
						'type' => 'true_false',
						'message' => 'Use web-based compiler',
						'default_value' => 1,
					),
					array (
						'key' => 'field_530fa4142ffc1',
						'label' => 'Menu Locations',
						'name' => 'menu_locations',
						'type' => 'repeater',
						'sub_fields' => array (
							array (
								'key' => 'field_530fa4252ffc2',
								'label' => 'Location',
								'name' => 'location',
								'type' => 'text',
								'column_width' => '',
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'formatting' => 'none',
								'maxlength' => '',
							),
						),
						'row_min' => '',
						'row_limit' => '',
						'layout' => 'table',
						'button_label' => '+ Add Menu Location',
					),
					array (
						'key' => 'field_530fa4432ffc3',
						'label' => 'Widget Areas',
						'name' => 'widget_areas',
						'type' => 'repeater',
						'instructions' => 'Also known as sidebars, these areas allow you to add widget content. ',
						'sub_fields' => array (
							array (
								'key' => 'field_530fa56b2ffc4',
								'label' => 'Name',
								'name' => 'area_name',
								'type' => 'text',
								'column_width' => '',
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'formatting' => 'none',
								'maxlength' => '',
							),
							array (
								'key' => 'field_530fabdd2ffc5',
								'label' => 'Widget Class',
								'name' => 'area_class',
								'type' => 'text',
								'column_width' => '',
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'formatting' => 'none',
								'maxlength' => '',
							),
						),
						'row_min' => '',
						'row_limit' => '',
						'layout' => 'table',
						'button_label' => '+ Add Widget Area',
					),
					array (
						'key' => 'field_530e5ce00cafb',
						'label' => 'Hide Sidebar Conditions',
						'name' => 'hide_sidebar_conditions',
						'type' => 'repeater',
						'instructions' => 'Define WordPress conditions where the sidebar will not display. Uses <a href="http://codex.wordpress.org/Conditional_Tags" target="_blank">WordPress Conditional Tags</a>.',
						'sub_fields' => array (
							array (
								'key' => 'field_530e5cf50cafc',
								'label' => 'Condition',
								'name' => 'condition',
								'type' => 'text',
								'column_width' => '',
								'default_value' => '',
								'placeholder' => 'is_front_page',
								'prepend' => '',
								'append' => '',
								'formatting' => 'none',
								'maxlength' => '',
							),
						),
						'row_min' => '',
						'row_limit' => '',
						'layout' => 'table',
						'button_label' => '+ Add Condition',
					),
					array (
						'key' => 'field_530e600cba750',
						'label' => 'Hide Sidebar on Page Templates',
						'name' => 'hide_sidebar_templates',
						'type' => 'checkbox',
						'instructions' => 'Define Page Templates where the sidebar will not display.',
						'choices' => array (
							'template-custom.php' => 'Custom Template',
						),
						'default_value' => '',
						'layout' => 'vertical',
					),
					array (
						'key' => 'field_530e384b47874',
						'label' => 'Services',
						'name' => '',
						'type' => 'tab',
					),
					array (
						'key' => 'field_530e388b47877',
						'label' => 'Google Analytics',
						'name' => 'enable_google_analytics',
						'type' => 'true_false',
						'message' => 'Enable Google Analytics',
						'default_value' => 0,
					),
					array (
						'key' => 'field_530e385247875',
						'label' => 'Google Analytics ID',
						'name' => 'analytics_id',
						'type' => 'text',
						'conditional_logic' => array (
							'status' => 1,
							'rules' => array (
								array (
									'field' => 'field_530e388b47877',
									'operator' => '==',
									'value' => '1',
								),
							),
							'allorany' => 'all',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'none',
						'maxlength' => '',
					),
					array (
						'key' => 'field_530e385248895',
						'label' => 'Site URL',
						'name' => 'analytics_site_url',
						'type' => 'text',
						'conditional_logic' => array (
							'status' => 1,
							'rules' => array (
								array (
									'field' => 'field_530e388b47877',
									'operator' => '==',
									'value' => '1',
								),
							),
							'allorany' => 'all',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'none',
						'maxlength' => '',
					),
					array (
						'key' => 'field_530e386347876',
						'label' => 'Demographics',
						'name' => 'analytics_demographics',
						'type' => 'true_false',
						'conditional_logic' => array (
							'status' => 1,
							'rules' => array (
								array (
									'field' => 'field_530e388b47877',
									'operator' => '==',
									'value' => '1',
								),
							),
							'allorany' => 'all',
						),
						'message' => 'Track demographics',
						'default_value' => 0,
					),
					array (
						'key' => 'field_530e38f803645',
						'label' => 'StatCounter',
						'name' => 'enable_statcounter',
						'type' => 'true_false',
						'message' => 'Enable StatCounter',
						'default_value' => 0,
					),
					array (
						'key' => 'field_530e390603646',
						'label' => 'StatCounter Project ID',
						'name' => 'statcounter_id',
						'type' => 'text',
						'conditional_logic' => array (
							'status' => 1,
							'rules' => array (
								array (
									'field' => 'field_530e38f803645',
									'operator' => '==',
									'value' => '1',
								),
							),
							'allorany' => 'all',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'none',
						'maxlength' => '',
					),
					array (
						'key' => 'field_530e391b03647',
						'label' => 'StatCounter Security Code',
						'name' => 'statcounter_security_code',
						'type' => 'text',
						'conditional_logic' => array (
							'status' => 1,
							'rules' => array (
								array (
									'field' => 'field_530e38f803645',
									'operator' => '==',
									'value' => '1',
								),
							),
							'allorany' => 'all',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'none',
						'maxlength' => '',
					),
					array (
						'key' => 'field_530e39497f914',
						'label' => 'Integrations',
						'name' => '',
						'type' => 'tab',
					),
					array (
						'key' => 'field_530f93dc199f0',
						'label' => 'Scripts',
						'name' => 'load_scripts',
						'type' => 'checkbox',
						'instructions' => 'These scripts enable additional functionality within the theme. <a href="http://go.nerdymind.com/npdocs" target="_blank">Documentation</a>',
						'choices' => array (
							'animatecss' => 'Animate.css',
							'flexslider' => 'FlexSlider',
							'lightbox' => 'Lightbox',
							'bootstrap_hover' => 'Menu on Hover',
							'vimeo_api' => 'Vimeo API',
						),
						'default_value' => '',
						'layout' => 'vertical',
					),
					array (
						'key' => 'field_530f7b5e81001',
						'label' => 'Additional Integrations',
						'name' => 'addl_integrations',
						'type' => 'checkbox',
						'instructions' => '<a href="http://go.nerdymind.com/npdocs" target="_blank">Documentation</a>',
						'choices' => array (
							'bbpress' => 'bbPress',
							'twitter' => 'Twitter API',
							'woocommerce' => 'WooCommerce',
						),
						'default_value' => '',
						'layout' => 'vertical',
					),
					array (
						'key' => 'field_530e5c47bd5f1',
						'label' => 'Additional Header Scripts',
						'name' => 'script_header',
						'type' => 'repeater',
						'sub_fields' => array (
							array (
								'key' => 'field_530e5c61bd5f2',
								'label' => 'Script URL',
								'name' => 'script_url',
								'type' => 'text',
								'column_width' => '',
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'formatting' => 'none',
								'maxlength' => '',
							),
						),
						'row_min' => '',
						'row_limit' => '',
						'layout' => 'table',
						'button_label' => '+ Add Script',
					),
					array (
						'key' => 'field_530e5c7cbd5f3',
						'label' => 'Additional Footer Scripts',
						'name' => 'script_footer',
						'type' => 'repeater',
						'sub_fields' => array (
							array (
								'key' => 'field_530e5c7cbd5f4',
								'label' => 'Script URL',
								'name' => 'script_url',
								'type' => 'text',
								'column_width' => '',
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'formatting' => 'none',
								'maxlength' => '',
							),
						),
						'row_min' => '',
						'row_limit' => '',
						'layout' => 'table',
						'button_label' => '+ Add Script',
					),
					array (
						'key' => 'field_530e630f15742',
						'label' => 'Post Types & Taxonomies',
						'name' => '',
						'type' => 'tab',
					),
					array (
						'key' => 'field_530e631715743',
						'label' => 'Post Types',
						'name' => 'post_types',
						'type' => 'repeater',
						'instructions' => 'Easily create post types here. You can also modify the breadcrumb behavior for existing post types. Icons use <a href="http://melchoyce.github.io/dashicons/" target="_blank">WordPress Dashicons</a>.
			
			<a href="http://codex.wordpress.org/register_post_type" target="_blank">Documentation</a>',
						'sub_fields' => array (
							array (
								'key' => 'field_530e632015744',
								'label' => 'Name',
								'name' => 'type_name',
								'type' => 'text',
								'column_width' => '',
								'default_value' => '',
								'placeholder' => 'Ex: film',
								'prepend' => '',
								'append' => '',
								'formatting' => 'html',
								'maxlength' => '',
							),
							array (
								'key' => 'field_530e7b5615747',
								'label' => 'Breadcrumb',
								'name' => 'type_breadcrumb',
								'type' => 'text',
								'column_width' => '',
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'formatting' => 'none',
								'maxlength' => '',
							),
							array (
								'key' => 'field_530e784315745',
								'label' => 'Create',
								'name' => 'type_create',
								'type' => 'true_false',
								'column_width' => '',
								'message' => 'Yes',
								'default_value' => 0,
							),
							array (
								'key' => 'field_530e7b1515746',
								'label' => 'Icon',
								'name' => 'type_icon',
								'type' => 'text',
								'conditional_logic' => array (
									'status' => 1,
									'rules' => array (
										array (
											'field' => 'field_530e784315745',
											'operator' => '==',
											'value' => '1',
										),
									),
									'allorany' => 'all',
								),
								'column_width' => '',
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'formatting' => 'none',
								'maxlength' => '',
							),
							array (
								'key' => 'field_5310ac44bad2b',
								'label' => 'Slug',
								'name' => 'type_slug',
								'type' => 'text',
								'conditional_logic' => array (
									'status' => 1,
									'rules' => array (
										array (
											'field' => 'field_530e784315745',
											'operator' => '==',
											'value' => '1',
										),
									),
									'allorany' => 'all',
								),
								'column_width' => '',
								'default_value' => '',
								'placeholder' => 'Ex: movies',
								'prepend' => '',
								'append' => '',
								'formatting' => 'none',
								'maxlength' => '',
							),
							array (
								'key' => 'field_5310ab7454c16',
								'label' => 'Singular',
								'name' => 'type_singular',
								'type' => 'text',
								'conditional_logic' => array (
									'status' => 1,
									'rules' => array (
										array (
											'field' => 'field_530e784315745',
											'operator' => '==',
											'value' => '1',
										),
									),
									'allorany' => 'all',
								),
								'column_width' => '',
								'default_value' => '',
								'placeholder' => 'Ex: Movie',
								'prepend' => '',
								'append' => '',
								'formatting' => 'none',
								'maxlength' => '',
							),
							array (
								'key' => 'field_5310ab7f54c17',
								'label' => 'Plural',
								'name' => 'type_plural',
								'type' => 'text',
								'conditional_logic' => array (
									'status' => 1,
									'rules' => array (
										array (
											'field' => 'field_530e784315745',
											'operator' => '==',
											'value' => '1',
										),
									),
									'allorany' => 'all',
								),
								'column_width' => '',
								'default_value' => '',
								'placeholder' => 'Ex: Movies',
								'prepend' => '',
								'append' => '',
								'formatting' => 'none',
								'maxlength' => '',
							),
							array (
								'key' => 'field_5310f9b03f5f6',
								'label' => 'Hierarchical',
								'name' => 'type_hierarchical',
								'type' => 'true_false',
								'conditional_logic' => array (
									'status' => 1,
									'rules' => array (
										array (
											'field' => 'field_530e784315745',
											'operator' => '==',
											'value' => '1',
										),
									),
									'allorany' => 'all',
								),
								'column_width' => '',
								'message' => 'Yes',
								'default_value' => 0,
							),
						),
						'row_min' => '',
						'row_limit' => '',
						'layout' => 'table',
						'button_label' => '+ Add Post Type',
					),
					array (
						'key' => 'field_530e7c87b3656',
						'label' => 'Taxonomies',
						'name' => 'taxonomies',
						'type' => 'repeater',
						'instructions' => 'Easily create taxonomies here. You can also modify the breadcrumb behavior for existing taxonomies.',
						'sub_fields' => array (
							array (
								'key' => 'field_530e7c93b3657',
								'label' => 'Name',
								'name' => 'tax_name',
								'type' => 'text',
								'column_width' => '',
								'default_value' => '',
								'placeholder' => 'genre',
								'prepend' => '',
								'append' => '',
								'formatting' => 'none',
								'maxlength' => '',
							),
							array (
								'key' => 'field_530e7d38b365a',
								'label' => 'Breadcrumb',
								'name' => 'tax_breadcrumb',
								'type' => 'text',
								'column_width' => '',
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'formatting' => 'none',
								'maxlength' => '',
							),
							array (
								'key' => 'field_530e7cb3b3658',
								'label' => 'Create',
								'name' => 'tax_create',
								'type' => 'true_false',
								'column_width' => '',
								'message' => 'Yes',
								'default_value' => 0,
							),
							array (
								'key' => 'field_530e7cf8b3659',
								'label' => 'Connect To',
								'name' => 'tax_connect',
								'type' => 'select',
								'conditional_logic' => array (
									'status' => 1,
									'rules' => array (
										array (
											'field' => 'field_530e7cb3b3658',
											'operator' => '==',
											'value' => '1',
										),
									),
									'allorany' => 'all',
								),
								'column_width' => '',
								'choices' => array (
									'post' => 'post',
									'page' => 'page',
									'attachment' => 'attachment',
									'revision' => 'revision',
									'nav_menu_item' => 'nav_menu_item',
									'forum' => 'forum',
									'topic' => 'topic',
									'reply' => 'reply',
									'nrd_event' => 'nrd_event',
									'nrd_element' => 'nrd_element',
									'acf' => 'acf',
									'product' => 'product',
									'product_variation' => 'product_variation',
									'shop_order' => 'shop_order',
									'shop_coupon' => 'shop_coupon',
									'movie' => 'movie',
								),
								'default_value' => '',
								'allow_null' => 1,
								'multiple' => 0,
							),
							array (
								'key' => 'field_5310ef3b63fab',
								'label' => 'Singular',
								'name' => 'tax_singular',
								'type' => 'text',
								'conditional_logic' => array (
									'status' => 1,
									'rules' => array (
										array (
											'field' => 'field_530e7cb3b3658',
											'operator' => '==',
											'value' => '1',
										),
									),
									'allorany' => 'all',
								),
								'column_width' => '',
								'default_value' => '',
								'placeholder' => 'Genre',
								'prepend' => '',
								'append' => '',
								'formatting' => 'none',
								'maxlength' => '',
							),
							array (
								'key' => 'field_5310ef7663fac',
								'label' => 'Plural',
								'name' => 'tax_plural',
								'type' => 'text',
								'conditional_logic' => array (
									'status' => 1,
									'rules' => array (
										array (
											'field' => 'field_530e7cb3b3658',
											'operator' => '==',
											'value' => '1',
										),
									),
									'allorany' => 'all',
								),
								'column_width' => '',
								'default_value' => '',
								'placeholder' => 'Genres',
								'prepend' => '',
								'append' => '',
								'formatting' => 'none',
								'maxlength' => '',
							),
							array (
								'key' => 'field_5310f351406bf',
								'label' => 'Hierarchical',
								'name' => 'tax_hierarchical',
								'type' => 'true_false',
								'conditional_logic' => array (
									'status' => 1,
									'rules' => array (
										array (
											'field' => 'field_530e7cb3b3658',
											'operator' => '==',
											'value' => '1',
										),
									),
									'allorany' => 'all',
								),
								'column_width' => '',
								'message' => 'Yes',
								'default_value' => 0,
							),
						),
						'row_min' => '',
						'row_limit' => '',
						'layout' => 'table',
						'button_label' => '+ Add Taxonomy',
					),
					array (
						'key' => 'field_530fbba9b072f',
						'label' => 'Social Networks',
						'name' => '',
						'type' => 'tab',
					),
					array (
						'key' => 'field_530fbbb7b0730',
						'label' => 'Social Networks',
						'name' => 'social_networks',
						'type' => 'repeater',
						'instructions' => 'Use the shortcode <code>[nerdpress_social_networks]</code> to show these networks on your site.',
						'sub_fields' => array (
							array (
								'key' => 'field_530fbbc2b0731',
								'label' => 'Name',
								'name' => 'net_name',
								'type' => 'text',
								'column_width' => '',
								'default_value' => '',
								'placeholder' => 'Ex: Twitter',
								'prepend' => '',
								'append' => '',
								'formatting' => 'none',
								'maxlength' => '',
							),
							array (
								'key' => 'field_530fbbebb0732',
								'label' => 'Link',
								'name' => 'net_link',
								'type' => 'text',
								'column_width' => '',
								'default_value' => '',
								'placeholder' => 'Ex: https://twitter.com/nerdy_mind',
								'prepend' => '',
								'append' => '',
								'formatting' => 'none',
								'maxlength' => '',
							),
							array (
								'key' => 'field_530fbc06b0733',
								'label' => 'Icon',
								'name' => 'net_icon',
								'type' => 'text',
								'instructions' => 'Uses <a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a>',
								'column_width' => '',
								'default_value' => '',
								'placeholder' => 'Ex: fa-twitter',
								'prepend' => '',
								'append' => '',
								'formatting' => 'none',
								'maxlength' => '',
							),
						),
						'row_min' => '',
						'row_limit' => '',
						'layout' => 'table',
						'button_label' => '+ Add Social Network',
					),
				),
				'location' => array (
					array (
						array (
							'param' => 'options_page',
							'operator' => '==',
							'value' => 'nerdpress-settings',
							'order_no' => 0,
							'group_no' => 0,
						),
					),
				),
				'options' => array (
					'position' => 'normal',
					'layout' => 'no_box',
					'hide_on_screen' => array (
					),
				),
				'menu_order' => 0,
			));
		}
		
	}

}

new NerdPressConfig();
?>