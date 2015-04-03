<?php
namespace Firebelly\Ajax;

/**
 * Add wp_ajax_url variable to global js scope
 */
function wp_ajax_url() {
  wp_localize_script('sage_js', 'wp_ajax_url', admin_url( 'admin-ajax.php'));
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\wp_ajax_url', 100);

/**
 * AJAX load more events
 */
function get_event_posts() {
  $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;
  $per_page = !empty($_REQUEST['per_page']) ? $_REQUEST['per_page'] : 2; // get_option('posts_per_page');
  $offset = ($page-1) * $per_page;
  $past_events = !empty($_REQUEST['past_events']) ? $_REQUEST['past_events'] : 0;
  // die($page . $per_page . $offset . $past_events);
  $event_posts = get_posts([
    'meta_query' => [
      [
        'key' => '_cmb2_event_timestamp',
        'value' => time(),
        'compare' => ($past_events ? '<=' : '>')
      ]
    ],
    'offset' => $offset,
    'posts_per_page' => $per_page,
    'post_type' => 'event',
    'orderby' => 'meta_value_num',
    'order' => ($past_events ? 'DESC' : 'ASC'),
    'meta_key' => '_cmb2_event_timestamp',
  ]);

  if ($event_posts): 
    foreach ($event_posts as $event_post)
      include(locate_template('templates/article-event.php'));
  endif;

  if (is_ajax()) die();
}
add_action( 'wp_ajax_get_event_posts', __NAMESPACE__ . '\\get_event_posts' );
add_action( 'wp_ajax_nopriv_get_event_posts', __NAMESPACE__ . '\\get_event_posts' );

/**
 * Silly ajax helper, returns true if xmlhttprequest
 */
function is_ajax() {
  return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
}



// add_action( 'wp_ajax_get_radar_posts', __NAMESPACE__ . '\\get_radar_posts' );
// add_action( 'wp_ajax_nopriv_get_radar_posts', __NAMESPACE__ . '\\get_radar_posts' );

// function get_radar_posts() {
//   include(locate_template('templates/recent-radar-and-news.php'));

//   die();
// }
