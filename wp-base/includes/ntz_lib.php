<?php 
function curPageURL() {
  $pageURL = 'http';
  if ($_SERVER["HTTPS"] == "on") { $pageURL .= "s"; }
  $pageURL .= "://";
  if ($_SERVER["SERVER_PORT"] != "80") {
    $pageURL .= $_SERVER["HTTP_HOST"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
  } else {
    $pageURL .= $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
  }
  return $pageURL;
}

function ntz_init() {
 if( !is_admin() ){
    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', PATH.'/js/lib/jquery-latest.min.js', '', 'x', 1 );
    wp_enqueue_script( 'ntz', PATH .'/js/script.js', array('jquery'), JS_VERSION, 1 );
  }
}
add_action( 'init', 'ntz_init' );

function is_child_of( $topid, $thispageid = null ){
  global $post;

  if( $thispageid == null ){
    $thispageid = $post->ID; # no id set so get the post object's id.
  }
  $current = get_page( $thispageid );

  if( $current->post_parent != 0 ){ # so there is a parent 
    if( $current->post_parent != $topid ) {
      return is_child_of( $topid, $current->post_parent ); # not that page, run again
    } else {
      return true; # are so it is 
    }
  } else {
    return false; # no parent page so return false
  } 
}


// =============================================
// = replace emails with images (spam related) =
// =============================================
global $emailPattern;
$emailPattern = ">[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?>";

function replaceEmails( $email ){
  $encodedEmail = '<img class="msgImg" src="'.PATH.'/msg.php?text='.base64_encode($email).'"/>';
  return $encodedEmail;
}
function ntz_replace_emails( $content ){
  global $emailPattern;
  $replacement = ' ${1}'; 

  $ret = preg_replace_callback( $emailPattern, create_function( '$m', 'return replaceEmails($m[0]);' ), $content );
  return $ret;
}

// add_filter( "the_content", "ntz_replace_emails" );

// ==================
// = custom excerpt =
// ==================
function new_excerpt( $words = 90, $link_text = 'view all', $allowed_tags = '<a>,<p>,<i>,<em>,<strong>,<b>,<blockquote>,<li>,<ul>,<ol>', $container = 'p', $smileys = 'no' ){
  global $post;
  if ( $allowed_tags == 'all' ) {
    $allowed_tags = '<a>,<p>,<i>,<em>,<b>,<strong>,<ul>,<ol>,<li>,<span>,<blockquote>,<img>';
  }
  $excerpt = $post->post_excerpt; 
  if( $excerpt ){
    echo '<p>',$excerpt,'</p>';
  }else {
    $text = preg_replace('/\[.*\]/', '', strip_tags(apply_filters('the_content', $post->post_content), $allowed_tags));
    $text = explode(' ', $text);
    $tot = count($text);
    for ($i=0; $i<$words; $i++) { $output .= $text[$i] . ' '; }
    $output = force_balance_tags($output.'');
    echo '<p>',$output,'</p>';
  }
  if( $link_text != 'hide' ){
    echo '<p class="ta-r readmoreWrap"><a href="'.get_permalink().'" class="readmore">'.$link_text.'</a></p>';
  }
}

// =============================
// = removing l10n from header =
// =============================
if ( !is_admin() ){
  function remove_l10n_from_header(){
    wp_deregister_script( 'l10n' );
  }
  add_action('init', 'remove_l10n_from_header'); 
}


// ================
// = short titles =
// ================
function get_short_title( $long=999 ){
  global $post;
  $title = get_the_title();
  return ( strlen( $title )>$long ? substr( $title, 0, $long ).' &#0133;' : $title );
}

// ==================================
// = search within custom post type =
// ==================================
function filterGet( $cleanThis ){
  $q = htmlspecialchars( trim( stripslashes( strip_tags( $cleanThis ) ) ), ENT_QUOTES, 'UTF-8' );
  $q = filter_var($q, FILTER_SANITIZE_STRING);
  return $q;
}
function search_in_all_custom_post_type( $query ) {
  if ( is_search() && is_array( $_GET['post_type'] ) ){
    $post_type = array_map( "filterGet", $_GET['post_type'] );
    $query->set( '&post_type', $post_type );
  }
  return $query;
}
//add_filter( 'pre_get_posts', 'search_in_all_custom_post_type' );

// ============================================
// = make default feed for a custom post type =
// ============================================
function custom_post_type_feed( $qv ) {
  if ( isset( $qv['feed'] ) )
    $qv['post_type'] = 'blog';
  return $qv;
}
//add_filter( 'request', 'custom_post_type_feed' );


// ======================================
// = grab feed from multiple post types =
// ======================================

function my_get_posts( $query ) {
  if ( is_feed() ){
    $query->set( 'post_type', array( 'post', 'another_post_type' ) );
  }
  return $query;
}
// add_filter( 'pre_get_posts', 'my_get_posts' );


// ==============================
// = adding extra params to url =
// ==============================
function addURLParameters( $url, $params ){
  foreach( $params as $key=>$param ) { $url = addURLParameter( $url, $key, $param ); }
  return $url;
}
function addURLParameter( $url, $paramName, $paramValue ) {
  $url_data = parse_url( $url );
  $params = array();
  parse_str( $url_data['query'], $params );
  $params[$paramName] = $paramValue;   
  $url_data['query'] = http_build_query( $params, '', '&' );
  return build_url( $url_data );
}

function build_url( $url_data ){
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


// ========================
// = nice formated arrays =
// ========================
if( !function_exists( 'print_pre' ) ) {
function print_pre( $s ) {
    echo '<pre>';
      print_r( $s );
    echo '</pre>';
  }
}



// ===============
// = js uploader =
// ===============
function ntz_img_uploader(){
  // add these enqueues to admin_init or admin_menu hook
  // wp_enqueue_script( array("jquery", "jquery-ui-core", "jquery-ui-sortable", 'media-upload', 'thickbox') );
  // wp_enqueue_style( array( "media", "thickbox", ) );
?>
  <script>
    jQuery(document).ready(function($) {
      var oldSendToEditor = window.send_to_editor;
      $('.ntzUploadTrigger').live('click', function() {
        var ntzUploadTarget = $(this).parent().find('.ntzUploadTarget'),
            ntzUploadTargetId = $(this).parent().find('.ntzUploadTargetId'),
            ntzGetId,
            attach_id_pattern = new RegExp("send\[[0-9]*\]");
        window.clearInterval( ntzGetId );

        ntzGetId = window.setInterval(function(){
          if( !$('#TB_iframeContent').length ){ window.clearInterval( ntzGetId ); return; };
          var iframe = $( $('#TB_iframeContent')[0].contentWindow.document.body ),
              uploadID = $('.savesend .button', iframe).filter(function(){ return $(this).closest('table:visible').length; }).attr('id');
              if( uploadID ){
                uploadID = uploadID.replace('send[', '');
                uploadID = uploadID.replace(']', '');
                if( ntzUploadTargetId.val() !== uploadID ){
                  ntzUploadTargetId.val( uploadID ).trigger( 'has_id' );
                }
              }
        }, 500);

        window.send_to_editor = function( html ) {
          console.log(html);
          imgurl = $('img',html).attr('src') || $(html).attr('src');
          ntzUploadTarget.val(imgurl).focus().blur();
          
          ntzUploadTarget = '';
          tb_remove();
          window.clearInterval( ntzGetId );
          ntzUploadTargetId.trigger( 'upload_complete' );
          if(typeof(oldSendToEditor)=='function') { 
            window.send_to_editor = oldSendToEditor;
          }
        };
        tb_show('Upload file', 'media-upload.php?type=image&amp;TB_iframe=true');
        return false;
      });
    });
  </script>
  <?php 
}