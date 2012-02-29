<?php 

$ntz_lib_includes = array(

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
  protected $wpdb;
  function __construct( $init = true ){
    global $wpdb;
    $this->wpdb = $wpdb;

    if( $init ){

    }
  }


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
        //$url .= ':' . $url_data['port'];
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


}//Ntz_utils 


global $ntzUtils;
$ntzUtils = new Ntz_utils();