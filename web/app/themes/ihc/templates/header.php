<?php use Roots\Sage\Nav\NavWalker; ?>

<header class="banner navbar navbar-default navbar-static-top" role="banner">
<?php get_search_form(); ?>
  <div class="container">
  	<h1 class="logo"><a href="/">IL Humanities</a></h1>
    <nav class="site-nav" role="navigation">
      <?php
      if (has_nav_menu('primary_navigation')) :
        wp_nav_menu(['theme_location' => 'primary_navigation', 'walker' => new NavWalker(), 'menu_class' => 'nav navbar-nav']);
      endif;
      ?>
    </nav>
  </div>
</header>
