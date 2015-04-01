<?php
/**
 * Template Name: Homepage
 */
?>

<?php 
// Main page data
// echo $post->post_title;
// echo apply_filters('the_content', $post->post_content);
?>


<h2>Thought of the Day</h2>
<?php 
echo \Firebelly\PostTypes\Thought\get_thoughts();
?>

<?php
// Submit a Thought form
\Firebelly\PostTypes\Thought\submit_form();
?>


<h2>Our Focus Areas</h2>
<ul class="focus-areas">
<?php 
$focus_areas = get_terms('focus_area');
foreach ($focus_areas as $focus_area) {
	echo '<li><a href="' . get_term_link($focus_area) . '">' . $focus_area->name . '</a></li>';
}
?>
</ul>


<h2>Attend an Event</h2>
<div class="events">
<?php 
// Recent blog & news posts
$news_posts = get_posts(['numberposts' => 4]);
if ($news_posts):
  foreach ($news_posts as $news_post) {
    include(locate_template('templates/article-news.php'));
  }
endif;
?>
</div>
<p><a href="#" class="load-more" data-more-container="events">+ Load More</a></p>


<h2>Blog &amp; News</h2>
<div class="blog-and-news">
<?php 
// Recent blog & news posts
$news_posts = get_posts(['numberposts' => 4]);
if ($news_posts):
  foreach ($news_posts as $news_post) {
    include(locate_template('templates/article-news.php'));
  }
endif;
?>
</div>
<p><a href="#" class="load-more" data-more-container="blog-and-news">+ Load More</a></p>