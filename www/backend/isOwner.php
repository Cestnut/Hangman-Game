<?php

require_once($_SERVER['DOCUMENT_ROOT']."/backend/settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/conn.php");

$room_ID = $_POST['roomID'];
$user_ID = $_SESSION['userID'];

$sql = "SELECT room.ID_room, game.ID_room, game.endTimestamp, game.ID_game FROM room 
JOIN game 
ON room.ID_room = game.ID_room 
WHERE room.ID_room = ? AND game.endTimestamp IS NULL";

$stmt = $conn->prepare("SELECT ID_host FROM room WHERE ID_room = ?");
$stmt->bind_param("s", $room_ID);
$stmt->execute();

$row = $stmt->get_result()->fetch_assoc();
if($row['ID_host'] == $user_ID)
    echo 1;
else
    echo 0;
?>