<?php global $ntz; wp_footer(); ?>

<!--[if lte IE 8]>
	<script src="<?php echo PATH;?>/js/lib/selectivizr.js" type="text/javascript" charset="utf-8"></script>
<![endif]-->
<?php if(is_user_logged_in() && current_user_can('update_core')) { ?> 
	<script>
		console.log('<?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds.');
	/* ]]> */
	</script>
<?php } ?>
</body>
</html>
