<?php
/* This is what includes everything else */
/* 	 	 * @TODO Clean up this comment */

/* This isn't really needed any more, we need to clean up nerdpress */

holdover from schoestack 
define( 'themeURI', get_template_directory_uri() );
define( 'themeFOLDER', get_template() );
define( 'themePATH', get_theme_root() );
define( 'themeNAME', wp_get_theme() );
define( 'nerdpress', true );

// Prioritize NerdPress config file since others depend on it
require_once( 'nerdpress-config.php' );

/* ===== Additional Functions ===== */

$files_to_include = array(
	'class-tgm-plugin-activation.php',
	'class.nerdpress.php',
	'functions.admin.php',
	'functions.fields.php',
	'functions.shortcodes.php',
);

foreach ( $files_to_include as $file ) {
	require_once( $file );
}