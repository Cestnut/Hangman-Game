<?php
require_once($_SERVER['DOCUMENT_ROOT']."/backend/settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/conn.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/functions/roomFunctions.php");


$roomID = $_GET['roomID'];
$userID = $_SESSION['userID'];
session_write_close(); //NECESSARY so other scripts using this same sessione can be executed and don't have to wait until this closes.

if ($lastMessageTimestamp = isUserInRoom($userID, $roomID)){
    
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');

    define('HEARTBEAT_PERIOD', 600); //after how many seconds send an heartbeat signal
    $heartbeatTime = time();

    $sql = "SELECT user.username, message.message, message.timestamp FROM message
            JOIN user ON message.ID_USER = user.ID_user
            WHERE message.timestamp > ? AND message.ID_room = ?
            ORDER BY message.timestamp ASC";
    $stmt = $conn->prepare($sql);

    while(1){
        if (!(isRoomOpen($roomID)) || connection_aborted() || isUserInRoom($userID, $roomID)) //returns true only if room closes or server knows the client disconnected. Namely after a message was sent and the client didn't answer.
            break;

        $stmt->bind_param("ss", $lastMessageTimestamp, $roomID);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
                $message = array("user" => $row['username'], "message" => $row['message']);
                echo "data:".json_encode($message)."\n\n";
                $lastMessageTimestamp = $row['timestamp'];
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