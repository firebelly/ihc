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

  $meta_boxes['division'] = array(
    'id'            => 'division',
    'title'         => __( 'Division', 'cmb2' ),
    'object_types'  => array( 'event', 'post', 'program', ),
    'context'       => 'side',
    'priority'      => 'default',
    'show_names'    => false,
    'fields'        => array(
      array(
          // 'name'     => 'Focus Area',
          'id'       => $prefix . 'division',
          'type'     => 'taxonomy_select',
          'taxonomy' => 'division',
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

  // Custom fields on Support Us page
  $meta_boxes['support_us_blocks'] = array(
    'id'            => 'support_us_blocks',
    'title'         => __( 'Support Us Blocks', 'cmb2' ),
    'object_types'  => array( 'page', ),
    'show_slugs'    => array( 'support-us' ),
    'show_on_cb'    => __NAMESPACE__ . '\\cmb2_show_for_slugs',
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
    'fields'        => array(
      array(
        'id'      => '_mcb-support-us-page-block-1',
        'name'    => __( 'Support Us Block 1', 'cmb2' ),
        'type'    => 'wysiwyg',
        'options' => array(
          'textarea_rows' => 10,
        ),
      ),
      array(
        'id'      => '_mcb-support-us-page-block-2',
        'name'    => __( 'Support Us Block 2', 'cmb2' ),
        'type'    => 'wysiwyg',
        'options' => array(
          'textarea_rows' => 10,
        ),
      ),
      array(
        'id'      => '_mcb-support-us-page-block-3',
        'name'    => __( 'Support Us Block 3', 'cmb2' ),
        'type'    => 'wysiwyg',
        'options' => array(
          'textarea_rows' => 10,
        ),
      ),
      array(
        'name'    => __( 'Support Us Sidebar', 'cmb2' ),
        'id'      => '_mcb-support-us-sidebar',
        'type'    => 'wysiwyg',
        'options' => array(
          'textarea_rows' => 10,
        ),
      ),
    ),
  );

  // Custom fields on Our Work page
  $meta_boxes['our_work_blocks'] = array(
    'id'            => 'our_work_blocks',
    'title'         => __( 'Our Work Blocks', 'cmb2' ),
    'object_types'  => array( 'page', ),
    'show_slugs'    => array( 'our-work' ),
    'show_on_cb'    => __NAMESPACE__ . '\\cmb2_show_for_slugs',
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
    'fields'        => array(
      array(
        'id'      => '_mcb-our-work-sidebar',
        'name'    => __( 'Our Work Sidebar', 'cmb2' ),
        'type'    => 'wysiwyg',
      ),
    ),
  );

  // Custom sidebar field for all pages
  $meta_boxes['general_page_blocks'] = array(
    'id'            => 'general_page_blocks',
    'title'         => __( 'Page Blocks', 'cmb2' ),
    'object_types'  => array( 'page', ),
    'exclude_slugs' => array( 'our-work', 'support-us' ), // hide on pages that have custom sidebars (holdover from old multiple content blocks setup)
    'show_on_cb'    => __NAMESPACE__ . '\\cmb2_exclude_for_slugs',
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
    'fields'        => array(
      array(
        'id'      => '_mcb-sidebar-content',
        'name'    => __( 'Page Sidebar', 'cmb2' ),
        'type'    => 'wysiwyg',
        'options' => array(
          'textarea_rows' => 10,
        ),
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
  remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=division');
  remove_submenu_page('edit.php?post_type=event', 'edit-tags.php?taxonomy=focus_area&amp;post_type=event');
  remove_submenu_page('edit.php?post_type=thought', 'edit-tags.php?taxonomy=focus_area&amp;post_type=thought');
  remove_submenu_page('edit.php?post_type=event', 'edit-tags.php?taxonomy=division&amp;post_type=event');
  remove_submenu_page('edit.php?post_type=thought', 'edit-tags.php?taxonomy=division&amp;post_type=thought');
}
function remove_post_metaboxes() {
  remove_meta_box( 'focus_areadiv','event','normal' ); // hide default Focus Area UI
  remove_meta_box( 'focus_areadiv','program','normal' );
  remove_meta_box( 'focus_areadiv','post','normal' );
  remove_meta_box( 'divisiondiv','event','normal' ); // hide default Focus Area UI
  remove_meta_box( 'divisiondiv','program','normal' );
  remove_meta_box( 'divisiondiv','post','normal' );
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

    // Filter by division?
    if (get_query_var('filter_division')) {
      $tax_query = array(
        array(
          'taxonomy' => 'division',
          'field' => 'slug',
          'terms' => get_query_var('filter_division')
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

/**
 * Exclude metabox on specific slugs
 * @param  object $cmb CMB2 object
 * @return bool        True/false whether to show the metabox
 */
function cmb2_exclude_for_slugs( $cmb ) {
  $slugs_to_exclude = $cmb->prop( 'exclude_slugs', array() );
  $post_slug = get_post_field( 'post_name', $cmb->object_id() );
  $excluded = in_array( $post_slug, $slugs_to_exclude, true );
  return ! $excluded;
}

/**
 * Show metabox on specific slugs
 * @param  object $cmb CMB2 object
 * @return bool        True/false whether to show the metabox
 */
function cmb2_show_for_slugs( $cmb ) {
  $slugs_to_show = $cmb->prop( 'show_slugs', array() );
  $post_slug = get_post_field( 'post_name', $cmb->object_id() );
  $show = in_array( $post_slug, $slugs_to_show, true );
  return $show;
}
