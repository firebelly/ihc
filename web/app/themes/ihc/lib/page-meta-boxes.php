<?php
/**
 * Extra fields for Pages
 */

namespace Firebelly\PostTypes\Pages;

// Custom CMB2 fields for post type
function metaboxes( array $meta_boxes ) {
  $prefix = '_cmb2_';

  $meta_boxes['page_header_metabox'] = array(
    'id'            => 'page_header_metabox',
    'title'         => __( 'Page Banners', 'cmb2' ),
    'object_types'  => array( 'page' ),
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
    'fields'        => array(
      array(
        'name' => 'Header Banner Text',
        'desc' => 'Shows in banner above Header Text',
        'id'   => $prefix . 'header_banner_text',
        'type' => 'textarea_small',
      ),
      array(
        'name' => 'Content Banner Text',
        'desc' => 'Shows in banner above main content',
        'id'   => $prefix . 'content_banner_text',
        'type' => 'text',
      ),
    ),
  );

  $meta_boxes['page_metabox'] = array(
    'id'            => 'page_metabox',
    'title'         => __( 'Header Text', 'cmb2' ),
    'object_types'  => array( 'page', 'program' ),
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
    'fields'        => array(
      
      // Header fields
      array(
        'name' => 'Header Text',
        'desc' => 'Shows at top of page behind featured image',
        'id'   => $prefix . 'header_text',
        'type' => 'wysiwyg',
        'options' => array(
          'textarea_rows' => 6,
        ),
      ),
      array(
        'name' => 'Secondary Header Text',
        'desc' => 'Shows below Header Text (as image caption on homepage)',
        'id'   => $prefix . 'secondary_header_text',
        'type' => 'wysiwyg',
        'options' => array(
          'textarea_rows' => 4,
        ),
      ),
    ),
  );

  $meta_boxes['page_secure_metabox'] = array(
    'id'           => 'secure_post_metabox',
    'title'        => 'Secure page?',
    'object_types' => array( 'page', 'post', ),
    'context'      => 'side',
    'priority'     => 'low',
    'show_names'   => true,
    'fields'       => array(
      array(
        'desc' => 'Make this page secure',
        // 'desc' => '',
        'id'   => $prefix . 'force_ssl',
        'type' => 'checkbox',
      ),
    ),
  );

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );
