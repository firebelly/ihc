<?php
/**
 * Division taxonomy page
 */

$division = get_queried_object();
$with_image_class = $header_bg = '';
$division_contacts = Taxonomy_MetaData::get(get_query_var('taxonomy'), $division->term_id, 'contacts');
$header_image = Taxonomy_MetaData::get(get_query_var('taxonomy'), $division->term_id, 'featured_image');
if ($header_image) {
  $with_image_class = 'with-image';
  $header_bg = \Firebelly\Media\get_header_bg($header_image, $division->term_id);
}
$header_text = $division->description;
$secondary_header_text = Taxonomy_MetaData::get(get_query_var('taxonomy'), $division->term_id, 'secondary_header_text');
$secondary_header_text = strip_tags($secondary_header_text, '<u><strong><em><a><br><br/><p>');
$with_secondary_header_class = ($secondary_header_text) ? 'with-secondary-header' : '';
?>

<div class="content-wrap <?= $with_image_class ?>">

  <header class="page-header <?= $with_image_class ?> <?= $with_secondary_header_class ?>">
    <div class="container">
      <div class="image-wrap"<?= $header_bg ?>>
        <h2 class="flag"><?= $division->name ?></h2>

        <div class="header-text">
          <h1><?= $header_text ?></h1>
        </div>
      </div>
    </div>
    <?php if ($secondary_header_text): ?>
      <div class="secondary-header-text"><?= $secondary_header_text ?></div>
    <?php endif; ?>
  </header>

  <main>
    <?php
    $related_programs = \Firebelly\PostTypes\Program\get_programs(['division' => $division->slug]);
    if ($related_programs):
    ?>
      <h2 class="flag"><?= $division->name ?> Programs:</h2>
      <div class="article-list masonry">
        <?php foreach ($related_programs as $program_post): ?>
          <?php include(locate_template('templates/article-program.php')); ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <aside class="main">
    <div class="article-list">
      <?= \Firebelly\Utils\get_related_event_post($division->slug) ?>
      <?= \Firebelly\Utils\get_related_news_post($division->slug) ?>
    </div>

    <?php if ($division_contacts): ?>
      <div class="contacts user-content">
        <h3>Focus Area Contacts</h3>
        <?= apply_filters('the_content', $division_contacts) ?>
      </div>
    <?php endif ?>
  </aside>

</div>