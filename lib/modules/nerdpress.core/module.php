<?php
define( 'themeURI', get_template_directory_uri() );
define( 'themeFOLDER', get_template() );
define( 'themePATH', get_theme_root() );
define( 'themeNAME', wp_get_theme() );
define( 'nerdpress', true );

// Prioritize NerdPress config file since others depend on it
require_once( 'config.php' );

/* ===== Additional Functions ===== */

$files_to_include = array(
	'class-tgm-plugin-activation.php',
	'class.nerdpress.php',
	'functions.admin.php',
	'functions.fields.php',
	'functions.shortcodes.php',
	'nerdpress-config.php',
);

foreach ( $files_to_include as $file ) {
	require_once( $file );
}