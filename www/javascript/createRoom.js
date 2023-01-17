window.onload = init

function init(){
    $("#back").on("click", leave);
    $("#createRoomButton").on("click", createRoom);
}

function leave(){
    window.location = "../html/userHome.html";
}

function createRoom(){

    let form = $("#createRoomForm");

    let name = form.find("[name='name']").val(); 
    console.log(name);
    $.ajax({
        url: "../backend/restAPI/roomAPI.php",
        method: "post",
        data:JSON.stringify({name:name})
      }).done(function(message) {
            console.log(message);
            message = JSON.parse(message);
            if(message.status == "success"){
                window.location = "../html/room.html?roomID=" + message.payload;
            }
            else if(message.status == "not_valid"){
                error(message.payload);
            }
            else if(message == "error"){
                error("Errore del server");
            }
            console.log(message);

        });
}

function error(message){
    $("#error").html(message);
}