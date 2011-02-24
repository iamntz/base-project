<?php 

function change_post_menu_label() {
	global $menu;
	global $submenu;
	$menu[5][0] = 'News';
	$submenu['edit.php'][5][0] = 'News';
	$submenu['edit.php'][10][0] = 'Add News';
	$submenu['edit.php'][16][0] = 'News Tags';
	echo '';
}
function change_post_object_label() {
	global $wp_post_types;
	$labels = &$wp_post_types['post']->labels;
	$labels->name = 'News';
	$labels->singular_name = 'News';
	$labels->add_new = 'Add News';
	$labels->add_new_item = 'Add News';
	$labels->edit_item = 'Edit News';
	$labels->new_item = 'News';
	$labels->view_item = 'View News';
	$labels->search_items = 'Search News';
	$labels->not_found = 'No News found';
	$labels->not_found_in_trash = 'No News found in Trash';
}
// ============================
// = rename default post name =
// ============================
// add_action( 'init', 'change_post_object_label' );
// add_action( 'admin_menu', 'change_post_menu_label' );



function create_post_type($type) {
  $labels = array(
    'name'										=>	$type['title'],
    'singular_name'						=>	$type['title'],
    'add_new'									=>	'New entry',
    'add_new_item'						=>	'New entry',
    'edit_item'								=>	'Edit entry',
    'new_item'								=>	'New entry',
    'view_item'								=>	'View entry',
    'search_items'						=>	'Search entry',
    'not_found'								=>	'No entry found',
    'not_found_in_trash' 			=>	'No entry found in Trash',
    'parent_item_colon'				=>	''
  );
  $args = array(
    'labels'							=>	$labels,
    'public'							=>	true,
    'publicly_queryable'	=>	true,
    'show_ui'							=>	true, 
    'query_var'						=>	true,
    'rewrite'							=>	true,
    'capability_type'			=>	'post',
    'hierarchical'				=>	false,
    'menu_position'				=> 6,
  	'taxonomies'					=>	$type['taxonomies'],
  	'supports'						=> is_array($type['supports']) ? 
  														$type['supports'] : 
  															array(
  																'title',
  																'editor',
  																'author',
  																'thumbnail',
  																'excerpt',
  																'trackbacks',
  																'custom-fields',
  																'comments',
  																'revisions',
  																'page-attributes'
  															)
  );
  register_post_type($type['name'],$args);
  if($type['hasCategories']==true){
	  register_taxonomy('do-'.$type['name'], array($type['name']), array("hierarchical" => true, "label" => 'Categories', "singular_label" => ucwords($type['name']) .' Category', "rewrite" => true));  
	  register_taxonomy_for_object_type($type['name'], 'do-'.$type['name']);
  }
  
}
add_action('init', 'post_type');
function post_type(){
	create_post_type(array('name'=>'work', 'title'=>'Our Work', 'taxonomies'=>array('do-work'), 'supports'=>array('title','editor','author','thumbnail','excerpt','custom-fields'), 'hasCategories'=>true));
 	//create_post_type(array('name'=>'', 'title'=>'', 'taxonomies'=>array('do-'), 'supports'=>array('title','editor','author','thumbnail','excerpt','custom-fields'), 'hasCategories'=>true ));
}

add_action('admin_head', 'cpt_icons');
function cpt_icons(){
	?>
	<style type="text/css" media="screen">
		#favorite-actions,
		#menu-links,
		#menu-media {
			display:none;
		}

	</style>
	<?php 
}
