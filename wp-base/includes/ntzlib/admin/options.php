<?php 

/*
 *  Class usage:

class Admin_options extends Ntz_utils{
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
      add_submenu_page( 'menu_id', 'Social Networks', 'Social Networks', 'edit_themes', 
      'settings_social_networks', array( &$this, 'settings_social_networks' ) );
  } // add_menus

  public function settings_init(){

    $this->social_settings = new Ntz_settings(array(
      "group" => "settings_social_networks_".$this->current_lang,
      "name"  => "settings_social_networks_".$this->current_lang,
      "save"  => null
    ));

    $this->general_settings = new Ntz_settings(array(
      "group" => "general_settings_".$this->current_lang,
      "name"  => "general_settings_".$this->current_lang,
      "save"  => null
    ));

  } // settings_init

  public function settings_main(){
    $current_lang = $this->current_lang;
    $this->general_settings->form_builder(array(
      "title"         => "Pages",
      "section_name"  => "general_settings_{$current_lang}",
      "fields"        => array(
        array(
          "type"  => "hidden",
          "name"  => "lang",
          "value" => $current_lang
        ),
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
    $current_lang = $this->current_lang;
    $this->social_settings->form_builder(array(
      "title"         => "Social Networks",
      "section_name"  => "social_settings_{$current_lang}",
      "fields"        => array(
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

$ntz_settings = new Admin_options();


// getting the options
// Add this in functions.php:

global $ntz;
$current_lang   = ( defined( ICL_LANGUAGE_CODE ) ? ICL_LANGUAGE_CODE : 'en' );
$ntz['general'] = get_option( "general_settings_{$current_lang}" );
$ntz['social']  = get_option( "general_settings_{$current_lang}" );

*/



/**
 * Utility class for setting & getting custom settings
 * @param array $options the settings group & settings name.
 */
class Ntz_settings extends Ntz_utils{
  protected $options;
  function __construct( $options = array() ){
    parent::__construct(false);

    $this->options = $options;

    if( !empty( $options ) ){
      register_setting( $options['group'], $options['name'], array( &$this, 'save_settings' ) );
    }
  }


  /**
   * callback for settings function, useful for post-processing
   * @param  string|array $input the submited form
   * @return string|array
   */
  public function save_settings( $inputs ){
    if( $this->options->save ){
      do_action( $this->options->save, $inputs );
    }
    if( is_array( $inputs ) ){
      foreach ( $inputs as $key => $input ) {
        $return[$key] = ( $this->clean( $input ) == '' ? null : $this->clean( $input ) );
      }
    }else{
      $return = ( $this->clean( $inputs ) == '' ? null : $this->clean( $inputs ) );
    }

    return $return;
  } // save_settings


