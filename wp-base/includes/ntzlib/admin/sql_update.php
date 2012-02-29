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

  $sql_tables[] = "CREATE TABLE $wpdb->hotel_room_types (
      `id` INT NOT NULL AUTO_INCREMENT,
      `added_on` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
      `name` VARCHAR(255) NOT NULL ,
      `description` TEXT NOT NULL,
      `lang` VARCHAR(10) NOT NULL DEFAULT 'en',
      `translation_of` INT NOT NULL,
      UNIQUE KEY id (id)
    ) $charset_collate;";

  $sql_tables[] = "CREATE TABLE $wpdb->hotel_room_relationship (
      `id` INT NOT NULL AUTO_INCREMENT,
      `hotel_id` INT NOT NULL,
      `translation_of_id` INT NOT NULL,
      `room_type_id` INT NOT NULL,
      `room_price_id` INT NOT NULL,
      `room_dates` INT NOT NULL,
      UNIQUE KEY id (id)
    ) $charset_collate;";

  $sql_tables[] = "CREATE TABLE $wpdb->hotel_room_prices (
      `id` INT NOT NULL AUTO_INCREMENT,
      `name` VARCHAR(255) NOT NULL,
      `base_price` VARCHAR(255) NOT NULL,
        `base_start_date` DATETIME NOT NULL,
        `base_end_date` DATETIME NOT NULL,
      `price_for_adult` VARCHAR(255) NOT NULL,
        `adult_start_date` DATETIME NOT NULL,
        `adult_end_date` DATETIME NOT NULL,
      `price_for_child` VARCHAR(255) NOT NULL,
        `child_start_date` DATETIME NOT NULL,
        `child_end_date` DATETIME NOT NULL,
      `availability` INT DEFAULT '10',
      UNIQUE KEY id (id)
    ) $charset_collate;";

  foreach( $sql_tables as $create_table ){
    dbDelta($create_table);
  }

  update_option('ntz_db_version', $ntz_db_version);
}




