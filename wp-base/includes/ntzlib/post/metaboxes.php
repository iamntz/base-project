<?php 

/* USAGE:

  $fields = array(
    array(
      "type"  => "checkbox",
      "label" => "check this out!",
      "name"  => "_checbox_name",
      "help"  => "small checkbox help"
    ),

    array(
      "type"  => "select",
      "label" => "select demo",
      "name"  => "_select_demo",
      "opts"  => array("option 1", "option 2"), 
      "sel"   => "option 1", // selected
      "help"  => "small select help"
    ),

    array(
      "type"  => "text", // you can use all text based inputs (text, email, url, etc)
      "label" => "just a normal text input",
      "name"  => "_input_text_demo",
      "value" => "default value",
      "help"  => ""//no help
    )
  );


  // usage: 
  $destinationSettingsMeta = new Ntz_Meta_box_builder(array(
    "fields"     => $fields,
    "post_type"  => array( 'post' ), // array of post types: post, page or any other custom post type
    "meta_title" => 'Meta box title',
    "meta_id"    => "metabox id", // useful for styling
    "position"   => "side", // normal/side/advanced
    "priority"   => "high" // high/core/default/low
    "meta_callback" => "custom_post_meta_calback"
    "save_callback" => "custom_post_meta_save_callback"
  ));

  add_action( 'custom_post_meta_calback', "add_some_extra_awesomeness" );
  function add_some_extra_awesomeness(){
    // extra code that will be executed at the end of metabox 
  } // add_some_extra_awesomeness

  add_action( 'custom_post_meta_save_callback', "add_some_extra_awesomeness_save" );
  function add_some_extra_awesomeness_save(){
    // extra code that will be executed after the metabox save
  } // add_some_extra_awesomeness_save

*/

/**
* custom meta boxes builder
*/
class Ntz_Meta_box_builder extends Ntz_utils{
  private $options;


  function __construct( $user_options ){
    $this->options = array_merge( array(
      "fields"        => array(),
      "post_type"     => array( 'post' ),
      "meta_title"    => 'meta box',
      "meta_id"       => "meta_id",
      "position"      => "normal",
      "priority"      => "low",
      "meta_callback" => 'meta_callback',
      "save_callback" => 'save_callback'
    ), $user_options );
    add_action( 'admin_init', array( &$this, 'meta_init' ) );
    add_action( 'save_post', array( &$this, 'save_meta' ) );
  }


  public function meta_init(){
    if( is_array( $this->options['post_type'] ) ){
      foreach ( $this->options['post_type'] as $post_type ) {
        add_meta_box( 
          $this->options['meta_id'],
          $this->options['meta_title'],
          array( &$this, 'add_meta' ),
          $post_type,
          $this->options['position'],
          $this->options['priority']
        );
      }
    }
  } // init


