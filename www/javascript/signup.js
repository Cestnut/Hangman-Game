window.onload = init

function init(){
    $("#signupButton").on("click", signup);
}

function signup(){

    let form = $("#signupForm");

    let name = form.find("[name='name']").val(); 
    let surname = form.find("[name='surname']").val(); 
    let username = form.find("[name='username']").val(); 
    let password = form.find("[name='password']").val(); 
    let confirmPassword = form.find("[name='confirmPassword']").val(); 
    let voucher = form.find("[name='voucher']").val(); 

    $.ajax({
        url: "../backend/signup.php",
        method: "post",
        data:{
            name:name,
            surname:surname,
            username:username,
            password:password,
            confirmPassword:confirmPassword,
            voucher:voucher
        }
      }).done(function(message) {
            console.log(message);
            message = JSON.parse(message);
            if(message.success){
                window.location = "../html/userHome.html";
            }
            else{
                let errors = message.errors;

                fields = ["name", "surname", "username", "password", "voucher"];
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