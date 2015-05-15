<?php 
use Firebelly\Utils;
$category = Utils\get_category($news_post);
$focus_area = Utils\get_focus_area($news_post);
// support legacy publication_dates via custom field
$publication_date = get_post_meta($news_post->ID, '_cmb2_publication_date', true);
$post_date_timestamp = $publication_date ? strtotime($publication_date) : strtotime($news_post->post_date);
?>
<article class="article">
  <?php 
  if (!empty($show_images) && has_post_thumbnail($news_post->ID)) {
    $thumb = wp_get_attachment_url(get_post_thumbnail_id($news_post->ID));
    echo '<a href="'.get_the_permalink($news_post).'" class="article-thumb" style="background-image:url('.$thumb.');"><span class="clip"></span></a>';
  } 
  ?>
  <div class="article-content">
    <time class="article-date" datetime="<?= date('c', $post_date_timestamp); ?>"><?= date('n/j', $post_date_timestamp); ?><?= (date('Y', $post_date_timestamp) != date('Y') ? '<span class="year">'.date('/Y', $post_date_timestamp).'</span>' : '') ?></time>
    <div class="article-content-wrap">
      <header class="article-header">
        <?php if ($category): ?><div class="article-category"><a href="<?= get_term_link($category); ?>"><?= $category->name; ?></a></div><?php endif; ?>
        <h1 class="article-title"><a href="<?= get_the_permalink($news_post); ?>"><?= wp_trim_words($news_post->post_title, 10); ?></a></h1>
      </header>
      <div class="article-excerpt">
        <p><?= Utils\get_excerpt($news_post); ?></p>
      </div>
      <?php if ($focus_area): ?><div class="article-tag"><a href="<?= get_term_link($focus_area); ?>"><?= $focus_area->name; ?></a></div><?php endif; ?>
    </div>
  </div>
</article>