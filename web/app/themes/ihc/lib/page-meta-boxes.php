<?php
/**
 * Extra fields for Pages
 */

namespace Firebelly\PostTypes\Pages;

// Custom CMB2 fields for post type
function metaboxes( array $meta_boxes ) {
  $prefix = '_cmb2_'; // Start with underscore to hide from custom fields list

  $meta_boxes['page_metabox'] = array(
    'id'            => 'page_metabox',
    'title'         => __( 'Extra Fields', 'cmb2' ),
    'object_types'  => array( 'page', 'program' ), // Post types to show these fields on
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true, // Show field names on the left
    'fields'        => array(
      
      // General page fields
      array(
        'name' => 'Header Banner Text',
        'desc' => 'Shows in banner above Header Text for Pages',
        'id'   => $prefix . 'header_banner_text',
        'type' => 'text',
      ),
      array(
        'name' => 'Header Text',
        'desc' => 'Shows at top of page behind featured image',
        'id'   => $prefix . 'header_text',
        'type' => 'wysiwyg',
      ),
      array(
        'name' => 'Secondary Header Text',
        'desc' => 'Shows below Header Text (as image caption on homepage)',
        'id'   => $prefix . 'secondary_header_text',
        'type' => 'wysiwyg',
      ),
      array(
        'name' => 'Content Banner Text',
        'desc' => 'Shows in banner above content',
        'id'   => $prefix . 'content_banner_text',
        'type' => 'text',
      ),

    ),
  );

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );

