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
$draft      = $_POST["draft"];
$name       = mysql_real_escape_string($_POST["name"]);
$content    = mysql_real_escape_string($_POST["content"]);
$pubDate    = strtotime( $_POST["pubDate"] );
$standalone = $_POST["standalone"];


# init template engine
$h2o = new h2o('tmpl/panel-action.html');

if (!$pubDate) {
    $dateStr = date('Y-m-d H:i:s');
} else {
    $dateStr = date('Y-m-d H:i:s', $pubDate);
}
if ($standalone) {
    $sa = 1;
} else {
    $sa = 0;
}

/*
$err = savePage($name, $dateStr, $content, $draft, $sa);
if (!$err) {
    $data = array(
        'blog' => $blog,
        'title' => 'Published page',
        'message' => 'Your page has been published/saved.',
        'body' => "<a href='/code/page/$name'>View page</a> | <a href='pages'>Back to control panel</a>"
    );
} else {
    $data = array(
        'blog' => $blog,
        'title' => 'Publishing failed!',
        'message' => 'Failed to save your page.',
        'body' => "Error details: $err"
    );
}
*/
  
# done!
db_close();

# render the page
  //echo $h2o->render(compact('data'));

echo "<em>draft:</em> " . $draft;
echo "<em>name:</em> " . $name;
echo "<em>content:</em> " . $content;
echo "<em>pubDate:</em> " . $pubDate;
echo "<em>standalone:</em> " . $standalone;

?>
