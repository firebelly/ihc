<?php
/**
 * Template Name: Homepage
 */

$total_events = \Firebelly\PostTypes\Event\get_events(['countposts' => 1]);
$total_news = wp_count_posts('post')->publish;
$header_bg = \Firebelly\Media\get_header_bg($post);
$header_banner_text = str_replace("\n","<br>",get_post_meta($post->ID, '_cmb2_header_banner_text', true));
$header_text = get_post_meta($post->ID, '_cmb2_header_text', true);
$header_text = str_replace("\n","<br>",strip_tags($header_text, '<u><br><br/>'));
$secondary_header_text = get_post_meta($post->ID, '_cmb2_secondary_header_text', true);
$secondary_header_text = strip_tags($secondary_header_text, '<u><strong><em><a><br><br/>');
?>

<div id="map" class="large"></div>

<header class="page-header with-image">
  <div class="container">
    <div class="image-wrap"<?= $header_bg ?>>
      <h2 class="flag"><?= $header_banner_text ?></h2>
      <h1><?= $header_text ?></h1>
      <?php if ($secondary_header_text) { ?>
        <p class="callout"><?= $secondary_header_text ?></p>
      <?php } ?>
    </div>
  </div>
</header>

<section class="focus-areas">
  <h2 class="flag">Our Divisions</h2>
  <ul class="focus-list">
    <?php
    $divisions = get_terms('division');
    foreach ($divisions as $division) {
      echo '<li><a href="' . get_term_link($division) . '">' . $division->name . '</a></li>';
    }
    ?>
  </ul>
</section>

<?php
// homepage shows all current events on map
echo \Firebelly\PostTypes\Event\get_events(['num_posts' => -1, 'map-points' => true]);
?>

<section class="event-cal">
  <h2 class="flag">Attend an Event</h2>
  <div class="events load-more-container article-list masonry">
    <?php echo \Firebelly\PostTypes\Event\get_events(['num_posts' => 3]); ?>
  </div>
  <div class="events-buttons">
    <?php if (ceil($total_events/3) > 1): ?>
      <div class="load-more" data-post-type="event" data-page-at="1" data-past-events="0" data-per-page="3" data-total-pages="<?= ceil($total_events/3) ?>"><a class="no-ajaxy button" href="#">Load More</a></div>
    <?php endif; ?>
    <p class="view-all"><a href="/events/" class="button">View All Events</a></p>
  </div>
</section>

<section class="news">
  <h2 class="flag">Blog &amp; News</h2>
  <div class="load-more-container article-list masonry">
    <?php
    // Recent Blog & News posts
    $news_posts = get_posts(['numberposts' => 4, 'category__not_in' => [9]]);
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

<div class="thought-of-the-day-wrapper">
  <?php include(locate_template('templates/thought-of-the-day.php')); ?>
</div>
