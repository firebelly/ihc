<?php 
$header_class = $header_bg = '';
if (has_post_thumbnail($post->ID)) {
  $background_image = wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'full', true);
  $header_class = 'with-image';
  $header_bg = ' style="background-image:url('.$background_image.');"';
}

// Programs use title for banner text
if ($post->post_type == 'program')
  $header_banner_text = $post->post_title;
else
  $header_banner_text = get_post_meta($post->ID, '_cmb2_header_banner_text', true);
// Custom fields for header text
$header_text = get_post_meta($post->ID, '_cmb2_header_text', true);
$secondary_header_text = get_post_meta($post->ID, '_cmb2_secondary_header_text', true);
?>

<header class="page-header <?= $header_class ?>"<?= $header_bg ?>
  <?php if ($header_banner_text): ?>
    <h4 class="flag"><?= $header_banner_text ?></h4>
  <?php endif; ?>

  <?php get_template_part('templates/share'); ?>

  <div class="header-text"><?= $header_text ?></div>

  <?php if ($secondary_header_text): ?>
    <div class="secondary-header-text"><?= $secondary_header_text ?></div>
  <?php endif; ?>
</header>
