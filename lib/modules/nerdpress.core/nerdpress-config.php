<?php

class NerdPressConfig {
	
	function __construct() {
		if ( is_admin() ) add_filter( 'acf/load_field/name=hide_sidebar_templates', array( &$this, 'nerdpress_load_config_templates' ) );
		if ( is_admin() ) add_filter( 'acf/load_field/name=tax_connect', array( &$this, 'nerdpress_load_config_post_types' ) );
		
		if ( function_exists( 'acf_add_options_sub_page' ) ) :
		
			$nerdpress_args = array(
				'title' => 'NerdPress Settings',
				'parent' => 'options-general.php',
				'capability' => 'manage_options',
				'slug' => 'nerdpress-settings',
			);
			
			acf_add_options_sub_page( $nerdpress_args );
		endif;		
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

}

new NerdPressConfig();
?>