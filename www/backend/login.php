<?php

require_once($_SERVER['DOCUMENT_ROOT']."/backend/settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/conn.php");

if(isset($_POST)){
    try{
        $username = $_POST["username"];
        $password1 = $_POST["password"];
        
        $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        
        $row = $stmt->get_result()->fetch_assoc();

        $password2 = $row['password'];

        if(password_verify($password1, $password2)){
            $status = "success";
            $_SESSION['userID'] = $row['ID_user'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['username'] = $row['username'];
        }
        else{
            $status = "wrong_credentials";
        }
    }
    catch(Exception $e){
        $status = "error";
    }

    echo $status;
}

?>