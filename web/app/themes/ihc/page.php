<?php while (have_posts()) : the_post(); ?>

<?php 
/**
 * Single Page
 */

$content_banner_text = get_post_meta($post->ID, '_cmb2_content_banner_text', true);
$body_content = apply_filters('the_content', $post->post_content);
$with_image_class = (has_post_thumbnail($post->ID)) ? 'with-image' : '';
?>

<article <?php post_class($with_image_class); ?>>

  <?php get_template_part('templates/page', 'image-header'); ?>

  <main>
    <?php if ($content_banner_text): ?>
      <h4 class="flag"><?= $content_banner_text ?></h4>
    <?php endif; ?>
    <div class="entry-content user-content">
      <?= $body_content ?>  
    </div>
  </main>

  <aside>
    <div class="article-list">
      <?= \Firebelly\Utils\get_related_event_post($post) ?>
      <?= \Firebelly\Utils\get_related_news_post($post) ?>
    </div>
  </aside>

</article>
<?php endwhile; ?>
