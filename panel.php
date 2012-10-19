<?php
require 'bin/h2o/h2o.php';
include_once("sparta_config.php");

// make sure we're logged in
include_once("Alibaba.class.php");
Alibaba::forceAuthentication();

$data = array(
    'blog' => $blog,
);

# init template engine
$h2o = new h2o('tmpl/panel.html');

# render the page
echo $h2o->render(compact('data'));

?>
