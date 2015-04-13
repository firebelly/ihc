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
add_action('after_setup_theme', __NAMESPACE__ . '\\setup');
