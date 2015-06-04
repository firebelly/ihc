<?php 
if (!$post) return; // if we somehow get to this partial w/out a post

$header_class = '';
if ($header_bg = \Firebelly\Media\get_header_bg($post)) {
  $header_class = 'with-image';
}

// Programs use title for banner text
if ($post->post_type == 'program')
  $header_banner_text = $post->post_title;
else
  $header_banner_text = str_replace("\n","<br>",get_post_meta($post->ID, '_cmb2_header_banner_text', true));
// Custom fields for header text
$header_text = str_replace("\n","<br>",get_post_meta($post->ID, '_cmb2_header_text', true));
$secondary_header_text = get_post_meta($post->ID, '_cmb2_secondary_header_text', true);
if ($secondary_header_text)
  $header_class .= ' with-secondary-header';
if (!$header_text)
  $header_class .= ' no-header-text';
?>

<header class="page-header <?= $header_class ?>">
  <div class="container">
    <div class="image-wrap"<?= $header_bg ?>>
      <?php if ($header_banner_text): ?>
      <h4 class="flag"><?= $header_banner_text ?></h4>
      <?php endif; ?>

      <?php get_template_part('templates/share'); ?>

      <div class="header-text">
        <?= $header_text ?>
      </div>
    </div>
  </div>
  <?php if ($secondary_header_text): ?>
    <div class="secondary-header-text"><?= $secondary_header_text ?></div>
  <?php endif; ?>
</header>
