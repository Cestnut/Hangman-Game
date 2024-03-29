<?php
require_once($_SERVER['DOCUMENT_ROOT']."/backend/settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/conn.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/functions/gameFunctions.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/functions/roomFunctions.php");



$gameID = $_GET['gameID'];
$userID = $_SESSION['userID'];
session_write_close(); //NECESSARY so other scripts using this same sessione can be executed and don't have to wait until this closes.
if (isUserInGame($userID, $gameID)){
    
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    $heartbeatTime = time();

    //tells the client how long a turn lasts
    $stmt = $conn->prepare("SELECT ID_room, max_time, word.word FROM game 
                            JOIN word ON word.ID_word = game.ID_word 
                            WHERE ID_game = ?");
    $stmt->bind_param("s", $gameID);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    echo "event: time\n";
    echo "data: ".$row['max_time']."\n\n";

    $word = $row['word'];
    echo "event: wordLenght\n";
    echo "data: ".strlen($word)."\n\n";

    $roomID = $row['ID_room'];
    $foundLetters = array();
    $lastGuessID = 0;
    $maxLives = -1;
    $turnUsername = 0;
    while(1){
        if (!(isRoomOpen($roomID)) || !(isUserInRoom($userID, $roomID))){ //returns true only if room closes or server knows the client disconnected. Namely after a message was sent and the client didn't answer.
            leaveGame($gameID, $userID);
            break;
        }
        $stmt = $conn->prepare("SELECT * FROM game JOIN user ON game.turnPlayerID = user.ID_user WHERE ID_game = ?");
        $stmt->bind_param("s", $gameID);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if($row['max_lives'] != $maxLives){
            $maxLives = $row['max_lives'];
            echo "event: lives\n";
            echo "data: ".$maxLives."\n\n";
        }

        if($row['username'] != $turnUsername && $row['username'] != null){
            $turnUsername = $row['username'];
            if($row['turnPlayerID'] == $userID){
                $current = true;
            }
            else{
                $current = false;
            }
            $turnPayload = json_encode(array("current" => $current, "username" => $turnUsername));
            echo "event: turn\n";
            echo "data: ".$turnPayload."\n\n";
        }

        if(!isGameActive($gameID)){
            //Rifacendo la query perché essendo finita la partita potrebbe non esserci più un giocatore di turno
            $stmt = $conn->prepare("SELECT * FROM game WHERE ID_game = ?");
            $stmt->bind_param("s", $gameID);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();

            $result = array("word" => $word);
            if($row['wordMask'] == (pow(2, strlen($word)+1) - 1)){ //controlla che la maschera abbia tutti i bit a 1
                $result['status'] = "victory";
            }
            else{
                $result['status'] = "defeat";
            }
            echo "event: finish\n";
            echo "data: ".json_encode($result)."\n\n";
            leaveGame($gameID, $userID);
            break;
        }

        $stringWordMask = ltrim(decbin($row['wordMask']), "0"); //removes all trailing zeroes generated by decbin
        $stringWordMask = substr($stringWordMask, 1); //The first character is always a 1 to always have a full lenght mask
        $newFoundLetters = array();
        for ($index = 0; $index < strlen($stringWordMask); $index++) {
            if($stringWordMask[$index] == "1"){
                $newFoundLetters[$index] = $word[$index];
            }
        }
        if($foundLetters != $newFoundLetters){
            $foundLetters = $newFoundLetters;
            echo "event: letters\n";
            echo "data: ".json_encode($newFoundLetters)."\n\n";
        }

        $stmt = $conn->prepare("SELECT guess.ID_guess, guess.word, user.username FROM guess 
                                JOIN game_partecipation ON guess.ID_game_partecipation = game_partecipation.ID_game_partecipation 
                                JOIN user ON game_partecipation.ID_user = user.ID_user 
                                WHERE ID_guess > ? AND ID_game = ? ORDER BY ID_guess ASC");

        $stmt->bind_param("ss", $lastGuessID, $gameID);
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            $data = array("user" => $row['username'], "word" => $row['word']);
            echo "event: guess\n";
            echo "data: ".json_encode($data)."\n\n";
            
            $lastGuessID = $row['ID_guess'];
        }

        ob_flush(); //Necessary to send data to the php buffer ready to send. If this wasn't used, no data would arrive to client until the script stopped
        flush(); //Flushes content from php buffer to client
        sleep(1);
    }
}

?>