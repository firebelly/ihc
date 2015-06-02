<?php use Roots\Sage\Titles; ?>

<div class="content-wrap">
  <header class="page-header">
    <div class="container">
      <div class="image-wrap">
        <div class="header-text">
          <h1><?= Titles\title(); ?></h1>
        </div>
      </div>
    </div>
  </header>

  <main>
  <?php if (!have_posts()) : ?>

    <div class="alert alert-warning">
      <?php _e('Sorry, no results were found.', 'sage'); ?>
    </div>

  <?php else: ?>

    <div class="masonry article-list">
    <?php while (have_posts()) : the_post(); ?>

      <?php 
      $show_images = is_search();

      if ($post->post_type=='post'):

        $news_post = $post;
        include(locate_template('templates/article-news.php'));

      elseif (preg_match('/(event)/',$post->post_type)):

        $event_post = $post;
        include(locate_template('templates/article-event.php'));

      elseif (preg_match('/(program|page)/',$post->post_type)):

        include(locate_template('templates/article-search.php'));

      endif;
      ?>

    <?php endwhile; ?>
    </div>

    <?php the_posts_navigation(); ?>

  <?php endif; ?>

  </main>
</div>
