<?php

require_once($_SERVER['DOCUMENT_ROOT']."/backend/settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/conn.php");

#Doesn't check if room is open. Used for the first join.
function joinRoom($room_id, $user_id){ 
    global $conn;

    $stmt = $conn->prepare("INSERT INTO room_partecipation (ID_room, ID_user) VALUES (?,?)");
    $stmt->bind_param("ss", $room_id, $user_id);
    $stmt->execute();
}

function leaveRoom($room_id, $user_id){ 
    global $conn;

    $stmt = $conn->prepare("DELETE FROM room_partecipation WHERE ID_room = ? AND  ID_user = ?");
    $stmt->bind_param("ss", $room_id, $user_id);
    $stmt->execute();
}

function isRoomOpen($room_id){
    global $conn;
    $sql = "SELECT room.ID_host, room_partecipation.ID_room FROM room 
            JOIN room_partecipation 
            ON room.ID_host = room_partecipation.ID_user 
            AND room.ID_room = room_partecipation.ID_room 
            WHERE room_partecipation.ID_room = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $room_id);
    $stmt->execute();

    $stmt->store_result();
    return $stmt->num_rows == 1;
}

function isRoomActive($room_id){
    //TOADD
    return 1;                             
}

function isUserInRoom($user_id, $room_id){
    global $conn;
    $sql = "SELECT * FROM room_partecipation 
            WHERE ID_user = ? AND ID_room = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_id, $room_id);
    $stmt->execute();

    $stmt->store_result();
    return $stmt->num_rows == 1;
}
?>