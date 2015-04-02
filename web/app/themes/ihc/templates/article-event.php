<?php 
$body = apply_filters('the_content', $event_post->post_content);
$start_time = get_post_meta($event_post->ID, '_cmb2_start_time', true);
$end_time = get_post_meta( $event_post->ID, '_cmb2_end_time', true);
$time_txt = $start_time . (!empty($end_time) ? '–'.$end_time : '');
$registration_url = get_post_meta($event_post->ID, '_cmb2_registration_url', true);
$address = get_post_meta($event_post->ID, '_cmb2_address', true);
$address = wp_parse_args($address, array(
    'address-1' => '',
    'address-2' => '',
    'city'      => '',
    'state'     => '',
    'zip'       => '',
 ));
?>
<article class="event">
  <h1><?= $event_post->post_title ?></h1>
  <h3><?= $time_txt ?></h3>
  <p><?= $address['city'] ?>, <?= $address['state'] ?> <?= $address['zip'] ?></p>
  <?php if (!empty($registration_url)): ?>
    <a class="register" href="<?= $registration_url ?>">Register</a>
  <?php endif; ?>
  <p class="more"><a href="<?= get_permalink($event_post->ID); ?>">+ More Details</a></p>
</article>
