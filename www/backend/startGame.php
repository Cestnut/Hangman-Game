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
        if ((int)$maxLives < 1 &&  (int)$maxLives > 999){
            $result["status"] = "not_valid";
            $result["payload"] = "Lives must be between 1 and 999";
        }
        if ((int)$maxTime < 1 &&  (int)$maxTime > 120){
            $result["status"] = "not_valid";
            $result["payload"] = "Turn must be between 1 and 120 seconds";
        }
    }
    else{
        $result["status"] = "not_valid";
        $result["payload"] = "The input must be numbers";
    }
/*
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
*/
    if($result['status'] == "success"){
        try{
            $wordArray = randomWord(); //First element is the ID, the second is the actual word
            $wordID = $wordArray[0]; 
            $word = strtolower($wordArray[1]);

            $gameID = createGame($maxLives, $maxTime, $wordID, $roomID);
            connectAllUsers($gameID, $roomID);
            $gameQueue = new gameQueue($gameID, $conn);
            print_r($gameQueue->queue);
            $firstPlayer = $gameQueue->next();
            $stmt = $conn->prepare("UPDATE game SET turnPlayerID = ? WHERE ID_game = ?");    
            $stmt->bind_param("ss", $firstPlayer, $gameID);
            $stmt->execute();

            $time = time();
            $lastGuessID = 0;
            $oldBitMask = 0;
            while(true){
                $stmt = $conn->prepare("SELECT * FROM guess ORDER BY ID_guess DESC LIMIT 1");
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                if ($lastGuessID != $row['ID_guess']){
                    //turno finito
                    
                    $lastGuessID = $row['ID_guess'];
                    $guessedWord = strtolower($row['word']);
                    $flagWin = true;
                    $bitMask = 1;
                    for ($index = 0; $index < strlen($word); $index++) {
                        $bitMask *= 2;
                        if(isset($guessedWord[$index]) && $word[$index] == $guessedWord[$index]){
                            $bitMask += 1;
                        }
                        else{
                            $flagWin = false;
                        }
                    }
                    $oldBitMask = $oldBitMask | $bitMask;
                    
                    $stmt = $conn->prepare("UPDATE game SET wordMask = ? WHERE ID_game = ?");    
                    $stmt->bind_param("ss", $oldBitMask, $gameID);
                    $stmt->execute();

                    if($flagWin){
                        endGame($gameID);
                        break;
                    }
                    if(endTurn($gameQueue, $gameID)){
                        break;
                    }
                }

                $sql = "SELECT * FROM game_partecipation
                        JOIN game ON game.turnPlayerID = game_partecipation.ID_user
                        WHERE game.ID_game = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $gameID);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows == 0){
                    if(endTurn($gameQueue, $gameID)){
                        break;
                    }
                }

                if($time + time() > $maxTime){
                    if(endTurn($gameQueue, $gameID)){
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
    if(--$maxLives == 0){
        return True;
    }
    else{
        $nextPlayer = $gameQueue->next();

        $stmt = $conn->prepare("UPDATE game SET turnPlayerID = ?, max_lives = ? WHERE ID_game = ?");    
        $stmt->bind_param("sss", $nextPlayer, $maxLives, $gameID);
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
        $nextUser = $this->queue[$this->currentTurn];
  
        while(!isUserInGame($nextUser, $this->gameID)){
          $this->removeElement($this->currentTurn);
          $nextUser = $this->queue[$this->currentTurn];
        }
        return $nextUser;
        $this->currentTurn = ($this->currentTurn + 1) % $this->size;
    }

    private function push($elem) {
      $this->queue[] = $elem;
      $this->size++;
    }

    private function removeElement($key){
        unset($this->queue[$key]);
        $this->queue = array_values($this->queue);
        $this->size--;
    }
  }

?>