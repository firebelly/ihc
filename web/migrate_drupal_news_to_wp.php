<?php 
// Import IHC news from Drupal 5

// Bootstrap WP
define('BASE_PATH', dirname(__FILE__).'/wp/');
define('WP_USE_THEMES', false);
require_once(BASE_PATH . 'wp-load.php');
require_once(BASE_PATH . 'wp-admin/includes/image.php');
global $wpdb;

// map drupal categories to new wp
$category_conversions = [
	'IHC in the News' => 'In The News',
	'Press Release'   => 'Press Release'
];

// Connect to old Drupal db
$drupal_db = new PDO('mysql:host=127.0.0.1;dbname=ihc_old;charset=utf8', 'root', 'root', array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

$prefix = '_cmb2_';
$start = !empty($_GET['start']) ? $_GET['start'] : '0';
$num = !empty($_GET['num']) ? $_GET['num'] : '10';
$next_url = '/migrate_drupal_news_to_wp.php?start='.($start+$num).'&num='.$num;

// timestamp was 6 hours ahead on drupal site
$time_offset = -(6 * 3600);

$stmt = $drupal_db->prepare("SELECT * FROM ihc_node WHERE type=? AND status=? LIMIT ?,?");
$stmt->execute(['news', 1, $start, $num]);
$nodes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>News Import-O-Matic</title>
  <?php if (!empty($_GET['autostart']) && count($nodes)>0): ?>
  	<meta http-equiv="refresh" content="5; url=<?= $next_url ?>&autostart=1">
  <?php endif; ?>
</head>
<body>

<?php
echo '<h1>Importing News '.$start.'-'.($start+$num).'</h1>';
echo '<p>Next: <a href="'.$next_url.'">'.$next_url.'</a></p>';
echo '<p>AutoNext: <a href="'.$next_url.'&autostart=1">'.$next_url.'</a></p>';
echo '<hr>';

if (count($nodes)==0) die('No more posts found.');

foreach($nodes as $node) {
	print_r($node);

	// Check if post already imported
	$imported = $wpdb->get_var( "SELECT COUNT(*) FROM wp_postmeta WHERE meta_key='_nid' AND meta_value=".$node['nid'] );

	if ($imported) {
		echo '<h2>Post already imported!</h2>';
	} else {
		// Get node category
		// field_news_category_value = IHC in the News, Press Release, Awards Received, Awards Given, 
		$category_sql = $drupal_db->prepare("SELECT * FROM ihc_content_field_news_category WHERE nid=?");
		$category_sql->execute([ $node['nid'] ]);
		$category_row = $category_sql->fetch();
		$category = $category_row['field_news_category_value'];

		// Get node body & teaser
		$body_sql = $drupal_db->prepare("SELECT body,teaser FROM ihc_node_revisions WHERE nid=? ORDER BY timestamp DESC LIMIT 1");
		$body_sql->execute([ $node['nid'] ]);
		$body_row = $body_sql->fetch();
		$body = $body_row['body'];
		$teaser = $body_row['teaser'];

		// Insert basic data into WP
		$post_id = wp_insert_post([
			'post_status' => 'publish',
			'post_type' => 'post',
			'post_author' => 1,
			'post_content' => $body,
			'post_excerpt' => $teaser,
			'post_title' => $node['title'],
			'post_date' => date('Y-m-d H:i:s', $node['created']),
		]);

		if($post_id) {
			echo '<h2>Post inserted ok: <a href="/wp/wp-admin/post.php?post='.$post_id.'&action=edit">'.$post_id.'</a> &nbsp; <small><a href="http://www.prairie.org/node/'.$node['nid'].'/edit">Old</a></small></h2>';

			// Store nid to avoid duplicate imports
			update_post_meta($post_id, '_nid', $node['nid']);

			// Get publication date field from this ridiculous table (oh, Drupal)
			echo '<p>SELECT * FROM ihc_content_type_news WHERE nid='.$node['nid'].' AND vid='.$node['vid'].'</p>';
			$news_sql = $drupal_db->prepare("SELECT * FROM ihc_content_type_news WHERE nid=? AND vid=?");
			$news_sql->execute([ $node['nid'], $node['vid'] ]);
			$news_row = $news_sql->fetch();
			print_r($news_row);
			$publication_date_value = $news_row['field_news_publication_date_value'];
			if ($publication_date_value) {
				update_post_meta($post_id, $prefix.'publication_date', date('m/d/Y', strtotime($publication_date_value)));
			}

			// Replace images!

			// [img_assist|nid=29337|title=|desc=|link=none|align=center|width=318|height=159]
			$teaser_with_new_images = preg_replace_callback('/\[img_assist\|nid=(\d+)\|title=\|desc=\|link=([^|]+)\|align=([^|]+)\|width=([^|]+)\|height=([^|]+)\]/', 'replace_dumb_drupal_img_tags', $teaser);
			$body_with_new_images = preg_replace_callback('/\[img_assist\|nid=(\d+)\|title=\|desc=\|link=([^|]+)\|align=([^|]+)\|width=([^|]+)\|height=([^|]+)\]/', 'replace_dumb_drupal_img_tags', $body);

			// Were there any images? Update post_content if so
			if ($body_with_new_images != $body || $teaser_with_new_images != $teaser) {
				wp_update_post([
					'ID'           => $post_id,
					'post_excerpt' => $teaser_with_new_images,
					'post_content' => $body_with_new_images,
				]);
			}
			
			// Set category if key exists in conversion table
			if (array_key_exists($category, $category_conversions)) {
				wp_set_object_terms($post_id, $category_conversions[$category], 'category');
			}
			
		} // if post inserted ok
	} // if post already imported
} // foreach nodes


//////////////////////////


// [0] => [img_assist|nid=29061|title=|desc=|link=none|align=left|width=300|height=360]
// [1] => 29061
// [2] => none
// [3] => left
// [4] => 300
// [5] => 360

/**
 * replace dumb drupal img shorttags
 */
function replace_dumb_drupal_img_tags($matches) {
	$nid = $matches[1];
	$new_img = get_drupal_image($nid);
	return '<img src="' . $new_img . '" data-link="' . $matches[2] . '"  data-align="' . $matches[3] . '" data-width="' . $matches[4] . '" data-height="' . $matches[5] . '">';
}

// ihc_files.nid 
// ihc_files.filename = '_original' 
// ihc_files.filepath = 'files/ihc/images/Rahm-Chuy-Debate.jpg'
// http://www.prairie.org/files/ihc/images/Rahm-Chuy-Debate.jpg

/**
 * get old drupal image from ihc_node & ihc_files
 */
function get_drupal_image($nid) {
	global $drupal_db, $post_id;

	$node_sql = $drupal_db->prepare("SELECT * FROM ihc_node WHERE nid=?");
	$node_sql->execute([ $nid ]);
	$node_row = $node_sql->fetch();
	$title = $node_row['title'];

	$image_sql = $drupal_db->prepare("SELECT * FROM ihc_files WHERE nid=? AND filename='_original'");
	$image_sql->execute([ $nid ]);
	$image_row = $image_sql->fetch();
	// import external image to wordpress and return new URL
	$new_img = import_image_to_wordpress('http://www.prairie.org/' . $image_row['filepath'], $post_id, $title);
	return $new_img;
}

/**
 * import an external image into wordpress media library
 */
function import_image_to_wordpress($file_url, $post_id, $title='') {
	global $wpdb;
	$site_url = get_option('siteurl');

	if(!$post_id) {
		return false;
	}

	// Directory to import to	
	$import_dir = '/app/uploads/migrated_media/';

	// If the directory doesn't exist, create it	
	if(!file_exists(dirname(__FILE__) . $import_dir)) {
		mkdir(dirname(__FILE__) . $import_dir);
	}

	// Get filename from url
	$tmp_arr = explode('/', $file_url);
	$new_filename = array_pop($tmp_arr);
	$new_file_url = $site_url . $import_dir . $new_filename;

	if (@fclose(@fopen($file_url, 'r'))) { // Make sure the remote file actually exists
		copy($file_url, dirname(__FILE__) . $import_dir . $new_filename);

		$file_info = getimagesize(dirname(__FILE__) . $import_dir . $new_filename);
		$file_title = (!empty($title)) ? $title : $new_filename;

		// Create an array of attachment data to insert into wp_posts table
		$image_data = [
			'post_author' => 1, 
			'post_date' => current_time('mysql'),
			'post_date_gmt' => current_time('mysql'),
			'post_title' => $file_title, 
			'post_status' => 'inherit',
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_name' => sanitize_title_with_dashes(str_replace('_', '-', $new_filename), '', 'save'),
			'post_modified' => current_time('mysql'),
			'post_modified_gmt' => current_time('mysql'),
			'post_parent' => $post_id,
			'post_type' => 'attachment',
			'guid' => $new_file_url,
			'post_mime_type' => $file_info['mime'],
			'post_excerpt' => '',
			'post_content' => ''
		];

		$uploads = wp_upload_dir();
		$save_path = $uploads['basedir'] . '/migrated_media/' . $new_filename;

		// Insert the database record
		$attach_id = wp_insert_attachment($image_data, $save_path, $post_id);

		// Generate metadata and thumbnails
		if ($attach_data = wp_generate_attachment_metadata($attach_id, $save_path)) {
			wp_update_attachment_metadata($attach_id, $attach_data);
		}

		// Optional make it the featured image of the post it's attached to
		// $rows_affected = $wpdb->insert($wpdb->prefix.'postmeta', array('post_id' => $post_id, 'meta_key' => '_thumbnail_id', 'meta_value' => $attach_id));
	} else {
		return false;
	}

	return $new_file_url;
}
?>
</body></html>
