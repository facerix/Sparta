<?php

include_once("Alibaba.class.php");

$username = $_POST["username"];
$password = $_POST["password"];

if ($username && $password) {

    if (Alibaba::login($username, $password)) {
        header("Location: panel");
    } else {
        Alibaba::redirectToLogin("Login failed");
    }

} else {

    
    # init template engine
    require 'bin/h2o/h2o.php';
    $h2o = new h2o('tmpl/login.html');
    
    # data to hand to the template
    $data = array(
        'message' => $_GET["message"]
    );
    
    # render the page
    echo $h2o->render(compact('data'));
}
?>