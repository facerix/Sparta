<?php
require 'bin/h2o/h2o.php';

// load db functions
include('model.php');

// connect to the DB in readonly mode
db_connect();

# get desired feed format
$format = $_GET['format'];
if ($format == '') { $format = 'atom'; }

# top 10 recent posts; no need for comments (or tags?)
$posts = getRecentPosts(10, 0, $format);
$latestPubDate = getLastUpdateDate();

# init template engine
if ($format == 'rss') {
    $h2o = new h2o('tmpl/feed-rss.html');
    $updateDate = strftime( "%a, %d %h %Y %H:%M:%I %z", $latestPubDate );
} else {
    $h2o = new h2o('tmpl/feed-atom.html');
    $updateDate = strftime( "%Y-%m-%dT%H:%M:%IZ", $latestPubDate );
}

# data to hand to the template
$data = array(
    'blog' => $blog,
    'posts' => $posts,
    'lastUpdate' => $updateDate
    
);


# done!
db_close();

# render the page
header('Content-type: text/xml');
echo $h2o->render(compact('data'));
?>
