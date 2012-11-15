<?php
require 'bin/h2o/h2o.php';

// load db functions
include('model.php');

// connect to the DB in insert mode
db_connect(1);


// make sure we're logged in
include_once("Alibaba.class.php");
Alibaba::forceAuthentication();


// grab and sanitize posted params
/* ============================================================================ 
NOTE: this is WAY too simple, and likely still leaves us exposed to some varieties of malicious attack.
    Better to use argument binding, via PDO or mysqli:
      mysqli : http://uk.php.net/manual/en/mysqli.prepare.php
      PDO    : http://uk.php.net/manual/en/book.pdo.php                           
============================================================================ */
$draft    = $_POST["draft"];
$guid     = $_POST["guid"];
$title    = mysql_real_escape_string($_POST["title"]);
$seoName  = mysql_real_escape_string($_POST["seoName"]);
$content  = mysql_real_escape_string($_POST["content"]);
$tags     = mysql_real_escape_string($_POST["tags"]);
$pubDate  = strtotime( $_POST["pubDate"] );

if (!$pubDate) {
    $dateStr = date('Y-m-d H:i:s');
} else {
    $dateStr = date('Y-m-d H:i:s', $pubDate);
}

$status = 'Saved';
if ($draft == 0) {
    $status = 'Published';
}

$err = savePost($guid, $title, $dateStr, $content, $draft, $tags, $seoName);
db_close();

if (!$err) {
    $response = $status . ' your post at ' . date('Y-m-d H:i:s');
} else {
    $response = 'Failed to save your post. Error details: $err';
}

echo $response;

?>
