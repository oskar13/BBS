<?php
require('config.php');
session_set_cookie_params(1200, BASE_PATH);
session_start();

if (isset($_POST['article_ID'])) {
	if (!isset($_SESSION['user_ID'])) {
		$user_ID = NULL;
	} else {
		$user_ID = $_SESSION['user_ID'];
	}
	try {
        $stmt = $conn->prepare("INSERT INTO comments (article_ID, user_ID, c_date, comment)
            VALUES (:article_ID, :user_ID, :c_date, :comment)");
        $stmt->execute(array('article_ID' => $_POST['article_ID'], 'c_date' => time(), 'user_ID' => $user_ID, 'comment' => $_POST['comment']));
    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
        die();
    }
    header('Location:news.php?article_ID='.$_POST['article_ID']);
    die();
}