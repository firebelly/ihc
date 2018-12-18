<?php
/**
 * Template Name: Support Us
 */

$content_banner_text = get_post_meta($post->ID, '_cmb2_content_banner_text', true);
$body_content = apply_filters('the_content', $post->post_content);
$with_image_class = (has_post_thumbnail($post->ID)) ? 'with-image' : '';
$support_us_block1 = get_post_meta($post->ID, '_mcb-support-us-page-block-1', true);
$support_us_block2 = get_post_meta($post->ID, '_mcb-support-us-page-block-2', true);
$support_us_block3 = get_post_meta($post->ID, '_mcb-support-us-page-block-3', true);
$support_us_sidebar = get_post_meta($post->ID, '_mcb-support-us-sidebar', true);
?>
<div class="content-wrap <?= $with_image_class ?>">

  <?php get_template_part('templates/page', 'image-header'); ?>

  <main>
    <?php if ($content_banner_text): ?>
      <h2 class="flag"><?= $content_banner_text ?></h2>
    <?php endif; ?>

    <div class="grid bigclicky">
      <div class="flex-item one-half user-content">
        <?= apply_filters('the_content', $support_us_block1) ?>
      </div>
      <div class="flex-item one-half user-content">
        <?= apply_filters('the_content', $support_us_block2) ?>
      </div>
    </div>

    <div class="one-column">
      <h2 class="flag">Support Through your purchases</h2>
      <div class="user-content">
        <?= apply_filters('the_content', $support_us_block3) ?>
      </div>
    </div>
  </main>

  <aside class="main">
  <?php include(locate_template('templates/thought-of-the-day.php')); ?>
    <div class="sidebar-content">
    <?= apply_filters('the_content', $support_us_sidebar) ?>
    </div>
  </aside>

</div>