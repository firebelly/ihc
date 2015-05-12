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

/**
 * Custom theme classes added to body
 */
function body_class( $classes ) {
  // Array of background hex values
  $background_array = ['c1d6d8', 'b7ddba', 'edbaba', 'e5d3be', 'c6c9cc', 'eac6af'];

  // Select random bg & accents for page
  $background = rand(1,6);
  $accent = rand(1,5);
  
  // Set global var to use when creating treated backgrounds
  define('IHC_BACKGROUND', $background_array[$background-1]);
  
  // Add to body_class()
  $classes[] = 'background-' . $background;
  $classes[] = 'accent-' . $accent;
  return $classes;
}
add_filter('body_class', __NAMESPACE__ . '\body_class');