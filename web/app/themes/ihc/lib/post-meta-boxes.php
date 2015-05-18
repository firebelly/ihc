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

  $meta_boxes['post_category'] = array(
    'id'            => 'post_category',
    'title'         => __( 'Category', 'cmb2' ),
    'object_types'  => array( 'post', ),
    'context'       => 'side',
    'priority'      => 'default',
    'show_names'    => false,
    'fields'        => array(
      array(
          'id'       => $prefix . 'post_category',
          'type'     => 'taxonomy_select',
          'taxonomy' => 'category',
      ),
    ),
  );

  $meta_boxes['focus_area'] = array(
    'id'            => 'focus_area',
    'title'         => __( 'Focus Area', 'cmb2' ),
    'object_types'  => array( 'event', 'post', 'program', ),
    'context'       => 'side',
    'priority'      => 'default',
    'show_names'    => false,
    'fields'        => array(
      array(
          // 'name'     => 'Focus Area',
          'id'       => $prefix . 'focus_area',
          'type'     => 'taxonomy_select',
          'taxonomy' => 'focus_area',
      ),
    ),
  );

  $meta_boxes['related_program'] = array(
    'id'            => 'related_program',
    'title'         => __( 'Related Program', 'cmb2' ),
    'object_types'  => array( 'event', 'post', ),
    'context'       => 'side',
    'priority'      => 'default',
    'show_names'    => true,
    'fields'        => array(
      array(
          // 'name'     => 'If set, will trump finding a related program by Focus Area',
          // 'desc'     => 'Select Program(s)...',
          'id'       => $prefix . 'related_program',
          'type'     => 'select',
          'show_option_none' => true,
          // 'type'     => 'pw_multiselect', // currently multiple=true is causing issues with pw_multiselect -nate 4/30/15 
          // 'multiple' => true, 
          'options'  => \Firebelly\CMB2\get_post_options(['post_type' => 'program', 'numberposts' => -1]),
      ),
    ),
  );

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );

/**
 * Remove unused WP Tags UI in admin, also hide default Category meta_box to use CMB2 select
 */
function remove_sub_menus() {
  remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=post_tag');
  remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=category');
  remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=focus_area');
  remove_submenu_page('edit.php?post_type=event', 'edit-tags.php?taxonomy=focus_area&amp;post_type=event');
}
function remove_post_metaboxes() {
  remove_meta_box( 'focus_areadiv','event','normal' ); // hide default Focus Area UI
  remove_meta_box( 'focus_areadiv','program','normal' );
  remove_meta_box( 'focus_areadiv','post','normal' );
  remove_meta_box( 'tagsdiv-post_tag','post','normal' ); // hide Tags UI
  remove_meta_box( 'categorydiv','post','normal' ); // hide Category UI
}
add_action('admin_menu', __NAMESPACE__ . '\remove_sub_menus');
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
