<?php

// load db functions
include('model.php');

// connect to the DB in readonly mode
db_connect();

header('Content-type: application/json');
echo json_encode(getTags());

?>