<?php
require_once($_SERVER['DOCUMENT_ROOT']."/backend/settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/conn.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/functions/roomFunctions.php");
require_once($_SERVER['DOCUMENT_ROOT']."/backend/functions/gameFunctions.php");



$maxLives = $_POST['maxLives'];
$maxTime = $_POST['maxTime'];
$roomID = $_POST['roomID'];
$userID = $_SESSION['userID'];

session_write_close();

//input validation
$result = array("status" => "success");
if(isUserRoomHost($userID, $roomID) && !isRoomActive($roomID)){
    if(is_numeric($maxLives) && is_numeric($maxTime)){
        if ((int)$maxLives < 1 ||  (int)$maxLives > 999){
            $result["status"] = "not_valid";
            $result["payload"] = "Lives must be between 1 and 999";
        }
        if ((int)$maxTime < 1 ||  (int)$maxTime > 120){
            $result["status"] = "not_valid";
            $result["payload"] = "Turn must be between 1 and 120 seconds";
        }
    }
    else{
        $result["status"] = "not_valid";
        $result["payload"] = "The input must be numbers";
    }

    header("Connection: close\r\n");
    ignore_user_abort(true);
    
    if (!ob_get_level()){ //opens a new buffer in case there are none
        ob_start();
    }
    
    echo json_encode($result);
    $size = ob_get_length(); //Returns the length of the output buffer contents, what was written and not sent to the client yet
    header("Content-Length: $size");
    ob_flush();
    flush();

    echo json_encode($result);
    if($result['status'] == "success"){
        try{
            $wordArray = randomWord(); //First element is the ID, the second is the actual word
            $wordID = $wordArray[0]; 
            $word = strtolower($wordArray[1]);
            $resultMask = pow(2, strlen($word)+1) - 1; //the mask where all bits are set to one
            $gameID = createGame($maxLives, $maxTime, $wordID, $roomID);
            connectAllUsers($gameID, $roomID);
            $gameQueue = new gameQueue($gameID, $conn);
            print_r($gameQueue->queue);
            $turnPlayer = $gameQueue->next();
            $stmt = $conn->prepare("UPDATE game SET turnPlayerID = ? WHERE ID_game = ?");    
            $stmt->bind_param("ss", $turnPlayer, $gameID);
            $stmt->execute();

            $time = time();
            $lastGuessID = 0;
            $oldBitMask = 0;
            while(true){
                $stmt = $conn->prepare("SELECT * FROM guess 
                                        JOIN game_partecipation ON guess.ID_game_partecipation = game_partecipation.ID_game_partecipation
                                        WHERE game_partecipation.ID_game = ? AND game_partecipation.ID_user = ? ORDER BY ID_guess DESC LIMIT 1");
                $stmt->bind_param("ss", $gameID, $turnPlayer);
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                if (isset($row['ID_guess']) && $lastGuessID < $row['ID_guess']){
                    //turno finito
                    echo "wrong word ".$gameID." ".$turnPlayer." ".$lastGuessID." ".$row['ID_guess']."\n";
                    $lastGuessID = $row['ID_guess'];
                    $guessedWord = strtolower($row['word']);
                    //
                    $bitMask = 1;
                    for ($index = 0; $index < strlen($word); $index++) {
                        $bitMask *= 2; //A ogni iterazione la bit mask viene shiftata di un posto a sinistra
                        if(isset($guessedWord[$index]) && $word[$index] == $guessedWord[$index]){
                            $bitMask += 1; //La lettera considerata in questo momento è corretta quindi si setta il bit a 1
                        }
                        //echo "\n".$index." ".$word[$index]." ".$guessedWord[$index]." ".$bitMask."\n"; 
                    }
                    $oldBitMask = $oldBitMask | $bitMask; //viene unita la bitmask appena trovata con quella calcolata fino ad ora
                    
                    $stmt = $conn->prepare("UPDATE game SET wordMask = ? WHERE ID_game = ?");    
                    $stmt->bind_param("ss", $oldBitMask, $gameID);
                    $stmt->execute();

                    if($oldBitMask == $resultMask){
                        endGame($gameID);
                        break;
                    }
                    if(endTurn($gameQueue, $gameID)){
                        endGame($gameID);
                        break;
                    }
                }
                else{
                    $sql = "SELECT * FROM game_partecipation WHERE ID_game = ? AND ID_user = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $gameID, $turnPlayer);
                    $stmt->execute();
                    $stmt->store_result();
                    if ($stmt->num_rows == 0){
                        echo "tunr player left\n";
                        if(endTurn($gameQueue, $gameID)){
                            endGame($gameID);
                            break;
                        }
                    }
                }

                if(time() > $maxTime + $time){
                    echo "time up\n";
                    if(endTurn($gameQueue, $gameID)){
                        endGame($gameID);
                        break;
                    }
                }
                sleep(0.5);
            }
        }
        catch (xception $e){
            endGame($gameID);
        }
    }
}

//return True if 0 lives have been reached
function endTurn($gameQueue, $gameID){
    global $conn;
    global $time;
    global $maxLives;
    global $turnPlayer;
    if(--$maxLives == 0){
        $stmt = $conn->prepare("UPDATE game SET max_lives = 0 WHERE ID_game = ?");
        $stmt->bind_param("s", $gameID);   
        $stmt->execute();
        return True;
    }
    else{
        $turnPlayer = $gameQueue->next();

        $stmt = $conn->prepare("UPDATE game SET turnPlayerID = ?, max_lives = ? WHERE ID_game = ?");    
        $stmt->bind_param("sss", $turnPlayer, $maxLives, $gameID);
        $stmt->execute();

        $time = time();
        return False;
    }
}

class gameQueue {
    public $queue = array();
    public $currentTurn = 0;
    public $size = 0;
    public $gameID;
    
    // Methods
    public function __construct($gameID, $conn){
        $this->gameID = $gameID;
        $stmt = $conn->prepare("SELECT * FROM game_partecipation WHERE ID_game = ? ORDER BY RAND()");
        $stmt->bind_param("s", $gameID);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $this->push($row["ID_user"]);
        }
    }

    public function next(){
        $nextUser = $this->queue[($this->currentTurn + 1) % $this->size];
        //itera eliminando tutti i giocatori non più connessi, finché non ne trova uno connesso.
        while(!isUserInGame($nextUser, $this->gameID)){
          $this->removeElement($this->currentTurn);
          echo $this->size."\n";
          $nextUser = $this->queue[($this->currentTurn + 1) % $this->size];
        }
        $this->currentTurn++;
        echo $nextUser."\n";
        return $nextUser;
    }

    private function push($elem) {
      $this->queue[] = $elem;
      echo $this->size."\n";
      $this->size++;
    }

    private function removeElement($key){
        unset($this->queue[$key]);
        $this->queue = array_values($this->queue);
        $this->size--;
    }
  }

?>