<?php 
$event = \Firebelly\PostTypes\Event\get_event_details($post);
$banner_text = $event->archived ? 'Past Event' : 'Attend An Event';
$article_tags = \Firebelly\Utils\get_article_tags($post);
?>
<article class="post event map-point" data-lat="<?= $event->lat ?>" data-lng="<?= $event->lng ?>" data-title="<?= $event->title ?>" data-desc="<?= $event->desc ?>" data-id="<?= $event->ID ?>">
  <main>
    <h4 class="flag"><?= $banner_text ?></h4>
    <time class="article-date flagged" datetime="<?= date('c', $event->event_start); ?>"><span class="month"><?= date('M', $event->event_start) ?></span> <span class="day"><?= date('d', $event->event_start) ?></span><?= ($event->year != date('Y') ? ' <span class="year">'.$event->year.'</span>' : '') ?></time>
    <div class="post-inner">
      <header>
        <h1 class="entry-title"><span><?= $post->post_title ?></span></h1>
      </header>
      <div class="entry-content user-content">
        <?= $event->body ?>
      </div>
      <footer>
        <?php if ($article_tags): ?><div class="article-tags"><?= $article_tags ?></div><?php endif; ?>
        <?php get_template_part('templates/share'); ?>
      </footer>
    </div>
  </main>

  <aside class="main">
    <h4 class="flag">Event Details</h4>
    <div id="map"></div>
    <div class="event-details">
      <h3>When:</h3>
      <p><?= date('l, F j, Y', $event->event_start) ?>
      <br><?= $event->time_txt ?></p>

      <h3>Where:</h3>
      <p><?= $event->venue ?>
      <br><?= $event->address['address-1'] ?> 
      <?php if (!empty($event->address['address-2'])): ?>
        <br><?= $event->address['address-2'] ?>
      <?php endif; ?>
      <br><?= $event->address['city'] ?>, <?= $event->address['state'] ?> <?= $event->address['zip'] ?>
      </p>

      <?php if (!empty($event->sponsor)): ?>
        <div class="sponsors">
          <h3>Sponsors:</h3>
          <?= apply_filters('the_content', $event->sponsor); ?>
        </div>
      <?php endif; ?>

      <?php if (!($event->archived)): ?>
        <h3>Cost:</h3>
        <p class="cost">
          <?php if (!$event->cost): ?>
            Free, open to the public.
          <?php else: ?>
            <?= $event->cost ?>
          <?php endif; ?>
        </p>
        <ul class="actions">
          <?php if (!empty($event->registration_url)): ?>
            <li><a class="register" target="_blank" href="<?= $event->registration_url ?>">Register For Event</a></li>
          <?php endif; ?>
          <li><a class="add-to-calendar" href="<?= $event->add_to_calendar_url ?>">Add To My Calendar</a></li>
        </ul>
      <?php endif; ?>
    </div>

    <div class="article-list">
      <?= \Firebelly\Utils\get_related_news_post($post) ?>
    </div>
  </aside>
</article>