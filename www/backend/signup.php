<?php
    require_once($_SERVER['DOCUMENT_ROOT']."/backend/settings.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/backend/conn.php");
    
    if(isset($_POST)){
    try{
        
        $username = $_POST["username"];
        $password = $_POST["password"];
        $confirmPassword = $_POST["confirmPassword"];
    
        $data = array("success" => false);

        $error = array("username"=>[], "password" => []);
        #Checks if mandatory fields are present
        if (!$username)
            $error['username'][] = "username richiesta";       
        if (!$password)
            $error['password'][] = "Password richiesta";
        if (!$confirmPassword)
            $error['password'][] = "Devi confermare la password";

        if (!($confirmPassword === $password))
            $error['password'][] = "Conferma password fallita";

        $data["errors"] = $error;
        if (array_filter($error)){
            echo json_encode($data);
        }
        else{
            $password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO user (username, password, role) VALUES (?,?, 'user')");
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $stmt->store_result();
            
            $user_id = $conn->insert_id;
            $_SESSION['userID'] = $user_id;
            $_SESSION['role'] = "user";
            $_SESSION['username'] = $username;
            $data["success"] = true;

            echo json_encode($data);
        }
    $conn->close();  
    }
    catch(Exception $e){
        if($conn->errno === ER_DUP_KEY){
            foreach ($conn->error_list as $element) {
                $words = explode(' ', $element['error']);
                $key = array_pop($words);
                $key = trim($key, "'");
                $data['errors'][$key][] = "Esiste già un account con questo username";
                }
            echo json_encode($data);
    }
        else echo "Errore durante la connessione al database\n";
    }
}
?>