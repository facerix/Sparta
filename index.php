<?php
require 'bin/h2o/h2o.php';

// load db functions
include('model.php');

// connect to the DB in readonly mode
db_connect();


// TBD: check for SQL injection!!!
$guid   = $_GET['id'];
$page   = $_GET['page'];
$post   = $_GET['post'];

if ($post != null) {

    # single post via SEO-friendly title
    $details = getPostBySEOName($post);

    if (! $details['post']['guid']) { show404(); }

    # init template engine
    $h2o = new h2o('tmpl/blog-post.html');

    # data to hand to the template
    $data = array(
        'blog' => $blog,
        'post' => $details['post'],
        'comments' => $details['comments'],
        'tags' => $details['tags']
    );

    if ($details['older']) { $data['older'] = $details['older']; }
    if ($details['newer']) { $data['newer'] = $details['newer']; }

} elseif ($guid != null) {

    # single post; more detail, get tags, etc
    $details = getPostDetails($guid);

    if (! $details['post']['guid']) { show404(); }

    # init template engine
    $h2o = new h2o('tmpl/blog-post.html');

    # data to hand to the template
    $data = array(
        'blog' => $blog,
        'post' => $details['post'],
        'comments' => $details['comments'],
        'tags' => $details['tags']
    );

    if ($details['older']) { $data['older'] = $details['older']; }
    if ($details['newer']) { $data['newer'] = $details['newer']; }

} elseif ($page != null) {

    # single page get details
    $details = getPageDetails($page);

    if (! $details['body']) { show404(); }

    echo $details['body'];
    exit;

} else {
    # recent posts; comment summary is okay, and no need for comment details
    $num    = $_GET['posts'];
    $offset = $_GET['offset'];
    if ($num == '') { $num = 3; }
    if ($offset == '') { $offset = 0; }

    $posts = getRecentPosts($num, $offset);

    # init template engine
    $h2o = new h2o('tmpl/blog-index.html');

    # data to hand to the template
    $data = array(
        'blog' => $blog,
        'posts' => $posts,
        'older' => intval($offset) + intval($num)
    );
    
    if ( $offset != 0 ) {
        $data['offset'] = $offset;
        $data['newer'] = intval($offset) - intval($num);
    }
}

# done!
db_close();

# render the page
echo $h2o->render(compact('data'));
?>