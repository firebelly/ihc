<div class="content-wrap">
  <header class="page-header">
    <div class="container">
      <div class="image-wrap">
        <div class="header-text">
          <h1><?= \Roots\Sage\Titles\title(); ?></h1>
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
      if ($post->post_type=='post'):

        $news_post = $post;
        include(locate_template('templates/article-news.php'));

      elseif (preg_match('/(event)/',$post->post_type)):

        $event_post = $post;
        include(locate_template('templates/article-event.php'));

      elseif (preg_match('/(program)/',$post->post_type)):

        $program_post = $post;
        include(locate_template('templates/article-program.php'));

      elseif (preg_match('/(page)/',$post->post_type)):

        include(locate_template('templates/article-page.php'));

      endif;
      ?>

    <?php endwhile; ?>
    </div>

    <?php the_posts_navigation(); ?>

  <?php endif; ?>

  </main>
</div>
