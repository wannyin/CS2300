<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Album</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Oswald" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="row banner">
        <div class="col-md-12" id="title_1">Formula One Grand Prix Winners<br><div id="title_2">in the History</div></div>
    </div>
    <div class="row menu">
        <div class="col-md-2"></div>
        <div class="col-md-2"><a href="index.php">HOME</a></div>
        <div class="col-md-2"><a href="album.php">ALBUM</a></div>
        <div class="col-md-2"><a href="search.php">SEARCH</a></div>

<?php

    $post_username = filter_input( INPUT_POST, 'user', FILTER_SANITIZE_STRING );
    $post_password = filter_input( INPUT_POST, 'password', FILTER_SANITIZE_STRING );

if (isset($_POST['log_out']) && isset($_SESSION['logged_user_by_sql'])) {
        $olduser = $_SESSION['logged_user_by_sql'];
        unset($_SESSION['logged_user_by_sql']);
        unset($_SESSION['logged_result']);
    }
elseif (!isset($_SESSION['logged_user_by_sql']) && (!empty( $post_username ) || !empty( $post_password )) ) {

        require_once 'script/config.php';
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        if ($mysqli->errno) {
            print($mysqli->error);
            exit();
        }

        $query = "SELECT * FROM User WHERE username = '$post_username'";
        $result = $mysqli->query($query);

        if ( $result && $result->num_rows == 1) {

            $row = $result->fetch_assoc();
            $db_hash_password = $row['password'];

            if( password_verify( $post_password, $db_hash_password ) ) {
                $db_username = $row['username'];
                $_SESSION['logged_user_by_sql'] = $db_username;
                $_SESSION['logged_result'] = true;
            }
            else $_SESSION['logged_result'] = false;
        }

        $mysqli->close();
}

if(isset($_SESSION['logged_user_by_sql'])){
?>
    <div class="col-md-2"><a href="add.php">ADD</a></div>
<?php
}
?>
    </div>
    </div>




