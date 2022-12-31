window.onload = init

function init(){
    $("#logout").on("click", logout);

}

function logout(){
    $.ajax({
        url: "../backend/logout.php",
        method: "get"
        }).done(function(){
            window.location = "../html/login.html";
        })
}