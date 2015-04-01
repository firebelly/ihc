<?php

namespace Firebelly\Utils;

/**
 * Bump up # search results
 */
function cpt_search( $query ) {
  if ( !is_admin() && is_search() ) {
    $query->set( 'posts_per_page', 100 );
  }
  return $query;
}
add_filter( 'pre_get_posts', __NAMESPACE__ . '\\cpt_search' );


/**
 * Janky redirects of single posts to listing view
 */
function custom_post_redirects() {
  global $post;
  if (is_page()) {
    // check if page has parent, and doesn't have children, and redirect to parent if so
    if (!empty($post->post_parent) && count(get_children($post->ID, 'ARRAY_A')) == 0) {
      $redirect = get_the_permalink($post->post_parent) . '#' . $post->post_name;
      wp_redirect($redirect, 301);
    }
  } else if (is_single() && $post->post_type != 'post') {
    $redirect = get_funky_url($post);
    if (!empty($redirect)) {
      wp_redirect($redirect, 301);
      exit();
    }
  }
}
add_action('template_redirect', __NAMESPACE__ . '\\custom_post_redirects');

function get_funky_url($post) {
  $url = false;
  if ($post->post_type=='initiative') {
    $term = get_first_term($post,'initiative_cat');
    $url = '/what-we-do/' . $term->slug . '/#' . $post->post_name;
  } elseif ($post->post_type=='grantee') {
    $url = '/catalyst-grants/#' . $post->post_name;
  } elseif ($post->post_type=='person') {
    $url = '/who-we-are/#' . $post->post_name;
  }
  return $url;
}

/**
 * Custom li'l excerpt function
 */
function get_excerpt( $post, $length=15, $force_content=false ) {
  $excerpt = trim($post->post_excerpt);
  if (!$excerpt || $force_content) {
    $excerpt = $post->post_content;
    $excerpt = strip_shortcodes( $excerpt );
    $excerpt = apply_filters( 'the_content', $excerpt );
    $excerpt = str_replace( ']]>', ']]&gt;', $excerpt );
    $excerpt_length = apply_filters( 'excerpt_length', $length );
    $excerpt = wp_trim_words( $excerpt, $excerpt_length, '...' );
  }
  return $excerpt;
}

/**
 * Silly ajax helper, returns true if xmlhttprequest
 */
function is_ajax() {
  return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
}

/**
 * Get top ancestor for post
 */
function get_top_ancestor($post){
  if (!$post) return;
  $ancestors = $post->ancestors;
  if ($ancestors) {
    return end($ancestors);
  } else {
    return $post->ID;
  }
}

/**
 * Get first term for post
 */
function get_first_term($post, $taxonomy='') {
  $return = false;
  if ($terms = get_the_terms($post->ID, $taxonomy))
    $return = array_pop($terms);
  return $return;
}

/**
 * Get page content from slug
 */
function get_page_content($slug) {
  $return = false;
  if ($page = get_page_by_path($slug))
    $return = apply_filters('the_content', $page->post_content);
  return $return;
}

/**
 * Get program tag for post -- if post is tagged either SEL or Digital Learning, return that term object
 */
function get_program_tag($post) {
  $return = false;
  if ($terms = get_the_terms($post->ID, 'post_tag')) {
    foreach($terms as $term) {
      if (preg_match('/(social\-emotional\-learning|digital\-learning|catalyst)/',$term->slug))
        $return = $term;
    }
  }
  return $return;
}

/**
 * Get num_pages for category given slug + per_page
 */
function get_total_pages($category, $per_page) {
  $cat_info = get_category_by_slug($category);
  $num_pages = ceil($cat_info->count / $per_page);
  return $num_pages;
}

/**
 * Get related resources
 */
function get_resources($post_id) {
    $files = get_post_meta($post_id, '_cmb2_resources', 1);

    foreach ((array)$files as $attachment_id => $attachment_url) {
      echo '<div class="file-list-image">';
      echo wp_get_attachment_image($attachment_id);
      echo '</div>';
    }
}

/**
 * Get post options for CMB2 select
 */
function cmb2_get_post_options( $query_args ) {

    $args = wp_parse_args( $query_args, array(
        'post_type'   => 'post',
        'numberposts' => 10,
        'post_parent' => 0,
    ) );

    $posts = get_posts( $args );

    $post_options = array();
    if ( $posts ) {
        foreach ( $posts as $post ) {
          $post_options[ $post->ID ] = $post->post_title;
        }
    }

    return $post_options;
}