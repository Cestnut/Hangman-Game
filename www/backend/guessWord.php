<?php
require_once($_SERVER['DOCUMENT_ROOT']."/backend/settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/conn.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/functions/gameFunctions.php");

    $gameID = $_POST['gameID'];
    $userID = $_SESSION['userID'];
    $word = htmlspecialchars($_POST['word']);

    if($gameID != '' && $word != '' && isset($_SESSION)){
        if (isGameActive($gameID) && isPlayerTurn($userID, $gameID)){
            $stmt = $conn->prepare("SELECT * FROM game_partecipation WHERE ID_game = ? AND ID_user = ?");
            $stmt->bind_param("ss", $gameID, $userID);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            
            //This ensures that a user will only be able to input a single word during their turn
            $conn->begin_transaction();

            $stmt = $conn->prepare("INSERT INTO guess (word, ID_game_partecipation) VALUES (?,?)");
            $stmt->bind_param("ss", $word, $row['ID_game_partecipation']);
            $stmt->execute();

            $stmt = $conn->prepare("UPDATE game SET turnPlayerID = NULL WHERE ID_game = ?");    
            $stmt->bind_param("s", $gameID);
            $stmt->execute();

            $conn->commit();
        }
    }
?>