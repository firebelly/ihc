<?php
/**
 * Custom short codes
 */

namespace Firebelly\Utils\ShortCodes;
use Firebelly\Utils;

// Shortcode [our_focus_areas]
add_shortcode('focus_areas', __NAMESPACE__ . '\focus_areas_shortcode');
function focus_areas_shortcode($atts) {
  $output = '<h3 class="accent">Our Focus Areas</h3>';
  $output .= '<ul class="focus-list">';
  $focus_areas = get_terms('focus_area');
  foreach ($focus_areas as $focus_area) 
    $output .= '<li><a href="' . get_term_link($focus_area) . '">' . $focus_area->name . '</a></li>';
  $output .= '</ul>';
  return $output;
}