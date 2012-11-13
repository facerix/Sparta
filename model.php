<?php

include('_globals.php');

# DB access to be used throughout the code

function getRecentPostSummaries($num) {
    $query = "SELECT guid,title,pubDate,seoName " .
             "FROM posts " .
             "WHERE draft=0 " .
             "ORDER BY pubDate DESC LIMIT $num";

    # get result
    $result = mysql_query($query) or die("Unable to retrieve recent post summaries");

    $posts = array();
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        # reformat date
        $dateStamp = strtotime( $row['pubDate'] );
        $row['isoDate'] = strftime( "%Y-%m-%dT%H:%M:%IZ", $dateStamp );
        $row['rssDate'] = strftime( "%a, %d %h %Y %H:%M:%I %z", $dateStamp );
        $row['pubDate'] = strftime( "%A, %B %e, %Y", $dateStamp );
        $row['pubTime'] = strftime( "%l:%M %P", $dateStamp );

        array_push($posts, $row);
    }

    return $posts;
}

function getRecentPosts($num, $offset=0) {
    $query = "SELECT posts.*, count(comments.post_guid) AS comments, group_concat(tags.tag) as taglist " .
             "FROM posts LEFT OUTER JOIN comments ON posts.guid=comments.post_guid " .
             "LEFT OUTER JOIN tags ON tags.post_guid=posts.guid " .
             "WHERE draft=0 " .
             "GROUP BY guid " .
             "ORDER BY pubDate DESC LIMIT $num OFFSET $offset";

    # get result
    $result = mysql_query($query) or die("Unable to retrieve selected post(s)");

    #if (mysql_num_rows($result) == 0) { show404(); }

    $posts = array();
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        # reformat date
        $dateStamp = strtotime( $row['pubDate'] );
        $row['isoDate'] = strftime( "%Y-%m-%dT%H:%M:%IZ", $dateStamp );
        $row['rssDate'] = strftime( "%a, %d %h %Y %H:%M:%I %z", $dateStamp );
        $row['pubDate'] = strftime( "%A, %B %e, %Y", $dateStamp );
        $row['pubTime'] = strftime( "%l:%M %P", $dateStamp );

        $row['tags'] = explode(',',$row['taglist']);

        array_push($posts, $row);
    }

    return $posts;
}

function _getPostTags($guid) {
    # get post tags (helper function)

    $query = "SELECT tag FROM tags WHERE post_guid='$guid' ORDER BY tag";
    $result = mysql_query($query) or die("Unable to retrieve selected post tags");

    $tags = array();
    while ($row = mysql_fetch_row($result)) {
        array_push($tags, $row[0]);
    }

    return $tags;
}

function getPostBySEOName($name) {
    # get post details
    $query = "SELECT * FROM posts WHERE seoName='$name'";
    $result = mysql_query($query) or die("Unable to retrieve post data for '" . $name . "'");
    $postdata = mysql_fetch_array($result, MYSQL_ASSOC);
    $guid = $postdata['guid'];

    # reformat date
    $dateStamp = strtotime( $postdata['pubDate'] );
    $postdata['isoDate'] = strftime( "%Y-%m-%dT%H:%M:%IZ", $dateStamp );
    $postdata['pubDate'] = strftime( "%A, %B %e, %Y", $dateStamp );
    $postdata['pubTime'] = strftime( "%l:%M %P", $dateStamp );


    # get post comments
    $query = "SELECT * FROM comments WHERE post_guid='$guid' ORDER BY addDate ASC";
    $result = mysql_query($query) or die("Unable to retrieve selected post comments");

    $comments = array();
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        array_push($comments, $row);
    }

    # get post tags
    $tags = _getPostTags($guid);

    return array(
        'post' => $postdata,
        'comments' => $comments,
        'tags' => $tags
    );

}

