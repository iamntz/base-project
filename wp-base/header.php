<?php
  global $ntz;
?><!DOCTYPE html>
<!--[if lt IE 7 ]> <html <?php language_attributes(); ?> class="no-js msie ie6 lte9 lte8 lte7"> <![endif]-->
<!--[if IE 7 ]>    <html <?php language_attributes(); ?> class="no-js msie ie7 lte9 lte8 lte7"> <![endif]-->
<!--[if IE 8 ]>    <html <?php language_attributes(); ?> class="no-js msie ie8 lte9 lte8"> <![endif]-->
<!--[if IE 9 ]>    <html <?php language_attributes(); ?> class="no-js msie ie9 lte9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html <?php language_attributes(); ?> class="no-js nomsie"> <!--<![endif]-->

<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<title><?php
  global $page, $paged;
  wp_title( '|', true, 'right' );
  bloginfo( 'name' );
  $site_description = get_bloginfo( 'description', 'display' );
  if ( $site_description && ( is_home() || is_front_page() ) ) { echo " | $site_description"; }
  if ( $paged >= 2 || $page >= 2 ) { echo ' | ' . sprintf( __('Page %s', 'ntz'), max( $paged, $page ) );} 
?></title>

  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <script type="text/javascript">
    document.documentElement.className = document.documentElement.className.replace('no-js', '');
  </script>

  <?php
  /*
    uncomment this block for native-looking app on ios devices 
    <meta name="apple-mobile-web-app-capable" content="yes"> 
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
  */ ?>

  <!-- !html5 elements for ie<9 -->
  <!--[if lte IE 8 ]> <script type="text/javascript">var htmlForIe = ["abbr" ,"article" ,"aside" ,"audio" ,"canvas" ,"details" ,"figcaption" ,"figure" ,"footer" ,"header" ,"hgroup" ,"mark" ,"meter" ,"nav" ,"output" ,"progress" ,"section" ,"summary" ,"time" ,"video"], htmlForIeLen = htmlForIe.length; for(i=0;i<htmlForIeLen;i++){ document.createElement(htmlForIe[i]); }</script> <![endif]-->

<link rel="profile" href="http://gmpg.org/xfn/11">
<?php if( WP_DEBUG ) { ?>
  <link rel="stylesheet" type="text/css" media="all" href="<?php echo THEME_PATH ?>/css_dev/screen.css?ver=<?php echo CSS_VERSION; ?>">
<?php } else { ?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo THEME_PATH ?>/css/screen.css?ver=<?php echo CSS_VERSION; ?>">
<?php } ?>

<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php 
//if( is_singular() && get_option( 'thread_comments' ) ) { wp_enqueue_script( 'comment-reply' ); }
  wp_head();
?>
</head>
<body <?php body_class(); ?>>

<?php 
