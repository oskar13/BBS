<?php
require('config.php');
session_set_cookie_params(1200, '/bbs');
session_start();

if(!isset($_SESSION['user_ID'])) {
    header("Location: login.php");
    exit();
}


////////////////////////////////////////////////////////////
//////////        SITE SETTINGS UPDATE       ///////////////
////////////////////////////////////////////////////////////
if (isset($_POST['site_title'])) {
    try {
        $stmt = $conn->prepare("SELECT post_ID FROM POSTS");
        $stmt->execute();
        } catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
}