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
                
                $stmt = $conn->prepare('SELECT thread.thread_ID, users.user_name, thread.post_date, thread.post_content, thread.sticky_level, thread.pic_ID
                FROM thread
                LEFT JOIN users ON thread.user_ID=users.user_ID
                WHERE board_ID=:board_ID
                ORDER BY thread.sticky_level DESC, thread.thread_ID DESC');

                $stmt->execute(array('board_ID' => $board_ID));
             
                $result = $stmt->fetchAll();
             
                if ( count($result) ) {
                    foreach($result as $thread_row) {
                ?>


                    <?php
                    $pic_ID = $thread_row['pic_ID'];
                    try {
                        
                        $stmt = $conn->prepare('SELECT pic_ID, pic_url, pic_url_thumb, pic_size, pic_name, file_x, file_y
                        FROM pictures
                        WHERE pic_ID =:pic_ID');

                        $stmt->execute(array('pic_ID' => $pic_ID));
                     

                        $thread_pic_info  = $stmt -> fetch();


                        } catch(PDOException $e) {
                            echo 'ERROR: ' . $e->getMessage();
                        }
                    ?>


                <div class="thread">


                    <div class="post-parent">

                        <?php if ($thread_pic_info==True) { ?>
                        <div class="file-info">File: <a href="#">1386011776979.jpg</a>-(<?php echo $thread_pic_info['pic_size']; ?> KB, 258x195, laptop.jpg)</div>
                        <a class="post-image" href="<?php echo $thread_pic_info['post_pic']; ?>"><img src="<?php echo $thread_pic_info['post_pic_thumb']; ?>"></a>
                        <?php } ?>
                        <header class="post-meta">
                            <span class="username"><?php echo $thread_row['user_name']; ?></span> <span class="post-date"><?php echo date('Y/m/d H:i:s', $thread_row['post_date']); ?></span> <a href="#" class="post-no">No. <?php echo $thread_row['thread_ID']; ?></a>
                        </header>
                        <div class="post-content">

                            <?php echo $thread_row['post_content'];?>

                        </div>

                    </div>

                    <?php
                    $thread_id=$thread_row['thread_ID'];


                    try {
                        
                        $stmt = $conn->prepare('SELECT reply.reply_ID, users.user_name, reply.post_date, reply.post_content, reply.pic_ID
                        FROM reply
                        LEFT JOIN users ON reply.user_ID = users.user_ID
                        WHERE thread_ID =:thread_ID
                        ORDER BY reply.reply_ID DESC');

                        $stmt->execute(array('thread_ID' => $thread_id));
                     
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
                                                    <span class="username"><?php echo $reply_row['user_name']; ?></span> <span class="post-date"><?php echo date('Y/m/d H:i:s', $reply_row['post_date']); ?></span> <a href="#" class="post-no">No. <?php echo $reply_row['reply_ID']; ?></a>
                                                    <?php if ($thread_pic_info==True) { ?>
                                                    <div class="file-info">File: <a href="#">1386011776979.jpg</a>-(<?php echo $reply_pic_info['pic_size']; ?> KB, 258x195, laptop.jpg)</div>
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
