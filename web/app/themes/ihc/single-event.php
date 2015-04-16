<?php 
$event = \Firebelly\PostTypes\Event\get_event_details($post);
$banner_text = $event->archived ? 'Past Event' : 'Attend An Event';
?>
<article class="post event map-point" data-lat="<?= $event->lat ?>" data-lng="<?= $event->lng ?>" data-title="<?= $event->title ?>" data-desc="<?= $event->desc ?>" data-id="<?= $event->ID ?>">

  <section class="main">
    <h3 class="banner"><?= $banner_text ?></h3>
    <h1><?= $post->post_title ?></h1>
    <div class="entry-content user-content"><?= $event->body ?></div>
  </section>  

  <aside>
    <div id="map"></div>

    <h3>When:</h3>
    <?= date('l, F j, Y', $event->event_start) ?>
    <br><?= $event->time_txt ?></p>

    <h3>Where:</h3>
    <?= $event->venue ?> 
    <br><?= $event->address['address-1'] ?> 
    <?php if (!empty($event->address['address-2'])): ?>
      <br><?= $event->address['address-2'] ?>
    <?php endif; ?>
    <br><?= $event->address['city'] ?>, <?= $event->address['state'] ?> <?= $event->address['zip'] ?>

    <?php if (!($event->archived)): ?>
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
    <?php endif; ?>
  </aside>

</article>
