<?php 
$body = apply_filters('the_content', $post->post_content);
$event_timestamp = get_post_meta($post->ID, '_cmb2_event_timestamp', true);
$venue = get_post_meta($post->ID, '_cmb2_venue', true);
$cost = get_post_meta($post->ID, '_cmb2_cost', true);
$end_time = get_post_meta( $post->ID, '_cmb2_end_time', true);
$start_time = date('g:iA', $event_timestamp);
$time_txt = $start_time . (!empty($end_time) ? '–' . preg_replace('/(^0| )/','',$end_time) : '');
$registration_url = get_post_meta($post->ID, '_cmb2_registration_url', true);
$address = get_post_meta($post->ID, '_cmb2_address', true);
$address = wp_parse_args($address, array(
    'address-1' => '',
    'address-2' => '',
    'city'      => '',
    'state'     => '',
    'zip'       => '',
 ));
$add_to_calendar_url = admin_url('admin-ajax.php') . "?action=event_ics&amp;id={$post->ID}&amp;nc=" . time();
?>
<article class="event">
  <h1><?= $post->post_title ?></h1>
  <div class="body"><?= $body ?></div>

  <aside>
    <div id="map"></div>

    <h3>When:</h3>
    <?= date('l, F j, Y', $event_timestamp) ?>
    <br><?= $time_txt ?></p>

    <h3>Where:</h3>
    <?= $venue ?> 
    <br><?= $address['address-1'] ?> 
    <?php if (!empty($address['address-2'])): ?>
      <br><?= $address['address-2'] ?>
    <?php endif; ?>
    <br><?= $address['city'] ?>, <?= $address['state'] ?> <?= $address['zip'] ?>

    <h3>Cost:</h3>
    <p class="cost">
    <?php if (!$cost): ?>
      Free, open to the public.
    <?php else: ?>
      <?= $cost ?>
    <?php endif; ?>
    </p>

    <ul>
      <?php if (!empty($registration_url)): ?>
        <li><a class="register" target="_blank" href="<?= $registration_url ?>">Register For Event</a></li>
      <?php endif; ?>
      <li><a class="add-to-calendar" href="<?= $add_to_calendar_url ?>">Add To My Calendar</a></li>
    </ul>
  </aside>
</article>
