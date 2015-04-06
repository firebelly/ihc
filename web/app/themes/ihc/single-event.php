<?php 
$event = \Firebelly\PostTypes\Event\get_event_details($post);
?>
<article class="post event map-point" data-lat="<?= $event->lat ?>" data-lng="<?= $event->lng ?>" data-title="<?= $event->title ?>" data-desc="<?= $event->desc ?>" data-id="<?= $event->ID ?>">
  <h1><?= $post->post_title ?></h1>
  <div class="body"><?= $event->body ?></div>

  <aside>
    <div id="map"></div>

    <h3>When:</h3>
    <?= date('l, F j, Y', $event->event_timestamp) ?>
    <br><?= $event->time_txt ?></p>

    <h3>Where:</h3>
    <?= $event->venue ?> 
    <br><?= $event->address['address-1'] ?> 
    <?php if (!empty($event->address['address-2'])): ?>
      <br><?= $event->address['address-2'] ?>
    <?php endif; ?>
    <br><?= $event->address['city'] ?>, <?= $event->address['state'] ?> <?= $event->address['zip'] ?>

    <h3>Cost:</h3>
    <p class="cost">
    <?php if (!$event->cost): ?>
      Free, open to the public.
    <?php else: ?>
      <?= $event->cost ?>
    <?php endif; ?>
    </p>

    <ul>
      <?php if (!empty($event->registration_url)): ?>
        <li><a class="register" target="_blank" href="<?= $event->registration_url ?>">Register For Event</a></li>
      <?php endif; ?>
      <li><a class="add-to-calendar" href="<?= $event->add_to_calendar_url ?>">Add To My Calendar</a></li>
    </ul>
  </aside>
</article>