function getPostDetails($guid) {

    # get post details
    $query = "SELECT * FROM posts WHERE guid='$guid'";
    $result = mysql_query($query) or die("Unable to retrieve post data for #" . $guid);
    $postdata = mysql_fetch_array($result, MYSQL_ASSOC);

    # reformat date
    $dateStamp = strtotime( $postdata['pubDate'] );
    $postdata['isoDate'] = strftime( "%Y-%m-%dT%H:%M:%IZ", $dateStamp );
    $postdata['pubDate'] = strftime( "%A, %B %e, %Y", $dateStamp );
    $postdata['pubTime'] = strftime( "%l:%M %P", $dateStamp );


    # get post comments
    $query = "SELECT * FROM comments WHERE post_guid='$guid' ORDER BY addDate ASC";
    $result = mysql_query($query) or die("Unable to retrieve selected post comments");

    $comments = array();
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        array_push($comments, $row);
    }

    # get post tags
    $tags = _getPostTags($guid);

    return array(
        'post' => $postdata,
        'comments' => $comments,
        'tags' => $tags
    );

}

function getPostsByTag($tag, $num=0, $offset=0) {
    # get posts first, then grab comment counts & tag lists for each
    #$query = "SELECT * FROM posts " .
    #    "WHERE guid in (SELECT distinct post_guid FROM tags where tag='$tag') AND draft=0 " .
    #    "ORDER BY pubDate DESC";

    # get blog constants
    global $blog;
    $me = $blog['author'];

    $criteria = "guid in (SELECT distinct post_guid FROM tags where tag='$tag') AND draft=0 ";
    $query = "SELECT posts.*, count(post_guid) AS comments " .
             "FROM posts LEFT OUTER JOIN comments ON posts.guid=comments.post_guid " .
             "WHERE $criteria " .
             "GROUP BY guid " .
             "ORDER BY pubDate DESC";
    if ($num) { $query = $query . " LIMIT $num"; }
    if ($offset) { $query = $query . " OFFSET $offset"; }

    # get result
    $result = mysql_query($query) or die("Unable to retrieve posts for tag '$tag'.");

    $posts = array();
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        # massage datestamps a bit
        $dateStamp = strtotime( $row['pubDate'] );
        $row['pubDate'] = strftime( "%A, %B %e, %Y", $dateStamp );
        $row['pubTime'] = strftime( "%l:%M %P", $dateStamp );


        # get tags for this post
        $row['tags'] = _getPostTags($row['guid']);

        array_push($posts, $row);
    }

    return $posts;
}

function savePost($guid, $title, $dateStr, $content, $draft, $tags, $seoName) {

    # get blog constants
    global $blog;
    $me = $blog['author'];

    if ($guid) {
        // update existing post
        $update = "UPDATE posts SET title='$title', pubDate='$dateStr', ".
                  "author='$me', body='$content', draft=$draft, seoName='$seoName' " .
                  "WHERE guid=$guid";
        $err = try_sql_execute($update);
        if ($err) {
            return ( "Failed to save post. <br/>Error details: " . $err );
        }
    
        // save tags (removing any existing ones first)
        $delTags = "DELETE FROM tags WHERE post_guid=$guid";
        $err = try_sql_execute($delTags);
        if ($err) {
            return ( "Failed to save post. <br/>Error details: " . $err );
        }

        $tagArray = explode(",", $tags);
        foreach ($tagArray as &$tag) {
            $trimmed = trim($tag);
            $insert = "INSERT INTO tags (tag, post_guid) VALUES ('$trimmed', $guid)";
            mysql_query($insert);
        }

    } else {
        // save new post
        $insert = "INSERT INTO posts (title, pubDate, author, body, draft, seoName)" .
                  " VALUES ('$title', '$dateStr', '$me', '$content', $draft, '$seoName')";
        $err = try_sql_execute($insert);
        if ($err) {
            return ( "Failed to save post. <br/>Error details: " . $err );
        }

        // get guid
        $select = "SELECT guid FROM posts WHERE title='$title' ORDER BY guid DESC";
        $errMsg = "Can't get post ID; your post has been saved, but its tags were not.<br/>Error details: ";
        $err = try_sql_execute($select);
        if ($err) {
            return ( $errMsg . $err );
        }
        $row = mysql_fetch_row($result);
        $guid = $row[0];

        // save tags
        $tagArray = explode(",", $tags);
        foreach ($tagArray as &$tag) {
            $trimmed = trim($tag);
            $insert = "INSERT INTO tags (tag, post_guid) VALUES ('$trimmed', $guid)";
            mysql_query($insert);
        }
    }

    return 0;
}

