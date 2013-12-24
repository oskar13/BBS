<?php
require('config.php');
session_set_cookie_params(1200, '/bbs');
session_start();

if(!isset($_SESSION['user_ID'])) {
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Admin</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="<?php echo BASE_PATH; ?>css/normalize.css">

        <link rel="stylesheet" href="<?php echo BASE_PATH; ?>css/admin.css">
    </head>
    <body>
        <div id="page-container">
            <nav id="nav">
                <ul>
                    <li><a href="admin.php">Admin Home</a></li><li><a href="?page=new_article">New Article</a></li><li><a href="?page=boards">Boards</a></li><li><a href="?page=bans">List of Bans</a></li><li><a href="?page=users">Manage Users</a></li>
                </ul>
            </nav>


            <?php
            if (isset($_REQUEST['page'])) {

                if ($_REQUEST['page'] == "boards") {
                    
                     try {
                        
                        $stmt = $conn->prepare('SELECT board_url, board_name
                        FROM boards');

                        $stmt->execute();
                     
                        $board_list = $stmt->fetchAll();

                        if ( count($board_list) ) {
                            ?>
                            <table>
                            <tr><th>Board Name</th><th></th><th></th></tr>
                            <?php
                            $odd_even = 0;
                            foreach($board_list as $board_list_row) {
                                echo "<tr class='";
                                if (0 == $odd_even % 2) {
                                    echo "even";
                                }
                                else {
                                    echo "odd";
                                }
                                $odd_even++;
                                echo "'><td><a href='". BASE_PATH . $board_list_row['board_url'] ."' title='". $board_list_row['board_name'] ."'>". $board_list_row['board_name'] ."</a></td><td><a href='" . BASE_PATH . $board_list_row['board_url'] . "'>/". $board_list_row['board_url'] ."/</a></td><td><a href='#'>Modify</a></td></tr>";

                            }
                            echo "</table>";
                        } else {
                            echo "No boards found";
                        }
                    } catch(PDOException $e) {
                        echo 'ERROR: ' . $e->getMessage();
                    }
                }
                /////////////////////////////////////////
                if ($_REQUEST['page'] == "bans") {
                    
                     try {
                        
                        $stmt = $conn->prepare('SELECT ban_ID, poster_ip, reason, ban_end, ban_begin
                        FROM banned');

                        $stmt->execute();
                     
                        $ban_list = $stmt->fetchAll();

                        if ( count($ban_list) ) {
                            ?>
                            <table>
                            <tr><th>IP</th><th>Reason</th><th>Placed on</th><th>Ends</th><th></th></tr>
                            <?php
                            $odd_even = 0;
                            foreach($ban_list as $ban_list_row) {
                                echo "<tr class='";
                                if (0 == $odd_even % 2) {
                                        echo "even";
                                    }
                                    else {
                                        echo "odd";
                                    }
                                    $odd_even++;
                                echo "'><td>". $ban_list_row['poster_ip'] ."</td><td>". $ban_list_row['reason'] ."</td><td>". $ban_list_row['ban_begin'] ."</td><td>". $ban_list_row['ban_end'] ."</td><td><a href='#'>delete</a></td></tr>";

                            } 
                            echo "</table>";
                        } else {
                            echo "No bans found :(";
                        }
                    } catch(PDOException $e) {
                        echo 'ERROR: ' . $e->getMessage();
                    }
                }
                /////////////////////////////////////////
                if ($_REQUEST['page'] == "users") {
                    
                     try {
                        
                        $stmt = $conn->prepare('SELECT user_ID, user_name, admin_level, banned
                        FROM users');

                        $stmt->execute();
                     
                        $user_list = $stmt->fetchAll();

                        if ( count($user_list) ) {
                            ?>
                            <table>
                            <tr><th>User name</th><th>Admin Level</th><th>Warnings</th><th></th><th></th></tr>
                            <?php
                            $odd_even = 0;
                            foreach($user_list as $user_list_row) {
                                echo "<tr class='";
                                if (0 == $odd_even % 2) {
                                        echo "even";
                                    }
                                    else {
                                        echo "odd";
                                    }
                                    $odd_even++;
                                echo "'><td>". $user_list_row['user_name'] ."</td><td>". $user_list_row['admin_level'] ."</td><td>";

                                try {
                
                                    $stmt = $conn->prepare('SELECT COUNT( * )
                                    FROM warnings
                                    WHERE user_ID =:user_ID');

                                    $stmt->execute(array('user_ID' => $user_list_row['user_ID'] ));
                                     

                                    $warnings = $stmt -> fetch(PDO::FETCH_ASSOC);


                                    } catch(PDOException $e) {
                                        echo 'ERROR: ' . $e->getMessage();
                                    }


                                    if ($warnings) { 
                                        echo "<a href='#'>" . $warnings['COUNT( * )'] . "</a>";
                                    } else {
                                        echo "0";
                                    }



                                echo "</td><td>";
                                if ($user_list_row['banned'] > 0) {
                                    echo "<span style='color:red'>banned</span>";
                                }
                                echo "</td><td><a href='#'>edit</a></td></tr>";

                            } 
                            echo "</table>";
                        } else {
                            echo "No users found :(";
                        }
                    } catch(PDOException $e) {
                        echo 'ERROR: ' . $e->getMessage();
                    }
                }
                /////////////////////////////////////////
            }
            ?>
        </div>
    </body>
</html>