<?php

namespace Firebelly\Utils;

/**
 * Bump up # search results
 */
function search_queries( $query ) {
  if ( !is_admin() && is_search() ) {
    $query->set( 'posts_per_page', 100 );
  }
  return $query;
}
add_filter( 'pre_get_posts', __NAMESPACE__ . '\\search_queries' );

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
function get_first_term($post, $taxonomy='category') {
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
 * Get focus area for post
 */
function get_focus_area($post) {
  if ($focus_area = get_post_meta($post->ID, '_cmb2_focus_area', true)) {
    return get_term($focus_area[0], 'focus_area');
  } else return false;
}

function get_focus_area_and_tags($post) {
  $return = false;
  if ($focus_area = get_focus_area($post)) {
    $return = '<a href="'.get_term_link($focus_area).'">'.$focus_area->name.'</a>';
  }
  // todo: pull tags also and prepend focus area to list
  return $return;
}

/**
 * Get category for post
 */
function get_category($post) {
  if ($category = get_the_category($post)) {
    return $category[0];
  } else return false;
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
function get_resources($post) {
  $files = get_post_meta($post->ID, '_cmb2_resources', true);
  if (empty($files)) return false;

  $output = '<ul class="resources">';
  foreach ((array)$files as $attachment_id => $attachment_url) {
    $post = get_post($attachment_id);
    $output .= '<li><a target="_blank" href="' . $attachment_url . '">' . $post->post_title . '</a></li>';
  }
  $output .= '</ul>';
  return $output;
}

/**
 * Get Related Event
 * @param  [Object or String] $post_or_focus_area [$post object or $focus_area slug]
 */
function get_related_event_post($post_or_focus_area) {
  if (is_object($post_or_focus_area)) {
    $focus_area = get_focus_area($post_or_focus_area);
  } else {
    $focus_area = $post_or_focus_area;
  }
  $output = '<div class="related related-events">';
  $output = '<h4 class="flag">Attend an Event</h4>';
  $output .= \Firebelly\PostTypes\Event\get_events(1, $focus_area);
  $output .= '<p class="more button"><a href="/events/">View All Events</a></p>';
  $output .= '</div>';
}

/**
 * Get Related News Post
 */
function get_related_news_post($post_or_focus_area) {
  global $news_post;
  if (is_object($post_or_focus_area)) {
    $focus_area = get_focus_area($post_or_focus_area);
  } else {
    $focus_area = $post_or_focus_area;
  }
  $posts = get_posts('numberposts=1&focus_area='.$focus_area);
  $output = '<div class="related related-news">';
  $output = '<h4 class="flag">Blog &amp; News</h4>';
  ob_start();
  foreach ($posts as $news_post)
    include(locate_template('templates/article-news.php'));
  $output .= ob_get_clean();
  $output .= '<p class="more button"><a href="/news/">View All Articles</a></p>';
  $output .= '</div>';
  return $output;
}