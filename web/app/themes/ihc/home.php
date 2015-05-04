<?php 
/**
 * Blog landing page
 */

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$per_page = get_option('posts_per_page');
$total_posts = $GLOBALS['wp_query']->found_posts;
$total_pages = ceil($total_posts / $per_page);
$post = get_page_by_path('/news');
?>

<?php get_template_part('templates/page', 'image-header'); ?>
<section class="main">
  <div class="entry-content">
    <h4 class="flag">Filter Posts</h4>

    <?php include(locate_template('templates/filters.php')); ?>

    <div class="load-more-container masonry article-list">
      <?php if (have_posts()): ?>
        <?php 
        while (have_posts()) : the_post();
          $news_post = $post;
          include(locate_template('templates/article-news.php'));
        endwhile; 
        ?>
      </div>
      <?php if ($total_pages>1): ?>
        <div class="load-more" data-page-at="<?= $paged ?>" data-focus-area="<?= $filter_focus_area ?>" data-program="<?= $filter_program ?>" data-per-page="<?= $per_page ?>" data-total-pages="<?= $total_pages ?>"><a class="no-ajaxy button" href="#">Load More</a></div>
      <?php endif ?>
    <?php else: ?>    
      <div class="notice">
        <p>No posts found.</p>
      </div>
    <?php endif; ?>
  </div>
</section>
<aside class="page-with-img">
    <?php include(locate_template('templates/thought-of-the-day.php')); ?>
  </aside>