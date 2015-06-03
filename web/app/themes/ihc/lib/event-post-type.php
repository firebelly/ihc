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
    'capability_type'     => 'event',
    'map_meta_cap'        => true
  );
  register_post_type( 'event', $args );

}
add_action( 'init', __NAMESPACE__ . '\post_type', 0 );

/**
 * Add capabilities to control permissions of Post Type via roles
 */
function add_capabilities() {
  $role_admin = get_role('administrator');
  // programs
  $role_admin->add_cap('edit_event');
  $role_admin->add_cap('read_event');
  $role_admin->add_cap('delete_event');
  $role_admin->add_cap('edit_events');
  $role_admin->add_cap('edit_others_events');
  $role_admin->add_cap('publish_events');
  $role_admin->add_cap('read_private_events');
  $role_admin->add_cap('delete_events');
  $role_admin->add_cap('delete_private_events');
  $role_admin->add_cap('delete_published_events');
  $role_admin->add_cap('delete_others_events');
  $role_admin->add_cap('edit_private_events');
  $role_admin->add_cap('edit_published_events');
  $role_admin->add_cap('create_events');
}
add_action('switch_theme', __NAMESPACE__ . 'add_capabilities');

/**
 * Custom admin columns for post type
 */
function edit_columns($columns){
  $columns = array(
    'cb' => '<input type="checkbox" />',
    'title' => 'Title',
    'event_dates' => 'Date',
    '_cmb2_venue' => 'Venue',
    'taxonomy-focus_area' => 'Focus Area',
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
    elseif ( $column == 'event_dates' ) {
      $timestamp_start = $custom['_cmb2_event_start'][0];
      $timestamp_end = !empty($custom['_cmb2_event_end'][0]) ? $custom['_cmb2_event_end'][0] : $timestamp_start;
      if ($timestamp_end != $timestamp_start) {
        $date_txt = date('m/d/Y g:iA', $timestamp_start) . ' – ' . date('m/d/Y g:iA', $timestamp_end);
      } else {
        $date_txt = date('m/d/Y g:iA', $timestamp_start);
      }
      echo $date_txt . ($timestamp_end < time() ? ' - <strong class="post-state">Past Event</strong>' : '');
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
          'id'      => $prefix . 'event_start',
          'type'    => 'text_datetime_timestamp',
      ),
      array(
          'name'    => 'End Date',
          // 'desc'    => '(Optional)',
          'id'      => $prefix . 'event_end',
          'type'    => 'text_datetime_timestamp',
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
      array(
          'name'    => 'Sponsor Organization(s)',
          'id'      => $prefix . 'sponsor',
          'type'    => 'wysiwyg',
          'options' => array(
            'textarea_rows' => 4,
          ),
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
          'type'    => 'text',
      ),
      array(
          'name'    => 'Registration URL',
          'desc'    => 'If set, shows "Register for Event" link, and adds "RSVP is required." to the Cost text.',
          'id'      => $prefix . 'registration_url',
          'type'    => 'text_url',
      ),
    ),
  );

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );

/**
 * Get Num Events, past or future
 */
function get_num_events($options=[]) {
  global $wpdb;
  // todo: also filter by focus_area and program in $options!
  $count = $wpdb->get_var($wpdb->prepare(
    "
    SELECT COUNT(*) FROM `wp_posts` wp
    INNER JOIN `wp_postmeta` wm ON (wm.`post_id` = wp.`ID` AND wm.`meta_key`='_cmb2_event_start')
    WHERE wp.post_status = 'publish'
    AND wp.post_type = 'event'
    AND wm.meta_value " . (!empty($options['past']) ? '<=' : '>') . " %s
    ",
    time()
  ));
  return $count;
}

/**
 * Get Events
 */
