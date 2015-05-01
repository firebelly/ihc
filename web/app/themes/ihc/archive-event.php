<?php 
/**
 * Events landing page
 */

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$per_page = get_option('posts_per_page');
$past_events = get_query_var('past_events', 0);
$event_program = get_query_var('event_program', '');
$event_focus_area = get_query_var('event_focus_area', '');
$total_events = \Firebelly\PostTypes\Event\get_num_events($past_events);
$total_pages = ceil($total_events / $per_page);

$page = get_page_by_path('/events');
$page_content = apply_filters('the_content', $page->post_content);
?>

<header>
  <div class="user-content">
    <?php echo $page_content; ?>
  </div>
</header>

<?php get_template_part('templates/page', 'image-header'); ?>
<div id="map" class="large hide"></div>

<section class="main">
  <ul>
    <li><a class="<?= $past_events ? '' : 'active' ?>" href="/events/"><h4 class="flag">Upcoming Events</h4></a></li> 
    <li><a class="<?= $past_events ? 'active' : '' ?> tab" href="/events/?past_events=1"><h4>Past Events</h4></a></li>  
  </ul>
  <form class="filters" action="/events" method="get">
    <div class="program-topic">Program: 
      <input type="hidden" name="past_events" value="<?= $past_events ?>">
      <select name="event_program">
      <option value="">ALL</option>
        <?php 
        $programs_related_to_events = $wpdb->get_results(
          "SELECT p.ID,p.post_title FROM {$wpdb->postmeta} pm 
          INNER JOIN {$wpdb->posts} p ON (p.ID=pm.meta_value) 
          INNER JOIN {$wpdb->posts} p2 ON (p2.ID=pm.post_id) 
          WHERE meta_key='_cmb2_related_program' AND p2.post_type='event' 
          GROUP BY pm.meta_value ORDER BY p.post_title"
        );
        foreach ($programs_related_to_events as $program):
        ?>
        <option <?= $event_program==$program->ID ? 'selected' : '' ?> value="<?= $program->ID ?>"><?= $program->post_title ?></option>
      <?php endforeach; ?>
      </select>
    </div>
    <div class="focus-area-topic">Focus Area:
      <select name="event_focus_area">
      <option value="">ALL</option>
        <?php $focus_areas = get_terms('focus_area');
        foreach ($focus_areas as $focus_area): ?>
        <option <?= $event_focus_area==$focus_area->slug ? 'selected' : '' ?> value="<?= $focus_area->slug ?>"><?= $focus_area->name ?></option>
      <?php endforeach; ?>
      </select>
    </div>
    <button class="button" type="submit">Filter</button>
  </form>

  <div class="events load-more-container article-list masonry">
    <?php echo \Firebelly\PostTypes\Event\get_events('', $event_focus_area, $event_program); ?>
  </div>
  
  <div class="load-more events button" data-page-at="<?= $paged ?>" data-past-events="<?= $past_events ?>" data-focus-area="<?= $event_focus_area ?>" data-program="<?= $event_program ?>" data-per-page="<?= $per_page ?>" data-total-pages="<?= $total_pages ?>"><a class="no-ajaxy" href="#">Load More</a></div>

</section>
<aside class="page-with-img">
    <?php include(locate_template('templates/thought-of-the-day.php')); ?>
</aside>
