<?php
/**
 * Template Name: Homepage
 */
use Firebelly\PostTypes\Thought;
?>

<?php 
// Main page data
// echo $post->post_title;
// echo apply_filters('the_content', $post->post_content);

Thought\submit_form();
?>
