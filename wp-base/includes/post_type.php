<?php 

function change_post_menu_label() {
  global $menu;
  global $submenu;
  $menu[5][0] = 'Blog';
  $submenu['edit.php'][5][0] = 'Blog';
  $submenu['edit.php'][10][0] = 'Add new';
  $submenu['edit.php'][16][0] = 'Blog Tags';
  echo '';
}
function change_post_object_label() {
  global $wp_post_types;
  $labels = &$wp_post_types['post']->labels;
  $labels->name = 'Blog';
  $labels->singular_name = 'Blog';
  $labels->add_new = 'Add new';
  $labels->add_new_item = 'Add new';
  $labels->edit_item = 'Edit new';
  $labels->new_item = 'Blog';
  $labels->view_item = 'View';
  $labels->search_items = 'Search Blog';
  $labels->not_found = 'No blog entry found';
  $labels->not_found_in_trash = 'No blog entry found in Trash';
}
// ============================
// = rename default post name =
// ============================
// add_action( 'init', 'change_post_object_label' );
// add_action( 'admin_menu', 'change_post_menu_label' );



function create_post_type($type) {
  $labels = array(
    'name'                    =>  $type['title'],
    'singular_name'           =>  $type['title'],
    'add_new'                 =>  'Add new',
    'add_new_item'            =>  'Add new',
    'edit_item'               =>  'Edit entry',
    'new_item'                =>  'Add new',
    'view_item'               =>  'View entry',
    'search_items'            =>  'Search entry',
    'not_found'               =>  'No entry found',
    'not_found_in_trash'      =>  'No entry found in Trash',
    'parent_item_colon'       =>  ''
  );
  $args = array(
    'labels'              =>  $labels,
    'public'              =>  true,
    'publicly_queryable'  =>  true,
    'show_ui'             =>  true, 
    'query_var'           =>  true,
    'rewrite'             =>  true,
    'capability_type'     =>  'post',
    'hierarchical'        =>  false,
    'has_archive'         =>  true, // http://www.wpmods.com/wordpress-3-1-custom-post-type-archives
    'menu_position'       => 6,
    'taxonomies'          =>  $type['taxonomies'],
    'supports'            => is_array($type['supports']) ? 
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
  register_post_type( $type['name'],$args );
  if( $type['hasCategories']==true ){
    register_taxonomy( 'do-'.$type['name'], array($type['name']), array( "hierarchical" => true, "label" => 'Categories', "singular_label" => ucwords($type['name']) .' Category', "rewrite" => true ) );  
    register_taxonomy_for_object_type( $type['name'], 'do-'.$type['name'] );
  }
  
}
add_action('init', 'post_type');
function post_type(){
  //create_post_type(array('name'=>'blog', 'title'=>'Blog', 'taxonomies'=>array('do-blog', 'post_tag'), 'supports'=>array('comments', 'title','editor','thumbnail','excerpt','custom-fields'), 'hasCategories'=>true));
  //create_post_type(array('name'=>'apps', 'title'=>'Apps', 'taxonomies'=>array('do-apps'), 'supports'=>array('title','editor','thumbnail','excerpt','custom-fields'), 'hasCategories'=>false));
  //create_post_type(array('name'=>'books', 'title'=>'Books', 'taxonomies'=>array('do-books'), 'supports'=>array('title','editor','thumbnail','excerpt','custom-fields'), 'hasCategories'=>false));
  //create_post_type(array('name'=>'music', 'title'=>'Music', 'taxonomies'=>array('do-music'), 'supports'=>array('title','editor','thumbnail','excerpt','custom-fields'), 'hasCategories'=>false));
  //create_post_type(array('name'=>'products', 'title'=>'Products', 'taxonomies'=>array('do-products'), 'supports'=>array('title','editor','thumbnail','excerpt','custom-fields'), 'hasCategories'=>false));
  //create_post_type(array('name'=>'charity', 'title'=>'Charity', 'taxonomies'=>array('do-charity'), 'supports'=>array('title','editor','thumbnail','excerpt','custom-fields'), 'hasCategories'=>false));
}

function remove_menus () {
  global $menu;
  $restricted = array( 'Posts', 'Media', 'Links' );
  end ( $menu );
  while ( prev($menu) ){
    $value = explode( ' ',$menu[key($menu)][0] );
    if( in_array( $value[0] != NULL?$value[0]:"" , $restricted ) ){
      unset( $menu[key($menu)] );
    }
  }
}
//add_action('admin_menu', 'remove_menus');


// adding a class for custom post type
function fb_add_body_class( $class ) {
  $post_type = 'my_example_post_type'; // the Post Type
  if ( get_query_var('post_type') === $post_type ) { // only, if post type is active
    $class[] = $post_type;
    $class[] = 'type-' . $post_type;
  }
  return $class;
}
// add_filter( 'body_class', 'fb_add_body_class' );