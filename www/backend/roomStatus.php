<?php
require_once($_SERVER['DOCUMENT_ROOT']."/backend/settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/conn.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/functions/roomFunctions.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/functions/gameFunctions.php");


$roomID = $_GET['roomID'];
$userID = $_SESSION['userID'];
session_write_close(); //NECESSARY so other scripts using this same sessione can be executed and don't have to wait until this closes.

if (isUserInRoom($userID, $roomID)){
    
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    
    define('HEARTBEAT_PERIOD', 600); //after how many seconds send an heartbeat signal
    $heartbeatTime = time();

    $roomName = "";
    $startEventSent = 0;
    while(1){
        if (!(isRoomOpen($roomID)) || connection_aborted() || !(isUserInRoom($userID, $roomID))){ //returns true only if server knows the client disconnected. Namely after a message was sent and the client didn't answer.
            echo "event: closed\n"; //is needed only in case the room was closed
            echo "data:\n\n";
            leaveRoom($roomID, $userID);
            break; 
        }

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
        
        $stmt = $conn->prepare("SELECT name FROM room WHERE ID_room = ?");
        $stmt->bind_param("s", $roomID);
        $stmt->execute();
    
        $row = $stmt->get_result()->fetch_assoc();
        if($row['name'] != $roomName){
            $roomName = $row['name'];
            echo "event: newName\n";
            echo 'data: ' .$roomName. "\n\n";
        }

        if(time() > $heartbeatTime + HEARTBEAT_PERIOD){
            echo ": heartbeat\n\n"; //Used by the webserver to know if the connection was closed by the client, in case new messages are never found
            $heartbeatTime = time();
        }

        ob_flush(); //Necessary to send data to the php buffer ready to send. If this wasn't used, no data would arrive to client until the script stopped
        flush(); //Flushes content from php buffer to client
        sleep(1);
    }
}

?>