<?php while (have_posts()) : the_post(); ?>

<?php 
/**
 * Single Page
 */

$content_banner_text = get_post_meta($post->ID, '_cmb2_content_banner_text', true);
$body_content = apply_filters('the_content', $post->post_content);
?>

<article <?php post_class(); ?>>

  <?php get_template_part('templates/page', 'image-header'); ?>

  <section class="main">
  <?php if ($content_banner_text): ?>
    <h4 class="flag"><?= $content_banner_text ?></h4>
  <?php endif; ?>
    <div class="entry-content user-content">
      <?= $body_content ?>  
    </div>
  </section>

  <aside>
    <div class="article-list">
      <?= \Firebelly\Utils\get_related_event_post($post) ?>
      <?= \Firebelly\Utils\get_related_news_post($post) ?>
    </div>
  </aside>

</article>
<?php endwhile; ?>
