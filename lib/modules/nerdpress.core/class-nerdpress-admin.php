<?php
class NerdPressAdmin {
	function __construct() {
		add_filter( 'admin_footer_text', array( &$this, 'add_footer_credits' ) );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'load_default_css') );
		add_action( 'admin_init', array( $this, 'add_color_scheme') );
		
		add_action( 'wp_before_admin_bar_render', array( $this, 'save_wp_admin_color_schemes_list' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_admin_bar_color' ) );
		
		add_action( 'admin_bar_menu', array( $this, 'set_admin_bar_options' ), 25 );
	}

	/**
	 * nerdpress_footer_admin function.
	 * 
	 * @access public
	 * @return void
		* @TODO Clean up this comment
		* @TODO Would like to wrap this whole file into it's own class
	 */
	function add_footer_credits() {
		echo '<strong>NerdPress</strong><sup>&trade;</sup> Framework for WordPress by <a href="http://nerdymind.com/" target="_blank">NerdyMind Marketing</a>. <em style="font-size: x-small">Made with &#x2764; in Fort Collins, Colorado.</em>';
	}

	/**
	 * Register the custom admin color scheme
	 *
	 * @TODO Implement RTL stylesheets
	 * @TODO Implement Icon colors
	 */	
	function add_color_scheme() {
		wp_admin_css_color(
			'nerdy',
			__( 'Nerdy', 'nerdy-color-scheme' ),
			get_template_directory_uri() . '/assets/css/nerdy-admin.css',
			array( '#008fc5', '#f59329', '#008fc5', '#f59329' )
		);
	}

	/**
	 * Make sure core's default `colors.css` gets enqueued, since we can't
	 * @import it from a plugin stylesheet. Also force-load the default colors
	 * on the profile screens, so the JS preview isn't broken-looking.
	 *
	 * Copied from Admin Color Schemes - http://wordpress.org/plugins/admin-color-schemes/
	 */	
	function load_default_css() {
		global $wp_styles;

		$color_scheme = get_user_option( 'admin_color' );

		if ( 'nerdy' === $color_scheme || in_array( get_current_screen()->base, array( 'profile', 'profile-network' ) ) ) {
			$wp_styles->registered[ 'colors' ]->deps[] = 'colors-fresh';
		}
	}
	
	/**
	 * Save the color schemes list into wp_options table
	 */
	function save_wp_admin_color_schemes_list() {
		global $_wp_admin_css_colors;

		if ( count( $_wp_admin_css_colors ) > 1 && has_action( 'admin_color_scheme_picker' ) ) {
			update_option( 'wp_admin_color_schemes', $_wp_admin_css_colors );
		}
	}

	/**
	 * Enqueue the registered color schemes on the front end
	 */
	function enqueue_admin_bar_color() {
		if ( ! is_admin_bar_showing() )
			return;

		$user_color = get_user_option( 'admin_color' );

		if ( isset( $user_color ) ) {
			$wp_admin_color_schemes = get_option( 'wp_admin_color_schemes' );
			wp_enqueue_style( $user_color, $wp_admin_color_schemes[$user_color]->url );
		}
	}

	/**
	 * set_admin_bar_options function.
	 * 
	 * adds glasses to header of admin pages
	 	 	 * @TODO Clean up this comment
	 * @access public
	 * @return void
	 */	
	function set_admin_bar_options() {
		global $wp_admin_bar;
		
		//$wp_admin_bar->remove_node( 'wp-logo' );
		
		$args = array(
			'id' => 'nerdy',
			'title' => '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA3NpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNS1jMDE0IDc5LjE1MTQ4MSwgMjAxMy8wMy8xMy0xMjowOToxNSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDoxMGU0N2U0NS1kNmFmLTRhZDItOWQ5Mi04MTI4N2Y1NDk1OTgiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OTM1M0VCMjU2QzM1MTFFM0FDQTU4MEU3ODhDOTI5QkEiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OTM1M0VCMjQ2QzM1MTFFM0FDQTU4MEU3ODhDOTI5QkEiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChNYWNpbnRvc2gpIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6ZmFmZmQwYWUtOTEwMi00NTUyLWI2OWMtMzA1MTA2Mzk0ZGY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjEwZTQ3ZTQ1LWQ2YWYtNGFkMi05ZDkyLTgxMjg3ZjU0OTU5OCIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PiCMzpQAAAEUSURBVHja7JaNDYIwEIXBOAAjMAJugBuwgTqBbGCYgLiBGxAn0A1gg7oBOkG9xmtyYv9oQpqYvuQLFHLm9Xp3mHLOk5BaJYEVDUQDf2+gAjq8KpUuOAdy4AgUuH4AV2DA+4+EAQWZ5rmJAmiBnpvFgFLGqX5oD4xAjescAyRTc+L9jc9TJePpEWTACVMn16UmvSKNjSLGRRuM/6oBel7lwoWZ0sUarzugDtmGL8/4O02nlyaVz2YUEiNFODrG9NOip4PoCZxneL+Q/j5gvE2NKQOyl100atqx02Sqxfc/ba+ahMzSVmKnW8PZZ2T6DdbMaCYa0+y69ZySWkzfggJ347YT36EQ/5RGA9FANBANhDbwFmAAtZFTB9f01MoAAAAASUVORK5CYII=">',
			'href' => '#',
		);
		
		$wp_admin_bar->add_node( $args );
		
		$current_theme = wp_get_theme();
		
		$args = array(
			'id' => 'nerdpress-version',
			'parent' => 'nerdy',
			'title' => $current_theme->get( 'Name' ) . ' v' . $current_theme->get( 'Version' ),
		);
		
		$wp_admin_bar->add_node( $args );
		
		if ( is_child_theme() ) :
		
			$parent_theme = wp_get_theme( 'nerdpress' );
			
			$args = array(
				'id' => 'nerdpress-child',
				'parent' => 'nerdy',
				'title' => 'Child of ' . $parent_theme->get( 'Name' ) . ' v' . $parent_theme->get( 'Version' ),
			);
			
			$wp_admin_bar->add_node( $args );
		
		endif;
		
		$args = array(
			'id' => 'nerdpress-link',
			'parent' => 'nerdy',
			'title' => 'Visit NerdyMind',
			'href' => 'http://nerdymind.com/',
			'meta' => array(
				'target' => '_blank',
			),
		);
		
		$wp_admin_bar->add_node( $args );
		
		$args = array(
			'id' => 'nerdpress-settings',
			'parent' => 'site-name',
			'title' => 'NerdPress Settings',
			'href' => admin_url( 'options-general.php?page=nerdpress-settings' ),
		);
		
		if ( ! is_admin() ) $wp_admin_bar->add_node( $args );
	}

}

$brand_wp = get_option( 'options_brand_wp' );

if ( $brand_wp ) 
	new NerdPressAdmin();