<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>
    <?php if ($category = \Firebelly\Utils\get_first_term($post)): ?>
      <h3 class="category-name"><a href="<?= get_term_link($category); ?>"><?php echo $category->cat_name; ?></a></h3>
    <?php endif; ?>
    <time class="article-date" datetime="<?php echo date('c', strtotime($post->post_date)); ?>"><?php echo date('j/n', strtotime($post->post_date)); ?></time>
    <?php if (has_post_thumbnail()) {
      $thumb = wp_get_attachment_url(get_post_thumbnail_id());
      echo '<div class="article-thumb" style="background-image:url('.$thumb.');"><img class="hide" src="'.$thumb.'"></div>';
    } ?>
    <div class="post-inner">
      <header>
        <h1 class="entry-title"><?php the_title(); ?></h1>
      </header>

      <?php if ($byline_area = get_post_meta($post->ID, '_cmb2_post_byline', true)): ?>
        <div class="byline-area user-content">
          <?php echo apply_filters('the_content', $byline_area); ?>
        </div>
      <?php endif; ?>
      <div class="entry-content user-content">
        <?php the_content(); ?>
      </div>
      <footer>
        <?= \Firebelly\Utils\get_focus_area_and_tags($post); ?>

        <?php get_template_part('templates/share'); ?>
      </footer>
      <?php comments_template('/templates/comments.php'); ?>
    </div>
  </article>
<?php endwhile; ?>
