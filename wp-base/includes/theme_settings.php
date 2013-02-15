<?php 
class Custom_Theme_Option extends Ntz_settings{
  protected $main_settings, $social_settings, $home_slider, $current_lang;
  function __construct( $reconstruct = false ){
    parent::__construct($reconstruct);

    // if you are using wpml plugin, this is useful to have different configs based on language
    $this->current_lang = ( defined( ICL_LANGUAGE_CODE ) ? ICL_LANGUAGE_CODE : 'en' );

    add_action( 'admin_menu', array( &$this, 'add_menus' ) );
    add_action( 'admin_init', array( &$this, 'settings_init' ) );
  }

  public function add_menus(){

    // main menu entry
    add_menu_page( 'Site Settings', 'Site Settings', 'edit_themes', 'menu_id', 
      array( &$this, 'settings_main' ), PATH.'/images/settings_logo.png' ); // 16x16px image
      
      // submenus
      add_submenu_page( 'menu_id', 'Analytics & Social Networks', 'Analytics & Social Networks', 'edit_themes', 
      'settings_social_networks', array( &$this, 'settings_social_networks' ) );
  } // add_menus

  public function settings_init(){

    $this->social_settings = new Ntz_settings(array(
      "group" => "ntz_settings_social_".$this->current_lang,
      "name"  => "ntz_settings_social_".$this->current_lang,
      "save"  => null
    ));

    $this->general_settings = new Ntz_settings(array(
      "group" => "ntz_settings_general_".$this->current_lang,
      "name"  => "ntz_settings_general_".$this->current_lang,
      "save"  => null
    ));

  } // settings_init

  public function settings_main(){
    add_action( 'language_selector', array( &$this, 'language_selector' ) );
    $this->general_settings->form_builder(array(
      "title"         => "Pages",
      "section_name"  => "ntz_settings_general_{$this->current_lang}",
      "before_fields" => 'language_selector',
      "fields"        => array(
        array(
          "label" => "Sample Page",
          "type"  => "page_selector",
          "name"  => "sample_page",
          "desc"  => "Lorem ipsum"
        ),
        array(
          "label" => "Sample field",
          "type"  => "text",
          "name"  => "sample_field",
          "attr"  => array(
            "placeholder" => "Sample Field"
          )
        )
      )
    ));
  } // settings_main

  public function settings_social_networks(){
    $this->social_settings->form_builder(array(
      "title"         => "Analytics & Social Networks",
      "section_name"  => "ntz_settings_social_{$this->current_lang}",
      "fields"        => array(
        array(
          "label" => "Google Analytics Tracking Code",
          "type"  => "text",
          "name"  => "g_analytics",
          "attr" => array(
            "placeholder" => "UA-XXXX-X"
          )
        ),
        array(
          "label" => "Twitter URL",
          "type"  => "text",
          "name"  => "twitter"
        ),
        array(
          "label" => "Facebook URL",
          "type"  => "text",
          "name"  => "facebook"
        )
      )
    ));
  } // settings_social_networks
}

$ntz_settings = new Custom_Theme_Option();