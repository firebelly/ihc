<?php 
use Firebelly\Utils;
$focus_area = Utils\get_focus_area($post);
$permalink = get_the_permalink($post);
?>
<article class="article">
<?php if (has_post_thumbnail($post->ID)) {
  $thumb = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
  echo '<a href="'.get_the_permalink($post).'" class="article-thumb" style="background-image:url('.$thumb.');"><span class="clip -left"></span><span class="clip -right"></span></a>';
} ?>
  <div class="article-content">
    <div class="article-content-wrap">
      <header class="article-header">
        <h1 class="article-title"><a class="no-ajaxy" href="<?php echo $permalink; ?>"><?php echo $post->post_title; ?></a></h1>
        <?php if ($focus_area): ?><div class="article-tag"><?php echo $focus_area->name; ?></div><?php endif; ?>
      </header>
      <div class="article-excerpt">
        <p><?php echo Utils\get_excerpt($post); ?></p>
      </div>
    </div>
  </div>
</article>