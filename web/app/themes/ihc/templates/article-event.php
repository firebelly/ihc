<?php 
$event = \Firebelly\PostTypes\Event\get_event_details($event_post);
?>
<article class="event map-point" data-lat="<?= $event->lat ?>" data-lng="<?= $event->lng ?>" data-title="<?= $event->title ?>" data-desc="<?= $event->desc ?>" data-id="<?= $event->ID ?>">
  <time datetime="<?= date('c', $event->event_start); ?>"><span class="month"><?= date('M', $event->event_start) ?></span> <span class="day"><?= date('d', $event->event_start) ?></span><?= ($event->year != date('Y') ? ' <span class="day">'.$event->year.'</span>' : '') ?></time>
  <h1><a href="<?= get_permalink($event->ID); ?>"><?= $event->title ?></a></h1>
  <h3><?= $event->time_txt ?></h3>
  <?php if (!empty($event->address['city'])): ?>
	  <p><?= $event->address['city'] ?>, <?= $event->address['state'] ?> <?= $event->address['zip'] ?></p>
	<?php endif; ?>
  <?php if (!empty($event->registration_url)): ?>
    <a class="register" href="<?= $event->registration_url ?>">Register</a>
  <?php endif; ?>
  <p class="more"><a href="<?= get_permalink($event->ID); ?>">More Details</a></p>
</article>
