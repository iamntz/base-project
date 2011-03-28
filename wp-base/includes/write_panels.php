<?php 
global $entryAttribs;
$entryAttribs = array(
	"microsite" => array(
		"name"	=>	"_ntz_",
		"label"	=>	"label",
		"desc"	=>	"description",
		"std"		=>	"",
		"type"	=>	"text"
	),
);

function new_meta_boxes($post_data, $meta_info) {
	global $post, $entryAttribs;
	echo '<div class="ntz_panel">';
	// nonce :
	
	foreach($entryAttribs as $o){
		$val = get_post_meta($post->ID, $o['name'], true);
		switch ($o['type']){
			case "text":
			default:
				echo '<p><label><span class="label">'.$o['label'].'</span> <input type="text" name="'.$o['name'].'" id="'.$o['name'].'" value="'.$val.'" /></label><br/><small>'.$o['desc'].'</small>
				<input type="hidden" name="'.$o['name'].'_nonce" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />
				</p>';
			break;
		}
	}	
	echo '</div>';
?>
<style type="text/css" media="screen">
	.ntz_panel {text-align:right}
	.ntz_panel .label {float:left;line-height:22px}
	#ntz_work_entries { text-align:left; overflow:hidden }
		#ntz_work_entries textarea,
		#ntz_work_entries input[type="text"] { width:100%; display:block; }
		#ntz_work_entries textarea { height:50px; }
</style>
	<?php 
}

function create_meta_box() {
	if ( function_exists('add_meta_box') ) {
		//add_meta_box( 'new-meta-boxes', 'Extra links', 'new_meta_boxes', 'post', 'side', 'high' );	
	}
}

function save_postdata( $post_id ) {
	global $post, $post_id, $entryAttribs;
	if ( in_array($_POST['post_type'], array('page')) ) {
		if ( !current_user_can( 'edit_page', $post_id ) ) {return $post_id;}
	} else {
		if ( !current_user_can( 'edit_post', $post_id )) {return $post_id;}
	}
	foreach($entryAttribs as $o){
		if ( !wp_verify_nonce( $_POST[$o['name'].'_nonce'], plugin_basename(__FILE__) )) {
			return $post_id;
		}
		switch ($o['type']){
			case "checkbox":
			case "radio":
				update_post_meta($post_id, $o['name'], isset($_POST[$o['name']]));
			break;
			default:
				update_post_meta($post_id, $o['name'], $_POST[$o['name']]);
			break;
		}
	}
}
add_action('admin_menu', 'create_meta_box');  
add_action('save_post', 'save_postdata');  