function deletePosts($guids='') {
    if ($guids) {
        $guidArray = explode(",", $guids);
        foreach ($guidArray as &$guid) {
            # remove post
            $sql = "DELETE FROM posts WHERE guid=$guid";
            mysql_query($sql) or die( "Failed to delete post. <br/>Error details: " . mysql_error() );

            # remove post tags
            $sql = "DELETE FROM tags WHERE post_guid=$guid";
            mysql_query($sql) or die( "Failed to delete post tags. <br/>Error details: " . mysql_error() );

            # remove post comments
            $sql = "DELETE FROM comments WHERE post_guid=$guid";
            mysql_query($sql) or die( "Failed to delete post comments. <br/>Error details: " . mysql_error() );
        }
    }
    return 0;
}

function getTags() {
    $query = "SELECT tag,count(post_guid) FROM tags INNER JOIN posts ON guid=post_guid WHERE draft=0 GROUP BY tag";
    $result = mysql_query($query) or die("Unable to retrieve selected post(s)");
    $tags = array();
    while ($row = mysql_fetch_row($result)) {
        $tags[$row[0]] = (int)$row[1];
    }

    return $tags;
}

function getLastUpdateDate() {
    $query = "SELECT pubDate FROM posts " .
             "ORDER BY pubDate DESC LIMIT 1";

    # get result
    $result = mysql_query($query) or die("Unable to retrieve updated date information");

    if (mysql_num_rows($result) == 0) {
        $theDate = time();
    } else {
        $row = mysql_fetch_row($result);
        $theDate = strtotime($row[0]);
    }

    return $theDate;
}


function getPageDetails($name) {

    # get page details
    $query = "SELECT * FROM pages WHERE name='$name'";
    $result = mysql_query($query) or die("Unable to retrieve page data for page '" . $name . "'");
    $pagedata = mysql_fetch_array($result, MYSQL_ASSOC);

    # reformat date
    $dateStamp = strtotime( $pagedata['pubDate'] );
    $pagedata['isoDate'] = strftime( "%Y-%m-%dT%H:%M:%IZ", $dateStamp );
    $pagedata['pubDate'] = strftime( "%A, %B %e, %Y", $dateStamp );
    $pagedata['pubTime'] = strftime( "%l:%M %P", $dateStamp );

    return $pagedata;
}

function savePage($name, $dateStr, $content, $standalone) {

    # get blog constants
    global $blog;
    $me = $blog['author'];
    
    if ($name) {
        // see if that page already exists
        $select = "SELECT name FROM pages WHERE name='$name'";
        $errMsg = "Can't get existing page details; your post cannot be saved.<br/>Error details: ";
        $result = mysql_query($select) or die( $errMsg . mysql_error());
        $row = mysql_fetch_row($result);
        $existing_name = $row[0];

        if ($existing_name) {
            // update existing page
            $update = "UPDATE pages SET pubDate='$dateStr', ".
                      "author='$me', body='$content', standalone=$standalone " .
                      "WHERE name='$name'";
            mysql_query($update) or die( "Failed to save page. <br/>Error details: " . mysql_error() );

        } else {
            // save new page
            $insert = "INSERT INTO pages (name, pubDate, author, body, standalone)" .
                      " VALUES ('$name', '$dateStr', '$me', '$content', $standalone)";
            mysql_query($insert) or die( "Failed to save page. <br/>Error details: " . mysql_error() );
        }
    } else {
        return "No name provided";
    }
    return 0;
}

function deletePages($names='') {
    if ($names) {
        $nameArray = explode(",", $names);
        foreach ($nameArray as &$name) {
            # remove page
            $sql = "DELETE FROM pages WHERE name='$name'";
            mysql_query($sql) or die( "Failed to delete page. <br/>Error details: " . mysql_error() );
        }
    }
    return 0;
}

$blacklist = array("a", "an", "the");
$whitespace = array(" ", ".", "\n", "\r", "\t");
function getSEO($title) {
    # get blog constants
    global $blacklist;
    global $whitespace;
 
    $work = str_ireplace($whitespace, "-", str_ireplace($blacklist, "", $title));
    return strtolower($work);
}


?>