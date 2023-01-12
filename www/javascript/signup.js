window.onload = init

function init(){
    $("#signupButton").on("click", signup);
}

function signup(){
    $("#errors").html("");
    let form = $("#signupForm");

    let username = form.find("[name='username']").val(); 
    let password = form.find("[name='password']").val(); 
    let confirmPassword = form.find("[name='confirmPassword']").val(); 
    console.log(username);
    $.ajax({
        url: "../backend/restAPI/userAPI.php",
        method: "post",
        data:JSON.stringify({
            username:username,
            password:password,
            confirmPassword:confirmPassword,
        })
      }).done(function(message) {
            console.log(message);
            message = JSON.parse(message);
            if(message.status == "success"){
                window.location = "../html/userHome.html";
            }
            else if(message.status == "not_valid"){
                let errors = message.payload;
                var container = document.getElementById("errors");

                errors.forEach(function (error){
                    var row = document.createElement("div");
                    row.innerHTML = error;
                    container.appendChild(row);
                })
            }});
}