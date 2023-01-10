<?php

require_once($_SERVER['DOCUMENT_ROOT']."/backend/settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/conn.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/functions/roomFunctions.php");

$room_ID = $_POST['roomID'];
if($room_ID && $_SESSION['userID']){

$response = array();
try{
    if(isUserInRoom($_SESSION['userID'], $room_ID)){ //user already in room is treated as success
        $response['status'] = "success";
    }
    elseif(isRoomOpen($room_ID)){
        joinRoom($room_ID, $_SESSION['userID']);
        $response['status'] = "success";
    }
    else{
        $response['status'] = "closed";
    }
}
catch(Exception $e){
    $response['status'] = "error";
}    

echo json_encode($response);
}
?>