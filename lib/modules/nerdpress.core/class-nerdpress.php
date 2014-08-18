<?php
/**
 * class.nerdpress.php, The main functionality file for nerdpress
 * 
 * This file controls many of the special features and output of nerdpress
 * 
 * 
 * @version 1.0
 * @package nerdpress
 */
class NerdPress {

	function __construct() {
/**
 * Page Now Global
 * @global string $pagenow['_var'] 
 * 
 */ 
		global $pagenow;
		
		add_action('init', array( &$this, 'init_filesystem' ) );
		add_action( 'widgets_init', array( &$this, 'register_widget_areas' ) );
		add_action( 'init', array( &$this, 'addl_integrations' ) );
		add_filter( 'roots_display_sidebar', array( &$this, 'hide_sidebar_on' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'load_scripts' ), 200 );		
		add_action( 'login_enqueue_scripts', array( &$this, 'login_logo' ) );
		add_filter( 'login_headerurl', array( &$this, 'login_url' ) );
		add_filter( 'login_headertitle', array( &$this, 'login_title' ) );
		add_filter( 'wp_revisions_to_keep', array( &$this, 'limit_revisions' ), 10, 2 );		
		add_filter( 'wp_title', array( &$this, 'seo_title' ), 10, 2 );
		add_action( 'wp_head', array( &$this, 'seo_description') );
		add_action( 'tgmpa_register', array( &$this, 'register_required_plugins' ) );
		add_filter( 'scpt_show_admin_menu', '__return_false' ); // Hide SuperCPT's useless icon menu item
		add_action( 'after_setup_theme', array( &$this, 'setup_post_types' ) );
		add_filter('comment_reply_link', array( &$this, 'bootstrap_reply_link_class' ) );
		add_filter( 'the_password_form', array( &$this, 'bootstrap_password_form' ) );
		add_action( 'wp_footer', array( &$this, 'statcounter' ) );
		add_action( 'init', array( &$this, 'load_menu_locations' ) );
		add_action( 'init', array( &$this, 'roots_cleanup' ), 200 );
		add_action( 'init', array( &$this, 'detect_mobile' ) );
		
		if ( !class_exists('WPSEO_Frontend') ) 
			add_action( 'wp_head', array( &$this, 'load_canonical' ) );
		
		add_shortcode( 'nerdpress_sitemap', array( &$this, 'sitemap' ) );
		add_shortcode( 'nerdpress_social_networks', array( &$this, 'social_networks' ) );
		add_shortcode( 'nerdpress_social_share', array( &$this, 'social_share' ) );
		
		if ( in_array( 'gravityforms/gravityforms.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && !get_option( 'rg_gforms_key' ) ) 
			add_action( 'init', array( &$this, 'setup_gravity_forms' ) );
		
		if ( in_array( 'siteorigin-panels/siteorigin-panels.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && !get_option( 'rg_gforms_key' ) ) 
			add_action( 'init', array( &$this, 'setup_nerdpress_panels' ) );
			
		if ( $pagenow == 'plugin-install.php' ) 
			add_action( 'admin_notices', array( &$this, 'plugin_install_warning' ) );
			
		if ( is_child_theme() ) :
/* 			if ( NerdPress::variable( 'use_compiler' ) == 1 ) : */
				add_filter( 'nerdpress_compiler', array( &$this, 'child_load_less' ) );
				add_action( 'after_setup_theme', array( &$this, 'child_monitor_less' ) );
/* 			endif; */
		endif;
	}

	/**
	 * init_filesystem function.
	 * 
	 * Came from Roots. Makes sure that the theme can write files to the server, nescisary for the compiler
	 * @TODO clean up this comment
	 * @access public
	 * @return The Location of a known file handler, if one is not present
	 */
	static function init_filesystem() {
		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH .'/wp-admin/includes/file.php' );
			WP_Filesystem();
		}
	}
	
	/**
	 * Allows us to return any settings set in Nerdpress Settings
	 * Gets variables from ACF (a wrapper for ACF)
	 * 
	 * @access public
	 * @param mixed $var
	 * @return a mixed set of variables and arrays from the option area of ACF
	 */
	static function variable( $var ) {
		if ( !function_exists( 'get_field' ) ) return false;
		
		if ( get_field( $var, 'option' ) ) 
			return get_field( $var, 'option' );
		else 
			return false;
	}


	/**
	 * Sidebar creater 
	 * 
	 * Gets the variables from nerdpress settings, creates widget areas programmatically 
	 *
	 * @access public
	 * @return one or more regiser_sidebar() objects
	 */
	static function register_widget_areas() {
		$widget_areas = self::variable( 'widget_areas' );
		
		if ( $widget_areas ) :
			foreach ( $widget_areas as $widget_area ) :
				$safe_name = strtolower( str_replace( ' ', '-', $widget_area['area_name'] ) );
			
				register_sidebar(array(
					'name' 				=> __( $widget_area['area_name'], 'nerdpress' ),
					'id' 					=> $safe_name,
					'class' 				=> $safe_name,
					'before_widget' 	=> '<section class="widget %1$s %2$s ' . $widget_area['area_class'] . '"><div class="widget-inner">',
					'after_widget' 		=> '</div></section>',
					'before_title' 		=> '<h3>',
					'after_title' 			=> '</h3>',
				));
			endforeach;
		endif;
	}
	
	/**
	 * widget_area function.
	 * 
	 * A wrapper for dynamic_sidebar - adds the edit link if you are an administrator
	 * Checks to make sure widget area exists, if not, echos error
	 * 
	 * @access public
	 * @param mixed $widget_area_id
	 * @return Sidebar output for the widget areas
	 */
	static function widget_area( $widget_area_id ) {
		global $wp_registered_sidebars;
		
		$safe_name = strtolower( str_replace( ' ', '-', $widget_area_id ) );
		
		if ( array_key_exists( $safe_name, $wp_registered_sidebars ) ) :
		
			if ( is_dynamic_sidebar( $safe_name ) ) :
				echo "\n" . '<div class="' . $wp_registered_sidebars[$safe_name]['class'] . '">' . "\n";
				dynamic_sidebar( $safe_name );
				echo "\n" . '</div>' . "\n";
				include( locate_template( 'templates/edit-link.php' ) );
			endif;
		
		else :
			if ( current_user_can( 'administrator' ) ) 
				echo '<div class="alert alert-danger"><strong>Problem!</strong> You asked for widget area <code>' . $widget_area_id . '</code> but no widget area exists with this name. 
				<a href="' . admin_url( 'options-general.php?page=nerdpress-settings' ) . '" class="btn btn-sm btn-default" target="_blank">
					<i class="fa fa-cog text-primary"></i> Add It</a></div>';
		endif;
	}
	
	
	/**
	 * addl_integrations function.
	 * 
	 * Looks in NP settings, checks the values of bbPress, Twitter API based off the integrations file - makes things play nice with WordPress
	 *
 	 * @TODO Check integrations docs
 	 * 
	 * @access public
	 * @return enques the selected integration settings 
	 */
	static function addl_integrations() {
		$addl_integrations = self::variable( 'addl_integrations' );
		
		if ( $addl_integrations ) :		
			foreach ( $addl_integrations as $integration ) {
				require_once( themePATH . '/' . themeFOLDER . '/lib/modules/nerdpress.core/integrations/' . $integration . '.php' );
			}		
		endif;
	}


	/**
	 * container_class function.
	 * 
   * Checks to see if the page should be full width or not, returns a class depending
 	 * 
	 * @access public
	 * @return the required class for the main container
	 */
	static function container_class() {
		global $post;
			
		if ( get_field( 'nrd_full_width' ) ) return 'full-width';
		else return 'container';
	}



	/**
	 * display_sidebar function.
	 * 
	 * Checks for the conditions of if the sidebar should be hidden or not, 
	 * for custom post types, you might need new conditions
	 *
 	 * @TODO change the behavior for this - checkboxes?? 
	 * @access public
	 * @return void
	 */
	static function display_sidebar() {
		$hide_sidebar_conditions = array();
		
		$hide_sidebar_conditions_option = self::variable( 'hide_sidebar_conditions' );
		
		if ( $hide_sidebar_conditions_option ) :
		foreach ( $hide_sidebar_conditions_option as $condition ) {
			$hide_sidebar_conditions[] = $condition['condition'];
		}
		endif;
		
		$hide_sidebar_templates = array();
		
		$hide_sidebar_templates_option = self::variable( 'hide_sidebar_templates' );
		
		if ( $hide_sidebar_templates_option ) :
			foreach ( $hide_sidebar_templates_option as $template ) {
				$hide_sidebar_templates[] = $template;
			}
		endif;
	
		$sidebar_config = new Roots_Sidebar(
			$hide_sidebar_conditions,
			$hide_sidebar_templates
		);
		
		return apply_filters('roots_display_sidebar', $sidebar_config->display);
	}


	/**
	 * hide_sidebar_on function.
	 * 
	 * Checks to see if the choice to hide the existing page is checked, hides the page if it is
	 * 
	 * @access public
	 * @param mixed $sidebar
	 * @return returns sidebar content, or nothing
	 */
	static function hide_sidebar_on( $sidebar ) {
		if ( get_field( 'nrd_hide_sidebar' ) ) return false;
		
		return $sidebar;
	}


	/**
	 * main_class function.
	 * 
	 * For the template wrapper - takes the main class value in settings if the page has a sidebar, and echos it out, if no sidebar, it's 12 columns
	 * @TODO clean up this comment
	 * @access public
	 * @return either returns the main class or a full width class
	 */
	static function main_class() {
		if ( self::display_sidebar() ) $class = self::variable( 'main_class' );
		else $class = 'col-sm-12';
		
		return $class;
	}


	/**
	 * sidebar_class function.
	 * 
	 * If the page has a sidebar, echos out what the settings for the sidebar class should be 
	 * @TODO clean up this comment
	 * @access public
	 * @return returns the sidebar class or nothing
	 */
	static function sidebar_class() {
		return self::variable( 'sidebar_class' );
	}
	
	
	/**
	 * load_scripts function.
	 * 
	 * Does a ton of shit
	 * in order
	 * disaibles the roots script file
	 * figures out the location of the css based on if the site is multisite or not
	 * enques the NP stype file
	 * enques font awesome from CDN
	 * enques bootstrap .js from CDN
	 * placeholder .js
	 * retina.js
	 * if GA is on, enques GA
	 * if StatCounter is on, enques StatCounter
	 * checks if other scripts are defined, 
	 * along with
	 * animate.css
	 * flexslider
	 * vimeo api
	 * bootstrap hover
	 * enques main.js
	 * 
	 * @TODO clean up this comment
	 * @access public
	 * @return void
	 * 
	 */
	static function load_scripts() {
		global $blog_id;
	
		wp_deregister_script( 'roots_scripts' );
		wp_dequeue_style( 'roots_main' );
		
		if ( is_multisite() ) 
			$the_css = get_template_directory_uri() . '/assets/css/main.min_id-' . $blog_id . '.css';
		else 
			$the_css = get_template_directory_uri() . '/assets/css/main.min.css';
		
		wp_enqueue_style( 'nerdpress', $the_css, false, '6c39f42987ae297a5a21e2bb35bf3402' );
		
		wp_register_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css' );
		wp_enqueue_style( 'font-awesome' );
		
		wp_enqueue_script( 'bootstrap', '//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js', array( 'jquery' ), NULL, true );
		
		wp_enqueue_script( 'placeholder', '//cdnjs.cloudflare.com/ajax/libs/jquery-placeholder/2.0.7/jquery.placeholder.min.js', array( 'jquery'), NULL, true );		
		wp_enqueue_script( 'retina', '//cdnjs.cloudflare.com/ajax/libs/retina.js/1.0.1/retina.js', NULL, NULL, true );
		
		if ( self::variable( 'analytics_id' ) ) 
			wp_enqueue_script( 'analytics', get_template_directory_uri() . '/assets/js/analytics.php', array( 'jquery' ), NULL, NULL );
		
		$load_scripts = self::variable( 'load_scripts' );
		$script_header = self::variable( 'script_header' );
		$script_footer = self::variable( 'script_footer' );
		
		if ( $load_scripts ) :
		
			if ( in_array( 'animatecss', $load_scripts ) ) :
				wp_register_style( 'animate-css', '//cdnjs.cloudflare.com/ajax/libs/animate.css/3.1.0/animate.min.css' );
				wp_enqueue_style( 'animate-css' );
			endif;
			
			if ( in_array( 'flexslider', $load_scripts ) ) 
				wp_enqueue_script( 'flexslider', '//cdnjs.cloudflare.com/ajax/libs/flexslider/2.2.2/jquery.flexslider-min.js', array( 'jquery'), '2.2.2', true );
				
			if ( in_array( 'lightbox', $load_scripts ) ) {
				wp_enqueue_style( 'ekko', '//cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/3.0.3a/ekko-lightbox.min.css' );
				wp_enqueue_script( 'ekko', '//cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/3.0.3a/ekko-lightbox.min.js', array( 'jquery' ), null, true );
			}
				
			if ( in_array( 'vimeo_api', $load_scripts ) ) 
				wp_enqueue_script( 'froogaloop', '//a.vimeocdn.com/js/froogaloop2.min.js', NULL, NULL, true );
				
			if ( in_array( 'bootstrap_hover', $load_scripts ) ) 
				wp_enqueue_script( 'bootstrap-hover', '//cdnjs.cloudflare.com/ajax/libs/bootstrap-hover-dropdown/2.0.2/bootstrap-hover-dropdown.min.js', array( 'jquery' ), NULL, true );
			
		endif;
			
		if ( $script_header ) :
			foreach ( $script_header as $script ) {
				wp_enqueue_script( $script['script_url'], $script['script_url'] );
			}
		endif;
		
		if ( $script_footer ) :
			foreach ( $script_footer as $script ) {
				wp_enqueue_script( $script['script_url'], $script['script_url'], NULL, NULL, true );
			}
		endif;
		
		wp_enqueue_script( 'main', get_stylesheet_directory_uri() . '/assets/js/main.js', array( 'jquery' ), NULL, true );
	}
	
	
	/**
	 * make_crumb function.
	 * 
	 * returns the array of breadcrumb parts to be ecoed out later
	 * look at breadcrumb.php
	 * Concatinates the array together
	 * @TODO clean up this comment
	 * @access public
	 * @param bool $url (default: false)
	 * @param mixed $text
	 * @return void
	 */
	static function make_crumb( $url = false, $text ) {
		global $breadcrumbs;
		
		$breadcrumbs[] = array(
			'url' => $url,
			'text' => $text,
		);
		
		return $breadcrumbs;
	}
	
	
	/**
	 * breadcrumbs function.
	 * 
	 Figures out what page you are on, and builds the breadcrumb for wherever you are
	 * @access public
	 * @return void
	 */
	static function breadcrumbs() {
/* 	if not on, skip this */
		if ( !self::variable( 'breadcrumbs' ) ) return;
		
		global $breadcrumbs, $post, $wp_query;
		
/* skip this if you are not these things		 */
		$skip_post_types = array(
			'forum',
			'topic',
			'reply',
		);
		
		
		$skip_taxonomies = array(
			'topic-tag',
		);
		
		$term = get_query_var( 'term' );
		$taxonomy = get_query_var( 'taxonomy' );
		$paged = get_query_var( 'paged' );
		
		// WooCommerce check - set a variable we can use to see if WooCommerce is on
		/**
		* @TODO make this a seperate function that gets called even if breadcrumbs is turned off
		*/
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
			$woo_active = true;
			
		// bbPress check 
		/**
		* @TODO make this a seperate function that gets called even if breadcrumbs is turned off
		*/
		if ( class_exists( 'bbPress' ) ) 
			$bbpress_active = true;
	
		$breadcrumbs = array();
		
		// Builds the Home URL
		/**
		* @TODO Make this possible to be a word, an icon, nothing, an image... not always font awesome
		*/
		self::make_crumb( home_url(), self::variable( 'breadcrumb_home_text' ) );
		
		// WooCommerce
		if ( $woo_active ) :
			$shop_id = get_option( 'woocommerce_shop_page_id' );
			
			if ( is_cart() || is_checkout() || is_woocommerce() && get_option( 'page_on_front' ) !== $shop_id ) 
				self::make_crumb( ( is_shop() ? null : get_permalink( $shop_id ) ), get_the_title( $shop_id ) );
		endif; // WooCommerce
		
		// Page -- Parents murdered in an alley
		if ( is_page() && !$post->post_parent )
/* 		throw out the url, and the name */ 
			self::make_crumb( null, get_the_title() );
		
		// Page -- Has parents
		if ( is_page() && $post->post_parent ) :
		
			$parent_id = $post->post_parent;
			
			// We have to put ancestors in the own array first because 
			// they're in a reverse order. We'll order them correctly in a second.
			$subcrumbs = array();
			
			while ( $parent_id ) :
				$page = get_page( $parent_id );
				
				$subcrumbs[] = array(
					'url' => get_permalink( $page->ID ),
					'text' => get_the_title( $page->ID ),
				);
				
				$parent_id = $page->post_parent;
			endwhile;
			
			// Reverse the ancestor order
			$subcrumbs = array_reverse( $subcrumbs );
			
			foreach ( $subcrumbs as $crumb => $data ) {
				// Make crumbs for each ancestor
/* 				Concatinate the breadcrumbs into one */
				self::make_crumb( $data['url'], $data['text'] );
			}
			
			// Finally, make crumb for current page
			self::make_crumb( null, get_the_title() );
			
		endif; // Page with parents
		
		// Author archive
/* 		find out who the offer is */
		if ( is_author() ) :
			global $author;
		
			$the_author = get_userdata( $author );
/* 			concatinate it in */
			self::make_crumb( null, $the_author->display_name . '\'s Posts' );
		
		endif; // Author archive
		
		if ( is_category() ) :
		
			$cat_object = $wp_query->get_queried_object();		
	        $this_cat = get_category( $cat_object->term_id );
	        
	        // Category has parents
	        // This is all hacky because we put the categories into a pipe-separated
	        // list and then split them out. This leaves the last element in the array
	        // as blank. So we cut that and make crumbs for the rest. Should 
	        // work nicely for nested categories.
	  /**
		* @TODO Audit this, we can't remember what this pipe shit is all about
		*/
	        if ( $this_cat->parent != 0 ) :
	        	$parents = get_category_parents( get_category( $this_cat->parent ), false, '|', false );
	        	$parents = explode( '|', $parents );
	        	unset( $parents[ count( $parents ) - 1 ] );
	        	
	        	foreach ( $parents as $parent ) {
	        		$the_cat = get_term_by( 'name', $parent, 'category' );
	        		$the_link = get_term_link( $the_cat );
		        	self::make_crumb( $the_link, $parent );
	        	}
	        endif;
	        
	        // The current category
	        self::make_crumb( null, single_cat_title( null, false ) );
	        
		endif; // Is category
		
		// Tagged
		if ( is_tag() ) :
		
			$tag_object = $wp_query->get_queried_object();
			$this_tag = get_tag( $tag_object );
			
			self::make_crumb( null, single_cat_title( null, false ) );
		
		endif; // Is tag
		
		// 404
		if ( is_404() ) :
			self::make_crumb( null, 'File Not Found (Error 404)' );
		endif; // 404
		
		// Search results
		if ( is_search() ) :
			self::make_crumb( null, 'Search Results for "' . get_search_query() . '"' );
		endif; // Search
		
		// Single post, except products
/* 		tries to handle not only single posts, but any single post that is created by nerdpress or any other plugin */


		if ( is_single() && !in_array( get_post_type(), $skip_post_types ) ) :
		
			$post_type = get_post_type_object( get_post_type() );

/* What post type has been created in the backend? */
			
			$post_type_config = self::variable( 'post_types' );
			
			if ( $post_type_config ) :
/* split them all out */			
				foreach ( $post_type_config as $type ) :
				
					if ( $type['type_name'] != $post_type->name ) continue;
					
					if ( $type['type_breadcrumb'] ) 
		/**
		* @TODO write better documentation regarding 
		*/
/* 					replaces whatever breadcrumb is the top level parent with whatever arbitrary page you would like by page ID */
						self::make_crumb( get_permalink( $type['type_breadcrumb'] ), get_the_title( $type['type_breadcrumb'] ) );
/* 						figure out if it has an archive */
					elseif ( $post_type->has_archive == 1 ) 
/* tries to grab post types from anywhere */
		/**
		* @TODO verify that this checks that the archive has content
		*/
/* 					if you have a taxonomy, grab the first one, stick it in there */
/* 					When createing a post type in the UI, you can define a breadcrumb */

						self::make_crumb( home_url( $post_type->rewrite['slug'] ), $post_type->labels->name );
/* 					if no taxonomy, skip that part */
					elseif ( $post_type->has_archive ) 
					/**
					* @TODO verify that this checks to see if the archive exists at all
					*/
						self::make_crumb( home_url( $post_type->has_archive ), get_the_title( get_page_by_path( $post_type->has_archive ) ) );
						
					else 
						self::make_crumb( home_url(), $post_type->labels->name );
				endforeach;
			
			endif;
					
			// get the taxonomy names of this object
/* 			grabs the first taxonmy that is found */
			$taxonomy_names = get_object_taxonomies( $post_type->name );
			
			// Detect any hierarchical taxonomies that might exist on this post type
/* 			if it's harerarchicar, grab all the kids */
			$hierarchical = false;
			
			foreach ( $taxonomy_names as $taxonomy_name ) :
				if ( !$hierarchical ) {
					$hierarchical = ( is_taxonomy_hierarchical( $taxonomy_name ) ) ? true : $hierarchical;
					$tn = $taxonomy_name;
				}
			endforeach;
			
			$args = ( is_taxonomy_hierarchical( $tn ) ) ? array( 'orderby' => 'parent', 'order' => 'DESC' ) : '';
			
			if ( $terms = wp_get_post_terms( $post->ID, $tn, $args ) ) {
				$main_term = $terms[0];
				
				if ( is_taxonomy_hierarchical( $tn ) ) {
					$ancestors = get_ancestors( $main_term->term_id, $tn );
					$ancestors = array_reverse( $ancestors );
					
					foreach ( $ancestors as $ancestor ) {
						$ancestor = get_term( $ancestor, $tn );
						self::make_crumb( get_term_link( $ancestor->slug, $tn ), $ancestor->name );
					}
				}
				self::make_crumb( get_term_link( $main_term->slug, $tn ), $main_term->name );
			}
			
/* 			And finally, on the last one, give it null */
			self::make_crumb( null, get_the_title() );
			
		endif; // Single post
		
		// Post type archive
/* 		exact same behavior as above, but with one less layer since it's just an archive */
		if ( is_post_type_archive() ) :
			
			$post_type = get_post_type_object( get_post_type() );
			
			$post_type_config = self::variable( 'post_types' );
			
			if ( $post_type_config ) :
					
				foreach ( $post_type_config as $type ) :
				
					if ( $type['type_name'] != $post_type->name ) continue;
					
					if ( $type['type_breadcrumb'] ) 
						self::make_crumb( get_permalink( $type['type_breadcrumb'] ), get_the_title( $type['type_breadcrumb'] ) );
						
					elseif ( $post_type->has_archive == 1 ) 
						self::make_crumb( home_url( $post_type->rewrite['slug'] ), $post_type->labels->name );
						
					elseif ( $post_type->has_archive ) 
						self::make_crumb( home_url( $post_type->has_archive ), get_the_title( get_page_by_path( $post_type->has_archive ) ) );
						
					else 
						self::make_crumb( home_url(), $post_type->labels->name );
						
				endforeach;
			
			endif;
		
		endif; // Post type archive
		
/* 		Tackles woocommerce, bbpress, any outlying taxonomy */
/* gets post types, category, taxonomy */
		// Generic taxonomy
		if ( is_tax() ) :
		
			$tax_object = $wp_query->get_queried_object();
			
			if ( !in_array( $tax_object->taxonomy, $skip_taxonomies ) ) :
						
				$this_term = get_term( $tax_object->term_id, $tax_object->taxonomy );
				$the_tax = get_taxonomy( $tax_object->taxonomy );
				
				$post_type_config = self::variable( 'post_types' );
				$taxonomy_config = self::variable( 'taxonomies' );
				
				if ( $taxonomy_config ) :
				
					foreach ( $taxonomy_config as $tax ) :
					
						if ( $tax['tax_name'] != $this_term->taxonomy ) continue;
						
						if ( $tax['tax_breadcrumb'] ) 
							self::make_crumb( get_permalink( $tax['tax_breadcrumb'] ), get_the_title( $tax['tax_breadcrumb'] ) );
							
						if ( $tax['tax_connect'] && !$tax['tax_breadcrumb'] ) :
							
							foreach ( $post_type_config as $type ) :
							
								if ( $type['type_name'] != $tax['tax_connect'] ) continue;
								
								$post_type = get_post_type_object( $tax['tax_connect'] );
								
								if ( $post_type->has_archive == 1 ) 
									self::make_crumb( home_url( $post_type->rewrite['slug'] ), $post_type->labels->name );
									
								elseif ( $post_type->has_archive ) 
									self::make_crumb( home_url( $post_type->has_archive ), get_the_title( get_page_by_path( $post_type->has_archive ) ) );
							
							endforeach;
							
						endif;
						
						self::make_crumb( null, $the_tax->labels->name );
						
					endforeach;
				
				endif;

				$parent_id = $this_term->parent;
				$subcrumbs = array();
				
				while ( $parent_id ) :
					$term = get_term_by( 'id', $parent_id, $tax_object->taxonomy );
					
					$subcrumbs[] = array(
						'url' => get_term_link( $term->term_id, $tax_object->taxonomy ),
						'text' => $term->name,
					);
					
					$parent_id = $term->parent;
				endwhile;
				
				$subcrumbs = array_reverse( $subcrumbs );
				
				foreach ( $subcrumbs as $crumb => $data ) {
					// Make crumbs for each ancestor
					self::make_crumb( $data['url'], $data['text'] );
				}
		        
		        // The current term
		        self::make_crumb( null, single_cat_title( null, false ) );
	        
	        endif;
		
		endif; // Generic taxonomy
		
		// Day archive
		if ( is_day() ) :
	        self::make_crumb( get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) ); // Year
	        self::make_crumb( get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ), get_the_time( 'F' ) ); // Month
	        self::make_crumb( null, get_the_time( 'd' ) ); // Day
		endif; // Day archive
		
		// Month archive
		if ( is_month() ) :
			self::make_crumb( get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) ); // Year
			self::make_crumb( null, get_the_time( 'F' ) ); // Month
		endif; // Month archive
		
