<?php
require('config.php');
session_start();


if (isset($_REQUEST['post_ID'])) {
    $post_ID = $_REQUEST['post_ID'];
} else {
    echo "No post specified";
    exit();
}

$board_url = NULL;
if (isset($_REQUEST['board_url'])) {
    $board_url = $_REQUEST['board_url'];
} 

try {
    $stmt = $conn->prepare('SELECT board_ID, board_meta, board_url, board_name
    FROM boards
    WHERE board_ID = (SELECT board_ID FROM posts WHERE post_ID = :post_ID)');
    $stmt->execute(array('post_ID' => $post_ID));
     
    $boards = $stmt -> fetch();
    
    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }

if ($boards['board_ID'] != True) {
    echo "No board found";
    exit();
}

if ($boards['board_url'] != $board_url) {
    echo "wrong url";
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
        <div class="board-list">[
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
            ]</div>
            <div id="board-title">
                <div id="banner"><div style="width:300px;height:100px;background:#d0d0d0;">placeholder</div></div>
                <h1>/<?php  echo $boards['board_url']; ?>/ - <?php  echo $boards['board_name']; ?></h1>
                <span><?php  echo $boards['board_meta']; ?></span>
            </div>
            <div id="new-thread">
                <form action="<?php echo BASE_PATH; ?>upload.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="parent_ID" value="<?php echo $post_ID; ?>">
                    <input type="hidden" name="board_ID" value="<?php echo $boards['board_ID']; ?>">
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
                 

                $glob_msg = $stmt -> fetch();


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
                $stmt = $conn->prepare('SELECT posts.post_ID, users.user_name, premissions.level, posts.poster_ip, posts.post_date, posts.post_content, posts.sticky_level, posts.pic_ID
                FROM posts
                LEFT JOIN users ON posts.user_ID = users.user_ID
                LEFT JOIN premissions ON posts.user_ID = premissions.premission_ID
                WHERE posts.post_ID =:post_ID ');

                $stmt->execute(array('post_ID' => $post_ID));
             
                $posts_row = $stmt->fetch();

                } catch(PDOException $e) {
                    echo 'ERROR: ' . $e->getMessage();
                }


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
                        <div class="file-info">File: <a href="#"><?php echo $posts_pic_info['pic_newname']; ?></a>-(<?php echo $posts_pic_info['pic_size']; ?> KB, <?php echo $posts_pic_info['file_x']; ?>x<?php echo $posts_pic_info['file_y']; ?>, <?php echo $posts_pic_info['pic_name']; ?>)</div>
                        <a class="post-image" href="<?php echo BASE_PATH."upload/" . $posts_pic_info['pic_newname']; ?>"><img src="<?php echo BASE_PATH."upload/" . $posts_pic_info['pic_thumbname']; ?>"></a>
                        <?php } ?>
                        <header class="post-meta">
                            <span class="username"><?php if ($posts_row['user_name'] ) {  echo $posts_row['user_name']; } else { echo "Anonymous"; }?></span> <span class="post-date"><?php echo date('Y/m/d H:i:s', $posts_row['post_date']); ?></span> <a href="#" class="post-no">No. <?php echo $posts_row['post_ID']; ?></a>
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
                   
                    try {
                        
                        $stmt = $conn->prepare('SELECT p.post_ID, users.user_name, p.poster_ip, p.post_date, p.post_content, p.pic_ID
                        FROM posts p
                        LEFT JOIN users ON p.user_ID=users.user_ID
                        WHERE p.parent_ID = :parent_ID
                        ORDER BY p.post_ID');

                        $stmt->execute(array('parent_ID' => $post_ID));
                     
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
                                                    <span class="username"><?php if ($reply_row['user_name'] ) {  echo $reply_row['user_name']; } else { echo "Anonymous"; }?></span> <span class="post-date"><?php echo date('Y/m/d H:i:s', $reply_row['post_date']); ?></span> <a href="#" class="post-no">No. <?php echo $reply_row['post_ID']; ?></a>
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
 

            <footer>


                
                 <div class="board-list">[
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
                ]</div>

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
