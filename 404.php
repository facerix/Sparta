<?php
require 'bin/h2o/h2o.php';

// load core functions/defs
include('_globals.php');

# init template engine
$h2o = new h2o('tmpl/blog-404.html');

# data to hand to the template
$data = array(
    'blog' => $blog
);

# render the page
echo $h2o->render(compact('data'));
?>