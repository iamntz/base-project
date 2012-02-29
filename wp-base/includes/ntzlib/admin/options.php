<?php 

/* example usage:

// this MUST be called on `admin_init`

$social_settings = new Ntz_settings(array(
   "group" => "social_settings",
   "name"  => "social_settings",
   "save"  => null
 ));

// this can be called as submenu callback
$social_settings->form_builder(array(
 "title"        => "Social Networks Settings",
 "section_name" => "social_settings",
 "fields"       => array(
   array(
     "label" => "Twitter Username:",
     "type"  => "text",
     "name"  => "twitter_user"
   ),
   array(
     "label" => "page selector",
     "type"  => "page_selector",
     "name"  => "some_page"
   )
 )
));
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
        $return[$key] = $this->clean( $input );
        
      }
    }else{
      $return = $this->clean( $inputs );
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
    ?>

  <div class="wrap">
    <h2><?php echo $options['title']; ?></h2>
    <form action="options.php?lang=<?php echo $_REQUEST['lang']; ?>" method="post" class="ntzForm">
      <?php 
        settings_errors();
        settings_fields( $this->options['group'] );

        $stored_options = get_option( $this->options['name'] );

        if( $options['before_fields'] ){
          do_action( $options['before_fields'] );
        }

        foreach ( $options['fields'] as $key => $field ) {
          $name          = $this->options['name']."[{$field['name']}]";
          $default_value = $field['value'];
          //$value         = stripslashes( str_replace( "\n\"", "__NEW_LINE__", $stored_options[$field['name']] ) );
          $value =  preg_replace('/[\r\n]+/', "", $stored_options[$field['name']] )  ;
          //$value         = str_replace( "__NEW_LINE__", "", $stored_options[$field['name']] );
          if( empty( $value ) ){
            $value = $default_value;
          }

          if( $field['type'] != 'textarea' ){
            $value = esc_attr( $value );
          }

          if( $field['type'] == 'info' ){
            echo "<p>{$field['text']}</p>";
            continue;
          }

          if( $field['type'] != 'hidden' ){
            echo "<p><label>{$field['label']}</label>";
          }

          $extra_attr = '';

          if( is_array( $field['attr'] ) ) {
            foreach ( $field['attr'] as $key => $attr ) {
              $extra_attr .= $key . '=' . $attr;
            }
          }

          switch( $field['type'] ){
            case "page_selector":
              echo $this->list_pages( $name, $value );
            break;
            case "textarea":
              echo "<textarea name='{$name}' {$extra_attr}>{$value}</textarea>";
            break;
            default:
              echo "<input type='{$field['type']}' name='{$name}' value='{$value}' {$extra_attr} />";
            break;
          }
          echo "\n\n";
        }

        if( $options['after_fields'] ){
          do_action( $options['after_fields'] );
        }

     ?>
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