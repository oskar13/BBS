<?php
require('config.php');
session_set_cookie_params(1200, '/bbs');
session_start();
$login_error = 0;


if (isset($_REQUEST['logout'])) {
	session_destroy();
	header("Location: login.php");
	exit();
}



if (isset($_POST['username']) && isset($_POST['password'])) {
    try {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $stmt = $conn->prepare('SELECT user_ID, user_name, user_pass, admin_level, banned, ban_reason
        FROM users
        WHERE user_name = :username AND user_pass = password(:password)');

        $stmt->execute(array('username' => $username, 'password' => $password ));
        $user_data = $stmt -> fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
    }
    if ($user_data['user_ID'] == True) {
    	$_SESSION['user_ID'] = $user_data['user_ID'];
    	$_SESSION['user_name'] = $user_data['user_name'];
    	$_SESSION['user_pass'] = $user_data['user_pass'];
    	$_SESSION['admin_level'] = $user_data['admin_level'];
    	$_SESSION['banned'] = $user_data['banned'];
    } else {
    	$login_error = 1;
    }
}


?>
<!DOCTYPE html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Login</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="<?php echo BASE_PATH; ?>css/normalize.css">

        <link rel="stylesheet" href="<?php echo BASE_PATH; ?>css/board.css">
    </head>
    <body>
        <div id="page-container">
        <?php
		if(!isset($_SESSION['user_ID'])) {
		?>
        <header id="page-header">
        <h1>Login</h1>
        </header>
        <?php if ($login_error > 0) { ?>
        <div class="error">
        	<p>Username or password is wrong!</p>
        </div>
        <?php } ?>
		<form action="<?php echo BASE_PATH; ?>login.php" method="post">
            <table> 
                <tr> 
                    <td class="label-col">Username</td> 
                    <td><input class="new-input" type="text" name="username" /></td> 
                </tr>
                <tr> 
                    <td class="label-col">Password</td> 
                    <td><input class="new-input" type="password" name="password"></td>
                </tr>
            </table> 
			<input type="submit" value="Submit">
		</form>


		<?php
		} else {
		?>
			<p>Welcome <?php echo $_SESSION['user_name']; ?>. <a href="?logout=1">Logout?</a></p>
		<?php
			if ($_SESSION['banned'] > 0) {
				echo "<big style='color:red'>Seems like you are banned!</big>";
				echo "<br>Reason:";
				    try {
				        $stmt = $conn->prepare('SELECT ban_reason
				        FROM users
				        WHERE user_ID = :user_ID');

				        $stmt->execute(array('user_ID' => $_SESSION['user_ID'] ));
				        $ban_reason = $stmt -> fetch();
				    } catch(PDOException $e) {
				            echo 'ERROR: ' . $e->getMessage();
				    }
				echo "<blockquote>". $ban_reason['ban_reason'] ."</blockquote>";
			}
		}


		?>
		</div>
	</body>
</html>