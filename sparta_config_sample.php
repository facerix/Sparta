<?php
# begin global variables (customize as appropriate)

$blog = array(
    'title' => 'This.Is.Sparta!',
    'url' => 'http://thisis.sparta.com/',
    'author' => 'leonidas',
    'description' => 'This is madness!',
    'postsOnIndex' => 3,
    'postsPerPage' => 5
);

$db = array(
    'hostname' => 'mysql.sparta.com',
    'dbname' => 'spartanblog',
    'username_rw' => 'sparta_inserts',   // read/write connection (for inserts/updates)
    'password_rw' => '$p@rta!',
    'username_ro' => 'sparta_public',    // read-only mode (for reads)
    'password_ro' => '3very!3ls3',
);

# end global vars
?>