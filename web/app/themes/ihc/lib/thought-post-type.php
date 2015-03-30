<?php
/**
 * Thought post type
 */

namespace Firebelly\PostTypes\Thought;

// Custom image size for post type?
add_image_size( 'thought-thumb', 350, null, null );

// Register Custom Post Type
function post_type() {

  $labels = array(
    'name'                => 'Thoughts',
    'singular_name'       => 'Thought',
    'menu_name'           => 'Thoughts',
    'parent_item_colon'   => '',
    'all_items'           => 'All Thoughts',
    'view_item'           => 'View Thought',
    'add_new_item'        => 'Add New Thought',
    'add_new'             => 'Add New',
    'edit_item'           => 'Edit Thought',
    'update_item'         => 'Update Thought',
    'search_items'        => 'Search Thoughts',
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
    'label'               => 'thought',
    'description'         => 'Thoughts',
    'labels'              => $labels,
    'supports'            => array( 'editor'),
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
  register_post_type( 'thought', $args );

}
add_action( 'init', __NAMESPACE__ . '\post_type', 0 );

// Custom admin columns for post type
function edit_columns($columns){
  $columns = array(
    'cb' => '<input type="checkbox" />',
    'content' => 'Content',
    '_cmb2_author' => 'Author',
    'taxonomy-focus_areas' => 'Focus Area(s)',
  );
  return $columns;
}
add_filter('manage_thought_posts_columns', __NAMESPACE__ . '\edit_columns');

function custom_columns($column){
  global $post;
  if ( $post->post_type == 'thought' ) {
    $custom = get_post_custom();
    if ( $column == 'featured_image' )
      echo the_post_thumbnail( 'thought-thumb' );
    elseif ( $column == 'content' )
      echo edit_post_link(get_the_content());
    else {
      $custom = get_post_custom();
      if (array_key_exists($column, $custom))
        echo $custom[$column][0];
    }
  }
}
add_action('manage_posts_custom_column',  __NAMESPACE__ . '\custom_columns');

// Custom CMB2 fields for post type
function metaboxes( array $meta_boxes ) {
  $prefix = '_cmb2_'; // Start with underscore to hide from custom fields list

  $meta_boxes['thought_metabox'] = array(
    'id'            => 'thought_metabox',
    'title'         => __( 'Extra Fields', 'cmb2' ),
    'object_types'  => array( 'thought', ), // Post type
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true, // Show field names on the left
    'fields'        => array(
      array(
          'name'    => 'Author',
          // 'desc'    => 'field description (optional)',
          'id'      => $prefix . 'author',
          'type'    => 'text',
      ),
    ),
  );

  return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes' );

function icon_types() {
  return [
    'catalyst' => 'Catalyst',
    'clock' => 'Clock',
    'digital-learning' => 'Digital Learning',
    'exclamation' => 'Exclamation',
    'letter' => 'Letter',
    'lightbulb' => 'Lightbulb',
    'news' => 'News',
    'pov' => 'Point Of View',
    'question' => 'Question',
    'radar' => 'Radar',
    'strategy' => 'Strategy',
    'team' => 'Team',
    'triad' => 'Triad',
    'tricircle' => 'Tricircle',
  ];
}

// Shortcode [thoughts]
add_shortcode('thoughts', __NAMESPACE__ . '\shortcode');
function shortcode($atts) {
  extract(shortcode_atts(array(
       'page' => '',
    ), $atts));
  $output = '';

  $args = array(
    'numberposts' => -1,
    'post_type' => 'thought',
    'orderby' => 'menu_order',
    );
  if ($page != '') {
    $args['meta_query'] = array(
        array(
            'key' => '_cmb2_pages_visible',
            'value' => array($page),
            'compare' => 'IN',
        )
    );
  }

  $thought_posts = get_posts($args);
  if (!$thought_posts) return false;

  foreach ($thought_posts as $post):
    $body = apply_filters('the_content', $post->post_content);
    $thumb = get_the_post_thumbnail($post->ID, 'thought-thumb');
    $icon = get_post_meta( $post->ID, '_cmb2_icon', true );

    $output .= <<<HTML
     <div class="slide-item">
       <div class="slider-content">
         <div class="wrap-inner">
           <h2 class="slide-title"><svg class="icon icon-{$icon}" role="img"><use xlink:href="#icon-{$icon}"></use></svg>{$post->post_title}</h2>
           {$body}
         </div>
       </div>
     </div>
HTML;
  endforeach;

  return $output;
}
