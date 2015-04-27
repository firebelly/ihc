<?php 
/**
 * Events landing page
 */

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$per_page = get_option('posts_per_page');
$past_events = get_query_var('past_events', 0);
$total_events = \Firebelly\PostTypes\Event\get_num_events($past_events);
$total_pages = ceil($total_events / $per_page);

$page = get_page_by_path('/events');
$page_content = apply_filters('the_content', $page->post_content);
?>

<header>
	<div class="user-content">
		<?php echo $page_content; ?>
	</div>

	<aside>
		<?php include(locate_template('templates/thought-of-the-day.php')); ?>
	</aside>
</header>

<div id="map" class="large"></div>

<section class="main">
	<ul>
		<li><a class="<?= $past_events ? '' : 'active' ?>" href="/events/">Upcoming Events</a></li>	
		<li><a class="<?= $past_events ? 'active' : '' ?>" href="/events/?past_events=1">Past Events</a></li>	
	</ul>

	<div class="events load-more-container article-list masonry">
		<?php echo \Firebelly\PostTypes\Event\get_events(); ?>
	</div>
	
	<div class="load-more events" data-page-at="<?= $paged ?>" data-past-events="<?= $past_events ?>" data-per-page="<?= $per_page ?>" data-total-pages="<?= $total_pages ?>"><a class="no-ajaxy" href="#">+ Load More</a></div>

</section>