  public function add_meta( $post_data, $meta_info ){
    $post_id = $post_data->ID;
    if( is_array( $this->options['fields'] ) && count( $this->options['fields'] ) > 0 ){

      foreach( $this->options['fields'] as $single_field ){
        $field       = null;
        $value       = get_post_meta( $post_id, $single_field['name'], true );
        if( !empty( $value ) && !array( $value ) ){
          $value = stripslashes( $value );
        }
        $label       = "<label for='{$single_field['name']}'>{$single_field['label']}</label> ";
        $extra_attr  = '';
        if( !empty( $value ) && !is_array( $value ) ){
          $maybe_value = unserialize( $value );
        }

        echo "<div class='ntz_meta_row'>";
          echo "<input type='hidden' name='ntz_custom_meta_nonce_{$single_field['name']}' value='" .  wp_create_nonce( "ntz_custom_meta_nonce_{$single_field['name']}" ) . "' />";


        if( is_array( $maybe_value ) ){
          $value = $maybe_value;
        }

        if( is_array( $single_field['attr'] ) && count( $single_field['attr'] ) > 0 ){
          foreach( $single_field['attr'] as $key => $attribute ){
            $extra_attr .= " {$key}='{$attribute}'";
          }
        }
        if( isset( $extra_attr['multiple'] ) && !empty( $extra_attr['multiple'] ) ){
          // adding array name in case the select is multiple (array)
          $single_field['name'] = $single_field['name'] . '[]';
        }

        switch( $single_field['type'] ){
          case "checkbox":
            $field = "<input {$extra_attr} type='checkbox' name='{$single_field['name']}' id='{$single_field['name']}'". ( $value == 1 ? " checked" : "" ) ." />";
          break; // checkbox

          case "multicheckbox":
            $field = "<ul class='multiCheckboxes'>";
            foreach( $single_field['values'] as $key => $multicheckbox_values ){
              $checked = '';

              foreach( $value as $value_key => $multicheckbox_value ){
                if( $value_key == $key ){
                  $checked = ' checked="checked"';
                }
              }

              $field .= "<li><label><input {$extra_attr} type='checkbox' {$checked} name='{$single_field['name']}[{$key}]' value='{$key}' /> {$multicheckbox_values}</label></li>";
            }
            $field .= "</ul>";
          break;

          case "select":
            $field = "<select {$extra_attr} name='{$single_field['name']}' id='{$single_field['name']}' class='widefat'>";
              foreach( $single_field['opts'] as $key => $option ){
                $selected = '';
                if( is_array( $value ) ){
                  foreach( $value as $value_key => $multiselect_value ){
                    if( $multiselect_value == $key ){
                      $selected = ' selected="selected"';
                    }
                  }
                }else {
                   $selected = ( $value == $key  ? " selected" : "" );
                }

                $field .= "<option value='{$key}' {$selected}>{$option}</option>";
              }
            $field .= "</select>";
          break; // select

          case "textarea":
            $field = "<textarea {$extra_attr} name='{$single_field['name']}' id='{$single_field['name']}' class='widefat'>{$value}</textarea>";
          break;

          case "file":
            $preview = ( (int)$value > 0 ? wp_get_attachment_image( (int)$value, 'thumbnail' ) : '' );
            $field = "
              <div class='upload_preview'>{$preview}</div>
              <input {$extra_attr} type='hidden' 
              name='{$single_field['name']}' id='{$single_field['name']}' 
              value=\"" . (int)$value . "\"
              class='ntzUploadTarget' />
              <span class='uploadTrigger button-secondary'>upload</span>
              ";
          break; // default

          default: // input
            $field = "<input {$extra_attr} type='{$single_field['type']}' 
              name='{$single_field['name']}' id='{$single_field['name']}' 
              value=\"" . esc_attr( $value ) . "\"
              class='widefat' />";
          break; // default
        }

          echo $label;
          echo $field;
          if( !empty( $single_field['help'] ) ){ // we don't want random 'br' tags
            echo "<br/><small>{$single_field['help']}</small>";
          }
        echo "</div>";
      }
    }
    do_action( $this->options['meta_callback'] );
    echo '<input type="hidden" name="ntz_do" value="save:custom_metaboxes"/>';
  } // add_meta


  public function save_meta( $post_id ){
    if( !empty( $_REQUEST['ntz_do'] ) && stripos( $_REQUEST['ntz_do'], 'custom_metaboxes' ) >= 0 ){
      $ntz_do = explode( ':', $this->clean( $_REQUEST['ntz_do'] ) );
    }
    if( $ntz_do[0] == 'save' && $ntz_do[1] == 'custom_metaboxes' ){

      foreach( $this->options['fields'] as $single_field ){
        $update_value = $_REQUEST[$single_field['name']];

        if( wp_verify_nonce( $_REQUEST["ntz_custom_meta_nonce_{$single_field['name']}"], "ntz_custom_meta_nonce_{$single_field['name']}" ) ){

          if( $single_field['type'] == 'checkbox' ){
            $update_value = isset( $update_value ) ? '1' : '0';
          }else {

            if( $single_field['type'] == 'number' ){
              $update_value = str_replace( ',', '.', $update_value );
            }

            if( is_array( $update_value ) ){ // multiple checkboxes or multiple selects
              $update_value = serialize( $update_value );
            }

          }
          update_post_meta( $post_id, $single_field['name'], $update_value );
        }

      }
      do_action( $this->options['save_callback']);
    }
  } // save_meta
}