		// Year archive
		if ( is_year() ) :
			self::make_crumb( null, get_the_time( 'Y' ) ); // Year
		endif; // Year archive
		
		// bbPress wrap
		// We check for the existence of the BBpress class
		if ( isset( $bbpress_active ) && 
			( bbp_is_topic_archive() || 
				bbp_is_search() || 
				bbp_is_forum_archive() || 
				bbp_is_single_view() || 
				bbp_is_single_forum() || 
				bbp_is_single_topic() || 
				bbp_is_single_reply() || 
				bbp_is_topic_tag() || 
				bbp_is_user_home() ) ) :
		/**
		* @TODO burn || into your head
		*/
			
			self::make_crumb( home_url( get_option( '_bbp_root_slug' ) ), 'Forums' );
		
			$ancestors = (array) get_post_ancestors( get_the_ID() );
			
			// Ancestors exist
			if ( !empty( $ancestors ) ) :
				// Loop through parents
				foreach ( (array) $ancestors as $parent_id ) :
				// Parents
				$parent = get_post( $parent_id );
				
				// Skip parent if empty or error
				if ( empty( $parent ) || is_wp_error( $parent ) ) continue;
				
					// Switch through post_type to ensure correct filters are applied
					switch ( $parent->post_type ) {
					
						// Forum
						case bbp_get_forum_post_type() :
							self::make_crumb( esc_url( bbp_get_forum_permalink( $parent->ID ) ), bbp_get_forum_title( $parent->ID ) );
						break;
						
						// Topic
						case bbp_get_topic_post_type() :
							self::make_crumb( esc_url( bbp_get_topic_permalink( $parent->ID ) ), bbp_get_topic_title( $parent->ID ) );
						break;
						
						// Reply
						case bbp_get_reply_post_type() :
							self::make_crumb( esc_url( bbp_get_reply_permalink( $parent->ID ) ), bbp_get_reply_title( $parent->ID ) );
						break;
						
					}
				endforeach;
			endif;
			
