<?php

require_once($_SERVER['DOCUMENT_ROOT']."/backend/settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/conn.php");

function isRoomOpen($room_id){
}

#Doesn't check if room is open. Used for the first join.
function joinRoom($room_id, $user_id){ 
    global $conn;

    $stmt = $conn->prepare("INSERT INTO room_partecipation (ID_room, ID_user) VALUES (?,?)");
    $stmt->bind_param("ss", $room_id, $user_id);
    $stmt->execute();
}
?>