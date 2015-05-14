<article class="program">
  <div class="article-content">
    <h2><a href="<?= get_the_permalink($program_post); ?>"><?= $program_post->post_title; ?></a></h2>
    <p><?= \Firebelly\Utils\get_excerpt($program_post); ?></p>
    <div class="more"><a class="button" href="<?= get_the_permalink($program_post); ?>">Learn More</a></div>
  </div>
</article>