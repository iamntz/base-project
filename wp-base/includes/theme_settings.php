<?php 

add_action( 'admin_menu', 'ntz_settings' );
function ntz_settings(){
	add_options_page( 'Site Settings', 'Site Settings', 'administrator', 'ntz_theme_settings', 'ntz_theme_settings' );
}
function ntz_theme_settings(){
	if( isset( $_POST['ntz_do']) && $_POST['ntz_do']=='save' ){
		foreach( $_POST as $ntz_settingsID=>$ntz_setting ){
			$ntz_settings[$ntz_settingsID] = $ntz_setting;
		}
		update_option( 'ntz_settings', json_encode($ntz_settings) );
	}
	ntz_custom_styles();
	$ntz_settings = json_decode( get_option('ntz_settings') );
	echo '<form class="wrap ntz_custom_form" method="post" action=""><h2>Site settings</h2> <input type="hidden" name="ntz_do" value="save" />';

	//echo '<p><label>News Page:</label>';
	//	echo ntz_drop_pages('news_page', $ntz_settings->news_page);
	//echo '</p>';
	//echo '<br/>';

	echo '<p><label>Google Analytics Account:</label>';
		echo '<input type="text" name="g_analytics" value="'.$ntz_settings->g_analytics.'" id="g_analytics" />';
	echo '</p>';
	
	echo '<p><label>Twitter Account:</label>';
		echo '<input type="text" name="twitter_user" value="'.$ntz_settings->twitter_user.'" id="twitter_user" />';
	echo '</p>';
	
	echo '<p><input type="submit" value="Save Settings" accesskey="s" class="button-primary" name="submit"></p>';
	echo '</form>';
}
function ntz_drop_pages( $select_name, $value=null, $defaultText = '------------' ){
	$all_pages = get_pages(0);
	$ret = '<select name="'.$select_name.'" id="'.$select_name.'"><option>'.$default.'</option>';
	foreach( $all_pages as $single_page ){
		$is_selected = ( $single_page->ID == $value ) ? ' selected="selected"' : '';
		$ret .= '<option value="'.$single_page->ID.'"'.$is_selected.'>'.$single_page->post_title.'</option>';
	}
	$ret .= '</select>';
	return $ret;
}

function ntz_drop_articles( $options=array( 'select_name', 'value'=>null, 'post_type'=>'Any' ) ){
	$ret = '<select name="'.$options['select_name'].'" id="'.$options['select_name'].'"><option>------------</option>';
	$all_articles = new WP_Query();
	$all_articles->query( 'showposts=9999&post_type='.$options['post_type'] );
	global $post;
	while ( $all_articles->have_posts() ){ $all_articles->the_post();
		$is_selected = ($post->ID == $options['value']) ? ' selected="selected"' : '';
		$ret .= '<option value="'.$post->ID.'"'.$is_selected.'>'.$post->post_title.'</option>';
	}
	$ret .= '</select>';
	return $ret;	
}

function ntz_custom_styles(){
	?>
	<style type="text/css" media="screen">
		.customTaxonomies fieldset {}
			.ntz_custom_form h6 { font-size:16px;font-weight:100; }
			.ntz_custom_form p { line-height:24px;overflow:hidden; }
			.ntz_custom_form label { float:left;width:300px;text-align:right;margin-right:15px; }
			.ntz_custom_form .text { width:500px; }
			.ntz_custom_form h2 { margin-bottom:20px; }
			.ntz_custom_form fieldset .button-primary { visibility:hidden;position:relative;top:-1px; }
			.ntz_custom_form fieldset p:hover .button-primary { visibility:visible; }
			.ntz_custom_form textarea { width:500px; }
			.ntz_custom_form textarea[name="home_page_slider"] {
				display:block;
				width:100%;
				height:450px;
			}
			.ntz_custom_form small { display:block; }
			.ntz_custom_form input[type="text"],
			.ntz_custom_form select { width:300px; }
	</style>
	<?php 
}
