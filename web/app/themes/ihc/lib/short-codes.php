<?php
/**
 * Custom short codes
 */

namespace Firebelly\Utils\ShortCodes;
use Firebelly\Utils;

// Shortcode [related_news]
add_shortcode('related_news', __NAMESPACE__ . '\related_news_shortcode');
function related_news_shortcode($atts) {
  extract(shortcode_atts(array(
       'tag' => '',
    ), $atts));
  $args = array(
    'numberposts' => 3,
    'tags' => $tag,
    'category_name' => 'happenings',
  );
  if ($tag != '') {
    $args['tax_query'] = array(
      array(
        'taxonomy' => 'post_tag',
        'field' => 'slug',
        'terms' => $tag
      )
    );
  }

?>
  <div class="grid wrap-extend">
    <div class="article-list flex-item grid">
      <?php
        // Happenings posts
        if ($happenings_posts = get_posts($args)):
          foreach ($happenings_posts as $happenings_post) {
            include(locate_template('templates/happenings-list.php'));
          }
        endif;
      ?>
    </div>
   </div>
<?php
}