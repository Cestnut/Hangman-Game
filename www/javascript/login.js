window.onload = init

function init(){
    $("#loginButton").on("click", login);
}

function login(){

    let form = $("#loginForm");


    let username = form.find("[name='username']").val(); 
    let password = form.find("[name='password']").val(); 
    $.ajax({
        url: "../backend/login.php",
        method: "post",
        data:{
            username:username,
            password:password,
        }
      }).done(function(message) {
            if(message == "success"){
                window.location = "../html/userHome.html";
            }
            else if(message == "wrong_credentials"){
                errorLogin("username o password errati");
            }
            else if(message == "error"){
                errorLogin("Errore del server");
            }

        });
}

function errorLogin(message){
    $("#errors").html(message);
}