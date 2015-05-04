<?php
/**
 * Extra fields, admin changes, and filters for Posts
 */

namespace Firebelly\PostTypes\Posts;

// // Custom CMB2 fields for post type
function metaboxes( array $meta_boxes ) {
  $prefix = '_cmb2_'; // Start with underscore to hide from custom fields list

  $meta_boxes['post_metabox'] = array(
    'id'            => 'post_metabox',
    'title'         => __( 'Extra Fields', 'cmb2' ),
    'object_types'  => array( 'post', ), // Post type
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true, // Show field names on the left
    'fields'        => array(
      // array(
      //   'name' => 'External Link URL',
      //   'desc' => 'Opens in new window when clicking In The News posts',
      //   'id'   => $prefix . 'url',
      //   'type' => 'text_url',
      // ),
      array(
        'name' => 'Byline area',
        'desc' => 'Shows above main post content',
        'id'   => $prefix . 'post_byline',
        'type' => 'wysiwyg',
        'options' => array(
          'textarea_rows' => 4,
        ),
      ),
      array(
        'name' => 'Publication Date',
        'desc' => 'Overrides Post Date if set (migrated from old news posts)',
        'id'   => $prefix . 'publication_date',
        'type' => 'text_date',
      ),
    ),
  );

  $meta_boxes['related_program'] = array(
    'id'            => 'related_program',
    'title'         => __( 'Related Program(s)', 'cmb2' ),
    'object_types'  => array( 'event', 'post', ),
    'context'       => 'side',
    'priority'      => 'low',
    'show_names'    => true,
    'fields'        => array(
      array(
          // 'name'     => 'If set, will trump finding a related program by Focus Area',
          'desc'     => 'Select Program(s)...',
          'id'       => $prefix . 'related_program',
          'type'     => 'multicheck',
          // 'type'     => 'pw_multiselect', // currently multiple=true is causing issues with pw_multiselect -nate 4/30/15 
          'multiple' => true, 
          'options'  => \Firebelly\CMB2\get_post_options(['post_type' => 'program', 'numberposts' => -1]),
      ),
    ),
  );


  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );

/**
 * Remove tags
 */
add_action('admin_menu', __NAMESPACE__ . '\remove_sub_menus');
function remove_sub_menus() {
    remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=post_tag');
}
function remove_post_metaboxes() {
  remove_meta_box( 'tagsdiv-post_tag','post','normal' ); // Tags Metabox
}
add_action('admin_menu', __NAMESPACE__ . '\remove_post_metaboxes');

/**
 * Add filter for Focus Area and Related Program(s)
 */
function news_filters($query){
  global $wp_the_query;
  if ($wp_the_query === $query && !is_admin() && !is_post_type_archive('event') && (is_home() || is_archive())) {

    // Filter by focus area?
    if (get_query_var('filter_focus_area')) {
      $tax_query = array(
        array(
          'taxonomy' => 'focus_area',
          'field' => 'slug',
          'terms' => get_query_var('filter_focus_area')
        )
      );
      $query->set('tax_query', $tax_query);
    }

    // Filter by program?
    if (get_query_var('filter_program')) {
      $meta_query = array(
        array(
          'key' => '_cmb2_related_program',
          'value' => [get_query_var('filter_program')],
          'compare' => 'IN',
        )
      );
      $query->set('meta_query', $meta_query);
    }
  }
}
add_action('pre_get_posts', __NAMESPACE__ . '\\news_filters');
