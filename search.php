<?php
require 'bin/h2o/h2o.php';

// load db functions
include('model.php');

// connect to the DB in readonly mode
db_connect();

$tag    = mysql_real_escape_string($_GET['tag']);
$num    = mysql_real_escape_string($_GET['num']);
$offset = mysql_real_escape_string($_GET['offset']);

# init template engine
$h2o = new h2o('tmpl/blog-index.html');

# data to hand to the template
$data = array(
    'blog'  => $blog,
    'posts' => array(),
    'tag'   => $tag
);

if ($tag != null) {
    $data['posts'] = getPostsByTag($tag, $num, $offset);
}

# done!
db_close();

# render the page
echo $h2o->render(compact('data'));
?>