<?php 
/**
 * Events landing page
 */

$paged = get_query_var('paged', 1);
$per_page = get_option('posts_per_page');
$total_events = wp_count_posts('event')->publish;
$total_pages = ceil($total_events / $per_page);

$page = get_page_by_path('/events');
$page_content = apply_filters('the_content', $page->post_content);
$secondary_content = apply_filters('the_content', get_post_meta($page->ID, '_cmb2_secondary_content', true));
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
		<li><a class="<?= get_query_var('past_events') ? '' : 'active' ?>" href="/events/">Upcoming Events</a></li>	
		<li><a class="<?= get_query_var('past_events') ? 'active' : '' ?>" href="/events/?past_events=1">Past Events</a></li>	
	</ul>

	<div class="events load-more-container">
		<?php \Firebelly\Ajax\get_event_posts(); ?>
	</div>
	
	<?php if($total_pages > 1): ?>
		<div class="load-more events" data-page-at="<?= $paged ?>" data-past-events="<?= get_query_var('past-events', 0) ?>" data-per-page="<?= $per_page ?>" data-total-pages="<?= $total_pages ?>"><a class="no-ajaxy" href="#">+ Load More</a></div>
	<?php endif; ?>

</section>
