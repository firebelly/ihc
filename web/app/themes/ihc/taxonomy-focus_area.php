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

<header class="<?= $header_class ?>"<?= $header_bg ?>>
  <h1 class="header-text"><?= $header_text ?></h1>
</header>

<section class="main">
  <?php 
  $related_programs = \Firebelly\PostTypes\Program\get_programs($focus_area->slug);
  if ($related_programs):
  ?>
    <h2 class="banner"><?= $focus_area->name ?> Programs:</h2>
    <div class="masonry articles-list">
      <?php 
      foreach ($related_programs as $program_post):
        include(locate_template('templates/article-program.php'));
      endforeach; 
      ?>
    </div>
  <?php endif; ?>
</section>

<aside>
  <div class="article-list">
    <?= \Firebelly\Utils\get_related_event_post($focus_area->slug) ?>
    <?= \Firebelly\Utils\get_related_news_post($focus_area->slug) ?>
  </div>
  <div class="contacts user-content">
    <h3>Focus Area Contacts</h3>
    <?= $focus_area_contacts ?>
  </div>
</aside>
