<?php
/**
 * Various media functions
 */

namespace Firebelly\Media;

// image size for popout thumbs
add_image_size( 'popout-thumb', 250, 300, ['center', 'top'] );

/**
 * Get header bg for post, duotone treated with the random IHC_BACKGROUND + Dark Blue
 * @param  string|object   $post_or_image (WP post object or background image)
 * @return HTML            background image code
 */
function get_header_bg($post_or_image, $thumb_id='') {
  $upload_dir = wp_upload_dir();
  $header_bg = $background_image = false;
  // If WP post object, get the featured image
  if (is_object($post_or_image)) {

    if (has_post_thumbnail($post_or_image->ID)) {
      $thumb_id = get_post_thumbnail_id($post_or_image->ID);
      $background_image = get_attached_file($thumb_id, 'full', true);
    }

  } else {

    // Absolute URLs, e.g. from a taxonomy page or other CMB2 file field
    if (preg_match('/^http/', $post_or_image)) {
      // If it's an absolute URL, make it relative
      $background_image = $upload_dir['basedir'] . str_replace('/app/uploads','',wp_make_link_relative($post_or_image));
    } else {
      // Not sure when this would ever happen...
      $background_image = $post_or_image;
    }

  }
  if ($background_image) {
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
      exec($convert_command.' '.$background_image.' +profile "*" -resize 1400x -quality 65 -modulate 100,0 -size 256x1! gradient:#44607f-#'.IHC_BACKGROUND.' -clut '.$treated_image);
    }
    $header_bg = ' style="background-image:url(' . $upload_dir['baseurl'] . '/backgrounds/' . $treated_filename . ');"';
  }
  return $header_bg;
}

/**
 * Get thumbnail image for post
 * @param  integer $post_id
 * @return string image URL
 */
function get_post_thumbnail($post_id, $size='medium') {
  $return = false;
  if (has_post_thumbnail($post_id)) {
    $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $size);
    $return = $thumb[0];
  }
  return $return;
}

/**
 * Get the file path (not URL) to a thumbnail of a particular size.
 * (get_attached_file() only returns paths to full-sized thumbnails.)
 * @param  int            $thumb_id - attachment id of thumbnail
 * @param  string|array   $size - thumbnail size string (e.g. 'full') or array [w,h]
 * @return path           file path to properly sized thumbnail
 */
function get_thumbnail_size_path($thumb_id, $size) {
  // Find the path to the root image. We can get this from get_attached_file.
  $old_path = get_attached_file($thumb_id, true);

  // Find the url of the image with the proper size
  $attr = wp_get_attachment_image_src($thumb_id, $size);
  $url = $attr[0];

  // Grab the filename of the sized image from the url
  $exploded_url = explode('/', $url);
  $filename = $exploded_url[count($exploded_url)-1];

  // Replace the filename in our path with the filename of the properly sized image
  $exploded_path = explode('/', $old_path);
  $exploded_path[count($exploded_path)-1] = $filename;
  $new_path = implode ('/', $exploded_path);

  return $new_path;
}

/**
 * Delete background images when attachment is deleted
 */
add_action('delete_attachment', __NAMESPACE__ . '\delete_background_images');
function delete_background_images($post_id) {
  // Get attachment image metadata
  $metadata = wp_get_attachment_metadata($post_id);
  if (!$metadata || empty($metadata['file']))
    return;

  $pathinfo = pathinfo($metadata['file']);
  $upload_dir = wp_upload_dir();
  $base_dir = trailingslashit($upload_dir['basedir']) . 'backgrounds/';
  $files = scandir($base_dir);

  foreach($files as $file) {
    // If filename matches background file, delete it
    if (strpos($file,$pathinfo['filename']) !== false) {
      @unlink($base_dir . '/' . $file);
    }
  }
}