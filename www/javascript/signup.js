window.onload = init //funzione da eseguire quando viene caricata la pagina

function init(){
    $("#signupButton").on("click", signup); //aggiunge il listener dell'evento click all'elemento con ID signupButton.
}

function signup(){
    $("#errors").html(""); //pulisce il div, per evitare che venga riempito in caso di più tentativi con errore
    let form = $("#signupForm");

    let username = form.find("[name='username']").val(); 
    let password = form.find("[name='password']").val(); 
    let confirmPassword = form.find("[name='confirmPassword']").val(); 

    //metodo che manda una richiesta HTTP al server
    $.ajax({
        url: "../backend/restAPI/userAPI.php",
        method: "post",
        data:JSON.stringify({ //viene messo un JSON come corpo del messaggio
            username:username,
            password:password,
            confirmPassword:confirmPassword
        })
      }).done(function(message) { //funzione che viene chiamata quando il server risponde. Il parametro message è la risposta del server
            console.log(message);
            message = JSON.parse(message);
            if(message.status == "success"){
                window.location = "../html/userHome.html";
            }
            else if(message.status == "not_valid"){
                //per ogni errore crea un div e lo appende al div errors
                let errors = message.payload;
                var container = document.getElementById("errors");

                errors.forEach(function (error){
                    var row = document.createElement("div");
                    row.innerHTML = error;
                    container.appendChild(row);
                })
            }
            else if(message.status == "error"){
                var container = document.getElementById("errors");
                var row = document.createElement("div");
                row.innerHTML = "Errore del server";
                container.appendChild(row);
            }
        });
}