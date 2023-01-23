window.onload = init
addEventListener('beforeunload', leave);
function init(){
    $("#sendMessage").on("click", sendMessage);
    $("#startGame").on("click", startGame);
    $("#leaveRoom").on("click", leave);
    $("#sendGuess").on("click", sendGuess);
    showGameForm();
}

const urlParams = new URLSearchParams(window.location.search);
const roomID = urlParams.get('roomID');

const roomStatusSource = new EventSource('../backend/roomStatus.php?roomID='+roomID);
roomStatusSource.addEventListener("newName", function(e) {
            $("#roomName").html(e.data);
       }); 

roomStatusSource.addEventListener("closed", function(e) {
            chatSource.close()    
            roomStatusSource.close()
            window.location = "../html/roomList.html";
        });

roomStatusSource.addEventListener("start", function(e) {
            initGame(e.data);
        });

const chatSource = new EventSource('../backend/chat.php?roomID='+roomID);
chatSource.addEventListener("message", function(e) {
            writeChatMessage(JSON.parse(e.data));
       });

var gameSource = 0;
var gameID = 0;

function writeChatMessage(messageJson){
    var container = document.getElementById("messages");
    var message = document.createElement("div");
    message.classList.add("message");

    var sender = document.createElement("span");
    sender.classList.add("username");
    sender.innerHTML = messageJson.user;
    message.appendChild(sender);

    var text = document.createElement("span");
    text.classList.add("content")
    text.innerHTML = messageJson.message;
    message.appendChild(text);
    
    container.appendChild(message);
}

function sendMessage(){
    var message = $("#newMessage").text();
    $("#newMessage").html("");
    $.ajax({
        url: "../backend/sendMessage.php",
        method: "post",
        data:{
            message:message,
            roomID:roomID
        }})
}

function leave(){
    $.ajax({
        url: "../backend/leaveRoom.php",
        method: "post",
        data:{
            roomID:roomID
        }
        }).done(function(){
            window.location = "../html/roomList.html";
        })
    }

function startGame(){
    $("#error").html("");
    let maxLives = $("#maxLives").val(); 
    let maxTime = $("#maxTime").val(); 
    $.ajax({
        url: "../backend/startGame.php",
        method: "post",
        data:{
            maxLives:maxLives,
            maxTime:maxTime,
            roomID:roomID
        }
      }).done(function(message) {

            message = JSON.parse(message);
            if(message.status == "success"){
            }
            else if(message.status == "not_valid"){
                $("#error").html(message.payload);
            }

        });
}

function showGameForm(){    
    $.ajax({
        url: "../backend/isOwner.php",
        method: "post",
        data:{
            roomID:roomID,
        }
      }).done(function(message) {
            if(message == 1){
                $("#startGameForm").show();
            }
            else{
                $("#startGameForm").hide();
            }
        });
}

function sendGuess(){
    
    let word = $("#newGuess").val(); 
    $("#newGuess").val('');

    if(word.trim() != ""){
        $.ajax({
            url: "../backend/guessWord.php",
            method: "post",
            data:{
                word:word,
                gameID:gameID
            }
        }).done(function(message) {
            });
    }
}

function initGame(ID){
    var maxTime = 0;
    $("#roomContainer").hide();
    $("#gameContainer").show();

    gameID = ID;
    gameSource = new EventSource('../backend/gameStatus.php?gameID='+gameID); 

    gameSource.addEventListener("time", function(e) {
        maxTime = e.data;
    }); 
    
    gameSource.addEventListener("letters", function(e) {
        var letters = JSON.parse(e.data);
        
        for (var key in letters) {
            var letterDiv = document.getElementById("letter"+key);
            letterDiv.classList.add("guessed-letter");
            letterDiv.innerHTML = letters[key].toUpperCase();
        }
    });

    gameSource.addEventListener("wordLenght", function(e) {
        var container = document.getElementById("letters");
        var lenght = parseInt(e.data);
        for (let i = 0; i < lenght; i++) {
                var entry = document.createElement("div");
                entry.setAttribute("id", "letter"+i);
                entry.classList.add("letter");
                entry.innerHTML = "<br>";
                container.appendChild(entry);
            }
    });
    
    gameSource.addEventListener("lives", function(e) {
        $("#lives").html("Vite: " + e.data);
         //La gestione del reset del timer è inserita qui perché il cambio vite coincide con la fine del turno. L'evento "turn" non accade se un utente gioca da solo
         clearInterval(timerID);
         timerID = startTimer();
    });
    
    var timerID = 0;
    gameSource.addEventListener("turn", function(e) {
        $("#guessForm").hide()
        $("#time").hide()
        var payload = JSON.parse(e.data);
        $("#turn").html("Turno di " + payload.username);

        if(payload.current == true){
            $("#guessForm").show();
            $("#time").show();
        }
    });
    
    gameSource.addEventListener("yourTurn", function(e) {
    });
    
    gameSource.addEventListener("finish", function(e) {
        clearInterval(timerID);
        let result = JSON.parse(e.data)
        if(result.status == "victory"){
            //victory
            $("#finishMessage").html("Avete vinto! La parola era '" + result.word +"'");
            for (let i = 0; i < result.word.length; i++) {
                var letterDiv = document.getElementById("letter"+i);
                letterDiv.classList.add("guessed-letter");
                letterDiv.innerHTML = result.word[i].toUpperCase();
            }
            
        }
        else{
            $("#finishMessage").html("Avete perso :( La parola era '" + result.word + "'");
        }
        setTimeout(function(){
            $("#finishMessage").html("");
            $("#guesses").html("");
            $("#letters").html("");
            
            $("#roomContainer").show();
            $("#gameContainer").hide();
            gameSource.close();
            gameSource = 0;
        }, 5000);
    });
    

    gameSource.addEventListener("guess", function(e) {
        entryJson = JSON.parse(e.data);
        var container = document.getElementById("guesses");
        var entry = document.createElement("div");
        entry.classList.add("message");

        var user = document.createElement("span");
        user.classList.add("username");
        user.innerHTML = entryJson.user;
        entry.appendChild(user);

        var text = document.createElement("span");
        text.classList.add("content")
        text.innerHTML = entryJson.word;
        entry.appendChild(text);
        
        container.appendChild(entry);
    });    

    function startTimer(){
        var i = maxTime;
        var timerID = setInterval(function() {
            $("#time").html(i-- + " secondi rimanenti");
        }, 1000);
        return timerID;
      }
    }