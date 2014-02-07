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
						'post_type' 			=> 'nerdpress_element',
						'posts_per_page' 	=> -1,
					);
					
					$wq = new WP_Query ( $wr );
					
					if ( $wq->have_posts() ) :
					
						while ( $wq->have_posts() ) : $wq->the_post();
					?>
						<option value="<?= get_field( 'element_type' ); ?>-<?= get_the_ID(); ?>"><? the_title(); ?> (<?= ucfirst( get_field( 'element_type' ) ); ?>) </option>
					<?
						endwhile; wp_reset_query();
					
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
			'post_type' => 'nerdpress_element',
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

// ACF fields for elements
if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_nerdpress-elements',
		'title' => 'NerdPress Elements',
		'fields' => array (
			array (
				'key' => 'field_52854c98304c9',
				'label' => 'Element Type',
				'name' => 'element_type',
				'type' => 'radio',
				'instructions' => 'What kind of element will you be making today?',
				'choices' => array (
					'gallery' => 'Gallery',
					'carousel' => 'Carousel',
					'accordion' => 'Accordion',
					'tabs' => 'Tabs',
				),
				'other_choice' => 0,
				'save_other_choice' => 0,
				'default_value' => '',
				'layout' => 'horizontal',
			),
			array (
				'key' => 'field_52854e4f15a7c',
				'label' => 'Build Your Gallery',
				'name' => '',
				'type' => 'tab',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_52854c98304c9',
							'operator' => '==',
							'value' => 'gallery',
						),
					),
					'allorany' => 'all',
				),
			),
			array (
				'key' => 'field_52854e6d15a7d',
				'label' => 'Columns',
				'name' => 'gal_columns',
				'type' => 'radio',
				'choices' => array (
					2 => 2,
					3 => 3,
					4 => 4,
					6 => 6,
				),
				'other_choice' => 0,
				'save_other_choice' => 0,
				'default_value' => '',
				'layout' => 'horizontal',
			),
			array (
				'key' => 'field_52854e9815a7e',
				'label' => 'Images',
				'name' => 'gal_images',
				'type' => 'gallery',
				'preview_size' => 'thumbnail',
				'library' => 'all',
			),
			array (
				'key' => 'field_52854ea915a7f',
				'label' => 'Build Your Carousel',
				'name' => '',
				'type' => 'tab',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_52854c98304c9',
							'operator' => '==',
							'value' => 'carousel',
						),
					),
					'allorany' => 'all',
				),
			),
			array (
				'key' => 'field_52854db462645',
				'label' => 'Carousel',
				'name' => 'carousel',
				'type' => 'repeater',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_52854c98304c9',
							'operator' => '==',
							'value' => 'carousel',
						),
					),
					'allorany' => 'all',
				),
				'sub_fields' => array (
					array (
						'key' => 'field_52854dd062646',
						'label' => 'Image',
						'name' => 'slide_image',
						'type' => 'image',
						'column_width' => '',
						'save_format' => 'url',
						'preview_size' => 'thumbnail',
						'library' => 'all',
					),
					array (
						'key' => 'field_52854dfc62647',
						'label' => 'Caption',
						'name' => 'slide_caption',
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
						'key' => 'field_52854e0662648',
						'label' => 'Link',
						'name' => 'slide_link',
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
				'row_min' => 1,
				'row_limit' => '',
				'layout' => 'table',
				'button_label' => '+ Add Slide',
			),
			array (
				'key' => 'field_52854f1ba9804',
				'label' => 'Build Your Accordion',
				'name' => '',
				'type' => 'tab',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_52854c98304c9',
							'operator' => '==',
							'value' => 'accordion',
						),
					),
					'allorany' => 'all',
				),
			),
			array (
				'key' => 'field_52854f39a9805',
				'label' => 'Accordion',
				'name' => 'accordion',
				'type' => 'repeater',
				'sub_fields' => array (
					array (
						'key' => 'field_52854f46a9806',
						'label' => 'Title',
						'name' => 'acc_title',
						'type' => 'text',
						'required' => 1,
						'column_width' => '',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'none',
						'maxlength' => '',
					),
					array (
						'key' => 'field_52854f53a9807',
						'label' => 'Content',
						'name' => 'acc_content',
						'type' => 'wysiwyg',
						'column_width' => '',
						'default_value' => '',
						'toolbar' => 'full',
						'media_upload' => 'yes',
					),
				),
				'row_min' => 1,
				'row_limit' => '',
				'layout' => 'table',
				'button_label' => '+ Add Accordion Section',
			),
			array (
				'key' => 'field_52854fa2ab49d',
				'label' => 'Build Your Tabs',
				'name' => '',
				'type' => 'tab',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_52854c98304c9',
							'operator' => '==',
							'value' => 'tabs',
						),
					),
					'allorany' => 'all',
				),
			),
			array (
				'key' => 'field_52854fb5ab49e',
				'label' => 'Tabs',
				'name' => 'tabs',
				'type' => 'repeater',
				'sub_fields' => array (
					array (
						'key' => 'field_52854fb5ab49f',
						'label' => 'Title',
						'name' => 'tab_title',
						'type' => 'text',
						'required' => 1,
						'column_width' => '',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'none',
						'maxlength' => '',
					),
					array (
						'key' => 'field_52854fb5ab4a0',
						'label' => 'Content',
						'name' => 'tab_content',
						'type' => 'wysiwyg',
						'column_width' => '',
						'default_value' => '',
						'toolbar' => 'full',
						'media_upload' => 'yes',
					),
				),
				'row_min' => 1,
				'row_limit' => '',
				'layout' => 'table',
				'button_label' => '+ Add Tab',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'nerdpress_element',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'acf_after_title',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}
?>