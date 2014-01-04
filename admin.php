<?php
require('config.php');
session_set_cookie_params(1200, BASE_PATH);
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
                    <li><a href="<?php echo BASE_PATH; ?>">Home</a></li><li><a href="?page=new_article">New Article</a></li><li><a href="?page=boards">Boards</a></li><li><a href="?page=bans">List of Bans</a></li><li><a href="?page=users">Manage Users</a></li><li><a href="?page=site_settings">Site settings</a></li>
                </ul>
            </nav>


            <?php
            if (isset($_REQUEST['page'])) {
                /////////////////////////////////////////
                if ($_REQUEST['page'] == "new_article") {
                    ?>
                    <form action="admin_post.php" method="post">
                        <input type="hidden" name="data" value="new_article" />
                        <dl>
                            <dt><label for="heading">Title</label></dt>
                            <dd><input class="input-text" type="text" name="heading" /></dd>
                            <br />
                            <dt><label for="content">Post content</label></dt>
                            <dd><textarea class="input-text" rows="8" cols="60" name="content"></textarea></dd>
                        </dl>
                        <input style="margin-top: 1em;" type="submit" value="Publish">
                    </form>
                    <?php
                }
                /////////////////////////////////////////
                if ($_REQUEST['page'] == "boards") {
                    
                    try {
                        $stmt = $conn->prepare('SELECT board_ID, board_url, board_name
                        FROM boards');

                        $stmt->execute();
                     
                        $board_list = $stmt->fetchAll();

                        if ( count($board_list) ) {
                            ?>
                            <div class="half">
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
                                echo "'><td><a href='". BASE_PATH . $board_list_row['board_url'] ."' title='". $board_list_row['board_name'] ."'>". $board_list_row['board_name'] ."</a></td><td><a href='" . BASE_PATH . $board_list_row['board_url'] . "'>/". $board_list_row['board_url'] ."/</a></td><td><a href='?page=boards&modify_board_ID=". $board_list_row['board_ID'] ."'>Modify</a></td></tr>";

                            }
                            ?>
                            </table>
                            </div>
                            <?php
                        } else {
                            echo "No boards found";
                        }
                    } catch(PDOException $e) {
                        echo 'ERROR: ' . $e->getMessage();
                    }
                    if (isset($_REQUEST['modify_board_ID'])) {
                        try {
                
                            $stmt = $conn->prepare('SELECT board_ID, board_meta, board_url, board_name
                            FROM boards
                            WHERE board_ID = :board_ID');
                            $stmt->execute(array('board_ID' => $_REQUEST['modify_board_ID'] ));
                            $modify_board = $stmt -> fetch(PDO::FETCH_ASSOC);
                        } catch(PDOException $e) {
                            echo 'ERROR: ' . $e->getMessage();
                        }
                        if ($modify_board) {
                            ?>
                            <div class="half-last">
                            <h3>Edit <?php echo $modify_board['board_name'] . " - /". $modify_board['board_url']."/"; ?></h3>
                            <form action="admin_post.php" method="post">
                                <input type="hidden" name="data" value="edit_board" />
                                <input type="hidden" name="board_ID" value="<?php echo $modify_board['board_ID']; ?>" />

                                <dl>
                                    <dt><label for="board_meta">Board Meta</label></dt>
                                    <dd><textarea class="input-text" rows="4" cols="50" name="board_meta"><?php echo $modify_board['board_meta']; ?></textarea></dd>
                                    <br />
                                    <dt><label for="board_url">Board URL</label></dt>
                                    <dd><input class="input-text" type="text" name="board_url" value="<?php echo $modify_board['board_url']; ?>" /></dd>
                                    <br />
                                    <dt><label for="board_name">Board Name</label></dt>
                                    <dd><input class="input-text" type="text" name="board_name" value="<?php echo $modify_board['board_name']; ?>" /></dd>
                                </dl>
                                <br>
                                <input type="submit" value="Update">
                                
                            </form>
                            <br>
                            <form action="admin_post.php" method="post">
                                <input type="hidden" name="data" value="delete_board" />
                                <input type="hidden" name="board_ID" value="<?php echo $modify_board['board_ID']; ?>" />
                                <input type="submit" value="Delete Board">
                            </form>
                            </div>
                            <?php 
                        }

                    } elseif (isset($_REQUEST['new_board'])) {
                        ?>
                        <div class="half-last">
                        <h3>New Board</h3>
                        <form action="admin_post.php" method="post">
                            <input type="hidden" name="data" value="new_board" />

                            <dl>
                                <dt><label for="board_name">Board Name</label></dt>
                                <dd><input class="input-text" type="text" name="board_name" /></dd>
                                <br />
                                <dt><label for="board_url">Board URL</label></dt>
                                <dd><input class="input-text" type="text" name="board_url" /></dd>
                                <br />
                                <dt><label for="board_meta">Board Meta</label></dt>
                                <dd><textarea class="input-text" rows="4" cols="50" name="board_meta"></textarea></dd>
                            </dl>
                            <br>
                            <input type="submit" value="Submit">
                            
                        </form>
                        </div>
                        <?php
                    } else {
                        ?>
                        <br />
                        <a href="?page=boards&new_board=1">Create a new board.</a>
                        <?php
                    }
                }
                /////////////////////////////////////////
                if ($_REQUEST['page'] == "bans") {
                    
                     try {
                        
                        $stmt = $conn->prepare('SELECT ban_ID, ip, reason, ban_end, ban_begin
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
                                echo "'><td>". $ban_list_row['ip'] ."</td><td>". $ban_list_row['reason'] ."</td><td>". date('Y/m/d H:i:s', $ban_list_row['ban_begin']) ."</td><td>". date('Y/m/d H:i:s', $ban_list_row['ban_end']) ."</td>";
                                echo "<td><form action='admin_post.php' method='post'><input type='hidden' name='data' value='del_ban' /><input type='hidden' name='del_ban_ID' value='". $ban_list_row['ban_ID'] ."' /><input type='submit' value='Delete Ban' /></form></td></tr>";

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
                            <div class="half">
                            <table>
                            <tr><th>User name</th><th>Admin Level</th><th>Warnings</th><th></th><th></th><th></th></tr>
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
                                        echo "<a href='?page=users&user_ID=". $user_list_row['user_ID'] ."&edit_warning=1'>" . $warnings['COUNT( * )'] . "</a>";
                                    } else {
                                        echo "0";
                                    }



                                echo "</td><td>";
                                if ($user_list_row['banned'] > 0) {
                                    echo "<span style='color:red'>banned</span>";
                                }
                                echo "</td><td><a href='?page=users&user_ID=". $user_list_row['user_ID'] ."'>edit</a></td>";
                                echo "<td><form action='admin_post.php' method='post'><input type='hidden' name='data' value='del_user' /><input type='hidden' name='user_ID' value='". $user_list_row['user_ID'] ."' /><input type='submit' value='delete' /></form></td></td>";
                                echo "</tr>";

                            } 
                            ?>
                            </table>
                            </div>
                            <div class="half-last">
                            <?php
                            if (isset($_REQUEST['new_user'])) {
                                ?>
                                <h3>New user:</h3>
                                <form action="admin_post.php" method="post">
                                    <input type="hidden" name="data" value="new_user" />
                                    <dl>
                                        <dt><label for="user_name">User Name</label></dt>
                                        <dd><input class="input-text" type="text" name="user_name" /></dd>
                                        <dt><label for="user_pass">Password</label></dt>
                                        <dd><input class="input-text" type="password" name="user_pass" /></dd>
                                        <dt><label for="user_pass_again">Password again</label></dt>
                                        <dd><input class="input-text" type="password" name="user_pass_again" /></dd>
                                        <dt><label for="admin_level">Admin Level</label></dt>
                                        <dd><input class="input-text" type="text" name="admin_level" /></dd>
                                    </dl>
                                    <input type="submit" value="Create" />
                                </form>
                                <?php                                
                            } else {
                                ?>
                                <a href="?page=users&new_user=1">Create a new user</a>
                                <?php
                            }
                            ?>
                            </div>
                            <div style="clear:both;"></div>
                            <?php

                            if (isset($_REQUEST['user_ID'])) {
                                try {
                
                                    $stmt = $conn->prepare('SELECT user_ID, user_name, admin_level, banned, ban_reason
                                    FROM users
                                    WHERE user_ID = :user_ID');

                                    $stmt->execute(array('user_ID' => $_REQUEST['user_ID'] ));

                                    $user = $stmt -> fetch(PDO::FETCH_ASSOC);

                                } catch(PDOException $e) {
                                    echo 'ERROR: ' . $e->getMessage();
                                }

                                if ($user['user_ID']) {
                                    if (!isset($_REQUEST['edit_warning'])) {
                                        ?>
                                        <form action="admin_post.php" method="post">
                                            <input type="hidden" name="data" value="edit_user" />
                                            <input type="hidden" name="user_ID" value="<?php echo $user['user_ID']; ?>" />
                                            <dl>
                                                <dt><label for="user_name">User Name</label></dt>
                                                <dd><input class="input-text" type="text" name="user_name" value="<?php echo $user['user_name']; ?>" /></dd>
                                                <dt><label for="admin_level">Admin Level</label></dt>
                                                <dd><input class="input-text" type="text" name="admin_level" value="<?php echo $user['admin_level']; ?>" /></dd>
                                            </dl>
                                            <fieldset>
                                            <legend> Ban user </legend>
                                                <dl>
                                                    <label for="banned">Banned</label>
                                                    <input type="checkbox" name="banned" value="1" <?php if ($user['banned']) {  echo "checked='checked'";  }  ?> />
                                                    <br />
                                                    <br />
                                                    <dt><label for="ban_reason">Reason</label></dt>
                                                    <dd><textarea class="input-text" rows="4" cols="50" name="ban_reason"><?php echo $user['ban_reason']; ?></textarea></dd>
                                                </dl>
                                            </fieldset>
                                            <input type="submit" value="Update">
                                        </form>


                                        <form action="admin_post.php" method="post">
                                            <input type="hidden" name="data" value="add_warning" />
                                            <input type="hidden" name="user_ID" value="<?php echo $user['user_ID']; ?>" />
                                            <fieldset>
                                            <legend> Add a warning </legend>
                                                <dl>
                                                    <dt><label for="comment">Comment</label></dt>
                                                    <dd><textarea class="input-text" rows="4" cols="50" name="comment"></textarea></dd>
                                                </dl>
                                                <br>
                                                <input type="submit" value="Submit">
                                            </fieldset>
                                            
                                        </form>
                                        <?php
                                    } else {
                                        try {
                                            
                                            $stmt = $conn->prepare('SELECT warning_ID, comment
                                            FROM warnings
                                            WHERE user_ID = :user_ID');
                                            $stmt->execute(array('user_ID' => $user['user_ID']));
                                         
                                            $warnings = $stmt->fetchAll();
                                         
                                            if ( count($warnings) ) {
                                                echo "<h2>Warnings for user: ". $user['user_name'] ."</h2>";
                                                foreach($warnings as $warnings_row) {
                                                    ?>
                                                    <form action="admin_post.php" method="post">
                                                        <input type="hidden" name="data" value="edit_warning" />
                                                        <input type="hidden" name="warning_ID" value="<?php echo $warnings_row['warnings_ID']; ?>" />
                                                        
                                                        <fieldset>
                                                        <legend> Warning ID: <?php echo $warnings_row['warning_ID']; ?> </legend>
                                                            <dl>
                                                                <dt><label for="comment">Comment</label></dt>
                                                                <dd><textarea class="input-text" rows="4" cols="50" name="comment"><?php echo $warnings_row['comment']; ?></textarea></dd>
                                                            </dl>
                                                            <br>
                                                            <input type="submit" value="Update">
                                                        </fieldset>
                                                        
                                                    </form>
                                                    <?php
                                                }  
                                            } else {
                                                echo "Now warnings found for ". $user['user_name'];
                                            }
                                        } catch(PDOException $e) {
                                            echo 'ERROR: ' . $e->getMessage();
                                        }
                                    }                     
                                }
                                
                            }


                        } else {
                            ?>
                            No users found.
                            <?php
                        }
                    } catch(PDOException $e) {
                        echo 'ERROR: ' . $e->getMessage();
                    }
                }
                /////////////////////////////////////////
                if ($_REQUEST['page'] == "site_settings") {

                    try {
                        $stmt = $conn->prepare("SELECT site_name, show_desc ,site_desc_title, site_desc 
                        FROM site_options WHERE site_ID = 1");

                        $stmt->execute();
                         

                        $site_settings = $stmt -> fetch(PDO::FETCH_ASSOC);


                        } catch(PDOException $e) {
                            echo 'ERROR: ' . $e->getMessage();
                        }

                    ?>
                    <form action="admin_post.php" method="post">
                        <input type="hidden" name="data" value="site_settings" />
                        <dl>
                            <dt><label for="site_title">Site title</label></dt>
                            <dd><input class="input-text" type="text" name="site_title" value="<?php echo $site_settings['site_name']; ?>" /></dd>
                        </dl>
                        <fieldset>
                        <legend> Site info </legend>
                            <dl>
                                <label for="show">Show info on the first page</label>
                                <input type="checkbox" name="show" value="1" <?php if ($site_settings['show_desc']) {  echo "checked='checked'";  }  ?> />
                                <br />
                                <br />
                                <dt><label for="site_desc_title">Site Description title</label></dt>
                                <dd><input class="input-text" type="text" name="site_desc_title" value="<?php echo $site_settings['site_desc_title']; ?>" ></dd>
                                <br />
                                <dt><label for="site_desc">Site Descritpion</label></dt>
                                <dd><textarea class="input-text" rows="4" cols="50" name="site_desc"><?php echo $site_settings['site_desc']; ?></textarea></dd>
                            </dl>
                        </fieldset>
                        <input type="submit" value="Update">
                    </form>
                    <?php
                }
                /////////////////////////////////////////
            }
            ?>
        </div>
    </body>
</html>