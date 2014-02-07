<?php

// Prioritize loading of some necessary core modules
require_once get_template_directory() . '/lib/modules/nerdpress.core/module.php';

if ( !function_exists( 'nerdpress_include_modules' ) ) :
/*
 * Use 'RecursiveDirectoryIterator' if PHP Version >= 5.2.11
 */
function nerdpress_include_modules() {
  // Include all modules from the nerdpress theme (NOT the child themes)
  $modules_path = new RecursiveDirectoryIterator( get_template_directory() . '/lib/modules/' );
  $recIterator  = new RecursiveIteratorIterator( $modules_path );
  $regex        = new RegexIterator( $recIterator, '/\/module.php$/i' );

  foreach( $regex as $item ) {
    require_once $item->getPathname();
  }
}
endif;


if ( !function_exists( 'nerdpress_include_modules_fallback' ) ) :
/*
 * Fallback to 'glob' if PHP Version < 5.2.11
 */
function nerdpress_include_modules_fallback() {
  // Include all modules from the nerdpress theme (NOT the child themes)
  foreach( glob( get_template_directory() . '/lib/modules/*/module.php' ) as $module ) {
    require_once $module;
  }
}
endif;


// PHP version control
$phpversion = phpversion();
if ( version_compare( $phpversion, '5.2.11', '<' ) ) :
  nerdpress_include_modules();
else :
  nerdpress_include_modules_fallback();
endif;


if ( !function_exists( 'nerdpress_theme_active' ) ) :
/*
 * The following function adds a 'nerdpress_activated' option in the database
 * and sets it to true, AFTER all the modules have been loaded above.
 * This helps skip the compiler on activation.
 */
function nerdpress_theme_active() {
  if ( get_option( 'nerdpress_activated' ) != true )
    add_option( 'nerdpress_activated', true );
}
endif;
add_action( 'after_setup_theme', 'nerdpress_theme_active' );
