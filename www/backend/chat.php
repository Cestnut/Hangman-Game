<?php
require_once($_SERVER['DOCUMENT_ROOT']."/backend/settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/conn.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/functions/roomFunctions.php");


$roomID = $_GET['roomID'];
if (isUserInRoom($_SESSION['userID'], $roomID)){

    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    
    define('HEARTBEAT_PERIOD', 0); //after how many seconds send an heartbeat signal
    $heartbeatTime = time();
    $lastMessageTimestamp = 0;
    $sql = "SELECT user.username, message.message, message.timestamp FROM message
            JOIN user ON message.ID_USER = user.ID_user
            WHERE message.timestamp > ? AND message.ID_room = ?
            ORDER BY message.timestamp ASC";
    $stmt = $conn->prepare($sql);

    while(1){
        if (connection_aborted())
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
        }
        ob_flush(); //Necessary to send data to the php buffer ready to send. If this wasn't used, no data would arrive to client until the script stopped
        flush(); //Flushes content from php buffer to client
        sleep(1);
    }
}

?>