<?php 
/**
 * This is base.php changes to this file should be largely un-nescisary, as it acts as a theme wrapper for everything else.
 * For an in-depth explanation of base.php and Roots Theme Wrappers check out:
 * http://roots.io/an-introduction-to-the-roots-theme-wrapper/
 */
get_template_part('templates/head'); ?>
<body <?php body_class(); ?>>

  <!--[if lt IE 8]>
    <div class="alert alert-warning">
      <?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'roots'); ?>
    </div>
  <![endif]-->

  <?php
    do_action('get_header');
    
    do_action( 'nerdpress_pre_navbar' );
    
    if ( !has_action( 'nerdpress_header_top_navbar_override' ) ) get_template_part('templates/header-top-navbar');
    else do_action( 'nerdpress_header_top_navbar_override' );
    
    do_action( 'nerdpress_post_navbar' );
  ?>

  <div class="wrap <?php echo NerdPress::container_class(); ?>" role="document">
    <div class="content row">
      <main class="main <?php echo NerdPress::main_class(); ?>" role="main">
      	<?php NerdPress::breadcrumbs(); ?>
      	
      	<?php get_template_part( 'templates/edit', 'link' ); ?>
      	
        <?php include roots_template_path(); ?>
      </main><!-- /.main -->
      <?php if ( NerdPress::display_sidebar() ) : ?>
        <aside class="sidebar <?php echo NerdPress::sidebar_class(); ?>" role="complementary">
          <?php include roots_sidebar_path(); ?>
        </aside><!-- /.sidebar -->
      <?php endif; ?>
    </div><!-- /.content -->
  </div><!-- /.wrap -->

  <?php get_template_part('templates/footer'); ?>

</body>
</html>