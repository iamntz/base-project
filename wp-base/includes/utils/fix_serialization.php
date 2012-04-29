<?php

// This code is a serialised string fixer for WordPress (and probably other systems).
// Simply select the table you need to fix in $table, and the code will change the string lengths for you.  Saves having to manually go through.
// Written 20090302 by David Coveney http://www.davecoveney.com and released under the WTFPL - ie, do what ever you want with the code, and I take no responsibility for it OK?
// To view the WTFPL go to http://sam.zoy.org/wtfpl/ (WARNING: it's a little rude, if you're sensitive)
//
// Thanks go to getmequick at gmail dot com who years ago posted up his preg_replace at http://uk2.php.net/unserialize and saved me trying to work it out.
//
// Before you start, do make a backup.  A backup that you know works, because this code has the scope to really break your data if you're careless.

require_once( 'wp-config.php' );
define( 'TABLE_PREFIX', $table_prefix );

cleanup_db_serialization(DB_NAME);

function cleanup_db_serialization($dbname){
  echo "Cleaning up...", $dbname, "\n";
  
  // connect to DB - the fields are obvious.  If you need to think about it too much you probably shouldn't be playing with this code.

  $host = ( DB_HOST != '' ? DB_HOST : 'localhost' );// normally localhost, but not necessarily.
  $usr  = DB_USER;       // your db userid
  $pwd  = DB_PASSWORD;   // your db password
  $db   = $dbname;       // your database

  $table        = TABLE_PREFIX . 'options';    // the table you need to fix
  $column       = 'option_value';   // the column with the serialised data in it
  $index_column = 'option_id';// the 

  mysql_connect( $host, DB_USER, DB_PASSWORD) or die( mysql_error() );
  mysql_select_db( $db ) or die( mysql_error() );

  // now let's get the data...

  $SQL   = "SELECT * FROM ".$table;
  $retid = mysql_query( $SQL );

  if (!$retid) { die( mysql_error()); }


  while ($row = mysql_fetch_array($retid)) {
      $value_to_fix = $row[$column];
      $index        = $row[$index_column];

  // don't need to output everything, uncomment if you want to see, but don't be surprised if some browsers break!

  //    echo ('changing option_id: '.$index.'<br/>');
  //    echo ('before: '.$value_to_fix.'<br/>');
      $fixed_value = __recalcserializedlengths($value_to_fix);
  //    echo ('after: '.$fixed_value.'<br/>');
    
      // now let's create the update query...
    
      $UPDATE_SQL = "UPDATE ".$table." SET ".$column." = '".mysql_real_escape_string($fixed_value)."' WHERE ".$index_column." = '".$index."'";
    
  //    echo 'update SQL - '.$UPDATE_SQL.'<br/><br/>';
  // and run it!  Autocommit seems to be the norm with mySQL setups, so none of that here.  
  // You may need to add it if you mod for Oracle or SQLServer.

      $result = mysql_query( $UPDATE_SQL );
      if (!$result) { die("ERROR: " . mysql_error() . "<br/>$SQL<br/>"); }

  }

}

function __recalcserializedlengths($sObject) {
   
    $__ret =preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $sObject );
   
    // return unserialize($__ret);
   return $__ret;
}