function get_events($options=[]) {
  if (empty($options['num_posts'])) $options['num_posts'] = get_option('posts_per_page');
  $args = [
    'numberposts' => $options['num_posts'],
    'post_type' => 'event',
    'meta_key' => '_cmb2_event_start',
    'orderby' => 'meta_value_num',
  ];
  // make sure we're only pulling upcoming or past events
  $args['order'] = !empty($_REQUEST['past_events']) ? 'DESC' : 'ASC';
  $args['meta_query'] = [
    [
      'key' => '_cmb2_event_start',
      'value' => time(),
      'compare' => (!empty($_REQUEST['past_events']) ? '<=' : '>')
    ]
  ];
  if (!empty($options['focus_area'])) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'focus_area',
            'field' => 'slug',
            'terms' => $options['focus_area'],
        )
    );
  }
  if (!empty($options['program'])) {
    $args['meta_query'][] = array(
      'key' => '_cmb2_related_program',
      'value' => array( (int)$options['program'] ),
      'compare' => 'IN',
    );
  }

  $event_posts = get_posts($args);
  if (!$event_posts) return false;
  $output = '';
  $show_view_all_button = (!empty($options['show_view_all_button']));
  foreach ($event_posts as $event_post):
    if (!empty($options['map-points'])):
      $event = get_event_details($event_post);
      $output .= '<span class="map-point" data-lat="' . $event->lat . '" data-lng="' . $event->lng . '" data-title="' . $event->title . '" data-desc="' . $event->desc . '" data-id="' . $event->ID . '"></span>';
    else:
      ob_start();
      include(locate_template('templates/article-event.php'));
      $output .= ob_get_clean();
    endif;
  endforeach;
  return $output;
}

/**
 * Geocode address for event and save in custom fields
 */
