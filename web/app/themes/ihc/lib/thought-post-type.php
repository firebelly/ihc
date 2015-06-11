<?php
/**
 * Thought post type
 */

namespace Firebelly\PostTypes\Thought;

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
    'show_in_nav_menus'   => false,
    'show_in_admin_bar'   => false,
    'menu_position'       => 20,
    'menu_icon'           => 'dashicons-admin-post',
    'can_export'          => false,
    'has_archive'         => false,
    'exclude_from_search' => true,
    'publicly_queryable'  => false,
    'query_var'           => false,
    'rewrite'             => $rewrite,
    'capability_type'     => 'thought',
    'map_meta_cap'        => true
  );
  register_post_type( 'thought', $args );

}
add_action( 'init', __NAMESPACE__ . '\post_type', 0 );

/**
 * Add capabilities to control permissions of Post Type via roles
 */
function add_capabilities() {
  $role_admin = get_role('administrator');
  $role_admin->add_cap('edit_thought');
  $role_admin->add_cap('read_thought');
  $role_admin->add_cap('delete_thought');
  $role_admin->add_cap('edit_thoughts');
  $role_admin->add_cap('edit_others_thoughts');
  $role_admin->add_cap('publish_thoughts');
  $role_admin->add_cap('read_private_thoughts');
  $role_admin->add_cap('delete_thoughts');
  $role_admin->add_cap('delete_private_thoughts');
  $role_admin->add_cap('delete_published_thoughts');
  $role_admin->add_cap('delete_others_thoughts');
  $role_admin->add_cap('edit_private_thoughts');
  $role_admin->add_cap('edit_published_thoughts');
  $role_admin->add_cap('create_thoughts');
}
add_action('switch_theme', __NAMESPACE__ . 'add_capabilities');

/**
 * Custom admin columns for post type
 */
function edit_columns($columns){
  $columns = array(
    'cb' => '<input type="checkbox" />',
    'title' => 'Title',
    'content' => 'Thought',
    '_cmb2_thought_of_the_day' => 'Thought of the Day?',
    '_cmb2_author' => 'Author',
    'taxonomy-focus_area' => 'Focus Area',
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
    elseif ( $column == '_cmb2_thought_of_the_day' )
      echo !empty($custom[$column][0]) ? '✓' : '';
    else {
      if (array_key_exists($column, $custom))
        echo $custom[$column][0];
      else echo $column;
    }
  }
}
add_action('manage_posts_custom_column',  __NAMESPACE__ . '\custom_columns');

/**
 * CMB2 custom fields
 */
function metaboxes(array $meta_boxes) {
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
      array(
          'name'    => 'Thought of the Day',
          'desc'    => 'When checked will clear out any previous Thought of the Day',
          'id'      => $prefix . 'thought_of_the_day',
          'type'    => 'checkbox',
      ),
    ),
  );

  return $meta_boxes;
}
add_filter('cmb2_meta_boxes', __NAMESPACE__ . '\metaboxes');

/**
 * Get Thoughts
 */
function get_thought_of_the_day() {
  $args = array(
    'numberposts' => 1,
    'post_type' => 'thought',
    'meta_query' => [
      [
        'key' => '_cmb2_thought_of_the_day',
        'value' => 'on',
        'compare' => '='
      ]
    ],
  );

  $thought_posts = get_posts($args);
  if (!$thought_posts) return false;
  foreach ($thought_posts as $post):
    $body = apply_filters('the_content', $post->post_content);
    $author = get_post_meta( $post->ID, '_cmb2_author', true );
    
    if ($focus = \Firebelly\Utils\get_first_term($post, 'focus_area'))
      $author .= '<br><a href="'.get_term_link($focus).'">'.$focus->name.'</a>';
    else
      $author .= '<br>Humanities';

    $output = <<<HTML
     <article>
       <blockquote>{$body}</blockquote>
       <cite>{$author}</cite>
     </article>
HTML;
  endforeach;
  return $output;
}

/**
 * Outputs a "Submit A Thought" submit form
 */
function submit_form() {
?>
  <form class="new-thought-form" method="post" action="">
    <textarea name="thought" required placeholder="Type your thought..."></textarea>
    <input type="text" name="author" required placeholder="Your Name">
    <div class="select-wrapper"><?php wp_dropdown_categories('show_option_none=Humanities&taxonomy=focus_area'); ?></div>
    <?php wp_nonce_field('new_thought'); ?>
    <!-- die bots --><div style="position: absolute; left: -5000px;"><input type="text" name="die_bots_5000" tabindex="-1" value=""></div>
    <input type="hidden" name="action" value="thought_submission">
    <div class="actions">
      <button type="submit" class="button">Submit Thought</button>
    </div>
  </form>
<?php
}

/**
 * Handle a Thought submission
 */
