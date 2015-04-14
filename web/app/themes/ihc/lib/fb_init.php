<?php

namespace Firebelly\Init;

/**
 * FB theme inits
 */

function setup() {
  // Register wp_nav_menu() menus
  // http://codex.wordpress.org/Function_Reference/register_nav_menus
  register_nav_menus([
    'footer_links' => __('Footer Links', 'sage')
  ]);
}
add_action('after_setup_theme', __NAMESPACE__ . '\setup');

/**
 * Custom nav highlighting for various pages
 */
function custom_nav_highlights($classes, $item) {
	// Focus Area taxonomy page should highlight "Our Work" and nothing else (was highlighting Events for some reason)
  if (is_singular('program') || is_tax('focus_area')) {
  	if (in_array('menu-our-work', $classes))
	    $classes[] = 'active';
	  else
	  	$classes = array_diff($classes, array('active'));
	}
  return $classes;
}
if (!is_admin()) { add_filter('nav_menu_css_class', __NAMESPACE__ . '\custom_nav_highlights', 20, 2); }
