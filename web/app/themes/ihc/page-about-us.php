<?php 
/**
 * Template Name: About Us
 */

$content_banner_text = get_post_meta($post->ID, '_cmb2_content_banner_text', true);
$body_content = apply_filters('the_content', $post->post_content);
?>

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

<aside class="main">
<?php include(locate_template('templates/thought-of-the-day.php')); ?>
  <div class="sidebar-content dark">
    <?= get_the_block('About Us Sidebar') ?>
    
    <h3>Our Focus Areas</h3>
    <ul class="focus-list">
    <?php 
    $focus_areas = get_terms('focus_area');
    foreach ($focus_areas as $focus_area) 
      echo '<li><a href="' . get_term_link($focus_area) . '">' . $focus_area->name . '</a></li>';
    ?>
    </ul>
  </div>
</aside>
