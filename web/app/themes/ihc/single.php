<?php while (have_posts()) : the_post(); ?>
<?php 
// support legacy publication_dates via custom field
$publication_date = get_post_meta($post->ID, '_cmb2_publication_date', true);
$post_date_timestamp = $publication_date ? strtotime($publication_date) : strtotime($post->post_date);
?>
  <article <?php post_class(); ?>>
    <section class="main">
      <?php if ($category = \Firebelly\Utils\get_first_term($post)): ?>
        <h4 class="flag"><a href="<?= get_term_link($category); ?>"><?php echo $category->name; ?></a></h4>
      <?php endif; ?>
      <time class="article-date" datetime="<?php echo date('c', $post_date_timestamp); ?>"><?php echo date('n/j', $post_date_timestamp); ?><?= (date('Y', $post_date_timestamp) != date('Y') ? '<span class="year">'.date('/Y', $post_date_timestamp).'</span>' : '') ?></time>
      <?php if (has_post_thumbnail()) {
        $thumb = wp_get_attachment_url(get_post_thumbnail_id());
        echo '<div class="article-thumb" style="background-image:url('.$thumb.');"><img class="hide" src="'.$thumb.'"></div>';
      } ?>
      <div class="post-inner">
        <header>
          <h1 class="entry-title"><span><?php the_title(); ?></span></h1>
        </header>

        <?php if ($byline_area = get_post_meta($post->ID, '_cmb2_post_byline', true)): ?>
          <div class="byline-area user-content">
            <?php echo apply_filters('the_content', $byline_area); ?>
          </div>
        <?php endif; ?>
        <div class="entry-content user-content">
          <?php the_content(); ?>
        </div>
        <?php comments_template('/templates/comments.php'); ?>
      </div>
    </section>
  </article>
<?php endwhile; ?>
