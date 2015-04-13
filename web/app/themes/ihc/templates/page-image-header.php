<?php 
$header_class = $header_bg = '';
if (has_post_thumbnail($post->ID)) {
  $background_image = wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'full', true);
  $header_class = 'with-image';
  $header_bg = ' style="background-image:url('.$thumb.');"';
}

// custom fields
$header_banner_text = get_post_meta($post->ID, '_cmb2_header_banner_text', true);
$header_text = get_post_meta($post->ID, '_cmb2_header_text', true);
$secondary_header_text = get_post_meta($post->ID, '_cmb2_secondary_header_text', true);
?>

<header class="<?= $header_class ?>"<?= $header_bg ?>>
   <div class="navbar-header">
    <h1><a class="navbar-brand" href="<?= esc_url(home_url('/')); ?>">Illinois Humanities</a><?php bloginfo('name'); ?></a>
    </h1>
    </div>
  <?php if ($header_banner_text): ?>
    <h4 class="banner"><?= $header_banner_text ?></h4>
  <?php endif; ?>

  <?php get_template_part('templates/share'); ?>

  <div class="header-text"><?= $header_text ?></div>

  <?php if ($secondary_header_text): ?>
    <div class="secondary-header-text"><?= $secondary_header_text ?></div>
  <?php endif; ?>
</header>
