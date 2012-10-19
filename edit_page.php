<?php
require 'bin/h2o/h2o.php';

// load db functions
include('model.php');

// connect to the DB in readonly mode
db_connect();


// make sure we're logged in
include_once("Alibaba.class.php");
Alibaba::forceAuthentication();


$name=$_GET['name'];
if ($name != null) {

    # get page detail (content, author, etc)
    $details = getPageDetails($name);

    // if (! $details['body']) { show404(); }

    # data to hand to the template
    $data = array(
        'blog' => $blog,
        'title' => 'Edit page',
        'page' => $details,
        'currDate' => $details['isoDate']
    );

} else {

    # data to hand to the template
    $data = array(
        'blog' => $blog,
        'title' => 'Create new page',
        'currDate' => date('Y-m-d H:i:s')
    );

}

# init template engine
$h2o = new h2o('tmpl/page-edit.html');

# render the page
echo $h2o->render(compact('data'));

?>