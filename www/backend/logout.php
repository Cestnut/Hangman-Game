<?php

session_start(); 
session_destroy(); //distrugge tutti i dati della sessione lato server
setcookie(session_name(),'',0,'/'); //imposta a 0 il cookie PHPSESSID dell'utente

?>