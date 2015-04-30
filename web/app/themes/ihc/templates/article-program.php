<article class="program">
  <h2><span><a href="<?= get_the_permalink($program_post); ?>"><?= $program_post->post_title; ?></a></span></h2>
  <p><?= \Firebelly\Utils\get_excerpt($program_post); ?></p>
  <p class="more button"><a href="<?= get_the_permalink($program_post); ?>">Learn More</a></p>
</article>