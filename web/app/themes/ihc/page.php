<?php 
/**
 * Single page
 */

$content_banner_text = get_post_meta($post->ID, '_cmb2_content_banner_text', true);
$body_content = apply_filters('the_content', $post->post_content);
$with_image_class = (has_post_thumbnail($post->ID)) ? 'with-image' : '';
$sidebar_content = get_the_block('Sidebar Content', true);
?>
<div class="content-wrap <?= $with_image_class ?>">

  <?php get_template_part('templates/page', 'image-header'); ?>

  <main>
    <?php if ($content_banner_text): ?>
      <h4 class="flag"><?= $content_banner_text ?></h4>
    <?php endif; ?>

    <div class="one-column">
      <div class="user-content">
        <?= $body_content ?>
      </div>
    </div>
  </main>

  <?php if ($sidebar_content): ?>
    <aside class="main">
      <?php if ($post->post_title==='About Us') include(locate_template('templates/thought-of-the-day.php')); ?>
      <div class="sidebar-content dark">
        <?= $sidebar_content ?>
      </div>
    </aside>
  <?php endif; ?>

</div>