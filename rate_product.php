<?php
session_start();
define('CHECK_ME', true);
$root_dir = (defined('ROOT_DIR')) ? ROOT_DIR : './';
include($root_dir . 'abc.php');

global $intro;



$product_id = (int) $_POST['product_id'];
$rating = (int) $_POST['rating'];

if ($product_id > 0 && $rating >= 1 && $rating <= 5) {
	global $intro;

	$sql = "INSERT INTO maa_product_ratings (product_id, rating, is_approved) VALUES ('$product_id', '$rating', 0)";
	$intro->db->query($sql);

	echo "تم إرسال تقييمك، وسيتم مراجعته من قبل الإدارة.";
} else {
	echo "invalid";
}
