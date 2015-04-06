<?php 
/**
 * Blog landing page
 */

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$per_page = get_option('posts_per_page');
$total_news = wp_count_posts('post')->publish;
$total_pages = ceil($total_news / $per_page);
$page = get_page_by_title('Blog');
$page_content = apply_filters('the_content', $page->post_content);
// $secondary_content = apply_filters('the_content', get_post_meta($page->ID, '_cmb2_secondary_content', true));
?>

<header>
	<div class="user-content">
		<?php echo $page_content; ?>
	</div>

	<aside>
		<?php include(locate_template('templates/thought-of-the-day.php')); ?>
	</aside>
</header>

<section class="main">
	<div class="filters hide">
		Program:
		Focus Area:
		<button>Filter</button>
	</div>

	<div class="load-more-container masonry article-list">
		<?php 
		while (have_posts()) : the_post();
			$news_post = $post;
			include(locate_template('templates/article-news.php'));
		endwhile; 
		?>
	</div>
	<div class="load-more" data-page-at="<?= $paged ?>" data-per-page="<?= $per_page ?>" data-total-pages="<?= $total_pages ?>"><a class="no-ajaxy" href="#">+ Load More</a></div>
</section>