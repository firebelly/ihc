<?php

namespace Firebelly\Init;

/**
 * Don't run wpautop before shortcodes are run! wtf Wordpress. from http://stackoverflow.com/a/14685465/1001675
 */
remove_filter( 'the_content', 'wpautop' );
add_filter( 'the_content', 'wpautop' , 99);
add_filter( 'the_content', 'shortcode_unautop',100 );

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


function mce_buttons_2($buttons) {
  array_unshift( $buttons, 'styleselect' );
  return $buttons;
}
add_filter('mce_buttons_2', __NAMESPACE__ . '\mce_buttons_2');

function simplify_tinymce($settings) {
  // What goes into the 'formatselect' list
  $settings['block_formats'] = 'Header 1=h1;Header 2=h2;Header 3=h3;Header 4=h4;Paragraph=p';

  $settings['inline_styles'] = 'false';
  if (!empty($settings['formats']))
    $settings['formats'] = substr($settings['formats'],0,-1).",underline: { inline: 'u', exact: true} }";
  else
    $settings['formats'] = "{ underline: { inline: 'u', exact: true} }";
  
  // What goes into the toolbars. Add 'wp_adv' to get the Toolbar toggle button back
  $settings['toolbar1'] = 'styleselect,bold,italic,underline,strikethrough,formatselect,bullist,numlist,blockquote,link,unlink,hr,wp_more,outdent,indent,AccordionShortcode,AccordionItemShortcode,fullscreen';
  $settings['toolbar2'] = '';
  $settings['toolbar3'] = '';
  $settings['toolbar4'] = '';

  // Clear most formatting when pasting text directly in the editor
  $settings['paste_as_text'] = 'true';
  // print_r($settings); exit;

  $style_formats = array(  
    // array(  
    //   'title' => 'Two Column',
    //   'block' => 'div',
    //   'classes' => 'two-column',
    //   'wrapper' => true,
    // ),  
    array(  
      'title' => 'Three Column',
      'block' => 'div',
      'classes' => 'three-column',
      'wrapper' => true,
    ),
  );  
  $settings['style_formats'] = json_encode( $style_formats );

  return $settings;
}
add_filter('tiny_mce_before_init', __NAMESPACE__ . '\simplify_tinymce');
