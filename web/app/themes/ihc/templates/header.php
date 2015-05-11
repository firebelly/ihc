<?php use Roots\Sage\Nav\NavWalker; ?>

<header class="banner" role="banner">
  <?php get_search_form(); ?>
  <h1 class="logo"><a href="/">IL Humanities</a></h1>
  <nav class="site-nav" role="navigation">
    <?php
    if (has_nav_menu('primary_navigation')) :
      wp_nav_menu(['theme_location' => 'primary_navigation', 'walker' => new NavWalker()]);
    endif;
    ?>
  </nav>
</header>
