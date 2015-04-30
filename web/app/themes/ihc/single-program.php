<?php 
/**
 * Single Program
 */
use Firebelly\Utils;
$content_banner_text = get_post_meta($post->ID, '_cmb2_content_banner_text', true);
$body_content = apply_filters('the_content', $post->post_content);
$header_banner_text = str_replace("\n","<br>",get_post_meta($post->ID, '_cmb2_header_banner_text', true));
$addl_info = get_post_meta($post->ID, '_cmb2_addl_info', true);
?>

<article <?php post_class(); ?>>

  <?php get_template_part('templates/page', 'image-header'); ?>

  <section class="main">
  <?php if ($content_banner_text): ?>
    <h4 class="banner"><?= $content_banner_text ?></h4>
  <?php endif; ?>
    <div class="entry-content user-content">
      <h2 class="banner"><?= $header_banner_text ?></h2>
      <?= $body_content ?>
    </div>
    
    <?php 
    $page_blocks = get_post_meta($post->ID, '_cmb2_page_blocks', true);

    foreach ($page_blocks as $page_block):
      $block_title = $block_body = '';
      if (!empty($page_block['title']))
        $block_title = $page_block['title'];
      if (!empty($page_block['body']))
        $block_body = apply_filters('the_content', $page_block['body']);
      ?>
      <div class="entry-content user-content">
        <h2 class="banner"><?= $block_title ?></h2>
        <?= $block_body ?>
      </div>
    <?php endforeach; ?>

  </section>

  <aside>
    <div class="article-list">
      <?= Utils\get_related_event_post($post) ?>
      <?= Utils\get_related_news_post($post) ?>
    </div>

    <?php if ($resources = Utils\get_resources($post)): ?>
      <h3>Resources</h3>
      <?= $resources ?>
    <?php endif; ?>

    <?php if ($addl_info): ?>
      <?= apply_filters('the_content', $addl_info); ?>
    <?php endif; ?>
  </aside>
</article>
