<?php
 global $ntz;
?><!DOCTYPE html>
<!--[if lt IE 7 ]> <html <?php language_attributes(); ?> class="no-js msie ie6 lte9 lte8 lte7"> <![endif]-->
<!--[if IE 7 ]>    <html <?php language_attributes(); ?> class="no-js msie ie7 lte9 lte8 lte7"> <![endif]-->
<!--[if IE 8 ]>    <html <?php language_attributes(); ?> class="no-js msie ie8 lte9 lte8"> <![endif]-->
<!--[if IE 9 ]>    <html <?php language_attributes(); ?> class="no-js msie ie9 lte9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html <?php language_attributes(); ?> class="no-js"> <!--<![endif]-->

<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php
	global $page, $paged;

	wp_title( '|', true, 'right' );

	bloginfo( 'name' );

	$site_description = get_bloginfo( 'description', 'display' );
	
	if ($site_description && (is_home() || is_front_page())) {echo " | $site_description";}
	if ($paged >= 2 || $page >= 2) {echo ' | ' . sprintf(__('Page %s', 'ntz'), max($paged, $page ));}	?></title>

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<!-- html5 elements for ie<9 -->
	<!--[if lte IE 8 ]> <script type="text/javascript" charset="utf-8"> /* <![CDATA[ */ var htmlForIe = ["abbr" ,"article" ,"aside" ,"audio" ,"canvas" ,"details" ,"figcaption" ,"figure" ,"footer" ,"header" ,"hgroup" ,"mark" ,"meter" ,"nav" ,"output" ,"progress" ,"section" ,"summary" ,"time" ,"video"], htmlForIeLen = htmlForIe.length; for(i=0;i<htmlForIeLen;i++){ document.createElement(htmlForIe[i]); } /* ]]> */ </script> <![endif]-->
<?php 
if(is_readable(get_template_directory().'/favicon.ico')){ ?>
  
	<link rel="shortcut icon" href="<?php echo PATH; ?>/favicon.ico">
	
<?php } ?>
<?php if(is_readable(get_template_directory().'/apple-touch-icon.png')){ ?>
	<link rel="apple-touch-icon" href="<?php echo PATH; ?>/apple-touch-icon.png">
	
<?php } ?>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo PATH ?>/css/screen.css" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<?php if(strlen($ntz->g_analytics)>0){ // displaying g.analytics before wp_head to avoid any script block ?>
<script>
	/* <![CDATA[ */
		var _gaq = [['_setAccount', '<?php echo $ntz->g_analytics; ?>'], ['_trackPageview']];
		(function(d, t) {
			var g = d.createElement(t), s = d.getElementsByTagName(t)[0];
			g.async = true;
			g.src = ('https:' == location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			s.parentNode.insertBefore(g, s);
		})(document, 'script');
	/* ]]> */
</script>
<?php }

//if(is_singular() && get_option('thread_comments')) { wp_enqueue_script('comment-reply'); }

	wp_head();
?>
</head>

<body <?php body_class(); ?>>
