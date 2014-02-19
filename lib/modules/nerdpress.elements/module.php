<?php
// Register Custom Post Type
function nerdpress_element_setup() {

	$labels = array(
		'name'                => _x( 'Elements', 'Post Type General Name', 'nerdpress' ),
		'singular_name'       => _x( 'Element', 'Post Type Singular Name', 'nerdpress' ),
		'menu_name'           => __( 'Elements', 'nerdpress' ),
		'parent_item_colon'   => __( 'Parent Element:', 'nerdpress' ),
		'all_items'           => __( 'All Elements', 'nerdpress' ),
		'view_item'           => __( 'View Element', 'nerdpress' ),
		'add_new_item'        => __( 'Add New Element', 'nerdpress' ),
		'add_new'             => __( 'New Element', 'nerdpress' ),
		'edit_item'           => __( 'Edit Element', 'nerdpress' ),
		'update_item'         => __( 'Update Element', 'nerdpress' ),
		'search_items'        => __( 'Search elements', 'nerdpress' ),
		'not_found'           => __( 'No elements found', 'nerdpress' ),
		'not_found_in_trash'  => __( 'No elements found in Trash', 'nerdpress' ),
	);
	$rewrite = array(
		'slug'                => 'element',
		'with_front'          => true,
		'pages'               => true,
		'feeds'               => false,
	);
	$args = array(
		'label'               => __( 'nrd_element', 'nerdpress' ),
		'description'         => __( 'NerdPress Elements', 'nerdpress' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'author', 'thumbnail', ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'show_in_admin_bar'   => true,
		'menu_position'       => 20,
		'menu_icon'           => 'dashicons-art',
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'rewrite'             => $rewrite,
		'capability_type'     => 'page',
	);
	register_post_type( 'nrd_element', $args );

}

// Hook into the 'init' action
add_action( 'init', 'nerdpress_element_setup', 0 );

class NerdPressElementWidget extends WP_Widget {
	function NerdPressElementWidget() {
		$widget_ops = array('classname' => 'nerdpress-element', 'description' => 'Displays a NerdPress element' );
		$this->WP_Widget('NerdPressElementWidget', 'NerdPress Element', $widget_ops);
	}
	
	function form($instance) {
		global $post;
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = $instance['title'];
		$element = $instance['element'];
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title: 
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('element'); ?>">Element: 
				<select class="widefat" id="<?php echo $this->get_field_id('element'); ?>" name="<?php echo $this->get_field_name('element'); ?>">
					<?
					$wr = array(
						'post_type' 			=> 'nrd_element',
						'posts_per_page' 	=> -1,
					);
					
					$elements = get_posts( $wr );
					
					if ( $elements ) :
						foreach ( $elements as $element ) :
					?>
						<option value="<?= get_field( 'element_type', $element->ID ); ?>-<?= $element->ID; ?>"><? echo $element->post_title; ?> (<?= ucfirst( get_field( 'element_type', $element->ID ) ); ?>) </option>
					<?
						endforeach;
					endif;
					?>
				</select>
			</label>
		</p>
		<?php
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		return $instance;
	}
	
	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		
		echo $before_widget;
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		$element = empty($instance['element']) ? ' ' : apply_filters('widget_title', $instance['element']);
		
		$elements = explode( '-', $element );
		
		$element_type = $elements[0];
		$element_id = $elements[1];
		
		if ( !empty($title) )
		echo $before_title . $title . $after_title;
		
		// WIDGET CODE GOES HERE
		$r = array(
			'post_type' => 'nrd_element',
			'p' 				=> $element_id,
		);
		
		$q = new WP_Query( $r );
		
		if ( $q->have_posts() ) :
		
			while ( $q->have_posts() ) : $q->the_post();

				get_template_part( 'templates/edit', 'link' );
				get_template_part( 'lib/modules/nerdpress.elements/template-' . $element_type );
				
			endwhile; wp_reset_query();
		
		endif;
		
		echo $after_widget;
	}
	
}

add_action( 'widgets_init', create_function('', 'return register_widget("NerdPressElementWidget");') );

// Columns
add_action('manage_nerdpress_element_posts_columns', 'nerdpress_element_column_headers', 10);

add_action('manage_nerdpress_element_posts_custom_column', 'nerdpress_element_column_values', 10, 2);

function nerdpress_element_column_headers($defaults) {
	$defaults['element_type'] = __( 'Type' );
    return $defaults;
}

function nerdpress_element_column_values($column_name, $post_id){
    switch( $column_name ) {
    	case 'element_type' :
    		echo ucfirst( get_field( 'element_type', get_the_ID() ) );
    		break;
            
        default: 
    }
}

require_once( dirname( __FILE__ ) . '/load-fields.php' );
?>