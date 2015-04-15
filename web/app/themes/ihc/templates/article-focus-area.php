<?php 
// $event = \Firebelly\PostTypes\Event\get_focus_area_details($event_post);
?>
<article class="focus-area">
  <div class="info">
    <h1><a href="<?= get_term_link($focus_area) ?>"><?= $focus_area->name ?></a></h1>
    <p><?= $focus_area->description ?></p>
    <p class="more"><a href="<?= get_term_link($focus_area) ?>">Learn More</a></p>
  </div>
  <div class="related">
  <?php 
  $related_programs = \Firebelly\PostTypes\Program\get_programs($focus_area->slug);
  if ($related_programs):
  ?>
    <h3>Program(s):</h3>
    <ul class="programs">
    <?php foreach ($related_programs as $program_post): ?>
      <li><a href="<?= get_permalink($program_post->ID) ?>"><?= $program_post->post_title ?></a></li>
    <?php endforeach; ?>
  <?php endif; ?>
  </div>
</article>
