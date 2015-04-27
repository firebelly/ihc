<?php
/**
 * Template Name: Homepage
 */

$total_events = \Firebelly\PostTypes\Event\get_num_events();
$total_news = wp_count_posts('post')->publish;
?>

<?php include(locate_template('templates/thought-of-the-day.php')); ?>
<div class="page-header with-image">
  <h4 class="flag">Year-round & State-wide</h4>
  <h1><span>Be</span>g<span>innin</span>g<span> the conversation</span>.<br> We fuel inquiry through conversation in ways that strengthen society.</h1>
  <p class="accent">image caption/link to event to bring it front and center for instant access.</p>
</div>

<section class="focus-areas">
  <h4 class="flag">Our Focus Areas</h4>
  <ul class="focus-list">
    <?php 
    $focus_areas = get_terms('focus_area');
    foreach ($focus_areas as $focus_area) {
      echo '<li><a href="' . get_term_link($focus_area) . '">' . $focus_area->name . '</a></li>';
    }
    ?>
  </ul>
</section>

<div id="map" class="large"></div>

<section class="event-cal">
  <h4 class="flag">Attend an Event</h4>
  <div class="events load-more-container article-list masonry">
    <?php echo \Firebelly\PostTypes\Event\get_events(3); ?>
  </div>
  <div class="events-buttons">
  <div class="load-more" data-page-at="1" data-past-events="0" data-per-page="3" data-total-pages="<?= ceil($total_events/3) ?>"><a class="no-ajaxy button" href="#">Load More</a></div>
  <p class="view-all"><a href="/events/" class="button">View All Events</a></p>
  </div>
</section>

<section class="news">
  <h4 class="flag">Blog &amp; News</h4>
  <div class="load-more-container article-list masonry">
    <?php 
    // Recent Blog & News posts
    $news_posts = get_posts(['numberposts' => 4]);
    if ($news_posts):
      foreach ($news_posts as $news_post) {
        include(locate_template('templates/article-news.php'));
      }
    endif;
    ?>
  </div>
  <div class="news-buttons">
    <div class="load-more" data-page-at="1" data-per-page="4" data-total-pages="<?= ceil($total_news/4) ?>"><a class="no-ajaxy button" href="#">Load More</a></div>
    <p class="view-all"><a href="/news/" class="button">All Articles</a></p>
  </div>
</section>