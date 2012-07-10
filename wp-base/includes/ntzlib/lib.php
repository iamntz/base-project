<?php 

$ntz_lib_includes = array(
  "general/pluralize",
  "post_type" => "post/post_type"
);

$ntz_lib_admin_includes = array(
  "metaboxes"     => "post/metaboxes",
  "theme_options" => "admin/options"
);

if( is_admin() ){
  $ntz_lib_includes = array_merge( $ntz_lib_includes, $ntz_lib_admin_includes );
}

if( is_array( $ntz_lib_includes ) ){
  foreach( $ntz_lib_includes as $key => $include ){
    require_once( "{$include}.php" );
  }
}

/**
* Ntz_utils - a collection of utilities for WordPress
* @author Ionut Staicu
* @license Private
* @link http://iamntz.com
* @version 1.0.0.0
*/

class Ntz_utils{
  protected $wpdb, $lib_path;
  function __construct( $init = true ){
    global $wpdb;
    $this->wpdb = $wpdb;
    $this->path = THEME_PATH;

    $this->lib_path = $this->path.'/includes/ntzlib';
    if( !$init ){
      add_action( 'admin_init', array( &$this, 'style_and_scripts' ) );
    }
    add_action( 'wp_ajax_get_image_versions', array( &$this, 'get_image_versions' ) );
  }

  /**
   *  A loader for all scripts & styles used across site
   *  
   *  @return void 
   */
  public function style_and_scripts(){
    wp_register_style( "ntz_admin_color_picker_css", "{$this->lib_path}/js/colorpicker/css/colorpicker.css", '', '1.0', 'all' );
    
    wp_register_style( "ntz_admin_css", "{$this->lib_path}/css/admin.css", array( 'ntz_admin_color_picker_css', 'media', 'thickbox' ), '1.0', 'all' );

    wp_register_script( "ntz_admin_color_picker_js", "{$this->lib_path}/js/colorpicker/js/colorpicker.js",  array("jquery"),  1, 1);

    wp_register_script( "ntz_admin_js", "{$this->lib_path}/js/admin.js", 
      array("jquery", "jquery-ui-core", 
        "jquery-ui-sortable", "media-upload", 
        "thickbox", "ntz_admin_color_picker_js"), 
      1, 1);

    if( is_admin() ){
      wp_enqueue_style( 'ntz_admin_css' );
      wp_enqueue_script( 'ntz_admin_js' );
    }

  } // style_and_scripts


  /**
   * get all versions of an image as an array
   * 
   * @param  integer $image_id the image id
   * @param  boolean $echo  either we are displaying (AJAX) or not the images
   * 
   * @return void|array
   * 
   * @todo split ajax functions to a separate class
   */
  public function get_image_versions( $image_id = 0, $echo = true ){
    $sizes = get_intermediate_image_sizes();
    $images = Array();
    $img_id = ( (int)$image_id > 0 ? $image_id : (int)$_REQUEST['img_id'] );
    if( is_array( $sizes ) ){
      foreach ( $sizes as $key => $size ) {
        $image_sized = wp_get_attachment_image( $img_id, $size );
        $images[$size] = $image_sized;
      }
    }

    if( $echo ){
      echo json_encode( $images );
      die();
    }else{
      return $images;
    }
  } // get_image_versions

  /**
   * A function that allows StdObjects (like terms, posts and so on )
   * to be sorted on a simple array basis
   * @param object $object the StdObject
   * @param array $sorter the simple array. e.g: array(1,2,3)
   * @return object
   */
  public function sort_std_class( $object, $sorter ){
    foreach( $object as $cat ) {
      $cat->sort_key = 999;
      for ( $i = 0; $i < count( $sorter ); $i++ ){
        if ( $sorter[$i] == $cat->term_id ){
          $cat->sort_key = $i;
        }
      }
    }
    usort( $object, array( $this, 'sort_std_class_helper' ) );
    return $object;
  } // sort_std_class
    protected function sort_std_class_helper( $a, $b ){
      if ( $a->sort_key == $b->sort_key ){ return 0; }
      return ( $a->sort_key < $b->sort_key ) ? -1 : 1;
    } // sort_std_class_helper


  /**
   * get current page url
   * @return string
   */
  public function get_url(){
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") { $pageURL .= "s"; }
    $pageURL .= "://";
    $pageURL .= $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
    return $pageURL;
  } // get_url


  /**
   * addURLParameters allows you to add parameters as an key=>value array
   * @param string $url    the url you want to alter. leave `null` to get current page url
   * @param array  $params the parameters you want to add
   */
  public function addURLParameters( $url = null, $params = array() ){
    if( !$url ){
      $url = $this->get_url();
    }
    foreach( $params as $key=>$param ) { $url = $this->addURLParameter( $url, $key, $param ); }
    return $url;
  }

    protected function addURLParameter( $url, $paramName, $paramValue ) {
      $url_data = parse_url( $url );
      $params = array();
      parse_str( $url_data['query'], $params );
      $params[$paramName] = $paramValue;   
      $url_data['query'] = http_build_query( $params, '', '&' );
      return $this->build_url( $url_data );
    }

    protected function build_url( $url_data ){
      $url = $url_data['scheme'] . '://';
      if( isset( $url_data['user'] ) ){
        $url .= $url_data['user'];
        if( isset( $url_data['pass'] ) ){
          $url .= ':' . $url_data['pass'];
        }
        $url .= '@';
      }
      $url .= $url_data['host'];
      if( isset( $url_data['port'] ) ){
        $url .= ':' . $url_data['port'];
      }
      $url .= $url_data['path'];
      if( isset( $url_data['query'] ) ){
        $url .= '?' . $url_data['query'];
      }
      if( isset( $url_data['fragment'] ) ){
        $url .= '#' . $url_data['fragment'];
      }
      return $url;
    }


