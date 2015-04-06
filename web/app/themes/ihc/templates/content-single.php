<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>
    <?php $category = \Firebelly\Utils\get_first_term($post); ?>
    <h3 class="category-name"><a href="<?= get_term_link($category); ?>"><?php echo $category->cat_name; ?></a></h3>
    <time class="article-date" datetime="<?php echo date('c', strtotime($post->post_date)); ?>"><?php echo date('j/n', strtotime($post->post_date)); ?></time>
    <?php if (has_post_thumbnail()) {
      $thumb = wp_get_attachment_url(get_post_thumbnail_id());
      echo '<div class="article-thumb" style="background-image:url('.$thumb.');"><img class="hide" src="'.$thumb.'"></div>';
    } ?>
    <div class="post-inner">
      <header>
        <h1 class="entry-title"><?php the_title(); ?></h1>
      </header>
      <div class="entry-content user-content">
        <?php the_content(); ?>
      </div>
      <footer>
        <?= \Firebelly\Utils\get_focus_area_and_tags($post); ?>

        <div class="share">
          <span class="st_facebook_custom">facebook</span>
          <span class="st_twitter_custom">twitter</span>
          <span class="st_email_custom" >email</span>
        </div>

      </footer>
      <?php comments_template('/templates/comments.php'); ?>
    </div>
  </article>
<?php endwhile; ?>
