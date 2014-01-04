<?php
require('config.php');
session_set_cookie_params(1200, BASE_PATH);
session_start();


try {
    $stmt = $conn->prepare("SELECT site_name
    FROM site_options WHERE site_ID = 1");

    $stmt->execute();
    

    $site_settings = $stmt -> fetch(PDO::FETCH_ASSOC);


} catch(PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo $site_settings['site_name']; ?> - News</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">


        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/blog.css">
    </head>
    <body>
        <div id="page-container">
        <header id="page-header">
            <h1>News</h1>
        </header>
        

        <?php
        if (!isset($_REQUEST['article_ID'])) {
            try {
                $stmt = $conn->prepare('SELECT a.article_ID, users.user_name, a.heading, a.a_date, a.content
                FROM articles a
                LEFT JOIN users ON a.user_ID=users.user_ID
                ORDER BY a.a_date DESC');
                $stmt->execute();
                $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
             
                if ( count($news) ) {
                    foreach($news as $news_row) {
                        ?>
                        <div class="blue-box">
                            <h2><?php echo $news_row['heading']; ?> <span class="article-meta">by <span class="article-author"><?php echo $news_row['user_name']; ?></span> on <?php echo date('Y/m/d H:i:s', $news_row['a_date']); ?></span></h2>
                            <div class="blue-box-inner">
                                <?php echo $news_row['content']; 
                                    try {
                                        $stmt = $conn->prepare('SELECT COUNT( * )
                                        FROM comments
                                        WHERE article_ID =:article_ID');

                                        $stmt->execute(array('article_ID' => $news_row['article_ID'] ));
                                        
                                        $comments = $stmt -> fetch(PDO::FETCH_ASSOC);

                                    } catch(PDOException $e) {
                                        echo 'ERROR: ' . $e->getMessage();
                                    }
                                    if ($comments) {
                                        echo "<p class='comments'><a href='?article_ID=". $news_row['article_ID'] ."'>Comments (" . $comments['COUNT( * )'] . ")</a></p>";
                                    }
                                
                                ?>
                            </div>
                        </div>
                        <?php
                    }  
                } else {
                    echo "No posts found :(";
                }
            } catch(PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }
        } else {
            try {
                $stmt = $conn->prepare('SELECT a.article_ID, users.user_name, a.heading, a.a_date, a.content
                FROM articles a
                LEFT JOIN users ON a.user_ID=users.user_ID
                WHERE a.article_ID = :article_ID');
                $stmt->execute(array('article_ID' =>  $_REQUEST['article_ID']));
                $article = $stmt->fetch(PDO::FETCH_ASSOC);
             
                ?>
                    <div class="blue-box">
                        <h2><?php echo $article['heading']; ?> <span class="article-meta">by <span class="article-author"><?php echo $article['user_name']; ?></span> on <?php echo date('Y/m/d H:i:s', $article['a_date']); ?></span></h2>
                        <div class="blue-box-inner">
                            <?php echo $article['content']; 
                            ?>
                            <h3>Comments</h3>
                            <?php
                            try {
                                
                                $stmt = $conn->prepare('SELECT c.comment_ID, users.user_name, c.c_date, c.comment
                                FROM comments c
                                LEFT JOIN users ON c.user_ID = users.user_ID
                                WHERE c.article_ID = :article_ID
                                ORDER BY c.c_date DESC');

                                $stmt->execute(array('article_ID' => $article['article_ID']));
                             
                                $comment = $stmt->fetchAll(PDO::FETCH_ASSOC);
                             
                                if ( count($comment) ) {
                                    foreach($comment as $comment_row) {
                            ?>


                            <div class="reply-container">
                                <div class="reply-arrows">>></div>
                                <div class="post-reply">
                                    <header class="post-meta">
                                        <span class="username"><?php echo $comment_row['user_name']; ?></span> <span class="post-date"><?php echo date('Y/m/d H:i:s', $comment_row['c_date']); ?></span> <a href="#" class="post-no">No. <?php echo $comment_row['comment_ID']; ?></a>
                                    </header>
                                    <div class="post-content">
                                        <?php echo $comment_row['comment']; ?>
                                    </div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>

                            <?php
                                    }  
                                } else {
                                    echo "No comments found";
                                }
                            } catch(PDOException $e) {
                                echo 'ERROR: ' . $e->getMessage();
                            }
                            ?>
                            
                            
                        </div>
                    </div>
                    <?php
            } catch(PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            } 

            ?>
            <h3>Add Comment</h3>
            <form action="news_post.php" method="post">
                <input type="hidden" name="article_ID" value="<?php echo $article['article_ID']; ?>">
                <textarea class="input-text" rows="4" cols="50" name="comment"></textarea>
                <input type="submit" value="Post">
            </form>
            <?php


        }

        
        ?>

        <footer id="page-footer">© Copyright <?php echo date('Y'); ?>, <?php echo $site_settings['site_name']; ?></footer>
        </div>


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
    </body>
</html>