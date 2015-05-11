<?php 
/**
 * Template Name: Our Work
 */

$content_banner_text = get_post_meta($post->ID, '_cmb2_content_banner_text', true);
$body_content = apply_filters('the_content', $post->post_content);
?>

<?php get_template_part('templates/page', 'image-header'); ?>

<section class="main">
  <h4 class="flag">Our Focus Areas</h4>
  <ul class="focus-list-large">
    <?php 
    $focus_areas = get_terms('focus_area');
    foreach ($focus_areas as $focus_area) {
      include(locate_template('templates/article-focus-area.php'));
    }
    ?>
  </ul>
</section>

<aside class="main">
  <?php include(locate_template('templates/thought-of-the-day.php')); ?>
  <div class="sidebar-content user-content dark">
    <?= get_the_block('Our Work Sidebar') ?>
  </div>
</aside>
