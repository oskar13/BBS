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

$query = ('SELECT posts.post_ID, users.user_name, posts.post_date, posts.post_pic, posts.post_pic_thumb, posts.post_content, posts.post_sticky_level, posts.parent_ID, posts.file_size, posts.file_name, posts.file_x, posts.file_y 
FROM posts
LEFT JOIN users ON posts.user_ID=users.user_ID
WHERE board_ID=1 
ORDER BY post_sticky_level DESC,post_ID DESC');
$kask=$connection->prepare($query);
$kask->bind_result( $post_ID, $user_name, $post_date, $post_pic, $post_pic_thumb, $post_content, $post_sticky_level, $parent_ID, $file_size, $file_name, $file_x, $file_y);
$kask->execute();
while($kask->fetch()){
echo "post_ID-->".$post_ID."<br>"; 
echo "user_name-->".$user_name."<br>";
echo "post_date-->".$post_date."<br>";
echo "post_pic-->".$post_pic."<br>";
echo "post_pic_thumb-->".$post_pic_thumb."<br>";
echo "post_content-->".$post_content."<br>";
echo "post_sticky_level-->".$post_sticky_level."<br>";
echo "parent_ID-->".$parent_ID, $file_size."<br>";
echo "file_name-->".$file_name."<br>";
echo "file_x-->".$file_x."<br>";
echo "file_y-->".$file_y."<br>";
echo "---------<br>";
}

        ?>



            <?php
                $query = ('SELECT posts.post_ID, users.user_name, posts.post_date, posts.post_pic, posts.post_pic_thumb, posts.post_content, posts.post_sticky_level, posts.parent_ID, posts.file_size, posts.file_name, posts.file_x, posts.file_y 
                FROM posts
                LEFT JOIN users ON posts.user_ID=users.user_ID
                WHERE board_ID=1 
                ORDER BY post_sticky_level DESC,post_ID DESC');
                $kask=$connection->prepare($query);
                $kask->bind_result( $post_ID, $user_name, $post_date, $post_pic, $post_pic_thumb, $post_content, $post_sticky_level, $parent_ID, $file_size, $file_name, $file_x, $file_y);
                $kask->execute();
                while($kask->fetch()){
            ?>
            <div class="thread">


                <div class="post-parent">

                    <div class="file-info">File: <a href="#">1386011776979.jpg</a>-(5 KB, 258x195, laptop.jpg)</div>
                    <a class="post-image" href="<?php echo $post_pic; ?>"><img src="<?php echo $post_pic_thumb; ?>"></a>

                    <header class="post-meta">
                        <span class="username"><?php echo $user_name; ?></span> <span class="post-date"><?php echo date('Y/m/d H:i:s', $post_date); ?></span> <a href="#" class="post-no">No. <?php echo $post_ID; ?></a>
                    </header>
                    <div class="post-content">
                        <?php echo $post_content; ?>
                    </div>

                </div>
                <?php
                    $query = ("SELECT posts.post_ID, users.user_name, posts.post_date, posts.post_pic, posts.post_pic_thumb, posts.post_content, posts.post_sticky_level, posts.parent_ID, posts.file_size, posts.file_name, posts.file_x, posts.file_y 
                    FROM posts
                    LEFT JOIN users ON posts.user_ID=users.user_ID
                    WHERE posts.board_ID=1 AND posts.parent_ID='?'
                    ORDER BY post_sticky_level DESC,post_ID DESC");
                    $kask2=$connection2->prepare($query);
                    
                    $kask2->bind_result( $post_ID_2, $user_name_2, $post_date_2, $post_pic_2, $post_pic_thumb_2, $post_content_2, $post_sticky_level_2, $parent_ID_2, $file_size_2, $file_name_2, $file_x_2, $file_y_2);
                    
                    $kask->bind_param("i", $post_ID);

                    $kask2->execute();
                    while($kask2->fetch()){
                ?>
                        <div class="reply-container">
                            <div class="reply-arrows">>></div>
                            <div class="post-reply">
                                <header class="post-meta">
                                    <span class="username"><?php echo $user_name_2; ?></span> <span class="post-date"><?php echo date('Y/m/d H:i:s', $post_date_2); ?></span> <a href="#" class="post-no">No. <?php echo $post_ID_2; ?></a>
                                </header>
                                <div class="post-content">
                                    <?php echo $post_content_2; ?>
                                </div>
                            </div>
                            <div style="clear:both;"></div>
                        </div>
                <?php
                    }
                ?>

            </div>
            <?php
            }
            $c
            ?>

<?php
$id=1;



try {
    $conn = new PDO('mysql:host=localhost;dbname=4chan', USER, PASS);
    $stmt = $conn->prepare('SELECT posts.post_ID, users.user_name, posts.post_date, posts.post_pic, posts.post_pic_thumb, posts.post_content, posts.post_sticky_level, posts.parent_ID, posts.file_size, posts.file_name, posts.file_x, posts.file_y 
                    FROM posts
                    LEFT JOIN users ON posts.user_ID=users.user_ID
                    WHERE posts.board_ID=1 AND posts.parent_ID = :id
                    ORDER BY post_sticky_level DESC,post_ID DESC');

    $stmt->execute(array('id' => $id));
 
    $result = $stmt->fetchAll();
 
    if ( count($result) ) {
        foreach($result as $row) {
            print_r($row);
            echo "<br> //*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*/*//<br>";
            echo "<h1>ID =".$row['post_ID']." /parent_ID =". $row['parent_ID'] ."</h1>";
        }  
    } else {
        echo "No rows returned.";
    }
} catch(PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}

echo "<br><br>";
var_dump($result);
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