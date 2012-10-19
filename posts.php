<?php
require 'bin/h2o/h2o.php';

// load db functions
include('model.php');

// connect to the DB in readonly mode
db_connect();


// make sure we're logged in
include_once("Alibaba.class.php");
Alibaba::forceAuthentication();


# get all posts; comment summary is okay, and no need for tags
$query = "SELECT guid, title, pubDate, author, draft, seoName " .
     "FROM posts " .
     "ORDER BY pubDate DESC LIMIT 50";

# get result
include('db_fetch.php');

$posts = array();
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    # reformat date
    $dateStamp = strtotime( $row['pubDate'] );
    $row['pubDate'] = strftime( "%m/%d/%y", $dateStamp );

    array_push($posts, $row);
}

for ( $i = 0; $i < count($posts); $i++ ) {
    # get tags
    $postguid = $posts[$i]['guid'];
    $query = "SELECT tag FROM tags WHERE post_guid='$postguid' ORDER BY tag";

    # get result
    $result=mysql_query($query) or die("Unable to retrieve post tags");

    $tags = array();
    while ($row = mysql_fetch_row($result)) {
        array_push($tags, $row[0]);
    }
    $posts[$i]['tags'] = implode(", ", $tags);
}

db_close();

# init template engine
$h2o = new h2o('tmpl/panel-posts.html');

# data to hand to the template
$data = array(
    'blog' => $blog,
    'posts' => $posts
);

# render the page
echo $h2o->render(compact('data'));
?>