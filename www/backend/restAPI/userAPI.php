<?php

require_once($_SERVER['DOCUMENT_ROOT']."/backend/settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/conn.php");

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
    $username = str_replace(" ","", $input['username']); //removes spaces from username
}

$response = array();
if($method == "POST"){
    $password = $input['password'];
    $confirmPassword = $input['confirmPassword'];
    if(!(isset($username) && $username != "")){
        $response['status'] = "not_valid";
        $response['payload'][] = "You have to provide username";
    }
    if(!(isset($password) && $password != "")){
        $response['status'] = "not_valid";
        $response['payload'][] = "You have to provide password";
    }
    if(!(isset($confirmPassword) && $confirmPassword != "")){
        $response['status'] = "not_valid";
        $response['payload'][] = "You have to provide confirmPassword";
    }
    if($password != $confirmPassword){
        $response['status'] = "not_valid";
        $response['payload'][] = "password and confirmPassword don't match";
    }
}
if($method=="PUT"){
    if(!(isset($username) && $username != "")){
        $response['status'] = "not_valid";
        $response['payload'] = "You have to provide username";
    }
    if($ID == ""){
        $response['status'] = "not_valid";
        $response['payload'] = "You have to provide an ID";
    }
}
if(!isset($response['status'])){
    try{
        // create stored queries based on HTTP method
        switch ($method) {
        case 'GET':
            if(isset($ID)){ //Checks if a specific ID was requested
                $stmt = $conn->prepare("SELECT * FROM user WHERE ID_user = ?");
                $stmt->bind_param("s", $ID);
            }
            else{
                $stmt = $conn->prepare("SELECT * FROM user");
            }
            break;

        case 'PUT':
            //Only the user can change his own name
            if($_SESSION['userID'] == $ID){         
                $stmt = $conn->prepare("UPDATE user SET username = ? WHERE ID_user = ?");
                $stmt->bind_param("ss", $username, $ID);
            }
            break;

        case 'POST':
            //hashes the password to insert in the db
            $password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO user (username, password, role) VALUES (?,?, 'user')");
            $stmt->bind_param("ss", $username, $password);
            break;

        case 'DELETE':
            //only a user can delete his own account
            if($_SESSION['userID'] == $ID){
                $stmt = $conn->prepare("DELETE FROM user WHERE ID_user = ?");
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
                $users = array();
                while ($row = $result->fetch_assoc()) {
                    $users[] = array("ID_user" => $row['ID_user'], "username" => $row['username']);
                }
                $response['payload'] = $users;
            } 
            elseif ($method == 'POST'){
                $user_ID = $conn->insert_id;
                $_SESSION['userID'] = $user_ID;
                $_SESSION['role'] = "user";
                $_SESSION['username'] = $username;
                //returns the ID of the user just created
                $response['payload'] = $user_ID;
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
