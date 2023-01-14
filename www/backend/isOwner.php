<?php

require_once($_SERVER['DOCUMENT_ROOT']."/backend/settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/conn.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/functions/roomFunctions.php");


$room_ID = $_POST['roomID'];
$user_ID = $_SESSION['userID'];

echo isUserRoomHost($user_ID, $room_ID);
?>