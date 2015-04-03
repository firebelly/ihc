<?php
/**
 * Event post type
 */

namespace Firebelly\PostTypes\Event;

// Custom image size for post type?
// add_image_size( 'event-thumb', 350, null, null );

/**
 * Register Custom Post Type
 */
function post_type() {

  $labels = array(
    'name'                => 'Events',
    'singular_name'       => 'Event',
    'menu_name'           => 'Events',
    'parent_item_colon'   => '',
    'all_items'           => 'All Events',
    'view_item'           => 'View Event',
    'add_new_item'        => 'Add New Event',
    'add_new'             => 'Add New',
    'edit_item'           => 'Edit Event',
    'update_item'         => 'Update Event',
    'search_items'        => 'Search Events',
    'not_found'           => 'Not found',
    'not_found_in_trash'  => 'Not found in Trash',
  );
  $rewrite = array(
    'slug'                => 'events',
    'with_front'          => false,
    'pages'               => true,
    'feeds'               => true,
  );
  $args = array(
    'label'               => 'event',
    'description'         => 'Events',
    'labels'              => $labels,
    'supports'            => array( 'title', 'editor'),
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'menu_position'       => 20,
    'menu_icon'           => 'dashicons-admin-post',
    'can_export'          => false,
    'has_archive'         => true,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'rewrite'             => $rewrite,
    'capability_type'     => 'page',
  );
  register_post_type( 'event', $args );

}
add_action( 'init', __NAMESPACE__ . '\post_type', 0 );

/**
 * Custom admin columns for post type
 */
function edit_columns($columns){
  $columns = array(
    'cb' => '<input type="checkbox" />',
    'title' => 'Title',
    '_cmb2_event_timestamp' => 'Date',
    '_cmb2_venue' => 'Venue',
    'taxonomy-focus_area' => 'Focus Area(s)',
  );
  return $columns;
}
add_filter('manage_event_posts_columns', __NAMESPACE__ . '\edit_columns');

function custom_columns($column){
  global $post;
  if ( $post->post_type == 'event' ) {
    $custom = get_post_custom();
    if ( $column == 'featured_image' )
      echo the_post_thumbnail( 'event-thumb' );
    elseif ( $column == '_cmb2_event_timestamp' ) {
      $timestamp = $custom[$column][0];
      echo date( 'm/d/Y g:iA', $timestamp ) . ($timestamp < time() ? ' - <strong class="post-state">Past Event</strong>' : '');
    } else {
      if (array_key_exists($column, $custom))
        echo $custom[$column][0];
    }
  }
}
add_action('manage_posts_custom_column',  __NAMESPACE__ . '\custom_columns');

/**
 * CMB2 custom fields
 */
function metaboxes( array $meta_boxes ) {
  $prefix = '_cmb2_'; // Start with underscore to hide from custom fields list

  $meta_boxes['event_when'] = array(
    'id'            => 'event_when',
    'title'         => __( 'Event When', 'cmb2' ),
    'object_types'  => array( 'event', ), // Post type
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true, // Show field names on the left
    'fields'        => array(
      array(
          'name'    => 'Start Date',
          'id'      => $prefix . 'event_timestamp',
          'type'    => 'text_datetime_timestamp',
      ),
      array(
          'name'    => 'End Time',
          'desc'    => '(Optional)',
          'id'      => $prefix . 'end_time',
          'type'    => 'text_time',
      ),
    ),
  );

  $meta_boxes['event_where'] = array(
    'id'            => 'event_where',
    'title'         => __( 'Event Where', 'cmb2' ),
    'object_types'  => array( 'event', ), // Post type
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true, // Show field names on the left
    'fields'        => array(
      array(
          'name'    => 'Venue',
          'id'      => $prefix . 'venue',
          'type'    => 'text',
      ),
      array(
          'name'    => 'Address',
          'id'      => $prefix . 'address',
          'type'    => 'address',
      ),
      // array(
      //     'name'    => 'Lat',
      //     'id'      => $prefix . 'lat',
      //     'type'    => 'text_small',
      // ),
      // array(
      //     'name'    => 'Lng',
      //     'id'      => $prefix . 'lng',
      //     'type'    => 'text_small',
      // ),
    ),
  );

  $meta_boxes['event_details'] = array(
    'id'            => 'event_details',
    'title'         => __( 'Event Details', 'cmb2' ),
    'object_types'  => array( 'event', ), // Post type
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true, // Show field names on the left
    'fields'        => array(
      array(
          'name'    => 'Cost',
          'desc'    => 'Leave blank or set to 0 to show "Free. Open to the public."',
          'id'      => $prefix . 'cost',
          'type'    => 'text_small',
      ),
      array(
          'name'    => 'Registration URL',
          'desc'    => 'If set, shows "Register for Event" link.',
          'id'      => $prefix . 'registration_url',
          'type'    => 'text_url',
      ),
    ),
  );

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );

/**
 * Get Events
 */
function get_events($num, $focus_area='') {
  $args = [
    'numberposts' => $num,
    'post_type' => 'event',
    'meta_key' => '_cmb2_event_timestamp',
    'orderby' => 'meta_value_num',
    'order' => 'ASC',
    'meta_query' => [
      'key' => '_cmb2_event_timestamp',
      'value' => time(),
      'compare' => '>'
    ]
  ];
  if ($focus_area != '') {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'focus_area',
            'field' => 'slug',
            'terms' => $focus_area,
        )
    );
  }

  $event_posts = get_posts($args);
  if (!$event_posts) return false;
  $output = '<div class="events">';
  foreach ($event_posts as $event_post):
    ob_start();
    include(locate_template('templates/article-event.php'));
    $output .= ob_get_clean();
  endforeach;
  $output .= '</div>';
  return $output;
}

function geocode_address($post_id, $post) {
  $address = get_post_meta($post_id, '_cmb2_address', 1);
  $address = wp_parse_args($address, array(
      'address-1' => '',
      'address-2' => '',
      'city'      => '',
      'state'     => '',
      'zip'       => '',
   ));

  if (!empty($address['address-1'])):
    $address_combined = $address['address-1'] . ' ' . $address['address-1'] . ' ' . $address['city'] . ', ' . $address['state'] . ' ' . $address['zip'];
    $request_url = "http://maps.google.com/maps/api/geocode/xml?sensor=false&address=" . urlencode($address_combined);

    $xml = simplexml_load_file($request_url);
    $status = $xml->status;
    if(strcmp($status, 'OK') == 0):
        $lat = $xml->result->geometry->location->lat;
        $lng = $xml->result->geometry->location->lng;
        update_post_meta($post_id, '_cmb2_lat', (string)$lat);
        update_post_meta($post_id, '_cmb2_lng', (string)$lng);
    endif;
  endif;
}
add_action('save_post_event', __NAMESPACE__ . '\\geocode_address', 20, 2);



/**
 * Generate an iCalendar .ics file for event
 */
