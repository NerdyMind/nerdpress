<?php
// Create the shortcodes and format for WP

/**
 * nerdpress_register_shortcode function.
 * 
 * @access public
 * @param mixed $shortcode
 * @return void
 * there are shortcodes for 
 * icons
 * buttons
 * labels
 * alerts
 * panels
 * a well
 * a modal
	* @TODO Clean up this comment
	* @TODO document this page!
	* bottom function registers it
	* bottom area makes sure short codes can be ran in in widgets
 */
function nerdpress_register_shortcode( $shortcode ) {
	// vars
	$function = "";
	
	// extract atts
	if( $shortcode['atts'] )
	{
		$function .= 'extract( shortcode_atts( array( ';
		
		foreach( $shortcode['atts'] as $att )
		{
			$function .= '"' . $att['name'] . '" => "' . $att['default'] . '", ';
		}
		
		$function .= '), $atts ) ); ';
	}	
	
	// add the php body
	$html = $shortcode['return'];
	$html = str_replace( '"', '\"', $html );
	
	// add the return
	$function .= ' return "' . $html . '";';	

	// create as a static function
	$function_name = create_function( '$atts, $content', $function );	
	
	// register the function as a shortcode
	add_shortcode( $shortcode['name'], $function_name );
}

$shortcodes = array();

// Add your shortcodes here!

$shortcodes[] = array(
	'name' 			=> 'icon',
	'atts' 				=> array(
		array(
			'name' 	=> 'icon',
			'default' 	=> '',
		),
		array(
			'name' 	=> 'class',
			'default' 	=> '',
		)
	),
	'return' 			=> '<i class="fa $icon $class"></i>',
);

$shortcodes[] = array(
	'name' 			=> 'button',
	'atts' 				=> array(
		array(
			'name' 	=> 'link',
			'default' 	=> '',
		),
		array(
			'name' 	=> 'class',
			'default' 	=> 'btn-primary',
		),
		array(
			'name' 	=> 'text',
			'default' 	=> 'Learn More',
		),
	),
	'return' 			=> '<a href="$link" class="btn $class">$text</a>',
);

$shortcodes[] = array(
	'name' 			=> 'label',
	'atts' 				=> array(
		array(
			'name' 	=> 'class',
			'default' 	=> 'label-primary',
		),
		array(
			'name' 	=> 'text',
			'default' 	=> '',
		),
	),
	'return' 			=> '<span class="label $class">$text</span>',
);

$shortcodes[] = array(
	'name' 			=> 'alert',
	'atts' 				=> array(
		array(
			'name' 	=> 'class',
			'default' 	=> 'alert-danger',
		),
		array(
			'name' 	=> 'content',
			'default' 	=> '',
		),
	),
	'return' 			=> '
	<p>
	<div class="alert $class alert-dismissable">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		$content
	</div>
	</p>',
);

$shortcodes[] = array(
	'name' 			=> 'panel',
	'atts' 				=> array(
		array(
			'name' 	=> 'class',
			'default' 	=> 'panel-default',
		),
		array(
			'name' 	=> 'heading',
			'default' 	=> '',
		),
		array(
			'name' 	=> 'content',
			'default' 	=> '',
		),
	),
	'return' 			=> '
	<div class="panel $class">
		<div class="panel-heading">
			<h3 class="panel-title">$heading</h3>
		</div>
		<div class="panel-body">
			<p>
				$content
			</p>
		</div>
	</div>',
);

$shortcodes[] = array(
	'name' 			=> 'well',
	'atts' 				=> array(
		array(
			'name' 	=> 'class',
			'default' 	=> '',
		),
		array(
			'name' 	=> 'content',
			'default' 	=> '',
		),
	),
	'return' 			=> '
	<div class="well $class">
		$content
	</div>',
);

$shortcodes[] = array(
	'name' 			=> 'modal',
	'atts' 				=> array(
		array(
			'name' 	=> 'id',
			'default' 	=> '',
		),
		array(
			'name' 	=> 'heading',
			'default' 	=> '',
		),
		array(
			'name' 	=> 'content',
			'default' => '',
		),
	),
	'return' 			=> '
	<!-- Modal -->
	<div class="modal fade" id="mod-$id" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title">$heading</h4>
			</div>
			<div class="modal-body">
				$content
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->',
);

// Do not edit below this line...


foreach ( $shortcodes as $shortcode ) :
	nerdpress_register_shortcode( $shortcode );
endforeach;

// Allow shortcode execution in widgets
add_filter( 'widget_text', 'shortcode_unautop' );
add_filter( 'widget_text', 'do_shortcode' );
?>