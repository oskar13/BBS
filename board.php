<?php
require('config.php');
session_set_cookie_params(1200, '/bbs');
session_start();


if (isset($_REQUEST['board_url'])) {
    $board_url = $_REQUEST['board_url'];
} else {
    echo "No board specified";
    exit();
}

if (isset($_REQUEST['page_no'])) {
    $page_no = $_REQUEST['page_no'];
} else {
    $page_no = 0;
}

//LIMIT jaoks, määrab mitmendast hakatakse kuvama postitusi
$skip = $page_no * POSTS_PER_PAGE;

// hea või halb otsus? kas kontrollida enne, kas tahvel on olemas, või teha suurem päring kõigile tahvlitele ning
// siis vaadata kas tahvel on olemas ja
// pärast saab neid andmeid kasutada tahvlite nimekirja loomisel

try {
    
    $stmt = $conn->prepare('SELECT board_ID, board_meta, board_url, board_name
    FROM boards
    WHERE board_url = :board_url');

    $stmt->execute(array('board_url' => $board_url));
     
    $boards = $stmt -> fetch();

    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }

$board_ID = $boards['board_ID'];

if ($boards['board_ID'] != True) {
    echo "No board found";
    exit();
}


?>

<!DOCTYPE html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>/<?php  echo $boards['board_url']; ?>/ - <?php  echo $boards['board_name']; ?></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="<?php echo BASE_PATH; ?>css/normalize.css">

        <link rel="stylesheet" href="<?php echo BASE_PATH; ?>css/board.css">
    </head>
    <body>
        <div id="page-container">

        <header id="page-header">
        <span class="board-list">[
        <?php

            try {
                
                $stmt = $conn->prepare('SELECT board_url, board_name
                FROM boards');

                $stmt->execute();
             
                $board_list = $stmt->fetchAll();
                $last_element = end($board_list);
                if ( count($board_list) ) {
                    foreach($board_list as $board_list_row) {

                    echo "<a href='". BASE_PATH . $board_list_row['board_url'] ."' title='". $board_list_row['board_name'] ."'>". $board_list_row['board_url'] ."</a>";
                    if ($last_element != $board_list_row) {
                        echo " / ";
                    }

                    }  
                } else {
                    //echo "No rows returned.";
                }
            } catch(PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }
            ?>
            ]</span>
            <?php
            if(isset($_SESSION['user_ID'])) {
                echo "<span id='user-meta'>";
                echo "Sup ".$_SESSION['user_name'];
                if ($_SESSION['admin_level'] > 0) {
                    echo " - <a href='". BASE_PATH ."admin.php'>Admin</a>";
                }
                echo " - <a href='". BASE_PATH ."login.php?logout=1'>Logout</a>";
                echo "</span>";
            }
            ?>
            <div id="board-title">
                <div id="banner"><div style="width:300px;height:100px;background:#d0d0d0;">placeholder</div></div>
                <h1>/<?php  echo $boards['board_url']; ?>/ - <?php  echo $boards['board_name']; ?></h1>
                <span><?php  echo $boards['board_meta']; ?></span>
            </div>
            <div id="new-thread">
                <form action="<?php echo BASE_PATH; ?>upload.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="board_ID" value="<?php echo $board_ID; ?>">
                        <table> 
                            <tr> 
                                <td class="label-col">Name</td> 
                                <td><input class="new-input" type="text" name="name" /></td> 
                            </tr>
                            <tr> 
                                <td class="label-col">Email</td> 
                                <td><input class="new-input" type="email" name="email"></td> 
                            </tr> 
                            <tr> 
                                <td class="label-col">Subject</td> 
                                <td><input class="new-input" type="text" name="subject"><input type="submit" value="Submit"></td> 
                            </tr> 
                            <tr> 
                                <td class="label-col">Comment</td> 
                                <td><textarea class="new-input" name="comment"></textarea></td> 
                            </tr> 
                            <tr> 
                                <td class="label-col">File</td> 
                                <td><input type="file" name="file" id="file"></td> 
                            </tr> 
                        </table> 
                </form> 
            </div> 




            <?php
            try {
                
                $stmt = $conn->prepare('SELECT message
                FROM global_message
                ORDER BY message_ID DESC');

                $stmt->execute();
                 

                $glob_msg = $stmt -> fetch(PDO::FETCH_ASSOC);


                } catch(PDOException $e) {
                    echo 'ERROR: ' . $e->getMessage();
                }
            if ($glob_msg==True) { ?>
            <div class="global-message">
                <?php echo "<p>". $glob_msg['message'] ."</p>"; ?>
            </div>
            <?php } ?>
        </header>


            <?php 
            try {
                $ppp = POSTS_PER_PAGE;
                $stmt = $conn->prepare('SELECT posts.post_ID, users.user_name, premissions.level, posts.poster_ip, posts.post_date, posts.post_name, posts.post_subject, posts.post_email, posts.post_content, posts.sticky_level, posts.pic_ID
                FROM posts
                LEFT JOIN users ON posts.user_ID = users.user_ID
                LEFT JOIN premissions ON posts.user_ID = premissions.premission_ID
                WHERE posts.board_ID =:board_ID AND posts.parent_ID IS NULL
                ORDER BY posts.sticky_level DESC , posts.last_reply_date DESC
                LIMIT :skip,:ppp');
/*
                $stmt->execute(array('board_ID' => $board_ID, 'ppp' => POSTS_PER_PAGE));
             
                $result = $stmt->fetchAll();
                //PDO EI LASE LIMITile seada parameetrit mis pole numbrina määratud
                //Peab kasutama bindParam();  
*/

    $stmt->bindParam(':board_ID', $board_ID, PDO::PARAM_INT);
    $stmt->bindParam(':skip', $skip, PDO::PARAM_INT);
    $stmt->bindParam(':ppp', $ppp, PDO::PARAM_INT);

    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);





                $row_count = count($result);
                if ( $row_count ) {
                    foreach($result as $posts_row) {
                ?>


                    <?php
                    $pic_ID = $posts_row['pic_ID'];
                    try {
                        
                        $stmt = $conn->prepare('SELECT pic_ID, pic_newname, pic_thumbname, pic_size, pic_name, file_x, file_y
                        FROM pictures
                        WHERE pic_ID =:pic_ID');

                        $stmt->execute(array('pic_ID' => $pic_ID));
                     

                        $posts_pic_info  = $stmt -> fetch();


                        } catch(PDOException $e) {
                            echo 'ERROR: ' . $e->getMessage();
                        }
                    ?>


                <div class="thread">


                    <div class="post-parent">

                        <?php if ($posts_pic_info==True) { ?>
                        <div class="file-info">File: <a href="<?php echo BASE_PATH."upload/" . $posts_pic_info['pic_newname']; ?>"><?php echo $posts_pic_info['pic_newname']; ?></a>-(<?php echo $posts_pic_info['pic_size']; ?> KB, <?php echo $posts_pic_info['file_x']; ?>x<?php echo $posts_pic_info['file_y']; ?>, <?php echo $posts_pic_info['pic_name']; ?>)</div>
                        <a class="post-image" href="<?php echo BASE_PATH."upload/" . $posts_pic_info['pic_newname']; ?>"><img src="<?php echo BASE_PATH."upload/" . $posts_pic_info['pic_thumbname']; ?>"></a>
                        <?php } ?>
                        <header class="post-meta">
                            <?php
                                if ($posts_row['post_subject']) {
                                    echo "<span class='subject'>";
                                    echo $posts_row['post_subject'];
                                    echo "</span>";
                                }
                            ?>
                            <?php
                                if ($posts_row['post_email'] ) {
                                    echo "<a href='mailto:".$posts_row['post_email']."'>";
                                }
                            ?>
                            <span class="username">
                            <?php
                                if ($posts_row['post_name']) {
                                    echo $posts_row['post_name'];
                                } else {
                                    if ($posts_row['user_name'] ) {
                                        echo $posts_row['user_name'];
                                    } else {
                                        echo "Anonymous";
                                    }
                                }
                            ?> 
                            <?php
                                if ($posts_row['post_email'] ) {
                                    echo "</a>";
                                }
                            ?>
                            </span>
                            <span class="post-date"><?php echo date('Y/m/d H:i:s', $posts_row['post_date']); ?></span> <a href="#" class="post-no">No. <?php echo $posts_row['post_ID']; ?></a>  [<a href="<?php echo BASE_PATH . $board_url ."/res/" .$posts_row['post_ID']; ?>">Reply</a>]</span>
                        </header>
                        <div class="post-content">

                            <?php echo $posts_row['post_content'];?>

                        </div>

                    </div>

                    <?php
                    /*
                    ----------------------------------------------------------------------------------------------
                    ----------------------------------------- POST REPLY -----------------------------------------
                    ----------------------------------------------------------------------------------------------                    
                    */
                    $parent_ID=$posts_row['post_ID'];

                    try {
                        $stmt = $conn->prepare('SELECT p.post_ID, users.user_name, p.poster_ip, p.post_date, p.post_name, p.post_subject, p.post_email, p.post_content, p.pic_ID
                        FROM (SELECT post_ID, user_ID ,poster_ip, post_date, post_name, post_subject, post_email, post_content, pic_ID, parent_ID
                            FROM posts
                            WHERE parent_ID=:parent_ID
                            ORDER BY post_ID DESC LIMIT 5) p
                        LEFT JOIN users ON p.user_ID=users.user_ID
                        ORDER BY p.post_ID');

                        $stmt->execute(array('parent_ID' => $parent_ID));
                     
                        $result2 = $stmt->fetchAll();
                        if ( count($result2) ) {
                            foreach($result2 as $reply_row) {
                    ?>




                    <?php
                    $pic_ID = $reply_row['pic_ID'];
                    try {
                        
                        $stmt = $conn->prepare('SELECT pic_ID, pic_newname, pic_thumbname, pic_size, pic_name, file_x, file_y
                        FROM pictures
                        WHERE pic_ID =:pic_ID');

                        $stmt->execute(array('pic_ID' => $pic_ID));
                     

                        $reply_pic_info  = $stmt -> fetch();


                        } catch(PDOException $e) {
                            echo 'ERROR: ' . $e->getMessage();
                        }


                    ?>


                                        <div class="reply-container">
                                            <div class="reply-arrows">>></div>
                                            <div class="post-reply">
                                                <header class="post-meta">
                                                    <?php
                                                        if ($reply_row['post_subject']) {
                                                            echo "<span class='subject'>";
                                                            echo $reply_row['post_subject'];
                                                            echo "</span>";
                                                        }
                                                    ?>
                                                    <span class="username">
                                                    <?php
                                                        if ($reply_row['post_name']) {
                                                            echo $reply_row['post_name'];
                                                        } else {
                                                            if ($reply_row['user_name'] ) {
                                                                echo $reply_row['user_name'];
                                                            } else {
                                                                echo "Anonymous";
                                                            }
                                                        }
                                                    ?> 
                                                    </span>
                                                    <span class="post-date"><?php echo date('Y/m/d H:i:s', $reply_row['post_date']); ?></span> <a href="#" class="post-no">No. <?php echo $reply_row['post_ID']; ?></a>
                                                    <?php if ($reply_pic_info==True) { ?>
                                                    <div class="file-info">File: <a href="#"><?php echo $reply_pic_info['pic_newname']; ?>.jpg</a>-(<?php echo $reply_pic_info['pic_size']; ?> KB, <?php echo $reply_pic_info['file_x']; ?>x<?php echo $reply_pic_info['file_y']; ?>, <?php echo $reply_pic_info['pic_name']; ?>)</div>
                                                    <a class="post-image" href="<?php echo BASE_PATH."upload/" . $reply_pic_info['pic_newname']; ?>"><img src="<?php echo BASE_PATH."upload/" . $reply_pic_info['pic_thumbname']; ?>"></a>
                                                    <?php } ?>
                                                </header>
                                                   <div class="post-content">
                                                    <?php echo $reply_row['post_content']; ?>
                                                </div>
                                               </div>
                                               
                                        </div>
                    <?php
                            }  
                        } else {
                            //echo "No rows returned.";
                        }


                    } catch(PDOException $e) {
                        echo 'ERROR: ' . $e->getMessage();
                    }


                    ?>

                    <div style="clear:both;"></div>
                </div>
                <?php
                        }  
                    } else {
                        echo "No threads.";
                    }
                } catch(PDOException $e) {
                    echo 'ERROR: ' . $e->getMessage();
                }


                ?>



            <footer>
            <div class="pagination">
                <?php 
                try {
                    
                    $stmt = $conn->prepare('SELECT COUNT( * )
                    FROM posts
                    WHERE board_ID =:board_ID AND parent_ID IS NULL');

                    $stmt->execute(array('board_ID' => $board_ID));
                     
                    $count_posts = $stmt -> fetch();

                    } catch(PDOException $e) {
                        echo 'ERROR: ' . $e->getMessage();
                    }


                $page_count = ($count_posts['COUNT( * )'] / POSTS_PER_PAGE);
                for ($i=0; $i < $page_count; $i++) { 
                    echo "[<a href='". BASE_PATH . $board_url ."/".$i."'>". $i ."</a>] ";
                }
                ?>
            </div>

                
                 <span class="board-list">[
                <?php
                    if ( count($board_list) ) {
                        foreach($board_list as $board_list_row) {

                        echo "<a href='". BASE_PATH . $board_list_row['board_url'] ."' title='". $board_list_row['board_name'] ."'>". $board_list_row['board_url'] ."</a>";
                        if ($last_element != $board_list_row) {
                            echo " / ";
                        }

                        }  
                    } else {
                        //echo "No rows returned.";
                    }
                ?>
                ]</span>

            </footer>


        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>

        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='//www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-XXXXX-X');ga('send','pageview');
        </script>
    </div>
    </body>
</html>
