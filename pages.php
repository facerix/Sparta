<?php
require 'bin/h2o/h2o.php';

// load db functions
include('model.php');

// connect to the DB in readonly mode
db_connect();


// make sure we're logged in
include_once("Alibaba.class.php");
Alibaba::forceAuthentication();


# get all pages
$query = "SELECT name, pubDate, author, draft, standalone " .
     "FROM pages " .
     "ORDER BY name ASC LIMIT 50";

# get result
$result=mysql_query($query) or die("Unable to retrieve page listing");

$pages = array();
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    # reformat date
    $dateStamp = strtotime( $row['pubDate'] );
    $row['pubDate'] = strftime( "%m/%d/%y", $dateStamp );

    array_push($pages, $row);
}

db_close();

# init template engine
$h2o = new h2o('tmpl/panel-pages.html');

# data to hand to the template
$data = array(
    'blog' => $blog,
    'pages' => $pages
);

# render the page
echo $h2o->render(compact('data'));
?>