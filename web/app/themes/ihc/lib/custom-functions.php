<?php

namespace Firebelly\Utils;

/**
 * Bump up # search results
 */
function search_queries( $query ) {
  if ( !is_admin() && is_search() ) {
    $query->set( 'posts_per_page', 40 );
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
    $excerpt = wp_trim_words( $excerpt, $excerpt_length );
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
  if ($focus_areas = get_the_terms($post->ID, 'focus_area')) {
    return $focus_areas[0]->name;
  } else return false;
}

/**
 * Get related Program
 */
function get_program($post) {
  if ($program = get_post_meta($post->ID, '_cmb2_related_program', true)) {
    return get_post($program);
  } else return false;
}

/**
 * Get Focus Area(s) and Program(s) "article-tag" list for post
 */
function get_article_tags($post) {
  $links = [];
  if ($focus_areas = get_the_terms($post->ID, 'focus_area')) {
    foreach($focus_areas as $focus_area)
      $links[] = '<a href="'.get_term_link($focus_area).'">'.$focus_area->name.'</a>';
  }
  if ($program = get_program($post)) {
    $links[] = '<a href="'.get_the_permalink($program).'">'.$program->post_title.'</a>';
  }
  return count($links) ? implode(', ', $links) : false;
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
    if ($post) {
      $output .= '<li><a target="_blank" href="' . $attachment_url . '">' . $post->post_title . '</a></li>';
    } else {
      $output .= '<li>Attachment not found.</li>';
    }
  }
  $output .= '</ul>';
  return $output;
}

/**
 * Get Related Event
 * @param  [Object or String] $post_or_focus_area [$post object or $focus_area slug]
 */
function get_related_event_post($post_or_focus_area) {
  $output = false;
  if (is_object($post_or_focus_area)) {
    $focus_area = get_focus_area($post_or_focus_area);
  } else {
    $focus_area = $post_or_focus_area;
  }
  if ($event = \Firebelly\PostTypes\Event\get_events(1, $focus_area)) {
    $output = '<div class="related related-events">';
    $output .= '<h4 class="flag">Attend an Event</h4>';
    $output .= $event;
    $output .= '<p class="view-all"><a class="button" href="/events/">View All Events</a></p>';
    $output .= '</div>';
  }
  return $output;
}

/**
 * Get Related News Post
 */
function get_related_news_post($post_or_focus_area) {
  global $news_post;
  $output = false;
  if (is_object($post_or_focus_area)) {
    $focus_area = get_focus_area($post_or_focus_area);
  } else {
    $focus_area = $post_or_focus_area;
  }
  $posts = get_posts('numberposts=1&focus_area='.$focus_area);
  if ($posts) {
    $output = '<div class="related related-news">';
    $output .= '<h4 class="flag">Blog &amp; News</h4>';
    ob_start();
    foreach ($posts as $news_post)
      include(locate_template('templates/article-news.php'));
    $output .= ob_get_clean();
    $output .= '<p class="view-all"><a class="button" href="/news/">View All Articles</a></p>';
    $output .= '</div>';
  }
  return $output;
}

/**
 * Get header bg for post, duotone treated with the random IHC_BACKGROUND + Dark Blue  
 * @param  [string|object] $post_or_image [WP post object or background image]
 */
function get_header_bg($post_or_image, $thumb_id='') {
  $header_bg = $background_image = false;
  // If WP post object, get the featured image
  if (is_object($post_or_image)) {
    if (has_post_thumbnail($post_or_image->ID)) {
      $thumb_id = get_post_thumbnail_id($post_or_image->ID);
      $background_image = get_attached_file($thumb_id, 'full', true);
    }
  } else {
    // These are sent from a taxonomy page
    $background_image = $post_or_image;
  }
  if ($background_image) {
    $upload_dir = wp_upload_dir();
    $base_dir = $upload_dir['basedir'] . '/backgrounds/';

    // Build treated filename with thumb_id in case there are filename conflicts
    $treated_filename = preg_replace("/.+\/(.+)\.(\w{2,5})$/", $thumb_id."-$1-".IHC_BACKGROUND.".$2", $background_image);
    $treated_image = $base_dir . $treated_filename;
  
    // If treated file doesn't exist, create it
    if (!file_exists($treated_image)) {

      // If the background directory doesn't exist, create it first
      if(!file_exists($base_dir)) {
        mkdir($base_dir);
      }
      $convert_command = (WP_ENV==='development') ? '/usr/local/bin/convert' : '/usr/bin/convert';
      exec($convert_command.' '.$background_image.' -resize 1400x -quality 65 -colorspace gray -level +10% +level-colors "#44607f","#'.IHC_BACKGROUND.'" '.$treated_image);
    }    
    $header_bg = ' style="background-image:url(' . $upload_dir['baseurl'] . '/backgrounds/' . $treated_filename . ');"';
  }
  return $header_bg;
}