  /**
   * Form builder for custom settings
   * @param  array  $options 
   *         @param string $option[title] the title for the settings form
   *         @param string $option[section_name] the name of the group name (setted on class init)
   *         @param array $option[fields] fields for settings. The structure is:
   *                                      @param string $fields['label'] the text that is near the form field
   *                                      @param string $fields['type'] what kind of form field is this? 
   *                                             right now only text and page selector are supported
   *                                      @param string $field['name'] the name of the form element
   * @todo  add color picker
   * @todo  add datepicker
   * @todo  add checkboxes & radios (or at least multiple selects)
   * @return string
   */
  public function form_builder( $options = array() ){
    $options = array_merge( array(
      "title"         => "",
      "section_name"  => "",
      "before_fields" => null,
      "after_fields"  => null,
      "fields"        => array()
    ), $options );
    $lang = !empty( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : 'en';
    ?>

  <div class="wrap">
    <h2><?php echo $options['title']; ?></h2>
    <form action="options.php?lang=<?php echo $lang; ?>" method="post" class="ntzForm">
      <table class="form-table">
      <?php 
        settings_errors();
        settings_fields( $this->options['group'] );

        $stored_options = get_option( $this->options['name'] );

        if( $options['before_fields'] ){
          do_action( $options['before_fields'] );
        }

        foreach ( $options['fields'] as $key => $field ) {
          $name    = $this->options['name']."[{$field['name']}]";
          $default = !empty( $field['default'] ) ? $field['default'] : '';
          $value   = !empty( $stored_options[$field['name']] ) ? $stored_options[$field['name']] : null;

          if( $field['type'] != 'hidden' ){
            echo "<tr>\n<th scope='row'><label>" . ( !empty( $field['label'] ) ? $field['label'] : '' ) ."</label></th>\n";
          }else {
            echo "<tr class='hidden_field'>\n";
          }

          if( empty( $value ) && !is_null( $value ) ){
            $value = $default;
          }

          $default = esc_attr( $default );
          $value   = esc_attr( $value );

          if( $field['type'] != 'textarea' ){
            $value = esc_attr( $value );
          }

          if( $field['type'] == 'info' ){
            echo "<td colspan='2' class='wide_row'>{$field['text']}</td>\n";
            continue;
          }

          $extra_attr = '';

          if( isset( $field['attr'] ) && is_array( $field['attr'] ) && count( $field['attr'] ) > 0 ) {
            foreach ( $field['attr'] as $key => $attr ) {
              $extra_attr .= " {$key}='{$attr}'";
            }
          }
          echo "<td>";
          switch( $field['type'] ){
            case "page_selector":
              echo $this->list_pages( $name, $value );
            break;

            case "textarea":
              echo "<textarea name='{$name}' {$extra_attr}>{$value}</textarea>";
            break;

            case "file":
              $img_size = isset( $field['preview_size'] ) ? $field['preview_size'] : 'thumbnail';
              $preview  = ( (int)$value > 0 ? wp_get_attachment_image( (int)$value, $img_size ) : '' );
              echo "
                <input {$extra_attr} type='hidden' 
                name='{$name}' id='{$name}' 
                value='{$value}'
                class='ntzUploadTarget' />
                <span class='uploadTrigger button-secondary'>upload</span>
                <span class='upload_preview' data-imgsize='{$img_size}' title='Double click to remove'>{$preview}</span>
                ";
            break; // default

            case "select":
              echo "<select name='{$name}' {$extra_attr}>";
                foreach( $field['opts'] as $option_key => $option ){
                  $selected = '';
                  if( $option_key == $value ){
                    $selected =' selected="selected"';
                  }
                  echo "<option value='{$option_key}' {$selected}>{$option}</option>";
                }
              echo "</select>";
            break;

            default:
              echo "<input type='{$field['type']}' name='{$name}' value='{$value}' class='regular-text' {$extra_attr} />";
            break;
          }

          if( isset( $default ) && !empty( $default ) && $default != $value ){
            echo "<a href='#' title='Restore to defaults' class='ntzRestoreDefault' data-default='{$default}'>&#10226;</a>";
          }
          if( !empty( $field['desc'] ) ){
            echo "<br/><span class='description'>{$field['desc']}</span>";
          }
        }
        echo "</td>\n";

        if( $options['after_fields'] ){
          do_action( $options['after_fields'] );
        }

     ?>
   </table>
      <p><input name="Submit" type="submit" value="Save Changes" class="button-primary" /></p>
    </form>
  </div>
    <?php 
  } // form_builder


  /**
   * function used to generate a <select> markup with all pages
   * @param  string  $select_name  the name of the select tag.
   * @param  integer $value        the id of the selected page.
   * @param  string  $default_text the default text of the select box.
   * @return string
   */
  public function list_pages( $select_name = null, $value = 0, $default_text = ' -- Select an Option --' ){
    if( !$select_name ){ return; }
    $all_pages = get_pages( 0 );
    $ret       = '<select name="' . $select_name . '" id="' . $select_name . '"><option>' . $default_text . '</option>';

    foreach( $all_pages as $single_page ){
      $sel  = ( $single_page->ID == $value ) ? ' selected="selected"' : '';
      $ret .= '<option value="' . $single_page->ID . '"' . $sel . '>' . $single_page->post_title . '</option>';
    }

    $ret .= '</select>';
    return $ret;
  } // list_pages


  /**
   * get a stored option
   * @param  string $option_name option name
   * @return string|array
   */
  public function get( $option_name = null ){
    if( !$option_name ){ return; }
    return get_option( $option_name );
  } // get

  function __get( $option_name ) {
    return $this->get( $option_name );
  }

}//Ntz_settings  extends Ntz_utils