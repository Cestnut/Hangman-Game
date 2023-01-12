<?php
require_once($_SERVER['DOCUMENT_ROOT']."/backend/settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/conn.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/functions/roomFunctions.php");

    $roomID = $_POST['roomID'];
    $userID = $_SESSION['userID'];
    $message = $_POST['message'];
    echo $roomID;
    echo $userID;
    echo $message;

    if($roomID != '' && $message != '' && isset($_SESSION)){
        if (isRoomOpen($roomID) && isUserInRoom($userID, $roomID)){
            $stmt = $conn->prepare("INSERT INTO message (message, ID_user, ID_room) VALUES (?,?,?)");
            $stmt->bind_param("sss", htmlspecialchars($message), $userID, $roomID);
            $stmt->execute();
        }
    }
?>