			// Topic archive
			if ( bbp_is_topic_archive() ) 
				self::make_crumb( null, bbp_get_topic_archive_title() );
			
			// Search page
			if ( bbp_is_search() ) 
				self::make_crumb( null, bbp_get_search_title() );
			
			// Forum archive
/*
			if ( bbp_is_forum_archive() ) 
				self::make_crumb( null, bbp_get_forum_archive_title() );
*/
			
			// View
			elseif ( bbp_is_single_view() ) 
				self::make_crumb( null, bbp_get_view_title() );
			
			if ( bbp_is_single_forum() ) 
				self::make_crumb( null, bbp_get_forum_title() );
			
			if ( bbp_is_single_topic() ) 
				self::make_crumb( null, bbp_get_topic_title() );
			
			if ( bbp_is_single_reply() ) 
				self::make_crumb( null, bbp_get_reply_title() );
				
			if ( bbp_is_topic_tag() ) {
				$topic_tag = $wp_query->get_queried_object();
				self::make_crumb( null, $topic_tag->name );
			}				
				
			if ( bbp_is_user_home() ) 
				self::make_crumb( null, 'Profile' );
				
		endif; // bbPress
		
		// Paged content
/* 		if you are on a multiple page or not, says page and then number */
		if ( $paged ) :
			self::make_crumb( null, 'Page ' . $paged );
		endif; // Paged
		
/* 		If you are not on home or front_page, go get the template and echo everything out */
		if ( !is_front_page() && !is_home() ) get_template_part( 'templates/breadcrumbs' );
	} // breadcrumbs
	
	
	/**
	 * sitemap function.
	 * 
	 * Gets the sitemap template
	 * pages can be excluded by using the page wide URI
	 * @access public
	 * @return void
	 */
	static function sitemap() {
		ob_start();
		get_template_part( 'templates/sitemap' );
		$sitemap = ob_get_contents();
		ob_end_clean();
		
		return $sitemap;
	}

	
	/**
	 * login_logo function.
	 * 
	 * Looks to see if the site logo file exists, if it does, it goes and gets a template for login 
	 * put image in:
	 * assets/img/site-logo.png
	 * Defaults url for image to the home page
	 * @TODO make this a seperate function that gets called even if breadcrumbs is turned off
	 * @access public
	 * @return void
	 */
	static function login_logo() {
		if ( !locate_template('assets/img/site-logo.png') ) return;
		get_template_part( 'templates/login', 'logo' );
	}
	
