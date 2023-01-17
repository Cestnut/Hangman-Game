window.onload = init;

function init(){
    $("#back").on("click", leave);
    buildTable();
}

function leave(){
    window.location = "../html/userHome.html";
}

function buildTable(){
    $.ajax({
        url: "../backend/restAPI/roomAPI.php",
        method: "get",
        data:{
            status:"open"
        }
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
    roomID = this.id; //this corrisponde all'oggetto che ha fatto partire la funzione, in questo caso il div che è stato cliccato, il cui id è proprio l'id della stanza.
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
            else if (message.status == "closed"){
                $("#error").html("La stanza è chiusa");
                document.getElementById(roomID).innerHTML = "";
                
            }
            else if (message.status == "closed"){
                $("#error").html("Errore del server");
            }
        });
}