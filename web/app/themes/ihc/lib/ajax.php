<?php
namespace Firebelly\Ajax;

add_action( 'wp_ajax_get_happenings_posts', __NAMESPACE__ . '\\get_happenings_posts' );
add_action( 'wp_ajax_nopriv_get_happenings_posts', __NAMESPACE__ . '\\get_happenings_posts' );

function get_happenings_posts() {
  $page = $_REQUEST['page'];
  $per_page = $_REQUEST['per_page'];
  $offset = ($page-1) * $per_page;
  $happenings_posts = get_posts(
    array(
      'offset' => $offset,
      'posts_per_page' => $per_page,
      'category_name' => 'happenings',
      )
    );

  if ($happenings_posts): 
  $show_images = true;
  foreach ($happenings_posts as $happenings_post)
    include(locate_template('templates/happenings-list.php'));
  endif;

  die();
}

add_action( 'wp_ajax_get_radar_posts', __NAMESPACE__ . '\\get_radar_posts' );
add_action( 'wp_ajax_nopriv_get_radar_posts', __NAMESPACE__ . '\\get_radar_posts' );

function get_radar_posts() {
  include(locate_template('templates/recent-radar-and-news.php'));

  die();
}
