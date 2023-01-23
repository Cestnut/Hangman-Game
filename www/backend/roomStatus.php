<?php
require_once($_SERVER['DOCUMENT_ROOT']."/backend/settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/conn.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/functions/roomFunctions.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/functions/gameFunctions.php");


$roomID = $_GET['roomID'];
$userID = $_SESSION['userID'];
session_write_close(); //NECESSARY so other scripts using this same sessione can be executed and don't have to wait until this closes.
ignore_user_abort(true); //NECESSARY so in case the user is inactive, the destructor functions will be executed

if (isUserInRoom($userID, $roomID)){
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    
    $roomName = "";
    $startEventSent = 0;
    $loop = true;
    while($loop){
        if(checkUserDisconnection($roomID, $userID)){
            $loop = false;
        }
        startEvent($gameID, $roomID, $userID);
        newNameEvent($roomID);
        ob_flush(); //Necessary to send data to the php buffer ready to send. If this wasn't used, no data would arrive to client until the script stopped
        flush(); //Flushes content from php buffer to client
        sleep(1);
    }
}

function checkUserDisconnection($roomID, $userID){

    if (!(isRoomOpen($roomID)) || !(isUserInRoom($userID, $roomID))){
        echo "event: closed\n"; //is needed only in case the room was closed
        echo "data:\n\n";
        leaveRoom($roomID, $userID);
        return true;
    }
}

function startEvent($gameID, $roomID, $userID){
    global $startEventSent;

    if($gameID = isRoomActive($roomID)){
        if(!$startEventSent && isUserInGame($userID, $gameID)){
            $startEventSent = 1;
            echo "event: start\n";
            echo "data:".$gameID."\n\n";
        }
    }
    else{
        $startEventSent = 0;
    }
}

function newNameEvent($roomID){
        global $conn;
        global $roomName;

        $stmt = $conn->prepare("SELECT name FROM room WHERE ID_room = ?");
        $stmt->bind_param("s", $roomID);
        $stmt->execute();
    
        $row = $stmt->get_result()->fetch_assoc();
        if($row['name'] != $roomName){
            $roomName = $row['name'];
            echo "event: newName\n";
            echo 'data: ' .$roomName. "\n\n";
        }
}
?>