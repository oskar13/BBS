<?php
require('config.php');
session_set_cookie_params(1200, BASE_PATH);
session_start();
$del_img = 0;

if (isset($_REQUEST['post_ID'])) {

	try {
		$stmt = $conn->prepare('SELECT poster_ip, user_ID, pic_ID, parent_ID
	    FROM posts
	    WHERE post_ID =:post_ID');

	    $stmt->execute(array('post_ID' => $_REQUEST['post_ID']));

	    $result = $stmt->fetch(PDO::FETCH_ASSOC);
	} catch(PDOException $e) {
	    echo 'ERROR: ' . $e->getMessage();
	    die();
	}

	if (isset($_REQUEST['ban'])) {
		$ban_begin = time();
		$ban_end = time() + 100;
		try {
	        $stmt = $conn->prepare("INSERT INTO banned (ip, ban_begin, ban_end)
	        VALUES (:ip, :ban_begin, :ban_end)");
	        $stmt->execute(array('ip' => $result['poster_ip'], 'ban_begin' => $ban_begin, 'ban_end' => $ban_end));
	    } catch(PDOException $e) {
	        echo 'ERROR: ' . $e->getMessage();
	        die();
	    }
	}

	if (isset($_REQUEST['del_post'])) {
	    try {
	        $stmt = $conn->prepare("DELETE FROM posts
	        WHERE post_ID = :post_ID");
	        $stmt->execute(array('post_ID' => $_REQUEST['post_ID']));
	    } catch(PDOException $e) {
	        echo 'ERROR: ' . $e->getMessage();
	        die();
	    }

	    if (!$result['parent_ID']) {
	    	try {
		        $stmt = $conn->prepare("SELECT posts.pic_ID, pictures.pic_newname, pictures.pic_thumbname
		        FROM posts
		        LEFT JOIN pictures ON posts.pic_ID = pictures.pic_ID
		        WHERE parent_ID = :post_ID");
		        $stmt->execute(array('post_ID' => $_REQUEST['post_ID']));

		        $pic_del = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ( count($pic_del) ) {
				        foreach($pic_del as $pic_del_row) {
				        	try {
							    $stmt = $conn->prepare("DELETE FROM pictures
							    WHERE pic_ID = :pic_ID");
							    $stmt->execute(array('pic_ID' => $pic_del_row['pic_ID']));
							} catch(PDOException $e) {
							    echo 'ERROR: ' . $e->getMessage();
							    die();
							}

							$pic = "upload/" . $pic_del_row['pic_newname'];
							$pic_thumb = "upload/" . $pic_del_row['pic_thumbname'];
							unlink($pic);
							unlink($pic_thumb);
				        }  
				} else {
				    //echo "No rows returned.";
				}

		    } catch(PDOException $e) {
		        echo 'ERROR: ' . $e->getMessage();
		        die();
		    }



	    	try {
		        $stmt = $conn->prepare("DELETE FROM posts
		        WHERE parent_ID = :post_ID");
		        $stmt->execute(array('post_ID' => $_REQUEST['post_ID']));
		    } catch(PDOException $e) {
		        echo 'ERROR: ' . $e->getMessage();
		        die();
		    }
	    }


	    $del_img = 1;
	}
	echo "<pre>";
	echo var_dump($del_img);
	echo "<pre>";

	if (isset($_REQUEST['del_img']) || ($del_img==1) ) {
		if ($result['pic_ID']) {
			try {
				$stmt = $conn->prepare('SELECT pic_ID, pic_newname, pic_thumbname
	            FROM pictures
	            WHERE pic_ID =:pic_ID');

			    $stmt->execute(array('pic_ID' => $result['pic_ID']));

			    $pic_result = $stmt->fetch(PDO::FETCH_ASSOC);

			} catch(PDOException $e) {
			    echo 'ERROR: ' . $e->getMessage();
			    die();
			}
			if ($pic_result) {
				$pic = "upload/" . $pic_result['pic_newname'];
				$pic_thumb = "upload/" . $pic_result['pic_thumbname'];
				unlink($pic);
				unlink($pic_thumb);
			}
			
			try {
			    $stmt = $conn->prepare("DELETE FROM pictures
			    WHERE pic_ID = :pic_ID");
			    $stmt->execute(array('pic_ID' => $result['pic_ID']));
			} catch(PDOException $e) {
			    echo 'ERROR: ' . $e->getMessage();
			    die();
			}
		}
	}
}