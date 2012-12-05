<?php

include_once("sparta_config.php");
define('DEBUG', false);  // change to true for development

# global functions

function db_connect($rw = 0) {
    global $db;
    if ($rw == 1) {
        // read/write connection (for inserts/updates)
        $username = $db['username_rw'];
        $password = $db['password_rw'];
    } else {
        // read-only mode (for reads)
        $username = $db['username_ro'];
        $password = $db['password_ro'];
    }

    mysql_connect($db['hostname'],$username,$password);
    @mysql_select_db($db['dbname']) or die("Unable to select database");
}

function db_close() {
    mysql_close();
}

# useful little function cribbed from http://snipplr.com/view/1358/mysql-fetch-all/
function mysql_fetch_all($result) {
    $all = array();
    while ($row = mysql_fetch_assoc($result)){ $all[] = $row; }
    return $all;
}

function safe_die($msg, $sql='') {
// idea for this helper function from here: http://board.phpbuilder.com/showthread.php?10340977-mysql_query()-or-die()
   $details = $msg;
   if(!empty($sql)) {
      $details .= " ($sql)"; 
   }
   $mysql = @mysql_error();
   if(!empty($mysql)) { 
      $details .= ": $mysql";
   }
   if(DEBUG) { 
      die($details);
   } else {
      error_log($details);
      die($msg);
   }
}

function show404() {
    // redirect to 404
    global $blog;
    $url = $blog['url'] . '/404';
    header('Location: '.$url);
    exit();
}

# end global functions

?>