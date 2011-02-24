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
	  wp_register_script('jquery', PATH.'/js/lib/jquery-latest.js', '', 'x', 1);
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





