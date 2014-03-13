<?php
class NerdPress {

	function __construct() {
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
		add_action( 'init', array( &$this, 'load_client_role' ) );
		
		add_shortcode( 'nerdpress_sitemap', array( &$this, 'sitemap' ) );
		add_shortcode( 'nerdpress_social_networks', array( &$this, 'social_networks' ) );
		add_shortcode( 'nerdpress_social_share', array( &$this, 'social_share' ) );
		
		if ( in_array( 'gravityforms/gravityforms.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && !get_option( 'rg_gforms_key' ) ) 
			add_action( 'init', array( &$this, 'setup_gravity_forms' ) );
		
		if ( in_array( 'nerdpress-panels/siteorigin-panels.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && !get_option( 'rg_gforms_key' ) ) 
			add_action( 'init', array( &$this, 'setup_nerdpress_panels' ) );
			
		if ( is_child_theme() ) :
			add_filter( 'nerdpress_compiler', array( &$this, 'child_load_less' ) );
			add_action( 'after_setup_theme', array( &$this, 'child_monitor_less' ) );
		endif;
	}

	function init_filesystem() {
		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH .'/wp-admin/includes/file.php' );
			WP_Filesystem();
		}
	}
	
	function variable( $var ) {
		if ( !function_exists( 'get_field' ) ) return false;
		
		if ( get_field( $var, 'option' ) ) 
			return get_field( $var, 'option' );
		else 
			return false;
	}

	function register_widget_areas() {
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
	
	function widget_area( $widget_area_id ) {
		global $wp_registered_sidebars;
		
		$safe_name = strtolower( str_replace( ' ', '-', $widget_area_id ) );
		
		if ( array_key_exists( $safe_name, $wp_registered_sidebars ) ) :
		
			if ( is_dynamic_sidebar( $safe_name ) ) :
				echo "\n" . '<div class="' . $all_widget_areas[$safe_name]['class'] . '">' . "\n";
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
	
	function addl_integrations() {
		$addl_integrations = self::variable( 'addl_integrations' );
		
		if ( $addl_integrations ) :		
			foreach ( $addl_integrations as $integration ) {
				require_once( themePATH . '/' . themeFOLDER . '/lib/modules/nerdpress.core/integrations/' . $integration . '.php' );
			}		
		endif;
	}

	function container_class() {
		global $post;
			
		if ( get_field( 'nrd_full_width' ) ) return 'full-width';
		else return 'container';
	}

	function navbar_class( $navbar = 'main' ) {
	  $fixed    = variable( 'navbar_fixed' );
	  $fixedpos = variable( 'navbar_fixed_position' );
	
	  if ( $fixed != 1 )
	    $class = 'navbar navbar-static-top';
	  else
	    $class = ( $fixedpos == 1 ) ? 'navbar navbar-fixed-bottom' : 'navbar navbar-fixed-top';
	
	  if ( $navbar != 'secondary' )
	    return $class;
	  else
	    return 'navbar';
	}

	function display_sidebar() {
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

	function hide_sidebar_on( $sidebar ) {
		if ( get_field( 'nrd_hide_sidebar' ) ) return false;
		
		return $sidebar;
	}

	function main_class() {
		if ( self::display_sidebar() ) $class = self::variable( 'main_class' );
		else $class = 'col-sm-12';
		
		return $class;
	}

	function sidebar_class() {
		return self::variable( 'sidebar_class' );
	}
	
	function load_scripts() {
	
		wp_deregister_script( 'roots_scripts' );
		
		wp_register_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css' );
		wp_enqueue_style( 'font-awesome' );
		
		wp_enqueue_script( 'bootstrap', '//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js', array( 'jquery' ), NULL, true );
		
		wp_enqueue_script( 'main', get_stylesheet_directory_uri() . '/assets/js/main.js', array( 'jquery' ), NULL, true );
		
		wp_enqueue_script( 'placeholder', get_template_directory_uri() . '/assets/js/vendor/jquery.placeholder.js', array( 'jquery'), NULL, true );		
		wp_enqueue_script( 'retina', get_template_directory_uri() . '/assets/js/vendor/retina.js', NULL, NULL, true );
		
		if ( self::variable( 'analytics_id' ) ) 
			wp_enqueue_script( 'analytics', get_template_directory_uri() . '/assets/js/analytics.php', array( 'jquery' ), NULL, NULL );
		
		$load_scripts = self::variable( 'load_scripts' );
		$script_header = self::variable( 'script_header' );
		$script_footer = self::variable( 'script_footer' );
		
		if ( $load_scripts ) :
		
			if ( in_array( 'animatecss', $load_scripts ) ) :
				wp_register_style( 'animate-css', get_template_directory_uri() . '/assets/css/animate.min.css' );
				wp_enqueue_style( 'animate-css' );
			endif;
			
			if ( in_array( 'flexslider', $load_scripts ) ) 
				wp_enqueue_script( 'flexslider', get_template_directory_uri() . '/assets/js/vendor/jquery.flexslider-min.js', array( 'jquery'), '2.2.0', true );
				
			if ( in_array( 'lightbox', $load_scripts ) ) 
				wp_enqueue_script( 'lightbox', get_template_directory_uri() . '/assets/js/vendor/ekko-lightbox.js', array( 'jquery'), NULL, true );
				
			if ( in_array( 'vimeo_api', $load_scripts ) ) 
				wp_enqueue_script( 'froogaloop', '//a.vimeocdn.com/js/froogaloop2.min.js', NULL, NULL, true );
				
			if ( in_array( 'bootstrap_hover', $load_scripts ) ) 
				wp_enqueue_script( 'bootstrap-hover', get_template_directory_uri() . '/assets/js/vendor/bootstrap-hover-dropdown.js', array( 'jquery' ), NULL, true );
			
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
	}
	
	function make_crumb( $url = false, $text ) {
		global $breadcrumbs;
		
		$breadcrumbs[] = array(
			'url' => $url,
			'text' => $text,
		);
		
		return $breadcrumbs;
	}
	
	function breadcrumbs() {
		if ( !self::variable( 'breadcrumbs' ) ) return;
		
		global $breadcrumbs, $post, $wp_query;
		
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
		
		// WooCommerce check
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
			$woo_active = true;
			
		// bbPress check
		if ( class_exists( 'bbPress' ) ) 
			$bbpress_active = true;
	
		$breadcrumbs = array();
		
		// Home URL
		self::make_crumb( home_url(), '<i class="fa fa-home fa-lg"></i>' );
		
		// Page -- Parents murdered in an alley
		if ( is_page() && !$post->post_parent ) 
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
				self::make_crumb( $data['url'], $data['text'] );
			}
			
			// Finally, make crumb for current page
			self::make_crumb( null, get_the_title() );
			
		endif; // Page with parents
		
		// Author archive
		if ( is_author() ) :
			global $author;
		
			$the_author = get_userdata( $author );
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
		if ( is_single() && !in_array( get_post_type(), $skip_post_types ) ) :
		
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
					
			// get the taxonomy names of this object
			$taxonomy_names = get_object_taxonomies( $post_type->name );
			
			// Detect any hierarchical taxonomies that might exist on this post type
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
			
			self::make_crumb( null, get_the_title() );
			
		endif; // Single post
		
		// Post type archive
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
		if ( $bbpress_active && 
			( bbp_is_topic_archive() || 
				bbp_is_search() || 
				bbp_is_forum_archive() || 
				bbp_is_single_view() || 
				bbp_is_single_forum() || 
				bbp_is_single_topic() || 
				bbp_is_single_reply() || 
				bbp_is_topic_tag() || 
				bbp_is_user_home() ) ) :
			
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
		if ( $paged ) :
			self::make_crumb( null, 'Page ' . $paged );
		endif; // Paged
		
		if ( !is_front_page() && !is_home() ) get_template_part( 'templates/breadcrumbs' );
	} // breadcrumbs
	
	function sitemap() {
		ob_start();
		get_template_part( 'templates/sitemap' );
		$sitemap = ob_get_contents();
		ob_end_clean();
		
		return $sitemap;
	}
	
	function login_logo() {
		if ( !locate_template('assets/img/site-logo.png') ) return;
		get_template_part( 'templates/login', 'logo' );
	}
	
	function login_url() {
		return get_bloginfo( 'url' );
	}
	
	function login_title() {
		return get_bloginfo( 'name' );
	}
	
	function limit_revisions( $num, $post ) {
		return 2;
	}
	
	function social_networks() {
		ob_start();
		get_template_part( 'templates/social', 'networks' );
		$social_networks = ob_get_contents();
		ob_end_clean();
		
		return $social_networks;
	}
	
	function seo_title( $title, $sep ) {
		global $post;
		
		$seo_title = get_field( 'nrd_seo_title' );
		
		if ( $seo_title ) $title = $seo_title . ' ' . $sep . ' ';
		
		return $title;
	}
	
	function page_title() {
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
	
	function seo_description() {
		global $post;
		
		$seo_desc = get_field( 'nrd_seo_desc' );
		
		if ( !$seo_desc ) return;
		
		echo '<meta name="description" content="' . htmlspecialchars_decode( $seo_desc, ENT_QUOTES ) . '"/>';	
	}
	
	function register_required_plugins() {
	
		if ( false === ( $plugins_list = get_transient( 'np_plugins_list' ) ) ) :		
			$plugins_list = wp_remote_get( 'http://repo.nerdymind.com/nerdpress-helpers/plugin-list.php' );
			set_transient( 'np_plugins_list', $plugins_list['body'], 86400 );
		endif;
		
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
	
	function setup_post_types() {
		
		// Check to make sure our class is here before we waste our time!
		if ( !class_exists( 'Super_Custom_Post_Type' ) ) 
			return;
		
		$post_types = self::variable( 'post_types' );
		$taxonomies = self::variable( 'taxonomies' );
		
		//print_r( $post_types );
		
		if ( $post_types ) :
		
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
	
	function bootstrap_reply_link_class( $class ) {
		$class = str_replace( "class='comment-reply-link", "class='comment-reply-link btn btn-primary btn-small", $class );
		return $class;
	}
	
	function bootstrap_password_form() {
		global $post;
		
		$label = 'pwbox-' . ( empty( $post->ID ) ? rand() : $post->ID );
		$content  = '<form action="';
		$content .= esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) );
		$content .= '" method="post">';
		$content .= __( 'This post is password protected. To view it please enter your password below:', 'nerdpress' );
		$content .= '<div class="input-group">';
		$content .= '<input name="post_password" id="' . $label . '" type="password" size="20" />';
		$content .= '<span class="input-group-btn">';
		$content .= '<input type="submit" name="Submit" value="' . esc_attr__( "Submit" ) . '" class="btn btn-default" />';
		$content .= '</span></div></form>';
		
		return $content;
	}
	
	function statcounter() {
		$statcounter_id = self::variable( 'statcounter_id' );
		
		if ( !$statcounter_id ) return;
		
		get_template_part( 'templates/statcounter' );
	}
	
	function load_menu_locations() {
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
	
	function load_client_role() {
		$caps = array(
			'activate_plugins' => true,
			'install_plugins' => true,
			'create_users' => true,
			'delete_users' => true,
			'edit_users' => true,
			'export' => true,
			'import' => true,
			'list_users' => true,
			'remove_users' => true,
			'promote_users' => true,
			'switch_themes' => true,
			'update_core' => true,
			'update_plugins' => true,
			'update_themes' => true,
			'edit_dashboard' => true,
			'moderate_comments' => true,
			'manage_categories' => true,
			'manage_links' => true,
			'edit_others_posts' => true,
			'edit_pages' => true,
			'edit_others_pages' => true,
			'edit_published_pages' => true,
			'publish_pages' => true,
			'delete_pages' => true,
			'delete_others_pages' => true,
			'delete_published_pages' => true,
			'delete_others_posts' => true,
			'delete_private_posts' => true,
			'edit_private_posts' => true,
			'read_private_posts' => true,
			'delete_private_posts' => true,
			'edit_private_pages' => true,
			'read_private_pages' => true,
			'edit_published_posts' => true,
			'upload_files' => true,
			'publish_posts' => true,
			'delete_published_posts' => true,
			'edit_posts' => true,
			'delete_posts' => true,
			'read' => true,
			'edit_theme_options' => true,
		);
		
		add_role(
			'np_client',
			__( 'Client' ),
			$caps
		);
		
		$client = get_role( 'np_client' );
		
		foreach ( $caps as $cap ) {
			if ( !$client->has_cap ( $cap ) ) $client->add_cap( $cap );
		}
		
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) :
		
			$woo_caps = array(
				'manage_woocommerce',
				'manage_woocommerce_orders',
				'manage_woocommerce_coupons',
				'manage_woocommerce_products',
				'view_woocommerce_reports',
				'edit_product',
				'read_product',
				'delete_product',
				'edit_products',
				'edit_others_products',
				'publish_products',
				'read_private_products',
				'delete_products',
				'delete_private_products',
				'delete_published_products',
				'delete_others_products',
				'edit_private_products',
				'edit_published_products',				
				'edit_shop_order',
				'read_shop_order',
				'delete_shop_order',
				'edit_shop_orders',
				'edit_others_shop_orders',
				'publish_shop_orders',
				'read_private_shop_orders',
				'delete_shop_orders',
				'delete_private_shop_orders',
				'delete_published_shop_orders',
				'delete_others_shop_orders',
				'edit_private_shop_orders',
				'edit_published_shop_orders',				
				'edit_shop_coupon',
				'read_shop_coupon',
				'delete_shop_coupon',
				'edit_shop_coupons',
				'edit_others_shop_coupons',
				'publish_shop_coupons',
				'read_private_shop_coupons',
				'delete_shop_coupons',
				'delete_private_shop_coupons',
				'delete_published_shop_coupons',
				'delete_others_shop_coupons',
				'edit_private_shop_coupons',
				'edit_published_shop_coupons',
				'manage_product_terms',
				'edit_product_terms',
				'delete_product_terms',
				'assign_product_terms',
				'manage_shop_order_terms',
				'edit_shop_order_terms',
				'delete_shop_order_terms',
				'assign_shop_order_terms',
				'manage_shop_coupon_terms',
				'edit_shop_coupon_terms',
				'delete_shop_coupon_terms',
				'assign_shop_coupon_terms',
			);
			
			foreach ( $woo_caps as $cap ) {
				if ( !$client->has_cap( $cap ) ) $client->add_cap( $cap );
			}
		
		endif;
		
		if ( class_exists( 'bbPress' ) ) :
		
			$bbp_caps = array(
				'publish_forums',
				'edit_forums',
				'edit_others_forums',
				'delete_forums',
				'delete_others_forums',
				'read_private_forums',
				'read_hidden_forums',
				'publish_topics',
				'edit_topics',
				'edit_others_topics',
				'delete_topics',
				'delete_others_topics',
				'read_private_topics',
				'publish_replies',
				'edit_replies',
				'edit_others_replies',
				'delete_replies',
				'delete_others_replies',
				'read_private_replies',
				'manage_topic_tags',
				'edit_topic_tags',
				'delete_topic_tags',
				'assign_topic_tags',
				'spectate',
				'participate',
				'moderate',
				'throttle',
				'view_trash',
			);
			
			foreach ( $bbp_caps as $cap ) {
				if ( !$client->has_cap( $cap ) ) $client->add_cap( $cap );
			}
		
		endif;	
	}
	
	function setup_gravity_forms() {
		update_option( 'rg_gforms_key', '55443c0c81be6d6cef07480d077ff677' );
		update_option( 'rg_gforms_disable_css', '1' );
		update_option( 'rg_gforms_enable_html5', '1' );
	}
	
	function setup_nerdpress_panels() {
		$value = 'a:8:{s:10:"animations";b:1;s:15:"bundled-widgets";b:1;s:10:"responsive";b:1;s:12:"mobile-width";i:780;s:12:"margin-sides";i:30;s:13:"margin-bottom";i:30;s:12:"copy-content";b:0;s:10:"inline-css";b:0;}';
		
		if ( get_option( 'nerdpress_panels_display' ) != $value ) 
			update_option( 'nerdpress_panels_display', $value );
	}
	
	function child_load_less( $bootstrap ) {
		return $bootstrap . '
		@import "' . get_stylesheet_directory() . '/assets/less/child.less";';
	}

	function child_monitor_less() {
		if ( filemtime( get_stylesheet_directory() . '/assets/less/child.less' ) > filemtime( nerdpress_css() ) ) 
			nerdpress_makecss();
	}
	
	function social_share( $atts ) {
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
	
	function pagination ( $pages = '', $range = 4 ) {
		global $paged, $page_links;
		
		if ( empty( $paged ) ) $paged = 1;
		
		$page_links = array();
		
		$page_links[] = array(
			'link' => ( $paged == 1 ) ? '#' : get_pagenum_link( $paged - 1 ),
			'text' => '<i class="fa fa-angle-double-left"></i>',
			'class' => ( $paged == 1 ) ? 'disabled' : '',
		);
		
		for ( $i = 1; $i < ( $pages + 1); $i++ ) {
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
	
} // End class

$nerdpress = new NerdPress();
global $nerdpress;
?>