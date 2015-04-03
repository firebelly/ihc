<?php 
$body = apply_filters('the_content', $event_post->post_content);
$event_timestamp = get_post_meta($event_post->ID, '_cmb2_event_timestamp', true);
$end_time = get_post_meta( $event_post->ID, '_cmb2_end_time', true);
$start_time = date('g:iA', $event_timestamp);
$time_txt = $start_time . (!empty($end_time) ? '–' . preg_replace('/(^0| )/','',$end_time) : '');
$registration_url = get_post_meta($event_post->ID, '_cmb2_registration_url', true);
$lat = get_post_meta($event_post->ID, '_cmb2_lat', true);
$lng = get_post_meta($event_post->ID, '_cmb2_lng', true);
$desc = date('M d, Y @ ', $event_timestamp) . $time_txt;
$address = get_post_meta($event_post->ID, '_cmb2_address', true);
$address = wp_parse_args($address, array(
    'address-1' => '',
    'address-2' => '',
    'city'      => '',
    'state'     => '',
    'zip'       => '',
 ));
$year = date('Y', $event_timestamp);
?>
<article class="event map-point" data-lat="<?= $lat ?>" data-lng="<?= $lng ?>" data-title="<?= $event_post->post_title ?>" data-desc="<?= $desc ?>" data-id="<?= $event_post->ID ?>">
  <time datetime="<?= date('c', $event_timestamp); ?>"><span class="month"><?= date('M', $event_timestamp) ?></span> <span class="day"><?= date('d', $event_timestamp) ?></span><?= ($year != date('Y') ? ' <span class="day">'.$year.'</span>' : '') ?></time>
  <h1><?= $event_post->post_title ?></h1>
  <h3><?= $time_txt ?></h3>
  <?php if (!empty($address['city'])): ?>
	  <p><?= $address['city'] ?>, <?= $address['state'] ?> <?= $address['zip'] ?></p>
	<?php endif; ?>
  <?php if (!empty($registration_url)): ?>
    <a class="register" href="<?= $registration_url ?>">Register</a>
  <?php endif; ?>
  <p class="more"><a href="<?= get_permalink($event_post->ID); ?>">+ More Details</a></p>
</article>
