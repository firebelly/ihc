<?php 
$event = \Firebelly\PostTypes\Event\get_event_details($post);
$banner_text = $event->archived ? 'Past Event' : 'Attend An Event';
?>
<article class="post event map-point" data-lat="<?= $event->lat ?>" data-lng="<?= $event->lng ?>" data-title="<?= $event->title ?>" data-desc="<?= $event->desc ?>" data-id="<?= $event->ID ?>">
  <section class="main">
    <h4 class="flag"><?= $banner_text ?></h4>
    <time class="article-date" datetime="<?= date('c', $event->event_start); ?>"><span class="month"><?= date('M', $event->event_start) ?></span> <span class="day"><?= date('d', $event->event_start) ?></span><?= ($event->year != date('Y') ? ' <span class="year">'.$event->year.'</span>' : '') ?></time>
      <div class="post-inner">
        <header>
          <h1 class="entry-title"><span><?= $post->post_title ?></span></h1>
        </header>
          <div class="entry-content user-content"><?= $event->body ?>
          <?php get_template_part('templates/share'); ?>
          </div>
      </div>
   </section>
  <aside>
    <h4 class="flag">Event Details</h4>
    <div id="map" ></div>
    <div class="event-details">
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
    </div>
    <div class="single-blog-post">
    <h4 class="flag">Blog & News</h4>
      <?php 
      while (have_posts()) : the_post();
        $news_post = $post;
        include(locate_template('templates/article-news.php'));
      endwhile; 
      ?>
      <div class="load-more" data-page-at="1" data-per-page="4" data-total-pages="<?= ceil($total_news/4) ?>"><a class="no-ajaxy button" href="#">Load More</a></div>
    </div>
  </aside>
</article>