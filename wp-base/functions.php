<?php
error_reporting(E_ALL - E_NOTICE);

global $ntz;
$ntz = json_decode(get_option('ntz_settings'));
define('PATH', get_bloginfo('stylesheet_directory'));

add_action('after_setup_theme', 'ntz_setup');
if (!function_exists('ntz_setup')){
	function ntz_setup(){
		add_editor_style();
	
		add_theme_support( 'post-thumbnails' );
	
		add_theme_support('automatic-feed-links');
	
		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(array(
			'primary' => __('Primary Navigation', 'ntz'),
		));
	}
}

add_filter( 'use_default_gallery_style', '__return_false' );
// ===================================
// = removing new admin bar (wp 3.1) =
// ===================================
add_filter( 'show_admin_bar', '__return_false' );
remove_action( 'personal_options', '_admin_bar_preferences' );

$requres = array('ntz_lib', 'post_type', 'theme_settings', 'write_panels'); // including all required libs
foreach($requres as $required){
	$includeThis = 'includes/'.$required.'.php';
	require ($includeThis);
}

function ntz_widgets_init() {
	register_sidebar(array(
		'name' => __('Widget name', 'ntz'),
		'id' => 'widget_name',
		'description' => __( 'Widget Description', 'ntz' ),
		'before_widget' => '<div class="widget-container %1$s %2$s">',
		'after_widget' => '</div>',
		'before_title' => '',
		'after_title' => '',
	));
}
add_action( 'widgets_init', 'ntz_widgets_init' );