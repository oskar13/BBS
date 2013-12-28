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
	$site_title = htmlspecialchars($_POST['site_title']);
	if (!isset($_POST['show'])) {
		$show_desc = NULL;
	} else {
		$show_desc = 1;
	}
	$site_desc_title = htmlspecialchars($_POST['site_desc_title']);
	$site_desc = htmlspecialchars($_POST['site_desc']);



    try {
        $stmt = $conn->prepare("UPDATE site_options SET site_name = :site_title,
		show_desc = :show_desc ,
		site_desc_title = :site_desc_title,
		site_desc = :site_desc WHERE site_ID =1");
        $stmt->execute(array('site_title' => $site_title, 'show_desc' => $show_desc, 'site_desc_title' => $site_desc_title, 'site_desc' => $site_desc));
    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
        die();
    }
    header('Location: admin.php?page=site_settings');
}

