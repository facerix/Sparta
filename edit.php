<?php
require 'bin/h2o/h2o.php';

// load db functions
include('model.php');

// connect to the DB in readonly mode
db_connect();


// make sure we're logged in
include_once("Alibaba.class.php");
Alibaba::forceAuthentication();


$guid=$_GET['id'];
if ($guid != null) {

    # single post; more detail, get tags, etc
    $details = getPostDetails($guid);

    if (! $details['post']['guid']) { show404(); }
    
    # data to hand to the template
    $data = array(
        'blog' => $blog,
        'title' => 'Edit post',
        'post' => $details['post'],
        'tags' => implode(", ", $details['tags']),
        'currDate' => $details['post']['isoDate']
    );

} else {

    # data to hand to the template
    $data = array(
        'blog' => $blog,
        'title' => 'Create new post',
        'currDate' => date('Y-m-d H:i:s'),
        'post' => array(
            'draft' => (int) TRUE
        )
    );

}

# init template engine
$h2o = new h2o('tmpl/panel-edit.html');

# render the page
echo $h2o->render(compact('data'));

?>