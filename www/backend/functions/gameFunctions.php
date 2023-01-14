<?php
require_once($_SERVER['DOCUMENT_ROOT']."/backend/settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/conn.php");

function isUserInGame($user_id, $game_id){ //if user is in game return ID of game partecipation
    global $conn;
    $sql = "SELECT * FROM game_partecipation 
            WHERE ID_user = ? AND ID_game = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_id, $game_id);
    $stmt->execute();

    $row = $stmt->get_result()->fetch_assoc();
    if(isset($row['ID_game_partecipation'])){
        return $row['ID_game_partecipation'];
    }
    else{
        return 0;
    }
    
}

function leaveGame($game_id, $user_id){
    global $conn;

    $stmt = $conn->prepare("DELETE FROM game_partecipation WHERE ID_game = ? AND  ID_user = ?");
    $stmt->bind_param("ss", $game_id, $user_id);
    $stmt->execute();
}

function createGame($maxLives, $maxTime, $wordID, $roomID){
    global $conn;
    $stmt = $conn->prepare("INSERT INTO game (max_lives, max_time, ID_word, ID_room) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $maxLives, $maxTime, $wordID, $roomID);
    $stmt->execute();
    return $conn->insert_id;
}

function endGame($game_id){
    global $conn;

    $stmt = $conn->prepare("UPDATE game SET endTimestamp= ? WHERE ID_game = ?");    
    $stmt->bind_param("ss", date("Y-m-d H:i:s"), $game_id);
    $stmt->execute();
}

//return a random Word ID
function randomWord(){
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM word ORDER BY RAND() LIMIT 1");
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return array($row['ID_word'], $row['word']);
}

//connect all users to game
function connectAllUsers($gameID, $roomID){
    global $conn;
    $stmt = $conn->prepare("INSERT INTO game_partecipation (ID_game, ID_user)
                            SELECT ?, ID_user FROM room_partecipation WHERE ID_room = ?");
    $stmt->bind_param("ss", $gameID, $roomID);
    $stmt->execute();
}

function isPlayerTurn($user_id, $game_id){
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM game WHERE turnPlayerID = ? AND ID_game = ?");
    $stmt->bind_param("ss", $user_id, $game_id);
    $stmt->execute();

    $stmt->store_result();
    return $stmt->num_rows == 1;
}

function isGameActive($game_id){
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM game WHERE endTimestamp IS NULL AND ID_game = ?");
    $stmt->bind_param("s", $game_id);
    $stmt->execute();

    $stmt->store_result();
    return $stmt->num_rows == 1;
}

?>