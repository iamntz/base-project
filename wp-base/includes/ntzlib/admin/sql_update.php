<?php

/**
* @todo  do OOP sql generator
*/
/*
class Ntz_SQL extends Ntz_utils{
  protected $db_version;
  function __construct( $options = array() ){
    parent::__construct(false);
    $db_version = '1.1';
    $db_current_version = get_option('ntz_db_version');

    $this->wpdb->custom_table = $this->wpdb->prefix.'custom_table';

  }

}//Ntz_SQL  extends Ntz_utils
*/

$ntz_db_version = '1.1';
$ntz_db_current_version = get_option('ntz_db_version');

global $wpdb;
$wpdb->hotel_room_types        = $wpdb->prefix.'hotel_room__types';

if($ntz_db_version != $ntz_db_current_version){
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

  $charset_collate = '';

  if($wpdb->supports_collation()) {
    if(!empty($wpdb->charset)) {
      $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
    }
    if(!empty($wpdb->collate)) {
      $charset_collate .= " COLLATE $wpdb->collate";
    }
  }

  $sql_tables[] = "CREATE TABLE $wpdb->foo (
      `id` INT NOT NULL AUTO_INCREMENT,
      `bar` VARCHAR(255) NOT NULL ,
      UNIQUE KEY id (id)
    ) $charset_collate;";

  foreach( $sql_tables as $create_table ){
    dbDelta($create_table);
  }

  update_option('ntz_db_version', $ntz_db_version);
}




