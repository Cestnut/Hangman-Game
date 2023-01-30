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
    $word = trim($input['word']);
}

$response = array();
if($method == "POST" || $method == "PUT"){
    
    if(!(isset($word) && $word != "")){
        $response['status'] = "not_valid";
        $response['payload'] = "You have to provide a word";
    }
    else if(strlen($word) > 15){
        $response['status'] = "not_valid";
        $response['payload'] = "The word can have max 15 characters";
    }
}

//only an admin can call any of the methods on word
if(!isset($response['status']) && $_SESSION['role'] == "admin"){
    try{
        // create stored queries based on HTTP method
        switch ($method) {
        case 'GET':
            if(isset($ID)){ //Checks if a specific ID was requested
                $stmt = $conn->prepare("SELECT * FROM word WHERE ID_word = ?");
                $stmt->bind_param("s", $ID);
            }
            else{
                $stmt = $conn->prepare("SELECT * FROM word");
            }
            break;

        case 'PUT':      
                $stmt = $conn->prepare("UPDATE word SET word = ? WHERE ID_word = ?");
                $stmt->bind_param("ss", $word, $ID);
            break;

        case 'POST':
                $stmt = $conn->prepare("INSERT INTO word(word) values (?)");
                $stmt->bind_param("s", $word);
            break;

        case 'DELETE':
                $stmt = $conn->prepare("DELETE FROM word WHERE ID_word = ?");
                $stmt->bind_param("s", $ID);
            break;
        }

        //Checks if stmt exist. It may not exist if requesting user didnt have enough permissions
            $response['status'] = "success";
            //execute query and get result set
            $stmt->execute();
            $result = $stmt->get_result();
            
            // print results, insert id or affected row count
            if ($method == 'GET') {
                $words = array();
                while ($row = $result->fetch_assoc()) {
                    $words[] = array("ID_word" => $row['ID_word'], "word" => $row['word']);
                }
                $response['payload'] = $words;
            } 
            elseif ($method == 'POST'){
                $word_ID = $conn->insert_id;
                //returns the ID of the word just created
                $response['payload'] = $word_ID;
            } 
            else { //For both UPDATE and DELETE methods
                $response['payload'] = $stmt->affected_rows;
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
$response['status'] = "denied";

echo json_encode($response);

?>
