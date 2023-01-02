window.onload = init

function init(){
    $("#signupButton").on("click", signup);
}

function signup(){

    let form = $("#signupForm");

    let username = form.find("[name='username']").val(); 
    let password = form.find("[name='password']").val(); 
    let confirmPassword = form.find("[name='confirmPassword']").val(); 

    $.ajax({
        url: "../backend/signup.php",
        method: "post",
        data:{
            username:username,
            password:password,
            confirmPassword:confirmPassword,
        }
      }).done(function(message) {
            console.log(message);
            message = JSON.parse(message);
            if(message.success){
                window.location = "../html/userHome.html";
            }
            else{
                let errors = message.errors;

                fields = ["username", "password"];
                fields.forEach(function(field) {
                    let errorsField = document.getElementById(field+"Errors");
                    let newErrorsDiv = document.createElement("div");
                    
                    errors[field].forEach(function(error){
                        let newErrorDiv = document.createElement("div");
                        newErrorDiv.innerHTML = error;
                        newErrorsDiv.appendChild(newErrorDiv);
                    })

                    errorsField.replaceChildren(newErrorsDiv);

                });
            }
        });
}