<?php 
/**
 * Template Name: About Us
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
    <?= $body_content ?>  
  </div>
</section>

<aside>
<?php include(locate_template('templates/thought-of-the-day.php')); ?>
  <div class="about-us-sidebar">
    <?= get_the_block('About Us Sidebar') ?>
  </div>
</aside>
