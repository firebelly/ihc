<?php 
/**
 * Events landing page
 */

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$per_page = get_option('posts_per_page');
$past_events = get_query_var('past_events', 0);
$filter_program = get_query_var('filter_program', '');
$filter_focus_area = get_query_var('filter_focus_area', '');
$total_events = \Firebelly\PostTypes\Event\get_events([
  'countposts' => 1,
  'past_events' => $past_events,
  'program' => $filter_program,
  'focus_area' => $filter_focus_area
]);
$total_pages = ($total_events > 0) ? ceil($total_events / $per_page) : 1;

$post = get_page_by_path('/events');
$with_image_class = (has_post_thumbnail($post->ID)) ? 'with-image' : '';
$page_content = apply_filters('the_content', $post->post_content);
?>
<div class="content-wrap <?= $with_image_class ?>">
  <?php get_template_part('templates/page', 'image-header'); ?>
  <div id="map" class="large hide"></div>

  <main>
    <ul class="flag-tabs">
      <li><a class="<?= $past_events ? '' : 'active' ?>" href="/events/">Upcoming Events</a></li> 
      <li><a class="<?= $past_events ? 'active' : '' ?> tab" href="/events/?past_events=1">Past Events</a></li>  
    </ul>

    <?php include(locate_template('templates/filters.php')); ?>

    <div class="events load-more-container article-list masonry">
      <?php if ($event_posts = \Firebelly\PostTypes\Event\get_events(['focus_area' => $filter_focus_area, 'program' => $filter_program, 'show_images' => true])): ?>
        <?= $event_posts ?>
      <?php else: ?>
        <div class="notice">
          <p>No posts found.</p>
        </div>
      <?php endif; ?>
    </div>
    
    <?php if ($total_pages>1): ?>
      <div class="load-more" data-post-type="event" data-page-at="<?= $paged ?>" data-past-events="<?= $past_events ?>" data-focus-area="<?= $filter_focus_area ?>" data-program="<?= $filter_program ?>" data-per-page="<?= $per_page ?>" data-total-pages="<?= $total_pages ?>"><a class="no-ajaxy button" href="#">Load More</a></div>
    <?php endif; ?>
  </main>
  <aside class="main">
      <?php include(locate_template('templates/thought-of-the-day.php')); ?>
  </aside>
</div>