function thought_submission() {
  $thought = filter_var($_REQUEST['thought'], FILTER_SANITIZE_STRING);
  $author = filter_var($_REQUEST['author'], FILTER_SANITIZE_STRING);
  $cat = filter_var($_REQUEST['cat'], FILTER_SANITIZE_NUMBER_INT);

  if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'new_thought')) {
    wp_send_json_error(['message' => 'Failed security check']);
  } elseif (!empty($_REQUEST['die_bots_5000'])) {
    wp_send_json_error(['message' => 'Failed bot check']);
  } else {

    $is_spam = 'false';

    // Check with Akismet if possible
    if(function_exists('akismet_http_post')) {
      if (akismet_get_key()) {
        global $akismet_api_host, $akismet_api_port;

        $data = array( 
          'comment_author'        => $author,
          'comment_content'       => $thought,
          'user_ip'               => $_SERVER['REMOTE_ADDR'],
          'blog'                  => site_url(),
        );
        $query_string = http_build_query($data);
        $response = akismet_http_post($query_string, $akismet_api_host, '/1.1/comment-check', $akismet_api_port);
        $is_spam = (is_array( $response) && isset( $response[1])) ? $response[1] : 'false';
      }
    }

    if ($is_spam !== 'false') {
      wp_send_json_error(['message' => 'Akismet has marked this as spam']);
    } else {
      $my_post = [
        'post_title'    => sprintf('Submission from %s', $author),
        'post_content'  => $thought,
        'post_type'     => 'thought',
        'post_author'   => 1,
        'tax_input'     => ['focus_area' => $cat]
      ];
      $post_id = wp_insert_post($my_post);
      update_post_meta($post_id, '_cmb2_author', $author);

      // Notify admin of submission?
      $thought_of_day_email = get_option('thought_of_day_email');
      if ($thought_of_day_email && is_email($thought_of_day_email)) {
        if ($cat>0) {
          $focus_area = get_term($cat, 'focus_area');
          $focus_area_name = $focus_area->name;
        } else {
          $focus_area_name = 'Humanities';
        }
        $email_txt = "You have a new Thought of the Day submission!";
        $email_txt .= "\n\nThought: " . $thought;
        $email_txt .= "\n\nAuthor: " . $author;
        $email_txt .= "\n\nFocus Area: " . $focus_area_name;
        $email_txt .= "\n\nEdit/Publish: ".admin_url('post.php?post=' . $post_id . '&action=edit');

        // Email user set in Site Settings
        wp_mail($thought_of_day_email, sprintf('New Thought of the Day submission from %s', $author), $email_txt);
      }

      // Pull response copy from Site Settings and return via json
      $thought_of_day_response = get_option('thought_of_day_response', 'Your submission is in review.');
      wp_send_json_success(['message' => sprintf($thought_of_day_response, $author)]);
    }

  }
}
add_action('wp_ajax_thought_submission', __NAMESPACE__ . '\\thought_submission');
add_action('wp_ajax_nopriv_thought_submission', __NAMESPACE__ . '\\thought_submission');


/**
 * Check if Thought of the Day is checked, clear out previous featured Thoughts if so
 */
function check_thought_of_day( $post_id ) {
  global $wpdb;
  if ( wp_is_post_revision( $post_id ) )
    return;

  if (!empty($_REQUEST['_cmb2_thought_of_the_day'])) {
    $wpdb->query("DELETE FROM wp_postmeta WHERE meta_key='_cmb2_thought_of_the_day'");
    $wpdb->query("INSERT INTO wp_postmeta SET meta_key='_cmb2_thought_of_the_day', meta_value='on', post_id={$post_id}");
  }
}
add_action('save_post', __NAMESPACE__ . '\check_thought_of_day');


/**
 * Handle AJAX response from CSV import form
 */
add_action( 'wp_ajax_thought_csv_upload', __NAMESPACE__ . '\thought_csv_upload' );
function thought_csv_upload() {
  global $wpdb;
  require_once 'import/thought-csv-importer.php';

  $importer = new \ThoughtCSVImporter;
  $return = $importer->handle_post();

  // Spits out json-encoded $return & die()s
  wp_send_json($return);
}

/**
 * Show link to CSV Import page
 */
add_action('admin_menu', __NAMESPACE__ . '\import_csv_admin_menu');
function import_csv_admin_menu() {
  add_submenu_page('edit.php?post_type=thought', 'Import CSV', 'Import CSV', 'manage_options', 'thoughts-csv-importer', __NAMESPACE__ . '\import_csv_admin_form');
}

/**
 * Basic CSV Importer admin page
 */
function import_csv_admin_form() {
?>
  <div class="wrap">
    <h2>Import CSV</h2>
    <form method="post" id="csv-upload-form" enctype="multipart/form-data" action="">
      <fieldset>
        <label for="csv_import">Upload file(s):</label>
        <input name="csv_import[]" id="csv-import" type="file" multiple>
        <div id="filedrag">or drop files here</div>
      </fieldset>
      <div class="progress-bar"><div class="progress-done"></div></div>
      <input type="hidden" name="action" value="thought_csv_upload">
      <p class="submit"><input type="submit" class="button" id="csv-submit" name="submit" value="Import"></p>
    </form>

    <div class="import-notes">
      <h3>Format Guide:</h3>
<pre>thought,author,focus_area
lorem ipsum,john doe,Media & Journalism
dolor sit amet,jane doe,Business</pre>
    </div>

  </div>
<?php
}