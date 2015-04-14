<?php use Roots\Sage\Nav\NavWalker; ?>

<!--[if lt IE 10]>
<div class="alert alert-warning"><?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'sage'); ?></div>
<![endif]-->

<header class="banner navbar navbar-default navbar-static-top" role="banner">
<?php get_search_form(); ?>
  <div class="container">
    <nav class="collapse navbar-collapse" role="navigation">
      <?php
      if (has_nav_menu('primary_navigation')) :
        wp_nav_menu(['theme_location' => 'primary_navigation', 'walker' => new NavWalker(), 'menu_class' => 'nav navbar-nav']);
      endif;
      ?>
    </nav>
  </div>
</header>
