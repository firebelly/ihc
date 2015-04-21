<?php
/**
 * Template Name: Homepage
 */

$total_events = \Firebelly\PostTypes\Event\get_num_events();
$total_news = wp_count_posts('post')->publish;
?>
<?php 
// Main page data
// echo $post->post_title;
// echo apply_filters('the_content', $post->post_content);
?>
<?php include(locate_template('templates/thought-of-the-day.php')); ?>
<div class="page-header with-image">
<h2>Year-round & State-wide</h2>
<h1>Beginning the conversation.
We fuel inquiry through conversation in ways that strengthen society.</h1>
<p>image caption/link to event to bring it front and center for instant access.</p>
</div>
<section class="focus-areas">
<h2>Our Focus Areas</h2>
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
<h2>Attend an Event</h2>
<div class="events load-more-container article-list masonry">
	<?php echo \Firebelly\PostTypes\Event\get_events(3); ?>
</div>
<div class="load-more events" data-page-at="1" data-past-events="0" data-per-page="3" data-total-pages="<?= ceil($total_events/3) ?>"><a class="no-ajaxy" href="#">Load More</a></div>
<p><a href="/events/" class="view-all">View All Events</a></p>
</section>
<section class="news">
<h2>Blog &amp; News</h2>
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
<div class="load-more" data-page-at="1" data-per-page="4" data-total-pages="<?= ceil($total_news/4) ?>"><a class="no-ajaxy" href="#">Load More</a></div>
<p class="all-articles"><a href="/news/" class="view-all">All Articles</a></p>
</div>
</section>