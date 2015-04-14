<article class="program">
  <h1><a href="<?= get_the_permalink($program_post); ?>"><?= $program_post->post_title; ?></a></h1>
  <p><?= \Firebelly\Utils\get_excerpt($program_post); ?></p>
  <p class="more"><a href="<?= get_the_permalink($program_post); ?>">Learn More</a></p>
</article>