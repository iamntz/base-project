<?php 
function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") { $pageURL .= "s"; }
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

global $ntz;
$ntz = json_decode(get_option('ntz_settings'));

function ntz_init() {
 if(!is_admin()){
	  wp_deregister_script('jquery');
	  wp_register_script('jquery', PATH.'/js/lib/jquery-latest.min.js', '', 'x', 1);
	  wp_enqueue_script('ntz', PATH .'/js/script.js', array('jquery'), 1, 1);
  }
}
add_action('init', 'ntz_init');

function is_child_of($topid, $thispageid = null){
	global $post;
	
	if($thispageid == null){
		$thispageid = $post->ID; # no id set so get the post object's id.
	}
	$current = get_page($thispageid);
	
	if($current->post_parent != 0){ # so there is a parent 
		if($current->post_parent != $topid) {
			return is_child_of($topid, $current->post_parent); # not that page, run again
		} else {
			return true; # are so it is	
		}
	} else {
		return false; # no parent page so return false
	}	
}


// =============================================
// = replace emails with images (spam related) =
// =============================================
global $emailPattern;
$emailPattern = ">[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?>";

function replaceEmails($email){
	$encodedEmail = '<img class="msgImg" src="'.PATH.'/msg.php?text='.base64_encode($email).'"/>';
	return $encodedEmail;
}
function ntz_replace_emails($content){
	global $emailPattern;
	$replacement = ' ${1}'; 

	$ret = preg_replace_callback($emailPattern, create_function('$m', 'return replaceEmails($m[0]);'), $content);
	return $ret;
}

// add_filter("the_content", "ntz_replace_emails");

// ==================
// = custom excerpt =
// ==================
function new_excerpt($words = 90, $link_text = 'view all', $allowed_tags = '<a>,<p>,<i>,<em>,<strong>,<b>,<blockquote>,<li>,<ul>,<ol>', $container = 'p', $smileys = 'no' ){
	global $post;
  if ( $allowed_tags == 'all' ) $allowed_tags = '<a>,<p>,<i>,<em>,<b>,<strong>,<ul>,<ol>,<li>,<span>,<blockquote>,<img>';
	$excerpt = $post->post_excerpt; 
	if($excerpt){
		echo '<p>',$excerpt,'</p>';
	}else {
	  $text = preg_replace('/\[.*\]/', '', strip_tags(apply_filters('the_content', $post->post_content), $allowed_tags));
	  $text = explode(' ', $text);
	  $tot = count($text);
	  for ($i=0; $i<$words; $i++) { $output .= $text[$i] . ' '; }
		$output = force_balance_tags($output.'');
		echo '<p>',$output,'</p>';
	}
	if($link_text != 'hide') {
		echo '<p class="ta-r readmoreWrap"><a href="'.get_permalink().'" class="readmore">'.$link_text.'</a></p>';
	}
}

// =============================
// = removing l10n from header =
// =============================
if ( !is_admin() ) {
	function my_init_method() {
		wp_deregister_script( 'l10n' );
	}
	add_action('init', 'my_init_method'); 
}


// ================
// = short titles =
// ================
function get_short_title($long=999){
	global $post;
	$title = get_the_title();
	return ( strlen($title)>$long ? substr( $title, 0, $long).' &#0133;' : $title );
}

// ==================================
// = search within custom post type =
// ==================================
function filterGet($cleanThis){
	$q = htmlspecialchars(trim(stripslashes(strip_tags($cleanThis))), ENT_QUOTES, 'UTF-8');
	$q = filter_var($q, FILTER_SANITIZE_STRING);
	return $q;
}
function searchAll( $query ) {
	if ( is_search() && is_array($_GET['post_type']) ){
		$post_type = array_map("filterGet", $_GET['post_type']);
		$query->set( '&post_type', $post_type);
	}	
	return $query;
}
add_filter( 'pre_get_posts', 'searchAll' );

// ============================================
// = make default feed for a custom post type =
// ============================================
function myfeed_request($qv) {
	if (isset($qv['feed']))
		$qv['post_type'] = 'blog';
	return $qv;
}
add_filter('request', 'myfeed_request');