  /**
   * Simple way to clean strings (_GET/_POST)
   * @param  string $cleanThis the string needs to be cleaned
   * @return string
   * @todo   clean arrays & multidimensional arrays
   */
  public function clean( $cleanThis = '' ){
    $q = htmlspecialchars( trim( stripslashes( $cleanThis ) ), ENT_QUOTES, 'UTF-8' );
    $q = filter_var($q, FILTER_SANITIZE_STRING);
    return $q;
  } // FunctionName




  /**
   * convert hex color to rgb
   * @param  string $color hex color
   * @return array
   */
  public function get_rgb( $color = '#000000' ){
    $rgb = array();

    for ( $x = 0; $x < 3; $x++ ){
      $rgb[$x] = hexdec( substr( $color, ( 2*$x ), 2 ) );
    }

    return $rgb;
  } // get_rgb


  /**
   * automatically detect if a background color is dark or bright
   * in order to correctly have a certain contrast
   * @param  string  $color the hex color
   * @return boolean
   */
  public function is_bright_color( $color = '#ffffff' ){
    $total = array_sum( $this->get_rgb( $color ) );
    if( $total <=463 ) {
      return false;
    } else {
      return true;
    }
  } // is_bright_color


  /**
   * adjust color nuance
   * @param  string  $color  hex color code
   * @param  integer $amount how much do you want to variate
   * @return string
   */
  public function adjust_color( $color = '#ffffff', $amount = 0 ){
    $color = str_replace('#','',$color);
    $rgb   = $this->get_rgb( $color );

    $r     = max( 0, min( 255, $rgb[0] + $amount ) );
    $g     = max( 0, min( 255, $rgb[1] + $amount ) );
    $b     = max( 0, min( 255, $rgb[2] + $amount ) );

    return dechex( $r ) . dechex( $g ) . dechex( $b );

  } // adjust_color


  /**
   * get an image based on its ID stored in DB
   * @param  string|integer  $attach     the ID or the URL of the image
   * @param  string          $size       the image size
   * @return string|boolean              the image URL or false
   * @todo play nice with remote images
   */
  public function get_attachment( $attach = '', $size = 'thumbnail' ){
    $src = false;
    if( is_numeric( $attach ) ){
      $src = wp_get_attachment_image_src( $attach, $size );
      $src = $src[0];
    }else {

    }
    return $src;
  } // get_attachment


  /**
   * add a custom image and a custom color for terms
   * 
   * @param array $term_names terms you want to enable these awesome features.
   * see save_category_image function for a hint on how to get these values on frontend
   */
  public function add_category_image( $term_names = array() ){
    foreach ( (array) $term_names as $term ) {
      add_action ( "{$term}_add_form_fields", array( &$this, 'term_image_and_color' ) );
      add_action ( "created_{$term}", array( &$this, 'save_category_image' ) );

      add_action ( "{$term}_edit_form_fields", array( &$this, 'term_image_and_color' ) );
      add_action ( "edited_{$term}", array( &$this, 'save_category_image' ) );
    }
  } // add_category_image

  public function term_image_and_color( $tag = null ){
      $category_images = get_option( 'category_images' );
      $category_color = get_option( 'category_colors' );
      $category_image = '';

      //if( is_array( $category_images ) && array_key_exists( $tag->term_id, $category_images ) ) {
      //  $category_image = wp_get_attachment_image( $category_images[$tag->term_id], 'thumbnail' );
      //}

      $editing_tag = (int)$_REQUEST['tag_ID'] > 0 ? true : false;

      if( $editing_tag ) {
        $wrapper = 'tr';
        $head    = 'th';
        $content = 'td';
      }else {
        $wrapper = 'div';
        $head    = 'div';
        $content = 'div';
      }
     ?>

      <<?php echo $wrapper; ?> class="form-field">

        <<?php echo $head; ?> scope="row" valign="top">
        <?php if ($category_image !=""){ 
          echo $category_image;
        } ?>
        </<?php echo $head; ?>>

        <<?php echo $content; ?>>
          <label>Category Image</label>
          <div class="upload_preview"><?php
            if( (int) $category_images[$tag->term_id] > 0 ){
              $preview_image = $this->get_attachment( (int) $category_images[$tag->term_id], 'thumbnail' );
              echo "<img src='{$preview_image}'>";
            }
          ?></div>
          <input type="hidden" name="category_image" class=" ntzUploadTarget" value="<?php echo $category_images[$tag->term_id]; ?>" />
          <a title="Upload image" class="ntzUploadTrigger button" href="#">Select image for category</a>

        </<?php echo $content; ?>>
      </<?php echo $wrapper; ?>>

      <<?php echo $wrapper; ?> class="form-field">

        <<?php echo $head; ?> scope="row" valign="top">

        </<?php echo $head; ?>>

        <<?php echo $content; ?>>
          <label>Category Color</label>
          <input type="text" name="category_color" class="ntzColorpicker" value="<?php echo $category_color[$tag->term_id]; ?>" />
        </<?php echo $content; ?>>
      </<?php echo $wrapper; ?>>

    <?php
  } // term_image_and_color

  public function save_category_image( $term_id = 0 ){
    if ( isset( $_POST['category_image'] ) ) {
      //load existing images
      $category_images = get_option( 'category_images' );

      //load existing colors
      $category_colors = get_option( 'category_colors' );

      $category_images[$term_id] = (int)$_POST['category_image'];
      $category_colors[$term_id] = $this->clean( $_POST['category_color'] );

      //save the option array
      update_option( 'category_images', $category_images );
      update_option( 'category_colors', $category_colors);
    }
  } // save_category_image


}//Ntz_utils 


global $ntzUtils;
$ntzUtils = new Ntz_utils();