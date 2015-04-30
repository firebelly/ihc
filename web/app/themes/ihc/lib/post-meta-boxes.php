<?php
/**
 * Extra fields for Posts
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