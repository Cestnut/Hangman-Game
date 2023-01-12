<?php
require_once($_SERVER['DOCUMENT_ROOT']."/backend/settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/conn.php");

function isUserInGame($user_id, $game_id){ //if user is in game return ID of game partecipation
    global $conn;
    $sql = "SELECT timestamp FROM game_partecipation 
            WHERE ID_user = ? AND ID_game = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_id, $game_id);
    $stmt->execute();

    $row = $stmt->get_result()->fetch_assoc();
    return $row['ID_game_partecipation'];
}

?>