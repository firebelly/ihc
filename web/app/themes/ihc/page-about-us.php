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
    <article class="row">
     <div class="plus"></div><div class="minus hide"></div>
        <h3 class="about-section">Board</h3>
        <div class="columns">
        <?= get_the_block('About Us Board') ?>
      </div>
      </article>
      <article class="row expanded">
      <div class="plus hide"></div><div class="minus"></div>
      <h3 class="about-section">Staff & Contacts</h3>
      <div class="columns">
        <?= get_the_block('About Us Staff & Project Contacts') ?>
      </div>
      </article>
      <article class="row">
      <div class="plus"></div><div class="minus hide"></div>
      <h3 class="about-section">Supporters</h3>
      <div class="columns">
        <?= get_the_block('About Us Supporters') ?>
      </div>
     <?= $body_content ?>  
    </article>

    <div class="entry-content user-content">
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
