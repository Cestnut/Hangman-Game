window.onload = init

function init(){
    $("#sendMessage").on("click", sendMessage);
    $("#startGame").on("click", startGame);
    $("#leaveRoom").on("click", leave);
    showGameForm();
}

const urlParams = new URLSearchParams(window.location.search);
const roomID = urlParams.get('roomID')

const roomStatusSource = new EventSource('../backend/roomStatus.php?roomID='+roomID);
roomStatusSource.addEventListener("newName", function(e) {
            console.log(e.data);
            $("#roomName").html(e.data);
       });       

roomStatusSource.addEventListener("closed", function(e) {
            chatSource.close()    
            roomStatusSource.close()
            console.log("room closed");
            window.location = "../html/roomList.html";
        });

roomStatusSource.addEventListener("start", function(e) {
            console.log("game started");
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
    
    var sender = document.createElement("span");
    sender.innerHTML = messageJson.user;
    message.appendChild(sender);

    var text = document.createElement("span");
    text.innerHTML = messageJson.message;
    message.appendChild(text);
    
    container.appendChild(message);
}

function sendMessage(){
    var message = $("#newMessage").html();
    $("#newMessage").html("");
    
    console.log(message);
    $.ajax({
        url: "../backend/sendMessage.php",
        method: "post",
        data:{
            message:message,
            roomID:roomID
        }}).done(function(result) {
            console.log(result);
        });
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

    let form = $("#startGameForm");
    let maxLives = form.find("[name='maxLives']").val(); 
    let maxTime = form.find("[name='maxTime']").val(); 
    $.ajax({
        url: "../backend/startGame.php",
        method: "post",
        data:{
            maxLives:maxLives,
            maxTime:maxTime,
            roomID:roomID
        }
      }).done(function(message) {
            if(message == "success"){
                console.log("Partita iniziata");
            }
            else if(message == "wrong_fields"){
                console.log("Campi errati");
            }
            else if(message == "error"){
                console.log("Server Error");
            }
            console.log(message);

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
                var container = document.getElementById("startGameForm");
                container.removeAttribute("hidden");
            }
        });
}

function sendGuess(){
    
    let form = $("#guessForm");
    let word = form.find("[name='guess']").val(); 
    $.ajax({
        url: "../backend/guessWord.php",
        method: "post",
        data:{
            word:word,
            gameID:gameID
        }
      }).done(function(message) {
            document.getElementById("guessForm").setAttribute("hidden", true);
            console.log(message);
        });
}

function initGame(ID){

    $("#sendGuess").on("click", sendGuess);

    document.getElementById("roomContainer").setAttribute("hidden", true);
    document.getElementById("gameContainer").removeAttribute("hidden");

    gameID = ID;
    gameSource = new EventSource('../backend/gameStatus.php?gameID='+gameID); 

    gameSource.addEventListener("time", function(e) {
        $("#time").html(e.data);
         console.log(e.data);
    }); 
    
    gameSource.addEventListener("letters", function(e) {
        var letters = JSON.parse(e.data);
        
        for (var key in letters) {
            var letterDiv = document.getElementById("letter"+key);
            letterDiv.innerHTML = letters[key];
        }
        console.log(e.data);
    });

    gameSource.addEventListener("wordLenght", function(e) {
        var container = document.getElementById("letters");
        var lenght = parseInt(e.data);
        for (let i = 0; i < lenght; i++) {
                var entry = document.createElement("div");
                entry.setAttribute("id", "letter"+i);
                entry.innerHTML = "<br>";
                container.appendChild(entry);
            }
        console.log(e.data);
    });
    
    gameSource.addEventListener("lives", function(e) {
        $("#lives").html(e.data);
         console.log(e.data);
    });
    
    gameSource.addEventListener("turn", function(e) {
        $("#turn").html("Turno di " + e.data);
         console.log(e.data);
    });
    
    gameSource.addEventListener("yourTurn", function(e) {
        document.getElementById("guessForm").removeAttribute("hidden");
        console.log(e.data);
    });
    
    gameSource.addEventListener("finish", function(e) {
        document.getElementById("guesses").innerHTML = "";
        document.getElementById("letters").innerHTML = "";
        document.getElementById("gameContainer").setAttribute("hidden", true);
        document.getElementById("roomContainer").removeAttribute("hidden");
        gameSource.close();
        gameSource = 0;
        console.log(e.data);
    });
    
    gameSource.addEventListener("guess", function(e) {
        console.log("guess");
        entryJson = JSON.parse(e.data);
        var container = document.getElementById("guesses");
        var entry = document.createElement("div");
    
        var user = document.createElement("span");
        user.innerHTML = entryJson.user;
        entry.appendChild(user);

        var text = document.createElement("span");
        text.innerHTML = entryJson.word;
        entry.appendChild(text);
        
        container.appendChild(entry);
        console.log(e.data);
    });    
}