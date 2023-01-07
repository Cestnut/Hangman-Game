window.onload = init

function init(){
    $("#createRoomButton").on("click", createRoom);
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
                console.log("../html/room.html/" + message.payload);
                //window.location = "../html/userHome.html";
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