function geocode_address($post_id, $post='') {
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
    if(strcmp($status, 'OK')===0):
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

  $event_start = get_post_meta($event_post->ID, '_cmb2_event_start', true);
  $event_end = get_post_meta( $event_post->ID, '_cmb2_event_end', true);
  $venue = get_post_meta($event_post->ID, '_cmb2_venue', true);
  $start_time = date('g:iA', $event_start);
  // $gmtOffset = 60 * 60 * get_option('gmt_offset');

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
    'DTSTART:' . get_ical_date($event_start),
    'DTEND:' . (!empty($event_end) ? get_ical_date($event_end) : ''),
    'DTSTAMP:' . get_ical_date(strtotime($event_post->post_modified)),
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

function get_ical_date($time, $incl_time=true){
  return $incl_time ? date('Ymd\THis', $time) : date('Ymd', $time);
}

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
 * Add query vars for events
 */
function add_query_vars_filter($vars){
  $vars[] = "past_events";
  $vars[] = "filter_program";
  $vars[] = "filter_focus_area";
  return $vars;
}
add_filter( 'query_vars', __NAMESPACE__ . '\\add_query_vars_filter' );

/**
 * Helper function to populate event object for listings & single view
 */
function get_event_details($post) {
  $event = [
    'ID' => $post->ID,
    'title' => $post->post_title,
    'body' => apply_filters('the_content', $post->post_content),
    'event_start' => get_post_meta($post->ID, '_cmb2_event_start', true),
    'event_end' => get_post_meta( $post->ID, '_cmb2_event_end', true),
    'venue' => get_post_meta($post->ID, '_cmb2_venue', true),
    'sponsor' => get_post_meta($post->ID, '_cmb2_sponsor', true),
    'cost' => get_post_meta($post->ID, '_cmb2_cost', true),
    'registration_url' => get_post_meta($post->ID, '_cmb2_registration_url', true),
    'lat' => get_post_meta($post->ID, '_cmb2_lat', true),
    'lng' => get_post_meta($post->ID, '_cmb2_lng', true),
    'add_to_calendar_url' => admin_url('admin-ajax.php') . "?action=event_ics&amp;id={$post->ID}&amp;nc=" . time(),
  ];
  $event['start_time'] = date('g:iA', $event['event_start']);
  if (!empty($event['event_end']) && $event['event_end'] != $event['event_start']) {
    $event['time_txt'] = $event['start_time'] . '–' . date('g:iA', $event['event_end']);
  } else {
    $event['time_txt'] = $event['start_time'];
  }
  
  $event['archived'] = ($event['event_end'] < time());
  $event['desc'] = date('M d, Y @ ', $event['event_start']) . $event['time_txt']; // used in map pins
  $event['year'] = date('Y', $event['event_start']);

  $address = get_post_meta($post->ID, '_cmb2_address', true);
  $event['address'] = wp_parse_args($address, array(
      'address-1' => '',
      'address-2' => '',
      'city'      => '',
      'state'     => '',
      'zip'       => '',
   ));
  return (object)$event;
}

/**
 * Alter WP query for Event archive pages
 * if "past_events" is set, only shows archived events
 */
// currently site is just using get_events() in event-post-type.php
//
// function event_query($query){
//   global $wp_the_query;
//   if ($wp_the_query === $query && !is_admin() && is_post_type_archive('event')) {
//     $meta_query = array(
//       array(
//         'key' => '_cmb2_event_start',
//         'value' => time(),
//         'compare' => (get_query_var('past_events') ? '<=' : '>')
//       )
//     );
//     $query->set('meta_query', $meta_query);
//     $query->set('orderby', 'meta_value_num');
//     $query->set('meta_key', '_cmb2_event_start');
//     // show events oldest->newest
//     $query->set('order', (get_query_var('past_events') ? 'DESC' : 'ASC'));

//     // focus area?
//     if (get_query_var('event_focus_area')) {
//       $tax_query = array(
//         array(
//           'taxonomy' => 'focus_area',
//           'field' => 'id',
//           'terms' => get_query_var('event_focus_area')
//         )
//       );
//       $query->set('tax_query', $tax_query);
//     }
//   }
// }
// add_action('pre_get_posts', __NAMESPACE__ . '\\event_query');


/**
 * Handle AJAX response from CSV import form
 */
add_action( 'wp_ajax_event_csv_upload', __NAMESPACE__ . '\event_csv_upload' );
function event_csv_upload() {
  global $wpdb;
  require_once 'import/event-csv-importer.php';

  $importer = new \EventCSVImporter;
  $return = $importer->handle_post();

  // Spits out json-encoded $return & die()s
  wp_send_json($return);
}

/**
 * Show link to CSV Import page
 */
add_action('admin_menu', __NAMESPACE__ . '\import_csv_admin_menu');
function import_csv_admin_menu() {
  add_submenu_page('edit.php?post_type=event', 'Import CSV', 'Import CSV', 'manage_options', 'csv-importer', __NAMESPACE__ . '\import_csv_admin_form');
}

/**
 * Basic CSV Importer admin page
 */
function import_csv_admin_form() {
?>
  <div class="wrap">
    <h2>Import CSV</h2>
    <form method="post" id="csv-upload-form" enctype="multipart/form-data" action="">
      <fieldset>
        <label for="csv_import">Upload file(s):</label>
        <input name="csv_import[]" id="csv-import" type="file" multiple>
        <div id="filedrag">or drop files here</div>
      </fieldset>
      <div class="progress-bar"><div class="progress-done"></div></div>
      <input type="hidden" name="action" value="event_csv_upload">
      <p class="submit"><input type="submit" class="button" id="csv-submit" name="submit" value="Import"></p>
    </form>

   <div class="import-notes">
      <h3>Uses these fields from Raiser's Edge export:</h3>
<pre>
Ev_Group                        (Related Program)
Ev_Start_Date
Ev_End_Date
Ev_Start_Time
Ev_End_Time
Ev_Note_1_01_Actual_Notes       (Body)
Ev_Note_1_02_Actual_Notes       (Fee)
Ev_Note_1_03_Actual_Notes       (Title)
Ev_Prt_1_01_CnBio_Name          (Sponsor)
Ev_Prt_1_02_CnBio_Name          (Location)
Ev_Prt_1_02_CnAdrPrf_Addrline1
Ev_Prt_1_02_CnAdrPrf_Addrline2
Ev_Prt_1_02_CnAdrPrf_City
Ev_Prt_1_02_CnAdrPrf_State
Ev_Prt_1_02_CnAdrPrf_ZIP
Ev_Prt_1_02_CnAdrPrf_County
</pre>
  </div>
<?php
}