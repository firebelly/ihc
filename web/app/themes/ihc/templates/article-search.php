<?php 
use Firebelly\Utils;
$article_tags = Utils\get_article_tags($post);
$permalink = get_the_permalink($post);
$has_image_class = has_post_thumbnail($post->ID) ? 'has-image' : '';
?>
<article class="article <?= $has_image_class ?>">
  <div class="article-content">
    <?php if ($thumb = \Firebelly\Media\get_post_thumbnail($post->ID)): ?>
      <a href="<?= get_the_permalink($post) ?>" class="article-thumb" style="background-image:url(<?= $thumb ?>);"><span class="clip -left"></span><span class="clip -right"></span></a>
    <?php endif; ?>
    <div class="article-content-wrap">
      <header class="article-header">
        <h1 class="article-title"><a class="no-ajaxy" href="<?php echo $permalink; ?>"><?php echo $post->post_title; ?></a></h1>
        <?php if ($article_tags): ?><div class="article-tags"><?= $article_tags ?></div><?php endif; ?>
      </header>
      <div class="article-excerpt">
        <p><?php echo Utils\get_excerpt($post); ?></p>
      </div>
    </div>
  </div>
</article>