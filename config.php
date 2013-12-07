<?php

define('HOST', '127.0.0.1');
define('USER', 'root');
define('PASS', '');
define('DBNAME', '4chan');

$connection=new mysqli(HOST, USER, PASS, DBNAME);

if ($connection->connect_errno) {
	echo "Failed to connect to MySQL: (" 
    . $connection->connect_errno . ") " . $connection->connect_error;
}

$connection2=new mysqli(HOST, USER, PASS, DBNAME);

if ($connection2->connect_errno) {
	echo "Failed to connect to MySQL: (" 
    . $connection2->connect_errno . ") " . $connection2->connect_error;
}
date_default_timezone_set('Europe/Tallinn');
