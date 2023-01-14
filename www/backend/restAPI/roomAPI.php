<?php

require_once($_SERVER['DOCUMENT_ROOT']."/backend/settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/conn.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/functions/roomFunctions.php");


// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];

//this check is necesary to avoid warning, annoying during test phase
if(isset($_SERVER['PATH_INFO'])){
    $request = explode('/', trim($_SERVER['PATH_INFO'],'/')); 
    $ID = $request[0]; //index 0 since it's supposed to be the only element
}
$input = json_decode(file_get_contents('php://input'), true); //reads body of request
if($input != ""){
    $input = array_map(function($val) { return htmlspecialchars($val); }, $input); //maps function to every entry and returns new array
    $roomName = trim($input['name']);
}

$response = array();
if($method == "POST" || $method == "PUT"){
    if(!(isset($roomName) && $roomName != "")){
        $response['status'] = "not_valid";
        $response['payload']= "You have to provide a name";
    }
}
if(!isset($response['status'])){
    try{
        // create stored queries based on HTTP method
        switch ($method) {
        case 'GET':

            if(isset($_GET['status'])){
                $status = $_GET['status'];
            }
            else
            {
                $status = "";
            }

            if(isset($ID)){ //Checks if a specific ID was requested
                $stmt = $conn->prepare("SELECT * FROM room WHERE ID_room = ?");
                $stmt->bind_param("s", $ID);
            }
            elseif ($status == "open") {
                $sql = "SELECT * FROM room 
                        JOIN room_partecipation 
                        ON room.ID_host = room_partecipation.ID_user 
                        AND room.ID_room = room_partecipation.ID_room";
                $stmt = $conn->prepare($sql);
            }
            else{
                $stmt = $conn->prepare("SELECT * FROM room");
            }
            break;

        case 'PUT':
            //Only the owner of a room can change its name
            if(isUserRoomHost($_SESSION['userID'], $ID)){         
                $stmt = $conn->prepare("UPDATE room SET name = ? WHERE ID_room = ?");
                $stmt->bind_param("ss", $roomName, $ID);
            }
            break;

        case 'POST':
            //only a logged user can create a room
            if(isset($_SESSION['userID'])){
                $stmt = $conn->prepare("INSERT INTO room (name, ID_host) VALUES (?, ?)");
                $stmt->bind_param("ss", $roomName, $_SESSION['userID']);
            }
            break;

        case 'DELETE':
            //only an admin can delete a room
            if($_SESSION['role'] == "admin"){
                $stmt = $conn->prepare("DELETE FROM room WHERE ID_room = ?");
                $stmt->bind_param("s", $ID);
            }
            break;
        }

        //Checks if stmt exist. It may not exist if requesting user didnt have enough permissions
        if(isset($stmt)){
            $response['status'] = "success";
            //execute query and get result set
            $stmt->execute();
            $result = $stmt->get_result();
            
            // print results, insert id or affected row count
            if ($method == 'GET') {
                $rooms = array();
                while ($row = $result->fetch_assoc()) {
                    $rooms[] = array("ID_room" => $row['ID_room'], "name" => $row['name']);
                }
                $response['payload'] = $rooms;
            } 
            elseif ($method == 'POST'){
                //connects current user to room
                $room_ID = $conn->insert_id;
                joinRoom($room_ID, $_SESSION['userID']);

                //returns the ID of the room just created
                $response['payload'] = $room_ID;
            } 
            else { //For both UPDATE and DELETE methods
                $response['payload'] = $stmt->affected_rows;
            }
            }
        else{
            $response['status'] = "denied";
        }

        }
    catch(Exception $e){
        if($conn->errno === ER_DUP_KEY){
            $response['status'] = "not_valid";
            $response['payload'] = "Already exists";
        }
        else{
            $response['status'] = "error";
        }
    }
}
echo json_encode($response);

?>
