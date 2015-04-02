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
    'slug'                => '',
    'with_front'          => false,
    'pages'               => false,
    'feeds'               => false,
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
    'has_archive'         => false,
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
    '_cmb2_event_date' => 'Date',
    '_cmb2_start_time' => 'Start Time',
    '_cmb2_venue' => 'Location',
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
    else {
      $custom = get_post_custom();
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
          'id'      => $prefix . 'event_date',
          'type'    => 'text_date',
      ),
      array(
          'name'    => 'Start Time',
          'id'      => $prefix . 'start_time',
          'type'    => 'text_time',
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
      //     'name'    => 'Lon',
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
  $args = array(
    'numberposts' => $num,
    'post_type' => 'event',
    'meta_key' => '_cmb2_event_date',
    'orderby' => 'meta_value',
    );
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
    $body = apply_filters('the_content', $event_post->post_content);
    $start_time = get_post_meta($event_post->ID, '_cmb2_start_time', true);
    $end_time = get_post_meta( $event_post->ID, '_cmb2_end_time', true);
    $time_txt = $start_time . (!empty($end_time) ? '–'.$end_time : '');
    $registration_url = get_post_meta($event_post->ID, '_cmb2_registration_url', true);
    $address = get_post_meta($event_post->ID, '_cmb2_address', true);
    $address = wp_parse_args($address, array(
        'address-1' => '',
        'address-2' => '',
        'city'      => '',
        'state'     => '',
        'zip'       => '',
     ));
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
        update_post_meta($post_id, '_cmb2_lat', $lat);
        update_post_meta($post_id, '_cmb2_lng', $lng);
    endif;
  endif;
}
add_action('save_post_event', __NAMESPACE__ . '\\geocode_address', 20, 2);
