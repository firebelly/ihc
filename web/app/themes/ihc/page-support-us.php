<?php 
/**
 * Template Name: Support Us
 */

$content_banner_text = get_post_meta($post->ID, '_cmb2_content_banner_text', true);
$body_content = apply_filters('the_content', $post->post_content);
?>

<?php get_template_part('templates/page', 'image-header'); ?>

<section class="main">
<?php if ($content_banner_text): ?>
  <h4 class="flag"><?= $content_banner_text ?></h4>
<?php endif; ?>
  <div class="entry-content user-content">
   <div class="two-column">
   
    <div class="column">
      <?= get_the_block('Support Us Page Block 1') ?>
    </div>
    <div class="column last">
      <?= get_the_block('Support Us Page Block 2') ?>
    </div>
   </div>
   <div class="one-column">
    <h4 class="flag">Support Through your purchases</h4>
    <div class="column">
      <?= get_the_block('Support Us Page Block 3') ?>
    </div>
  </div>
</section>
<aside class="main">
<?php include(locate_template('templates/thought-of-the-day.php')); ?>
  <div class="support-us-sidebar">
  <?= get_the_block('Support Us Sidebar') ?>
  </div>
</aside>
