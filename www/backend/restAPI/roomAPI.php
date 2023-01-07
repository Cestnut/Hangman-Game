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

$response = array();

if($input['name'] == ""){ //checks if body wasn't empty
    
    $response['status'] = "not_valid";
    $response['payload'] = "Can't be null";
    echo json_encode($response);
}
else{ 
    $input = array_map(function($val) { return htmlspecialchars($val); }, $input); //maps function to every entry and returns new array
    try{
        // create stored queries based on HTTP method
        switch ($method) {
        case 'GET':
            if($ID != ""){ //Checks if a specific ID was requested
                $stmt = $conn->prepare("SELECT * FROM room WHERE ID_room = ?");
                $stmt->bind_param("s", $ID);
            }
            else{
                $stmt = $conn->prepare("SELECT * FROM room");
            }
            break;

        case 'PUT':
            $stmt1 = $conn->prepare("SELECT ID_host FROM room WHERE ID_room = ?");
            $stmt1->bind_param("s", $ID);
            $stmt1->execute();
            $row = $stmt1->get_result()->fetch_assoc();

            //Only the owner of a room can change its name
            if($_SESSION['userID'] == $row['ID_host']){         
                $stmt = $conn->prepare("UPDATE room SET name = ? WHERE ID_room = ?");
                $stmt->bind_param("ss", $input['name'], $ID);
            }
            break;

        case 'POST':
            //only a logged user can create a room
            if(isset($_SESSION['userID'])){
                $stmt = $conn->prepare("INSERT INTO room (name, ID_host) VALUES (?, ?)");
                $stmt->bind_param("ss", $input['name'], $_SESSION['userID']);
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

        echo json_encode($response);
        }
    catch(Exception $e){
        if($conn->errno === ER_DUP_KEY){
            $response['status'] = "not_valid";
            $response['payload'] = "Already exists";
        }
        else{
            $response['status'] = "error";
        }
        echo json_encode($response);
    }
}
?>
