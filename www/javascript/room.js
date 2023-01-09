window.onload = init

function init(){
    $("#sendMessage").on("click", sendMessage);
}

const urlParams = new URLSearchParams(window.location.search);
const roomID = urlParams.get('roomID')

const chatSource = new EventSource('../backend/chat.php?roomID='+roomID);
chatSource.onmessage = function(e) {
            writeChatMessage(JSON.parse(e.data));
            //console.log(JSON.parse(e.data));
       };

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