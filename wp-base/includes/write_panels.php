<?php 
global $entryAttribs;
$entryAttribs = array(
	"file" => array(
		"name"	=>	"_ntz_post_image",
		"label"	=>	"post image",
		"desc"	=>	"this image is displayed in top-right corner",
		"type"	=>	"upload"
	), // file
	"text" => array(
		"name"	=>	"_ntz_text",
		"label"	=>	"label",
		"desc"	=>	"description",
		"type"	=>	"text"
	), // text
	"checkbox" => array(
		"name"	=>	"_ntz_check",
		"label"	=>	"agree?",
		"desc"	=>	"description",
		"type"	=>	"checkbox"
	),	// checkbox
	"radio" => array(
		"name"	=>	"_ntz_radio",
		"desc"	=>	"description",
		"label"	=>	"Pick one: ",
		"type"	=>	"radio",
		"items"		=>	array(
			array(
				"label"		=>	"Male?",
				"value"		=>	"m",
				"default"	=>	0
			),
			array(
				"label"		=>	"Female?",
				"value"		=>	"f",
				"default"	=>	1
			)
		)
	), // radio
	"select"	=>	array(
		"name"		=>	"_ntz_select",
		"desc"		=>	"description",
		"label"		=>	"dropdown",
		"type"		=>	"select",
		"items"		=>	array(
			array(
				"option"	=>	"option 1",
				"value"		=>	"val1",
				"default"	=>	1
			),
			array(
				"option"	=>	"option 2",
				"value"		=>	"val2",
				"default"	=>	0
			),
			array(
				"option"	=>	"option 3",
				"value"		=>	"val3",
				"default"	=>	0
			)
		)
	), // select
);

function new_meta_boxes( $post_data, $meta_info ) {
	global $post, $entryAttribs;
	$hasUploader = false;
	echo '<div class="ntz_panel">';
	foreach( $entryAttribs as $o ){
		$val = get_post_meta( $post->ID, $o['name'], true );
		echo '<p><input type="hidden" name="'.$o['name'].'_nonce" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
		switch ( $o['type'] ){
			case "checkbox":
				$isChecked = ( $val == 1 ? 'checked="checked"' : '' ); // we store checked checkboxes as 1
				echo '<label>'.$o['label'].' <input type="checkbox" name="'.$o['name'].'" id="'.$o['name'].'" '.$isChecked.' /></label>';
			break; // checkbox

			case "radio":
				echo '<span class="label">'.$o['label'].'</span><span class="radioItems">';
				foreach( $o['items'] as $radio ){
					if( $val=='' && $radio['default'] == 1 ) { $val = $radio['value']; }
					$isChecked = ( $val == $radio['value'] ) ? 'checked="checked"' : '';
					echo '<label><input type="radio" name="'.$o['name'].'" value="'.$radio['value'].'" '.$isChecked.' /> '.$radio['label'].'</label>';
				}
				echo '</span>';
			break;// radio

			case "select":
				echo '<label><span class="label textLabel">'.$o['label'].'</span></label> <select name="'.$o['name'].'">';
				foreach( $o['items'] as $dropdown_option ){
					if( $val=='' && $dropdown_option['default'] == 1 ) { $val = $dropdown_option['value']; }
					$isSelected = ( $val == $dropdown_option['value'] ) ? 'selected="selected"' : '';
					echo '<option value="'.$dropdown_option['value'].'" '.$isSelected.'>'.$dropdown_option['option'].'</option>';
				}
				echo '</select>';
			break;// select

			case "upload":
				$hasUploader = true;
				echo '<label><span class="label textLabel">'.$o['label'].'</span> <input type="text" name="'.$o['name'].'" id="'.$o['name'].'" value="'.$val.'" class="ntzUploadTarget" /> <button class="ntzUploadTrigger button-secondary">&#x25B2;</button></label>';
			break;

			case "text":
			default:
				echo '<label><span class="label textLabel">'.$o['label'].'</span> <input type="text" name="'.$o['name'].'" id="'.$o['name'].'" value="'.$val.'" /></label>';
			break; // text & default
		}// swtich
		echo '<br/><small class="desc">'.$o['desc'].'</small></p>';
	}// foreach
	echo '</div>';

if( $hasUploader==true ){ ntz_img_uploader(); } ?>

<style type="text/css" media="screen">
	.ntz_panel p {
		overflow:auto
	}
	.ntz_panel select {min-width:150px;}
	.ntz_panel .label {float:left; width:100px;}
	.ntz_panel .label.textLabel { line-height:22px;}
	.ntz_panel .radioItems label{
		margin-left:10px;
	}
	.ntz_panel .desc {
		float:left;
		width:100%;
		text-align:right;
	}
</style>
	<?php 
}

function create_meta_box() {
	if ( function_exists( 'add_meta_box' ) ) {
		add_meta_box( 'ntz_meta_box', 'Post Meta Box', 'new_meta_boxes', 'post', 'side', 'high' );	 // change `post` with custom post type 
	}
}

function save_postdata( $post_id ) {
	global $post, $post_id, $entryAttribs;
	if ( in_array( $_POST['post_type'], array('page') ) ) {
		if ( !current_user_can( 'edit_page', $post_id ) ) {return $post_id;}
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) ) {return $post_id;}
	}
	foreach($entryAttribs as $o){
		if ( !wp_verify_nonce( $_POST[$o['name'].'_nonce'], plugin_basename(__FILE__) )) {
			return $post_id;
		}
		switch ($o['type']){
			case "checkbox":
				update_post_meta( $post_id, $o['name'], isset( $_POST[$o['name']] ) );
			break;
			default:
				update_post_meta($post_id, $o['name'], $_POST[$o['name']]);
			break;
		}
	}
}
add_action( 'admin_menu', 'create_meta_box' );  
add_action( 'save_post', 'save_postdata' );