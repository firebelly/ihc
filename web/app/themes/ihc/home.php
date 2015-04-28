<?php 
/**
 * Blog landing page
 */

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$per_page = get_option('posts_per_page');
$total_news = wp_count_posts('post')->publish;
$total_pages = ceil($total_news / $per_page);
$post = get_page_by_path('/news');
?>

<?php get_template_part('templates/page', 'image-header'); ?>
<section class="main">
<div class="entry-content">
	<h4 class="flag">Filter Posts</h4>
	<div class="filters">
		<div class="program-topic">Program:</div>
		<div class="focus-area-topic">Focus Area:</div>
		<button class="button">Filter</button>
	</div>
	<div class="load-more-container masonry article-list">
		<?php 
		while (have_posts()) : the_post();
			$news_post = $post;
			include(locate_template('templates/article-news.php'));
		endwhile; 
		?>
	</div>
	<div class="load-more" data-page-at="<?= $paged ?>" data-per-page="<?= $per_page ?>" data-total-pages="<?= $total_pages ?>"><a class="no-ajaxy button" href="#">Load More</a></div>
	</div>
</section>
<aside>
		<?php include(locate_template('templates/thought-of-the-day.php')); ?>
	</aside>