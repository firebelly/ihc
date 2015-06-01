<?php 
$event = \Firebelly\PostTypes\Event\get_event_details($event_post);
$article_tags = \Firebelly\Utils\get_article_tags($event_post);
?>
<article class="event map-point" data-lat="<?= $event->lat ?>" data-lng="<?= $event->lng ?>" data-title="<?= $event->title ?>" data-desc="<?= $event->desc ?>" data-id="<?= $event->ID ?>">
  <div class="article-content">
    <time class="article-date flagged" datetime="<?= date('c', $event->event_start); ?>"><span class="month"><?= date('M', $event->event_start) ?></span> <span class="day"><?= date('d', $event->event_start) ?></span><?= ($event->year != date('Y') ? ' <span class="year">'.$event->year.'</span>' : '') ?></time>
    <div class="article-content-wrap"> 
      <h1 class="article-title"><a href="<?= get_permalink($event->ID); ?>"><?= $event->title ?></a></h1>
      <div class="event-details">
        <p class="time"><?= $event->time_txt ?></p>
        <?php if (!empty($event->address['city'])): ?>
          <p class="address"><?= $event->address['city'] ?>, <?= $event->address['state'] ?> <?= $event->address['zip'] ?></p>
        <?php endif; ?>
        <?php if ($article_tags): ?><div class="article-tags"><?= $article_tags ?></div><?php endif; ?>
      </div>
      <ul class="actions">
        <?php if (!empty($event->registration_url)): ?>
          <a target="_blank" class="register" href="<?= $event->registration_url ?>">Register</a>
        <?php endif; ?>
        <li><a class="more" href="<?= get_permalink($event->ID); ?>">More Details</a></li>
      </ul>
      <?php if (!empty($show_view_all_button)): ?><p class="view-all"><a class="button" href="/events/">View All Events</a></p><?php endif; ?>
    </div>
  </div>
</article>
