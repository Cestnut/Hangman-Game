//window.onload = init

function init(){
    var maxLives = document.getElementById("livesField");
    var maxTime = document.getElementById("maxTimeField");
    var maxPlayers = document.getElementById("maxPlayersField");

    maxLives.addEventListener("keydown", isNumberKey);
    maxTime.addEventListener("keydown", isNumberKey);
    maxPlayers.addEventListener("keydown", isNumberKey);
}

function isNumberKey(key){
    code = key.key;
    var allowed = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "Delete", "Backspace", "Tab", "ArrowLeft", "ArrowRight"];  
    if (!(allowed.includes(code)))
        key.preventDefault();
}