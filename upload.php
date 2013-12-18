<?php
require('config.php');
session_set_cookie_params(1200, '/bbs');
session_start();

if (!isset($_POST['board_ID'])) {
  exit();
}
$image_skip = false;
$pic_ID = NULL;
$allowedExts = array("gif", "jpeg", "jpg", "png");
$temp = explode(".", $_FILES["file"]["name"]);
$extension = end($temp);

if (empty($_FILES['file']['name'])) {
    
    if (isset($_POST['parent_ID'])) {
      $image_skip = true;
    } else {
      echo "no image selected, error";
      exit();
    }
}

if ($image_skip == false) {






if ((($_FILES["file"]["type"] == "image/gif")
|| ($_FILES["file"]["type"] == "image/jpeg")
|| ($_FILES["file"]["type"] == "image/jpg")
|| ($_FILES["file"]["type"] == "image/pjpeg")
|| ($_FILES["file"]["type"] == "image/x-png")
|| ($_FILES["file"]["type"] == "image/png"))
&& ($_FILES["file"]["size"] < 3000000)
&& in_array($extension, $allowedExts))
  {
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
    }
  else
    {
    echo "Upload: " . $_FILES["file"]["name"] . "<br>";
    echo "Type: " . $_FILES["file"]["type"] . "<br>";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
    echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";

    $name = $_FILES["file"]["name"];
    $end_temp = explode(".", $name);
  $ext = end($end_temp);
  $pic_newname_no_ext = time().mt_rand(1,1000);
  $pic_newname = $pic_newname_no_ext.".".$ext;




    if (file_exists("upload/" . $pic_newname))
      {
        $pic_newname = ($pic_newname.mt_rand(1,100));
      }


    if (file_exists("upload/" . $pic_newname))
      {
        echo "<h1>YOU IS WINRAR</h1>";
      echo $pic_newname . " already exists. ";
      }
    else
      {
      move_uploaded_file($_FILES["file"]["tmp_name"],
      "upload/" .$pic_newname);
      echo "Stored in: " . "upload/" . $pic_newname;
      list($width, $height) = getimagesize("upload/" .$pic_newname);
      echo "x". $width."y".$height;

      /*$pic_url = "upload/" . $pic_newname;
      $pic_url_thumb = "upload/" . "thumb_" .$pic_newname_no_ext.".jpg";*/
      $pic_thumbname = "thumb_" .$pic_newname_no_ext.".jpg";
      $pic_size = ($_FILES["file"]["size"] / 1024);
      $pic_name = $_FILES["file"]["name"];
      $file_x = $width;
      $file_y = $height;

      try {
                
          $stmt = $conn->prepare("INSERT INTO pictures (pic_newname ,pic_thumbname ,pic_size ,pic_name ,file_x ,file_y)
          VALUES (:pic_newname, :pic_thumbname, :pic_size, :pic_name, :file_x, :file_y)");
          $stmt->execute(array('pic_newname' => $pic_newname, 'pic_thumbname' => $pic_thumbname, 'pic_size' => $pic_size, 'pic_name' => $pic_name ,'file_x' => $file_x ,'file_y' => $file_y));
          echo "<h1>";
          $pic_ID = $conn->lastInsertId();
          echo "</h1>";
          } catch(PDOException $e) {
              echo 'ERROR: ' . $e->getMessage();
          }






// http://stackoverflow.com/questions/16024770/create-thumbnail-after-upload-php

   function img_resize($src, $dest, $width, $height, $rgb = 0xFFFFFF, $quality = 100)
   {
        if (!file_exists($src))
            return false;

        $size = getimagesize($src);

        if ($size === false)
            return false;

        $format = strtolower(substr($size['mime'], strpos($size['mime'], '/') + 1));
        $icfunc = "imagecreatefrom" . $format;
        if (!function_exists($icfunc))
            return false;

        $x_ratio = $width / $size[0];
        $y_ratio = $height / $size[1];

        $ratio = min($x_ratio, $y_ratio);
        $use_x_ratio = ($x_ratio == $ratio);

        $new_width = $use_x_ratio ? $width : floor($size[0] * $ratio);
        $new_height = !$use_x_ratio ? $height : floor($size[1] * $ratio);
        $new_left = $use_x_ratio ? 0 : floor(($width - $new_width) / 2);
        $new_top = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);

        $isrc = $icfunc($src);
        $idest = imagecreatetruecolor($width, $height);

        imagefill($idest, 0, 0, $rgb);

        if (($format == 'gif') or ($format == 'png')) {
            imagealphablending($idest, false);
            imagesavealpha($idest, true);
        }

        if ($format == 'gif') {
            $transparent = imagecolorallocatealpha($idest, 255, 255, 255, 127);
            imagefilledrectangle($idest, 0, 0, $width, $height, $transparent);
            imagecolortransparent($idest, $transparent);
        }

        imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, $new_width, $new_height, $size[0], $size[1]);

        /* getResultImage($idest, $dest, $size['mime']); */
        imagejpeg($idest, $dest, $quality);

        imagedestroy($isrc);
        imagedestroy($idest);

        return true;
    }
/*
    function getResultImage($dst_r, $dest_path, $type)
    {
        switch ($type) {
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/pjpeg':
                return imagejpeg($dst_r, $dest_path, 90);
                break;
            case 'image/png';
                return imagepng($dst_r, $dest_path, 2);
                break;
            case 'image/gif';
                return imagegif($dst_r, $dest_path);
                break;
            default:
                return;
        }
    }
*/



$last_reply_date = time();



if (isset($_POST['parent_ID'])) {
  $thumb_size = 125;
            
} else {
  $thumb_size = 250;
}

if ($file_x >= $file_y) {
  $width = $thumb_size;
  $height = ($file_y*($width/$file_x));
} else {
  $height = $thumb_size;
  $width = ($file_x*($height/$file_y));
}

$pic_url = "upload/" . $pic_newname;
$pic_url_thumb = "upload/" . "thumb_" .$pic_newname_no_ext.".jpg";


img_resize($pic_url, $pic_url_thumb, $width, $height, $rgb = 0x000000, $quality = 50);
     }
    }
  }
