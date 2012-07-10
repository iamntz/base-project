<?php
error_reporting( E_ALL - E_NOTICE );

//global $wpdb;
//$wpdb->show_errors();

define( 'CSS_VERSION', '1' );
define( 'JS_VERSION', '1' );

// wpml constants 
define( 'ICL_DONT_LOAD_NAVIGATION_CSS', true );
define( 'ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', true );
define( 'ICL_DONT_LOAD_LANGUAGES_JS', true );
define( 'ICL_DONT_PROMOTE', true );

global $ntz;
$current_lang   = ( defined( ICL_LANGUAGE_CODE ) ? ICL_LANGUAGE_CODE : 'en' );
$ntz['general'] = get_option( "ntz_settings_general_{$current_lang}" );
$ntz['social']  = get_option( "ntz_settings_social_{$current_lang}" );

define( 'THEME_PATH', get_bloginfo( 'stylesheet_directory' ) );

add_action( 'after_setup_theme', 'ntz_setup' );
if ( !function_exists('ntz_setup') ){
  function ntz_setup(){
    add_editor_style(); // Default: editor-style.css 

    add_theme_support( 'post-thumbnails' );
    //set_post_thumbnail_size( 100, 100, true );

    //add_image_size( $name, $width, $height, $crop ); 
    add_post_type_support( 'page', 'excerpt' );
    add_theme_support( 'automatic-feed-links' );

    // This theme uses wp_nav_menu() in one location.
    register_nav_menus( array(
      'primary' => __( 'Primary Navigation', 'ntz' ),
    ));
  }
}

add_filter( 'use_default_gallery_style', '__return_false' );
// ===================================
// = removing new admin bar (wp 3.1) =
// ===================================
//add_filter( 'show_admin_bar', '__return_false' );
//remove_action( 'personal_options', '_admin_bar_preferences' );

$requires = array( 'ntz_lib', 'ntzlib/lib', 'post_type' ); // including all required libs

if( is_admin() ){
  $requires = array_merge( $requires, array( 'write_panels', 'theme_settings' ) );
}

foreach($requires as $required){
  $includeThis = 'includes/'.$required.'.php';
  require ( $includeThis );
}

function ntz_widgets_init() {
  register_sidebar( array(
    'name' => __( 'Widget name', 'ntz' ),
    'id' => 'widget_name',
    'description' => __( 'Widget Description', 'ntz' ),
    'before_widget' => '<div class="widget-container %1$s %2$s">',
    'after_widget' => '</div>',
    'before_title' => '',
    'after_title' => '',
  ) );
}
//add_action( 'widgets_init', 'ntz_widgets_init' );