<?php
/**
 * Filter dropdowns for Events and Blog
 */

$filter_program = get_query_var('filter_program', '');
$filter_focus_area = get_query_var('filter_focus_area', '');
$past_events = get_query_var('past_events', 0);
$post_type = is_post_type_archive('event') ? 'event' : 'post';
?>
  <form class="filters" action="/<?= $post_type == 'event' ? 'events' : 'news' ?>/" method="get">
    <div class="program-topic">Program: 
      <input type="hidden" name="past_events" value="<?= $past_events ?>">
      <select name="filter_program">
      <option value="">ALL</option>
        <?php 
        $programs_related = $wpdb->get_results(
          "SELECT p.ID,p.post_title FROM {$wpdb->postmeta} pm 
          INNER JOIN {$wpdb->posts} p ON (p.ID=pm.meta_value) 
          INNER JOIN {$wpdb->posts} p2 ON (p2.ID=pm.post_id) 
          WHERE meta_key='_cmb2_related_program' AND p2.post_type='{$post_type}' 
          GROUP BY pm.meta_value ORDER BY p.post_title"
        );
        foreach ($programs_related as $program):
        ?>
        <option <?= $filter_program==$program->ID ? 'selected' : '' ?> value="<?= $program->ID ?>"><?= $program->post_title ?></option>
      <?php endforeach; ?>
      </select>
    </div>
    <div class="focus-area-topic">Focus Area:
      <select name="filter_focus_area">
      <option value="">ALL</option>
        <?php $focus_areas = get_terms('focus_area');
        foreach ($focus_areas as $focus_area): ?>
        <option <?= $filter_focus_area==$focus_area->slug ? 'selected' : '' ?> value="<?= $focus_area->slug ?>"><?= $focus_area->name ?></option>
      <?php endforeach; ?>
      </select>
    </div>
    <button class="button" type="submit">Filter</button>
  </form>
