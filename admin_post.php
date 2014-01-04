<?php
require('config.php');
session_set_cookie_params(1200, BASE_PATH);
session_start();

if(!isset($_SESSION['user_ID'])) {
    header("Location: login.php");
    exit();
}
////////////////////////////////////////////////////////////
//////////                ARTICLE            ///////////////
////////////////////////////////////////////////////////////


if ($_POST['data'] == "new_article") {
    if (!isset($_POST['content']) || !isset($_POST['heading'])) {
        echo "Error";
        die();
    }

    if (empty($_POST['content']) || empty($_POST['heading'])) {
        echo "Empty input not allowed!";
        die();
    }

    try {
        $stmt = $conn->prepare("INSERT INTO articles (user_ID, a_date, heading, content)
            VALUES (:user_ID, :a_date, :heading, :content)");
        $stmt->execute(array('user_ID' => $_SESSION['user_ID'], 'a_date' => time(), 'heading' => $_POST['heading'], 'content' => $_POST['content']));
    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
        die();
    }
    header('Location: admin.php?page=new_article');
}


////////////////////////////////////////////////////////////
//////////                BOARDS             ///////////////
////////////////////////////////////////////////////////////

if ($_POST['data'] == "edit_board") {
    if (!isset($_POST['board_ID'])) {
        echo "No ID, error";
        die();
    }

    if (!isset($_POST['board_url']) || !isset($_POST['board_name'])) {
        echo "Error";
        die();
    }

    if (empty($_POST['board_url']) || empty($_POST['board_name'])) {
        echo "Empty input not allowed!";
        die();
    }

    try {
        $stmt = $conn->prepare("UPDATE boards SET 
            board_meta = :board_meta,
            board_url = :board_url,
            board_name = :board_name
            WHERE board_ID = :board_ID");
        $stmt->execute(array('board_ID' => $_POST['board_ID'], 'board_meta' => $_POST['board_meta'], 'board_url' => $_POST['board_url'], 'board_name' => $_POST['board_name']));
    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
        die();
    }
    header('Location: admin.php?page=boards');
    die();
}



if ($_POST['data'] == "new_board") {
    if (!isset($_POST['board_url']) || !isset($_POST['board_name'])) {
        echo "Error";
        die();
    }

    if (empty($_POST['board_url']) || empty($_POST['board_name'])) {
        echo "Empty input not allowed!";
        die();
    }

    try {
        $stmt = $conn->prepare("INSERT INTO boards (board_name, board_url, board_meta)
            VALUES (:board_name, :board_url, :board_meta)");
        $stmt->execute(array('board_meta' => $_POST['board_meta'], 'board_url' => $_POST['board_url'], 'board_name' => $_POST['board_name']));
    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
        die();
    }
    header('Location: admin.php?page=boards');
    die();
}

if (($_POST['data'] == "delete_board") && isset($_POST['board_ID'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM boards
        WHERE board_ID = :board_ID");
        $stmt->execute(array('board_ID' => $_POST['board_ID']));
    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
        die();
    }

    try {
        $stmt = $conn->prepare("DELETE FROM posts
        WHERE board_ID = :board_ID");
        $stmt->execute(array('board_ID' => $_POST['board_ID']));
    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
        die();
    }
    header('Location: admin.php?page=boards');
    die();
}

////////////////////////////////////////////////////////////
//////////                 BANS              ///////////////
////////////////////////////////////////////////////////////

if (($_POST['data'] == "del_ban") && isset($_POST['del_ban_ID'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM banned
        WHERE ban_ID = :ban_ID");
        $stmt->execute(array('ban_ID' => $_POST['del_ban_ID']));
    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
        die();
    }
    header('Location: admin.php?page=bans');
    die();
}

////////////////////////////////////////////////////////////
//////////             MANAGE USERS          ///////////////
////////////////////////////////////////////////////////////



if ($_POST['data'] == "new_user") {
    if (isset($_POST['user_name']) && isset($_POST['user_pass']) && isset($_POST['user_pass_again'])) {
        if (!isset($_POST['admin_level'])) {
            $_POST['admin_level'] = 0;
        }

        if ($_POST['user_pass'] != $_POST['user_pass_again']) {
            echo "<h3>Passwords didnt match!</h3>";
            die();
        }

        try {
            $stmt = $conn->prepare("INSERT INTO users (user_name, user_pass, admin_level)
            VALUES (:user_name, password(:user_pass), :admin_level)");
            $stmt->execute(array('user_name' => $_POST['user_name'], 'user_pass' => $_POST['user_pass'], 'admin_level' => $_POST['admin_level']));
        } catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
            die();
        }
        header('Location: admin.php');
        die();
    }

}

if ($_POST['data'] == "edit_user") {
    if (isset($_POST['user_ID'])) {
        if (!isset($_POST['user_name'])) {
            echo "ERROR: You have inserted a blank user name.";
            die();
        }
        if (!isset($_POST['admin_level'])) {
            $_POST['admin_level'] = 0;
        }

        if (!isset($_POST['banned'])) {
            $_POST['banned'] = 0;
            $_POST['ban_reason'] = NULL;
        }

        try {
            $stmt = $conn->prepare("UPDATE users SET 
            user_name = :user_name,
            admin_level = :admin_level,
            banned = :banned,
            ban_reason = :ban_reason
            WHERE user_ID = :user_ID");

            $stmt->execute(array('user_name' => $_POST['user_name'], 'admin_level' => $_POST['admin_level'], 'banned' => $_POST['banned'], 'ban_reason' => $_POST['ban_reason'], 'user_ID' => $_POST['user_ID']));
        } catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
            die();
        }
        header('Location: admin.php?page=users');
        die();
    }
}

if ($_POST['data'] == "add_warning") {
    if (isset($_POST['user_ID'])) {

        if (!isset($_POST['comment'])) {
            echo "You need to instert a comment to the warning!";
            die();
        }

        try {
            $stmt = $conn->prepare("INSERT INTO warnings (user_ID,comment)
            VALUES (:user_ID, :comment)");

            $stmt->execute(array('user_ID' => $_POST['user_ID'], 'comment' => $_POST['comment']));
        } catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
            die();
        }
        header('Location: admin.php?page=users');
        die();
    }
}

if ($_POST['data'] == "edit_warning") {
    if (isset($_POST['warning_ID'])  && isset($_POST['comment'])) {
        try {
            $stmt = $conn->prepare("UPDATE warnings SET 
            comment = :comment
            WHERE warning_ID = :warning_ID");

            $stmt->execute(array('warning_ID' => $_POST['warning_ID'], 'comment' => $_POST['comment']));
        } catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
            die();
        }
        header('Location: admin.php?page=users');
        die();
    }
}

if (($_POST['data'] == "del_user") && isset($_POST['user_ID'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM users
        WHERE user_ID = :user_ID");
        $stmt->execute(array('user_ID' => $_POST['user_ID']));
    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
        die();
    }
    header('Location: admin.php?page=users');
    die();
}

////////////////////////////////////////////////////////////
//////////        SITE SETTINGS UPDATE       ///////////////
////////////////////////////////////////////////////////////
if ($_POST['data'] == "site_settings") {
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
    die();
}

