<?php
$check_ip = $_SERVER["REMOTE_ADDR"];

try {
	$stmt = $conn->prepare('SELECT ban_ID, reason, ban_end
    FROM banned
    WHERE ip =:ip');

    $stmt->execute(array('ip' => $check_ip));

    $is_banned = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
    die();
}

if ($is_banned) {
	echo "you are banned";
	die();
}