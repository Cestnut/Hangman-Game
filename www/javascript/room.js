window.onload = init

function init(){
    $("#sendMessage").on("click", sendMessage);
    $("#startGame").on("click", startGame);
    $("#leaveRoom").on("click", leave);
    $("#sendGuess").on("click", sendGuess);
    showGameForm();

    //thilin serve per ascoltare la tastiera, il tasto invio per la chat
    document.getElementById("newMessage").addEventListener("keyup", function(event) {
        event.preventDefault();
        if (event.keyCode === 13) {
          document.getElementById("sendMessage").click();
        }
      });

    //thilin serve per ascoltare la tastiera, il tasto invio per la guessbox
      document.getElementById("newGuess").addEventListener("keyup", function(event) {
        event.preventDefault();
        if (event.keyCode === 13) {
          document.getElementById("sendGuess").click();
        }
      });
}

const urlParams = new URLSearchParams(window.location.search);
const roomID = urlParams.get('roomID');

const roomStatusSource = new EventSource('../backend/roomStatus.php?roomID='+roomID);
roomStatusSource.addEventListener("newName", function(e) {
            console.log(e.data);
            $("#roomName").html(e.data);
       });      

//thilin serve per ascoltare la tastiera, il tasto invio per la chat
       $("#newMessage").keypress(function(event) {
        if (event.which == 13) {
            event.preventDefault();
            $("#sendMessage").click();
        }
    });
//thilin serve per ascoltare la tastiera, il tasto invio per il guessbox
    $("#newGuess").keypress(function(event) {
        if (event.which == 13) {
            event.preventDefault();
            $("#sendGuess").click();
        }
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
    var message =': ' +  $("#newMessage").html();
    $("#newMessage").html("");
    // sostituisco i vari tag con degli spazi.
    var message = message.replace("<div>", " ").replace("<div><br></div>", " ").replace("<br>", " ").replace("&nbsp", " ").replace(" &nbsp", " ").replace("</div>", " ").replace("<br />", " ").replace(";", " ");
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
    $("#error").html("");
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
        console.log(message);

            message = JSON.parse(message);
            if(message.status == "success"){
                console.log("Partita iniziata");
            }
            else if(message.status == "not_valid"){
                $("#error").html(message.payload);
                console.log("Campi errati");
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
    if(word.trim() != ""){
        $.ajax({
            url: "../backend/guessWord.php",
            method: "post",
            data:{
                word:word,
                gameID:gameID
            }
        }).done(function(message) {
                console.log(message);
            });
    }
}

function initGame(ID){
    var maxTime = 0;
    document.getElementById("roomContainer").setAttribute("hidden", true);
    document.getElementById("gameContainer").removeAttribute("hidden");

    gameID = ID;
    gameSource = new EventSource('../backend/gameStatus.php?gameID='+gameID); 

    gameSource.addEventListener("time", function(e) {
        maxTime = e.data;
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
        $("#lives").html("Vite: " + e.data);
         console.log(e.data);
         //La gestione del reset del timer è inserita qui perché il cambio vite coincide con la fine del turno. L'evento "turn" non accade se un utente gioca da solo
         clearInterval(timerID);
         timerID = startTimer();
    });
    
    var timerID = 0;
    gameSource.addEventListener("turn", function(e) {
        $("#turn").html("Turno di " + e.data);
         console.log(e.data);
         
         console.log("timerID: "+timerID);
    });
    
    gameSource.addEventListener("yourTurn", function(e) {
        console.log(e.data);
    });
    
    gameSource.addEventListener("finish", function(e) {
        clearInterval(timerID);
        let result = JSON.parse(e.data)
        console.log(result);
        if(result.status == "victory"){
            //victory
            $("#finishMessage").html("Avete vinto! La parola era '" + result.word +"'");
        }
        else{
            $("#finishMessage").html("Avete perso :( La parola era '" + result.word + "'");
        }
        setTimeout(function(){
            $("#finishMessage").html("");
            document.getElementById("guesses").innerHTML = "";
            document.getElementById("letters").innerHTML = "";
            document.getElementById("gameContainer").setAttribute("hidden", true);
            document.getElementById("roomContainer").removeAttribute("hidden");
            gameSource.close();
            gameSource = 0;
            console.log(e.data);
        }, 5000);
    });
    
    gameSource.addEventListener("guess", function(e) {
        console.log("guess");
        entryJson = JSON.parse(e.data);
        var container = document.getElementById("guesses");
        var entry = document.createElement("div");
    
        var user = document.createElement("span");
        user.innerHTML = "user " + entryJson.user;
        entry.appendChild(user);

        var text = document.createElement("span");
        text.innerHTML = ": " + entryJson.word;
        entry.appendChild(text);
        
        container.appendChild(entry);
        console.log(e.data);
    });    

    function startTimer(){
        var i = maxTime;
        console.log("max Time: "+maxTime);
        var timerID = setInterval(function() {
            $("#time").html(i-- + " secondi rimanenti");
        }, 1000);
        return timerID;
      }
    }