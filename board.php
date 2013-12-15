<?php
require('config.php');
session_start();
$board_ID = 1;
?>

<!DOCTYPE html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>board title</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">


        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/board.css">
    </head>
    <body>
        <div id="page-container">

        <header id="page-header">
            <div class="board-list">[ <a href="#">b</a> / <a href="#">v</a> / <a href="#">i</a> / <a href="#">g</a> / <a href="#">gif</a> ]</div>
            <div id="board-title">
                <div id="banner"><div style="width:300px;height:100px;background:#d0d0d0;">placeholder</div></div>
                <h1>/g/ - Technology</h1>
                <span>meta html</span>
            </div>

            <div id="new-thread">
                <form > 
                    <tbody> 
                        <table> 
                            <tr> 
                                <td></td> 
                                <td></td> 
                            </tr> 
                        </table> 
            
                    </tbody> 
                </form> 
            </div> 


            <div class="global-message">
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            </div>
        </header>


            <?php 
            try {
                
                $stmt = $conn->prepare('SELECT posts.post_ID, users.user_name, posts.poster_ip, posts.post_date, posts.post_content, posts.sticky_level, posts.pic_ID
                FROM posts
                LEFT JOIN users ON posts.user_ID=users.user_ID
                WHERE board_ID=:board_ID AND parent_ID IS NULL
                ORDER BY posts.sticky_level DESC, last_reply_date DESC');

                $stmt->execute(array('board_ID' => $board_ID));
             
                $result = $stmt->fetchAll();
             
                if ( count($result) ) {
                    foreach($result as $posts_row) {
                ?>


                    <?php
                    $pic_ID = $posts_row['pic_ID'];
                    try {
                        
                        $stmt = $conn->prepare('SELECT pic_ID, pic_url, pic_url_thumb, pic_size, pic_name, file_x, file_y
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
                        <div class="file-info">File: <a href="#"><?php echo $posts_pic_info['pic_ID']; ?>.jpg</a>-(<?php echo $posts_pic_info['pic_size']; ?> KB, <?php echo $posts_pic_info['file_x']; ?>x<?php echo $posts_pic_info['file_y']; ?>, <?php echo $posts_pic_info['pic_name']; ?>)</div>
                        <a class="post-image" href="<?php echo $posts_pic_info['post_pic']; ?>"><img src="<?php echo $posts_pic_info['post_pic_thumb']; ?>"></a>
                        <?php } ?>
                        <header class="post-meta">
                            <span class="username"><?php echo $posts_row['user_name']; ?></span> <span class="post-date"><?php echo date('Y/m/d H:i:s', $posts_row['post_date']); ?></span> <a href="#" class="post-no">No. <?php echo $posts_row['post_ID']; ?></a>
                        </header>
                        <div class="post-content">

                            <?php echo $posts_row['post_content'];?>

                        </div>

                    </div>

                    <?php
                    $parent_ID=$posts_row['post_ID'];

                    try {
                        
                        $stmt = $conn->prepare('SELECT p.post_ID, users.user_name, p.poster_ip, p.post_date, p.post_content, p.pic_ID
                        FROM (SELECT post_ID, user_ID ,poster_ip, post_date, post_content, pic_ID, parent_ID
                            FROM posts
                            WHERE parent_ID=:parent_ID
                            ORDER BY post_ID DESC LIMIT 3) p
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
                        
                        $stmt = $conn->prepare('SELECT pic_ID, pic_url, pic_url_thumb, pic_size, pic_name, file_x, file_y
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
                                                    <span class="username"><?php echo $reply_row['user_name']; ?></span> <span class="post-date"><?php echo date('Y/m/d H:i:s', $reply_row['post_date']); ?></span> <a href="#" class="post-no">No. <?php echo $reply_row['post_ID']; ?></a>
                                                    <?php if ($reply_pic_info==True) { ?>
                                                    <div class="file-info">File: <a href="#"><?php echo $reply_pic_info['pic_ID']; ?>.jpg</a>-(<?php echo $reply_pic_info['pic_size']; ?> KB, <?php echo $reply_pic_info['file_x']; ?>x<?php echo $reply_pic_info['file_y']; ?>, <?php echo $reply_pic_info['pic_name']; ?>)</div>
                                                    <a class="post-image" href="<?php echo $reply_pic_info['post_pic']; ?>"><img src="<?php echo $reply_pic_info['post_pic_thumb']; ?>"></a>
                                                    <?php } ?>
                                                </header>
                                                   <div class="post-content">
                                                    <?php echo $reply_row['post_content']; ?>
                                                </div>
                                               </div>
                                               <div style="clear:both;"></div>
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
                <div class="pagination">[<a href="#">1</a>] [<a href="#">2</a>] [<a href="#">3</a>] [<a href="#">4</a>] [<a href="#">5</a>]</div>
                <div class="board-list">[ <a href="#">b</a> / <a href="#">v</a> / <a href="#">i</a> / <a href="#">g</a> / <a href="#">gif</a> ]</div>
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