//	added to filters at the top
	
	
	/**
	 * login_url function.
	 * 
	 * makes home URL available for the login page
	 * @access public
	 * @return void
	 * @TODO Clean up this comment
	 */
	 
	
	static function login_url() {
		return get_bloginfo( 'url' );
	}
	
	
	/**
	 * login_title function.
	 * 
	 * makes name of site available for the 
	 * @access public
	 * @return void
	 * @TODO Clean up this comment
	 */
	static function login_title() {
		return get_bloginfo( 'name' );
	}
	
	
	/**
	 * limit_revisions function.
	 * 
	 * Limits the number of revisions to 2
	 * @TODO Clean up this comment
	 * @TODO change number of revisions to 5
	 * @access public
	 * @param mixed $num
	 * @param mixed $post
	 * @return void
	 */
	static function limit_revisions( $num, $post ) {
		return 2;
	}
	
	
	/**
	 * social_networks function.
	 * 
	 * Goes and grabs social networks from NerdPress Settings
	 * Creates the short code to echo out the social network template
	 * templates/socialnetworks.php
	 * 
	 * @TODO Clean up this comment
	 * @access public
	 * @return void
	 */
	static function social_networks() {
		ob_start();
		get_template_part( 'templates/social', 'networks' );
		$social_networks = ob_get_contents();
		ob_end_clean();
		
		return $social_networks;
	}
	
	
	/**
	 * seo_title function.
	 * 
	 * If an SEO title is entered for the current page, SEO title overrides the existing title
	 * @TODO Clean up this comment
	 * @access public
	 * @param mixed $title
	 * @param mixed $sep
	 * @return void
	 */
	static function seo_title( $title, $sep ) {
		global $post;
		
		$seo_title = get_field( 'nrd_seo_title' );
		
		if ( $seo_title ) $title = $seo_title . ' ' . $sep . ' ';
		
		return $title;
	}
	
	
	/**
	 * page_title function.
	 * 
	 * Calls every use case for the title, figures out what it should be depending on if it exists or not
	 * @TODO Clean up this comment
	 * @access public
	 * @return void
	 */
	static function page_title() {
		global $post;
		
		$seo_heading = get_field( 'nrd_seo_heading' );
		
		if ( is_home() ) {
			if ( get_option( 'page_for_posts', true ) ) {
				return get_the_title(get_option('page_for_posts', true));
		} else {
			return __('Latest Posts', 'nerdpress');
		}
		} elseif (is_archive()) {
			$term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
		if ($term) {
		return apply_filters('single_term_title', $term->name);
		} elseif (is_post_type_archive()) {
			return apply_filters('the_title', get_queried_object()->labels->name);
		} elseif (is_day()) {
			return sprintf(__('Daily Archives: %s', 'nerdpress'), get_the_date());
		} elseif (is_month()) {
			return sprintf(__('Monthly Archives: %s', 'nerdpress'), get_the_date('F Y'));
		} elseif (is_year()) {
			return sprintf(__('Yearly Archives: %s', 'nerdpress'), get_the_date('Y'));
		} elseif (is_author()) {
			$author = get_queried_object();
			return sprintf(__('Author Archives: %s', 'nerdpress'), $author->display_name);
		} else {
			return single_cat_title('', false);
		}
		} elseif (is_search()) {
			return sprintf(__('Search Results for %s', 'nerdpress'), get_search_query());
		} elseif (is_404()) {
			return __('Not Found', 'nerdpress');
		} elseif ( $seo_heading ) {
			return $seo_heading;
		} else {
			return get_the_title();
		}
	}
	
	
	/**
	 * seo_description function.
	 * 
	 * checks if a meta has been written on the post, echos out the description 
	 * hooked in WP-head
	 * @TODO Clean up this comment
	 * @TODO find out how to find functions that call this function how to show hooks
	 * @access public
	 * @return void
	 */
	static function seo_description() {
		global $post;
		
		$seo_desc = get_field( 'nrd_seo_desc' );
		
		if ( !$seo_desc ) return;
		
		echo '<meta name="description" content="' . htmlspecialchars_decode( $seo_desc, ENT_QUOTES ) . '"/>';	
	}
	
	
	/**
	 * register_required_plugins function.
	 * 
	 * This does a bunch of stuff and uses another class
	 * @access public
	 * @return void
	 	 * @TODO Clean up this comment
	 */
	static function register_required_plugins() {
	
/* 	goes to a location, gets a json list of things we need */
		if ( false === ( $plugins_list = get_transient( 'np_plugins_list' ) ) ) :		
			$plugins_list = wp_remote_get( 'http://repo.nerdymind.com/nerdpress-helpers/plugin-list.php' );
/* 			the contents of this file gets stored in a transient */
			set_transient( 'np_plugins_list', $plugins_list['body'], 86400 );
		endif;
		
		$plugins_list = get_transient( 'np_plugins_list' );
		
		if ( ! is_wp_error( $plugins_list ) ) 
			$plugins = json_decode( $plugins_list, true );
	
		// Change this to your theme text domain, used for internationalising strings
		$theme_text_domain = 'nerdpress';
	
	
			/**
			* Array of configuration settings. Amend each line as needed.
			* If you want the default strings to be available under your own theme domain,
			* leave the strings uncommented.
			* Some of the strings are added into a sprintf, so see the comments at the
			* end of each line for what each argument will be.
			* @TODO Clean up this comment
			* @TODO Research TGMPA class class-tgm-plugin-activation.php
				 * @TODO explore how to update code we bake in
			*/
		$config = array(
			'domain'       		=> $theme_text_domain,         	// Text domain - likely want to be the same as your theme.
			'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
			'parent_menu_slug' 	=> 'plugins.php', 				// Default parent menu slug
			'parent_url_slug' 	=> 'plugins.php', 				// Default parent URL slug
			'menu'         		=> 'nerdpress-plugins', 	// Menu slug
			'has_notices'      	=> true,                       	// Show admin notices or not
			'is_automatic'    	=> false,					   	// Automatically activate plugins after installation or not
			'message' 			=> '',							// Message to output right before the plugins table
			'strings'      		=> array(
				'page_title' 	=> __( 'NerdPress Plugins', $theme_text_domain ),
				'menu_title' 	=> __( 'NerdPress Plugins', $theme_text_domain ),
				'installing' 		=> __( 'Installing Plugin: %s', $theme_text_domain ), // %1$s = plugin name
				'oops' 			=> __( 'Something went wrong with the plugin API.', $theme_text_domain ),
				'notice_can_install_required' 	=> _n_noop( 'NerdPress requires the following plugin: %1$s.', 'NerdPress requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
				'notice_can_install_recommended' 	=> _n_noop( 'NerdPress recommends the following plugin: %1$s.', 'NerdPress recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
				'notice_cannot_install' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
				'notice_can_activate_required' 		=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
				'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
				'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
				'notice_ask_to_update' 					=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
				'notice_cannot_update' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
				'install_link' 									=> _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
				'activate_link'							 		=> _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
				'return' 											=> __( 'Return to NerdPress Plugins', $theme_text_domain ),
				'plugin_activated' 							=> __( 'Plugin activated successfully.', $theme_text_domain ),
				'complete' 										=> __( 'All plugins installed and activated successfully. %s', $theme_text_domain ), // %1$s = dashboard link
				'nag_type' 										=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
			)
		);
	
		if ( $plugins ) 
			tgmpa( $plugins, $config );	
	}
	
/* 	after a bunch of config is generated, the class is ran */


	/**
	 * setup_post_types function.
	 * 
	 * checks to make sure that "Super Custom Post Type" is active
	 * Looks in the settings page
	 * creates the things in the settings area
	 * @TODO Clean up this comment
	 * @access public
	 * @return void
	 */
	static function setup_post_types() {
		
		// Check to make sure our class is here before we waste our time!
		if ( !class_exists( 'Super_Custom_Post_Type' ) ) 
			return;
		
		$post_types = self::variable( 'post_types' );
		$taxonomies = self::variable( 'taxonomies' );
		
		//print_r( $post_types );
		
		if ( $post_types ) :
		
/* 		Assigns configuration settings, passes it to custom post type for it to work it's magic */
			foreach ( $post_types as $post_type => $options ) :
				if ( $options['type_create'] != true ) continue;
				
				$$post_type = new Super_Custom_Post_Type( 
					$options['type_name'], 
					$options['type_singular'], 
					$options['type_plural'], 
					array( 'hierarchical' => $options['type_hierarchical'] ? true : false ) 
				);					
					
				if ( $options['type_icon'] != '' ) 
					$$post_type->set_icon( $options['type_icon'] );
					
				if ( $options['type_slug'] != '' ) 
					$$post_type->cpt['rewrite'] = array(
						'slug' => $options['type_slug'],
						'with_front' => true,
						'pages' => true,
						'feeds' => true,
					);
			endforeach;
		
		endif;
		
		if ( $taxonomies ) :
/* Loops through taxonomies, makes stuff with super custom taxonomy  */			
			foreach ( $taxonomies as $taxonomy => $options ) :
			
				if ( $options['tax_create'] != true ) continue;
				
				$$taxonomy = new Super_Custom_Taxonomy( 
					$options['tax_name'], 
					$options['tax_singular'], 
					$options['tax_plural'], 
					$options['tax_hierarchical'] ? 'cat' : 'tag'
				);
				
				if ( $options['tax_connect'] ) 
					$$taxonomy->connect_post_types( $options['tax_connect'] );
			
			endforeach;
		
		endif;
		
	}
	
	
	/**
	 * bootstrap_reply_link_class function.
	 * 
	 * makes wp play nicer with WordPress
	 * makes the comment button look cool
	 *
	 * @access public
	 * @param mixed $class
	 * @return void
	 */
	static function bootstrap_reply_link_class( $class ) {
		$class = str_replace( "class='comment-reply-link", "class='comment-reply-link btn btn-primary btn-sm", $class );
		return $class;
	}
	
	
	/**
	 * bootstrap_password_form function.
	 * 
	 * If a page has a password defined, it makes the form look nice in bootstrap
	 * @access public
	 * @return void
	 	 * @TODO Clean up this comment
	 */
	static function bootstrap_password_form() {
		global $post;
		
		$label = 'pwbox-' . ( empty( $post->ID ) ? rand() : $post->ID );
		$content = '<div class="row space-top20"><div class="col-sm-8 col-sm-offset-2">';
		$content  .= '<form action="';
		$content .= esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) );
		$content .= '" method="post">';
		$content .= __( 'This post is password protected. To view it, please enter your password below:', 'nerdpress' );
		$content .= '<div class="input-group">';
		$content .= '<input name="post_password" id="' . $label . '" type="password" class="form-control" size="20" />';
		$content .= '<span class="input-group-btn">';
		$content .= '<input type="submit" name="Submit" value="' . esc_attr__( "Submit" ) . '" class="btn btn-primary" />';
		$content .= '</span></div></form>';
		$content .= '</div></div>';
		
		return $content;
	}
	
	
	/**
	 * statcounter function.
	 * 
	 * Checks to see if the statcounter ID has been set, echos out JS if it has
	 * tacked onto WP footer
	 * @TODO Clean up this comment
	 * @access public
	 * @return void
	 */
	static function statcounter() {
		$statcounter_id = self::variable( 'statcounter_id' );
		
		if ( !$statcounter_id ) return;
		
		get_template_part( 'templates/statcounter' );
	}
	
	
	/**
	 * load_menu_locations function.
	 * 
	 * Checks menu locations box, if there is something in there, create it
	 * 
	 * @access public
	 * @return void
	 	 * @TODO Clean up this comment
	 */
	static function load_menu_locations() {
		$menu_locations = self::variable( 'menu_locations' );
		
		if ( $menu_locations ) :
			foreach ( $menu_locations as $location ) {
				$the_location = array(
					$location['location'] => __( $location['location'], 'nerdpress' ),
				);
				
				register_nav_menus( $the_location );
			}
		endif;
	}
		
	
	/**
	 * setup_gravity_forms function.
	 * 
	 * sets defaults for Gravity forms so we don't have to do it every time. 
	 * adds out liscense kee
	 * disables gravity forms css
	 * forces HTML5 output
		* @TODO Clean up this comment
		* @TODO Remove liscense key from public repo
	 * @access public
	 * @return void
	 */
	static function setup_gravity_forms() {
		update_option( 'rg_gforms_key', '55443c0c81be6d6cef07480d077ff677' );
		update_option( 'rg_gforms_disable_css', '1' );
		update_option( 'rg_gforms_enable_html5', '1' );
	}
	
	
	/**
	 * setup_nerdpress_panels function.
	 * 
	 defaults for panels plugin, might not work any longer
	 	 * @TODO Clean up this comment
	 	 	 * @TODO Debug pannel settings still working
	 * @access public
	 * @return void
	 */
	static function setup_nerdpress_panels() {
		$value = 'a:8:{s:10:"animations";b:1;s:15:"bundled-widgets";b:1;s:10:"responsive";b:1;s:12:"mobile-width";i:780;s:12:"margin-sides";i:30;s:13:"margin-bottom";i:30;s:12:"copy-content";b:0;s:10:"inline-css";b:0;}';
		
		if ( get_option( 'siteorigin_panels_display' ) != $value ) 
			update_option( 'siteorigin_panels_display', $value );
	}
	
	
	/**
	 * child_load_less function.
	 * 
	 * Detects if the child theme is being used
	 * adds it to the list of files to be compiled
	 * @access public
	 * @param mixed $bootstrap
	 	 	 * @TODO Clean up this comment
	 * @return void
	 */
	static function child_load_less( $bootstrap ) {
		return $bootstrap . '
		@import "' . get_stylesheet_directory() . '/assets/less/child.less";';
	}


	/**
	 * child_monitor_less function.
	 * 
	 * @access public
	 * @return void
	 * Looks at modified time to trigger compiler if it's a new version
	 	 	 * @TODO Clean up this comment
	 */
	static function child_monitor_less() {
		if ( file_exists( get_stylesheet_directory() . '/assets/less/child.less' ) ) {
			if ( filemtime( get_stylesheet_directory() . '/assets/less/child.less' ) > filemtime( nerdpress_css() ) ) 
				nerdpress_makecss();
		}		
	}
	
	
	/**
	 * social_share function.
	 * 
	 * shortcode for share buttons
	 * enques .js
	 * enques the template
	 * @TODO Clean up this comment
	 * @access public
	 * @param mixed $atts
	 * @return void
	 */
	static function social_share( $atts ) {
		global $share_url;
				
		extract( 
			shortcode_atts( 
				array(
					'url' => '',
				), $atts
			)
		);
		
		$share_url = $url;
	
		wp_enqueue_script( 'nerdpress-social-share', get_template_directory_uri() . '/assets/js/plugins/nerdpress-social-share.js', array( 'jquery'), null, true );
		
		ob_start();
		get_template_part( 'templates/social-share' );
		$social_share = ob_get_contents();
		ob_end_clean();
		
		return $social_share;
	}
	
	
	/**
	 * pagination function.
	 * 
	 * If you are on a page with multiple pages, echos out page numbers at the bottom
	 * Builds an array here, passes the array to the template to echo it out
	 * templates/pagination/pagination.php
	 * @TODO Clean up this comment
	 * @access public
	 * @param string $pages (default: '')
	 * @param int $range (default: 4)
	 * @return void
	 */
	static function pagination ( $pages = '', $range = 4 ) {
		global $paged, $page_links;
		
		if ( empty( $paged ) ) $paged = 1;
		
		$page_links = array();
		
		$page_links[] = array(
			'link' => ( $paged == 1 ) ? '#' : get_pagenum_link( $paged - 1 ),
			'text' => '<i class="fa fa-angle-double-left"></i>',
			'class' => ( $paged == 1 ) ? 'disabled' : '',
		);
		
		for ( $i = 1; $i < ( $pages + 1 ); $i++ ) {
			$page_links[] = array(
				'link' => get_pagenum_link( $i ),
				'text' => $i,
				'class' => ( $i == $paged ) ? 'active' : '',
			);
		}
		
		$page_links[] = array(
			'link' => ( $paged == $pages ) ? '#' : get_pagenum_link( $paged + 1 ),
			'text' => '<i class="fa fa-angle-double-right"></i>',
			'class' => ( $paged == $pages ) ? 'disabled' : '',
		);
		
		if ( $page_links ) :
			ob_start();
			get_template_part( 'templates/pagination' );
			$pagination = ob_get_contents();
			ob_end_clean();
			
			echo $pagination;
		endif;
	}
	
	
	/**
	 * roots_cleanup function.
	 * 
	 * Roots forces a canonical URL, this removes the roots canonical
	 * @access public
	 * @return void
	 */
	static function roots_cleanup() {
		 remove_action('wp_head', 'roots_rel_canonical');
	}
	
	
	/**
	 * load_canonical function.
	 * 
	 * If a page has a cononical URL, echoes out the URL in the head
	 * @access public
	 * @return void
	 	 	 * @TODO Clean up this comment

	 */
	static function load_canonical() {
		global $post;
		
		if ( get_field( 'nrd_seo_canonical' ) ) 
			echo "\r\n" . '<link rel="canonical" href="' . get_field( 'nrd_seo_canonical' ) . '" />' . "\r\n";
	}
	
	
	/**
	 * plugin_install_warning function.
	 * 
	 * Inserts a warning on the add plugin page to remind clients that plugins can be dangerous
	* @TODO Clean up this comment
	* @TODO Add ability to dismiss this message
	 * @access public
	 * @return void
	 */
	static function plugin_install_warning() {
		get_template_part( 'templates/admin', 'plugin-notice' );
	}

	
	/**
	 * detect_mobile function.
	 * 
	 * Does a User agent sniff, decides if you are on android, ipod, iphone, or ipad
	 * defines if you on a mobile device or not
	 * @TODO Clean up this comment

	 * @access public
	 * @return void
	 */
	static function detect_mobile() {
		$ua = strtolower( $_SERVER['HTTP_USER_AGENT'] );
		
		if ( stripos( $ua, 'android' ) !== false || 
			stripos( $ua, 'iphone' ) !== false || 
			stripos( $ua, 'ipod' ) !== false || 
			stripos( $ua, 'ipad' ) !== false ) {
			define( 'NERDPRESS_IS_MOBILE', true );
			
			add_filter( 'body_class', array( &$this, 'mobile_body_classes' ) );
		}
	}
	
	
	/**
	 * mobile_body_classes function.
	 * 
	 * Adds a class for each platform, allows for platform specific CSS
	 * @TODO Clean up this comment
	 * @access public
	 * @param mixed $classes
	 * @return void
	 */
	static function mobile_body_classes( $classes ) {
		$ua = strtolower( $_SERVER['HTTP_USER_AGENT'] );
		
		if ( stripos( $ua, 'android' ) !== false ) 
			$classes[] = 'android';
			
		if ( stripos( $ua, 'iphone' ) !== false || stripos( $ua, 'ipod' ) !== false || stripos( $ua, 'ipad' ) !== false ) 
			$classes[] = 'ios';
			
		if ( stripos( $ua, 'iphone' ) !== false ) 
			$classes[] = 'iphone';
			
		if ( stripos( $ua, 'ipod' ) !== false ) 
			$classes[] = 'ipod';
			
		if ( stripos( $ua, 'ipad' ) !== false ) 
			$classes[] = 'ipad';		
			
		$classes[] = 'mobile';
		
		return $classes;
	}
	
} // End class

$nerdpress = new NerdPress();
global $nerdpress;
?>