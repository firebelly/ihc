<?php 
$event = \Firebelly\PostTypes\Event\get_event_details($event_post);
// $body = apply_filters('the_content', $event_post->post_content);
// $event_timestamp = get_post_meta($event_post->ID, '_cmb2_event_timestamp', true);
// $end_time = get_post_meta( $event_post->ID, '_cmb2_end_time', true);
// $start_time = date('g:iA', $event_timestamp);
// $time_txt = $start_time . (!empty($end_time) ? '–' . preg_replace('/(^0| )/','',$end_time) : '');
// $registration_url = get_post_meta($event_post->ID, '_cmb2_registration_url', true);
// $lat = get_post_meta($event_post->ID, '_cmb2_lat', true);
// $lng = get_post_meta($event_post->ID, '_cmb2_lng', true);
// $desc = date('M d, Y @ ', $event_timestamp) . $time_txt;
// $address = get_post_meta($event_post->ID, '_cmb2_address', true);
// $address = wp_parse_args($address, array(
//     'address-1' => '',
//     'address-2' => '',
//     'city'      => '',
//     'state'     => '',
//     'zip'       => '',
//  ));
// $year = date('Y', $event_timestamp);
?>
<article class="event map-point" data-lat="<?= $event->lat ?>" data-lng="<?= $event->lng ?>" data-title="<?= $event->post_title ?>" data-desc="<?= $event->desc ?>" data-id="<?= $event->ID ?>">
  <time datetime="<?= date('c', $event->event_timestamp); ?>"><span class="month"><?= date('M', $event->event_timestamp) ?></span> <span class="day"><?= date('d', $event->event_timestamp) ?></span><?= ($event->year != date('Y') ? ' <span class="day">'.$event->year.'</span>' : '') ?></time>
  <h1><a href="<?= get_permalink($event->ID); ?>"><?= $event->title ?></a></h1>
  <h3><?= $event->time_txt ?></h3>
  <?php if (!empty($event->address['city'])): ?>
	  <p><?= $event->address['city'] ?>, <?= $event->address['state'] ?> <?= $event->address['zip'] ?></p>
	<?php endif; ?>
  <?php if (!empty($event->registration_url)): ?>
    <a class="register" href="<?= $event->registration_url ?>">Register</a>
  <?php endif; ?>
  <p class="more"><a href="<?= get_permalink($event->ID); ?>">+ More Details</a></p>
</article>
