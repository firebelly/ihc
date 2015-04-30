<?php 
/**
 * Template Name: About Us
 */

$content_banner_text = get_post_meta($post->ID, '_cmb2_content_banner_text', true);
$body_content = apply_filters('the_content', $post->post_content);
?>

<?php get_template_part('templates/page', 'image-header'); ?>

<section class="main">
  <div class="entry-content user-content">
  <?php if ($content_banner_text): ?>
    <h4 class="flag"><?= $content_banner_text ?></h4>
  <?php endif; ?>
  <div class="one-column">
      <div class="column">
        <h3 class="about-section">Board</h3>
        <?= get_the_block('About Us Board') ?>
      </div>
      <div class="column expanded">
        <h3 class="about-section">Staff & Contacts</h3>
        <?= get_the_block('About Us Staff & Project Contacts') ?>
      </div>
      <div class="column">
         <h3 class="about-section">Supporters</h3>
        <?= get_the_block('About Us Supporters') ?>
      </div>
    <?= $body_content ?>  
    </div>
  </div>
</section>

<aside class="page-with-img">
<?php include(locate_template('templates/thought-of-the-day.php')); ?>
  <div class="about-us-sidebar">
    <?= get_the_block('About Us Sidebar') ?>
  </div>
</aside>
