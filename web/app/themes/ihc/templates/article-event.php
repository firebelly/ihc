<?php
$event = \Firebelly\PostTypes\Event\get_event_details($event_post);
$article_tags = \Firebelly\Utils\get_article_tags($event_post);
$has_image_class = !empty($show_images) && has_post_thumbnail($event_post->ID) ? 'has-image' : '';
$event_url = get_permalink($event_post);
?>
<article class="event map-point <?= $has_image_class ?>"  data-url="<?= $event_url ?>" data-lat="<?= $event->lat ?>" data-lng="<?= $event->lng ?>" data-title="<?= $event->title ?>" data-desc="<?= $event->desc ?>" data-id="<?= $event->ID ?>">
  <div class="article-content">
    <?php if (!empty($show_images) && $thumb = \Firebelly\Media\get_post_thumbnail($event_post->ID)): ?>
      <a href="<?= get_the_permalink($event_post) ?>" class="article-thumb" style="background-image:url(<?= $thumb ?>);"></a>
    <?php endif; ?>
    <time class="article-date flagged" datetime="<?= date('c', (int)$event->event_start); ?>">
    <?php if (date('d', (int)$event->event_start) != date('d', (int)$event->event_end)) { ?>
      <span class="month event-start"><?= date('M d', (int)$event->event_start) ?></span>
      <span class="month event-end"><?= date('M d', (int)$event->event_end) ?></span>
    <?php } else { ?>
      <span class="month"><?= date('M', (int)$event->event_start) ?></span> <span class="day"><?= date('d', (int)$event->event_start) ?></span><?= ($event->year < date('Y') ? ' <span class="year">'.$event->year.'</span>' : '') ?>
    <?php } ?>
    </time>
    <div class="article-content-wrap">
      <h1 class="article-title"><a href="<?= $event_url ?>"><?= $event->title ?></a></h1>
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
        <?php elseif (!empty($event->registration_embed)): ?>
          <a class="register" href="<?= get_the_permalink($event_post) ?>#register">Register</a>
        <?php endif; ?>
        <li><a class="more" href="<?= $event_url ?>">More Details</a></li>
      </ul>
      <?php if (!empty($show_view_all_button)): ?><p class="view-all"><a class="button" href="/events/">View All Events</a></p><?php endif; ?>
    </div>
  </div>
</article>