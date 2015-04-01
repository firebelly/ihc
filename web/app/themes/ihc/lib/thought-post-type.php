<?php
/**
 * Thought post type
 */

namespace Firebelly\PostTypes\Thought;

// Custom image size for post type?
add_image_size( 'thought-thumb', 350, null, null );

/**
 * Register Custom Post Type
 */
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
  register_post_type( 'thought', $args );

}
add_action( 'init', __NAMESPACE__ . '\post_type', 0 );

/**
 * Custom admin columns for post type
 */
function edit_columns($columns){
  $columns = array(
    'cb' => '<input type="checkbox" />',
    'title' => 'Title',
    'content' => 'Thought',
    '_cmb2_author' => 'Author',
    'taxonomy-focus_area' => 'Focus Area(s)',
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
      echo get_the_content();
      // echo edit_post_link(get_the_content()) . ($post->post_status != 'publish' ? " - <strong class=\"post-status\">{$post->post_status}</strong>" : '');
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

/**
 * Shortcode [thoughts]
 */
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
add_shortcode('thoughts', __NAMESPACE__ . '\shortcode');

/**
 * Outputs a "Submit A Thought" submit form
 */
function submit_form() {
?>
  <form name="new_thought" method="post" action="" class="submit-thought">
    <textarea name="thought" required></textarea>
    <input type="text" name="author" required>
    <?php wp_dropdown_categories('show_option_none=Select Focus Area&taxonomy=focus_area'); ?>
    <?php wp_nonce_field('new_thought'); ?>
    <!-- die bots --><div style="position: absolute; left: -5000px;"><input type="text" name="die_bots_5000" tabindex="-1" value=""></div>
    <input type="hidden" name="action" value="thought_submission">
    <button type="submit">Submit Thought</button>
  </form>
<?php
}

/**
 * Handle a Thought submission
 */
function thought_submission() {
  if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'new_thought')) {
    wp_send_json_error(['message' => 'Failed security check']);
  } elseif (!empty($_REQUEST['die_bots_5000'])) {
    wp_send_json_error(['message' => 'Failed bot check']);
  } else {
    $my_post = [
      'post_title'    => sprintf('Submission from %s', $_REQUEST['author']),
      'post_content'  => $_REQUEST['thought'],
      'post_type'     => 'thought',
      'post_author'   => 1,
      'tax_input'     => ['focus_area' => $_REQUEST['cat']]
    ];
    $post_id = wp_insert_post($my_post);
    update_post_meta($post_id, '_cmb2_author', $_REQUEST['author']);
    wp_send_json_success(['message' => sprintf('Thought from %s added ok', $_REQUEST['author'])]);
  }
}
add_action( 'wp_ajax_thought_submission', __NAMESPACE__ . '\\thought_submission' );
add_action( 'wp_ajax_nopriv_thought_submission', __NAMESPACE__ . '\\thought_submission' );
