window.onload = init;

function init(){
    buildTable();
}

function buildTable(){
    $.ajax({
        url: "../backend/restAPI/roomAPI.php",
        method: "get"
      }).done(function(message) {
            console.log(message);
            message = JSON.parse(message);
            if (message.status == "success"){
                message.payload.forEach(function (room) {
                    var container = document.getElementById("roomList");
                    
                    var row = document.createElement("div");
                    row.setAttribute("id", room.ID_room);    
                    row.setAttribute("name", room.name);    
                    row.innerHTML = room.name;
                    row.addEventListener("click", connectRoom);

                    container.appendChild(row);
                });
            }
        });
}

function connectRoom(){
    roomID = this.id;
    $.ajax({
        url: "../backend/roomConnection.php",
        method: "POST",
        data: {
            roomID: roomID
        }
      }).done(function(message) {
            console.log(message);
            message = JSON.parse(message);
            if (message.status == "success"){
                window.location = "../html/room.html?roomID=" + roomID;
            }
        });
}