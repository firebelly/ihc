<?php
/**
 * Extra fields for Posts
 */

namespace Firebelly\PostTypes\Posts;

// // Custom CMB2 fields for post type
// function metaboxes( array $meta_boxes ) {
//   $prefix = '_cmb2_'; // Start with underscore to hide from custom fields list

//   $meta_boxes['post_metabox'] = array(
//     'id'            => 'post_metabox',
//     'title'         => __( 'Extra Fields', 'cmb2' ),
//     'object_types'  => array( 'post', ), // Post type
//     'context'       => 'normal',
//     'priority'      => 'high',
//     'show_names'    => true, // Show field names on the left
//     'fields'        => array(
//       array(
//         'name' => 'External Link URL',
//         'desc' => 'Opens in new window when clicking In The News posts',
//         'id'   => $prefix . 'url',
//         'type' => 'text_url',
//       ),
//     ),
//   );

//   return $meta_boxes;
// }
// add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );
