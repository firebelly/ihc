<?php
/**
 * Focus Area taxonomy page 
 */

$focus_area = get_queried_object();
$header_class = $header_bg = '';
$focus_area_contacts = Taxonomy_MetaData::get(get_query_var('taxonomy'), $focus_area->term_id, 'contacts');
$header_image = Taxonomy_MetaData::get(get_query_var('taxonomy'), $focus_area->term_id, 'featured_image');
if ($header_image) {
  $header_class = 'with-image';
  $header_bg = ' style="background-image:url('.$header_image.');"';
}
$header_text = $focus_area->description;
?>

<header class="page-header <?= $header_class ?>">
  <div class="container" <?= $header_bg ?>>
    <h4 class="flag"><?= $focus_area->name ?></h4>

    <div class="header-text">
      <h1><?= $header_text ?></h1>
    </div>
  </div>
</header>

<section class="main">
  <?php 
  $related_programs = \Firebelly\PostTypes\Program\get_programs($focus_area->slug);
  if ($related_programs):
  ?>
    <h4 class="flag"><?= $focus_area->name ?> Programs:</h4>
    <div class="masonry articles-list">
      <?php 
      foreach ($related_programs as $program_post):
        include(locate_template('templates/article-program.php'));
      endforeach; 
      ?>
    </div>
  <?php endif; ?>
</section>

<aside class="main">
  <div class="related article-list">
    <?= \Firebelly\Utils\get_related_event_post($focus_area->slug) ?>
    <?= \Firebelly\Utils\get_related_news_post($focus_area->slug) ?>
  </div>

  <?php if ($focus_area_contacts): ?>
    <div class="contacts user-content">
      <h3>Focus Area Contacts</h3>
      <?= $focus_area_contacts ?>
    </div>
  <?php endif ?>
</aside>
