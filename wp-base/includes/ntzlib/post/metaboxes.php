<?php 
/**
* custom meta boxes builder
*/
class Ntz_Meta_box_builder extends Ntz_utils{
  private $options;
  function __construct( $user_options ){
    $this->options = array_merge( array(
      "fields"        => array(),
      "post_type"     => 'post',
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
    add_meta_box( 
      $this->options['meta_id'],
      $this->options['meta_title'],
      array( &$this, 'add_meta' ),
      $this->options['post_type'],
      $this->options['position'],
      $this->options['priority']
    );
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

        echo "<p>";
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
        echo "</p>";
      }
    }
    do_action( $this->options['meta_callback'] );
    echo '<input type="hidden" name="ntz_do" value="save:custom_metaboxes"/>';
  } // add_meta

  public function save_meta( $post_id ){
    if( !empty( $_REQUEST['ntz_do'] ) && stripos( $_REQUEST['ntz_do'], 'custom_metaboxes' ) >= 0 ){
      $ntz_do = explode( ':', filterGet( $_REQUEST['ntz_do'] ) );
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