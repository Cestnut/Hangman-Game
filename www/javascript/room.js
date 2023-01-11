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
        });

const chatSource = new EventSource('../backend/chat.php?roomID='+roomID);
chatSource.addEventListener("message", function(e) {
            writeChatMessage(JSON.parse(e.data));
       });

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
                //window.location = "../html/userHome.html";
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