else
  {
  echo "Invalid file";
  exit();
  }






}















$last_reply_date = time();
if (isset($_POST['parent_ID'])) {
  $parent_ID = $_POST['parent_ID'];
  try {
    $stmt = $conn->prepare('UPDATE posts 
    SET last_reply_date = :last_time
    WHERE post_ID =:parent_ID');

    $stmt->execute(array('last_time' => $last_reply_date, 'parent_ID' => $parent_ID));
    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
} else {
  $parent_ID = NULL;
}





$user_ID = 1;
$poster_ip = $_SERVER["REMOTE_ADDR"];
$post_date = time();
$post_subject = NULL;
$post_email = NULL;
$post_name = NULL;
$post_content = NULL;
$board_ID = $_POST['board_ID'];
$sticky_level = 0;
$del_pass = "asdf";




if (isset($_POST['name'])) {
  $post_name = htmlspecialchars($_POST['name']);
}

if (isset($_POST['email'])) {
  $post_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
}

if (isset($_POST['subject'])) {
  $post_subject = htmlspecialchars($_POST['subject']);
}

if (isset($_POST['comment'])) {
  $post_content = (nl2br(htmlspecialchars($_POST['comment'])));
}

if (isset($_SESSION['user_level'])) {
  if ($_SESSION['user_level'] == 3) {
    if (isset($_POST['sticky_level'])) {
      if(preg_match('/^\d+$/',$_GET['id'])) {
        $sticky_level = $_POST['sticky_level'];
      } else {
        echo "<br>Invalid input for sticky level!";
      }
    }
  }
}


echo "1111111";
      try {
                
          $stmt = $conn->prepare("INSERT INTO posts (user_ID ,poster_ip ,post_date ,post_name ,post_email ,post_subject ,post_content ,board_ID ,sticky_level ,del_pass ,pic_ID ,parent_ID ,last_reply_date)
          VALUES (:user_ID ,:poster_ip ,:post_date ,:post_name ,:post_email ,:post_subject ,:post_content ,:board_ID ,:sticky_level ,:del_pass ,:pic_ID ,:parent_ID ,:last_reply_date)");
          echo "222222222222222";
          $stmt->execute(array('user_ID' => $user_ID, 'poster_ip' => $poster_ip, 'post_date' => $post_date, 'post_name' => $post_name , 'post_email' => $post_email , 'post_subject' => $post_subject , 'post_content' => $post_content ,'board_ID' => $board_ID ,'sticky_level' => $sticky_level, 'del_pass' => $del_pass, 'pic_ID' => $pic_ID, 'parent_ID' => $parent_ID, 'last_reply_date' => $last_reply_date ));
          echo "333333333333333";
          } catch(PDOException $e) {
              echo 'ERROR: ' . $e->getMessage();
          }
















  echo "<pre>";
  echo var_dump($_FILES);
  echo "</pre>";
  echo $_POST['name'];

  echo $_POST['subject'];
?> 