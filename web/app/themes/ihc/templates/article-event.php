<article class="event">
  <h1><?= $event_post->post_title ?></h1>
  <h3><?= $time_txt ?></h3>
  <p><?= $address['city'] ?>, <?= $address['state'] ?> <?= $address['zip'] ?></p>
  <?php if (!empty($registration_url)): ?>
    <a class="register" href="<?= $registration_url ?>">Register</a>
  <?php endif; ?>
  <p class="more"><a href="<?= get_permalink($event_post->ID); ?>">+ More Details</a></p>
</article>