function event_ics() {
  $event_id = preg_replace('/\D/', '', $_REQUEST['id']);
  if (!$event_id) die('No ID sent');

  $event_post = get_post($event_id);
  if (!$event_post) die('No Event found');

  if (!empty($_REQUEST['plaintext'])) { // for debugging
      header('Content-Type: text/plain; charset=utf-8');
  } else {
      header('Content-Type: text/calendar; charset=utf-8');
      header('Content-Disposition: attachment; filename="ihs-event-' . $event_post->post_name . '.ics"');
  }

  $event_timestamp = get_post_meta($event_post->ID, '_cmb2_event_timestamp', true);
  $venue = get_post_meta($event_post->ID, '_cmb2_venue', true);
  $end_time = get_post_meta( $event_post->ID, '_cmb2_end_time', true);
  $start_time = date('g:iA', $event_timestamp);
  $gmtOffset = 60 * 60 * get_option('gmt_offset');

  $ics = [
    'BEGIN:VCALENDAR',
    'VERSION:2.0',
    'METHOD:PUBLISH',
    'CALSCALE:GREGORIAN',
    'PRODID:-//IHC Events//1.0//EN',
    'BEGIN:VEVENT',
    "UID:event-{$event_id}@" . parse_url(get_option('home'), PHP_URL_HOST),
    'SUMMARY:' . $event_post->post_title,
    'URL:' . get_permalink($event_post->ID),
    'LOCATION:' . $venue,
    'DTSTART:' . date('YmdTHisZ', $event_timestamp - $gmtOffset),
    // 'DTEND:' . date('YmdTHisZ', $event_end_timestamp - $gmtOffset),
    'DTSTAMP:' . date('YmdTHisZ', strtotime($event_post->post_modified) - $gmtOffset),
    'END:VEVENT',
    'END:VCALENDAR',
  ];

  foreach ($ics as $line) {
    echo wordwrap("{$line}\n", 75, "\n\t", TRUE);
  }

  die();
}
add_action('wp_ajax_event_ics', __NAMESPACE__ . '\\event_ics');
add_action('wp_ajax_nopriv_event_ics', __NAMESPACE__ . '\\event_ics');

// custom URLs like /events/2014/11/the-event-name
// function event_rewrite_tag() {
//   global $wp_rewrite;
//   $event_structure = '/events/%year%/%monthnum%/%postname%';
//   $wp_rewrite->add_rewrite_tag("%event%", '([^/]+)', "event=");
//   $wp_rewrite->add_permastruct('event', $event_structure, true);
// }
// add_action('init', __NAMESPACE__ . '\\event_rewrite_tag', 10, 0);

// add_filter('post_type_link', __NAMESPACE__ . '\\event_permalink', 10, 3);
// // Adapted from get_permalink function in wp-includes/link-template.php
// function event_permalink($permalink, $post_id, $leavename) {
//     $post = get_post($post_id);
//     $rewritecode = array(
//         '%year%',
//         '%monthnum%',
//         '%day%',
//         $leavename? '' : '%postname%',
//         '%post_id%',
//     );
 
//     if ( '' != $permalink && !in_array($post->post_status, array('draft', 'pending', 'auto-draft')) ) {
//         $unixtime = strtotime($post->post_date);
     
//         $date = explode(" ",date('Y m d H i s', $unixtime));
//         $rewritereplace =
//         array(
//             $date[0],
//             $date[1],
//             $date[2],
//             $post->post_name,
//             $post->ID,
//         );
//         $permalink = str_replace($rewritecode, $rewritereplace, $permalink);
//     } else { // if they're not using the fancy permalink option
//     }
//     return $permalink;
// }

/**
 * Add query var "past_events"
 */
function add_query_vars_filter($vars){
  $vars[] = "past_events";
  return $vars;
}
add_filter( 'query_vars', __NAMESPACE__ . '\\add_query_vars_filter' );

/**
 * Alter WP query for Event archive pages
 * if "past_events" is set, only shows archived events
 */
function event_query($query){
  global $wp_the_query;
  if ($wp_the_query === $query && !is_admin() && is_post_type_archive('event')) {
    $meta_query = array(
      array(
        'key' => '_cmb2_event_timestamp',
        'value' => time(),
        'compare' => (get_query_var('past_events') ? '<=' : '>')
      )
    );
    $query->set('meta_query', $meta_query);
    $query->set('orderby', 'meta_value_num');
    $query->set('meta_key', '_cmb2_event_timestamp');
    // show events oldest->newest
    $query->set('order', (get_query_var('past_events') ? 'DESC' : 'ASC'));
  }
}
add_action('pre_get_posts', __NAMESPACE__ . '\\event_query');
