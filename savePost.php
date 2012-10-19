<?php
require 'bin/h2o/h2o.php';

// load db functions
include('model.php');

// connect to the DB in insert mode
db_connect(1);


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

# init template engine
$h2o = new h2o('tmpl/panel-action.html');

if (!$pubDate) {
    $dateStr = date('Y-m-d H:i:s');
} else {
    $dateStr = date('Y-m-d H:i:s', $pubDate);
}

$err = savePost($guid, $title, $dateStr, $content, $draft, $tags, $seoName);
if (!$err) {
    $data = array(
        'blog' => $blog,
        'title' => 'Published post',
        'message' => 'Your post has been published/saved.',
        'body' => "<a href='?p=$seoName'>View post</a> | <a href='posts'>Back to control panel</a>"
    );
} else {
    $data = array(
        'blog' => $blog,
        'title' => 'Publishing failed!',
        'message' => 'Failed to save your post.',
        'body' => "Error details: $err"
    );
}

# done!
db_close();

# render the page
echo $h2o->render(compact('data'));
?>
