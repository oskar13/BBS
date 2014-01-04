<?php

define('HOST', '127.0.0.1');
define('USER', 'root');
define('PASS', '');
define('DBNAME', '4chan');

define('BASE_PATH', '/bbs/');
define('POSTS_PER_PAGE', 10);

define('DEL_LEVEL', 3);

$conn = new PDO('mysql:host=127.0.0.1;dbname=4chan;charset=utf8', USER, PASS);
date_default_timezone_set('Europe/Tallinn');
