<?php
require('config.php');
session_set_cookie_params(1200, BASE_PATH);
session_start();


try {
    $stmt = $conn->prepare("SELECT site_name, show_desc ,site_desc_title, site_desc 
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
        <title><?php echo $site_settings['site_name']; ?></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">


        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/front.css">
    </head>
    <body>
        <div id="page-container">
        <?php
            if(isset($_SESSION['user_ID'])) {
                echo "<span id='user-meta'>";
                echo $_SESSION['user_name'];
                if ($_SESSION['admin_level'] > 0) {
                    echo " - <a href='". BASE_PATH ."admin.php'>Admin</a>";
                }
                echo " - <a href='". BASE_PATH ."login.php?logout=1'>Logout</a>";
                echo "</span>";
            } else {
                echo " <a href='". BASE_PATH ."login.php'>Login</a>";
            }
        ?>
        <header id="page-header">
            <h1><?php echo $site_settings['site_name']; ?></h1>
        </header>
        <?php
        if ($site_settings['show_desc']) {
        ?>
        <div class="blue-box">
        <?php
            echo "<h2>". $site_settings['site_desc_title'] ."</h2>";
            ?>
            <div class="blue-box-inner">
                <?php
                echo $site_settings['site_desc'];
                ?>
            </div>
        </div>
        <?php
        }
        ?>
        
        <div class="blue-box">
            <h2>List of boards</h2>
            <div class="blue-box-inner">
            <?php
                try {
                    
                    $stmt = $conn->prepare('SELECT board_url, board_name
                    FROM boards');

                    $stmt->execute();
                 
                    $board_list = $stmt->fetchAll();

                    if ( count($board_list) ) {
                        ?>
                        <table>
                        <?php
                        $row_c = 0;
                        $row_c_c = 0;
                        $last_element = end($board_list);
                        foreach($board_list as $board_list_row) {
                            if (0 == $row_c % 3) {
                                echo "<tr>";
                                $row_c_c = 0;
                            }



                            
                            echo "<td><a href='". BASE_PATH . $board_list_row['board_url'] ."' title='". $board_list_row['board_name'] ."'>". $board_list_row['board_name'] ."</a> - <a href='" . BASE_PATH . $board_list_row['board_url'] . "'>/". $board_list_row['board_url'] ."/</a></td>";
                            $row_c++;
                            if (((0 == $row_c % 3)&&($row_c_c == 1)) || ($last_element == $board_list_row) ) {
                                echo "</tr>";
                            } else {
                                $row_c_c = 1;
                            }
                            
                        }
                        echo "</table>";
                    } else {
                        echo "No boards found";
                    }
                } catch(PDOException $e) {
                    echo 'ERROR: ' . $e->getMessage();
                }
                ?>
            </div>
        </div>
        
        <div class="half blue-box">
            <h2>Latest Images</h2>
            <div class="blue-box-inner" id="latest-images">
                <ul>
<?php
try {
    $stmt = $conn->prepare('SELECT p.post_ID, pictures.pic_thumbname, p.parent_ID, boards.board_url
    FROM posts p
    LEFT JOIN pictures ON p.pic_ID=pictures.pic_ID
    LEFT JOIN boards ON p.board_ID=boards.board_ID
    WHERE pictures.pic_thumbname IS NOT NULL
    ORDER BY p.post_ID DESC
    LIMIT 0,3');
    $stmt->execute();
    $recent_images = $stmt->fetchAll();
    if ( count($recent_images) ) {
        foreach($recent_images as $recent_images_row) {
            echo "<li>";
            if ($recent_images_row['parent_ID']) {
                echo "<a href='".  BASE_PATH . $recent_images_row['board_url'] ."/res/" .$recent_images_row['parent_ID'] ."/#". $recent_images_row['post_ID'] ."'>";
            } else {
                echo "<a href='".  BASE_PATH . $recent_images_row['board_url'] ."/res/" .$recent_images_row['post_ID'] . "'>";
            }
            echo "<img src='" . BASE_PATH . "upload/" . $recent_images_row['pic_thumbname'] . "'>";
            echo "</a>";
            echo "</li>";
        }
    } else {
        //echo "No rows returned.";
    }
} catch(PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}
?>
                </ul>
            </div>
        </div>

        <div class="half-last blue-box">
            <h2>Latest Posts</h2>
            <div class="blue-box-inner" id="latest-posts">
                <ul>
<?php
try {
    $stmt = $conn->prepare('SELECT p.post_ID , p.parent_ID,boards.board_url, boards.board_name , SUBSTRING(p.post_content, 1, 100)
    FROM posts p
    LEFT JOIN boards ON p.board_ID=boards.board_ID
    WHERE post_content != ""
    ORDER BY post_ID DESC
    LIMIT 0,10');
    $stmt->execute();
    $recent_posts = $stmt->fetchAll();
    if ( count($recent_posts) ) {
        foreach($recent_posts as $recent_posts_row) {
            echo "<li>";
            if ($recent_posts_row['parent_ID']) {
                echo $recent_posts_row['board_name'] .": <a href='". $recent_posts_row['board_url'] . "/res/" . $recent_posts_row['parent_ID'] ."/#". $recent_posts_row['post_ID'] ."'>". $recent_posts_row['SUBSTRING(p.post_content, 1, 100)'] . "</a>";
                
            } else {
                echo $recent_posts_row['board_name'] .": <a href='". $recent_posts_row['board_url'] . "/res/" . $recent_posts_row['post_ID'] ."'>". $recent_posts_row['SUBSTRING(p.post_content, 1, 100)'] . "</a>";
            }
            
            echo "</li>";
        }
    } else {
        //echo "No rows returned.";
    }
} catch(PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}
?>
                </ul>
            </div>
        </div>
        <div style="clear:both;"></div>





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