<?php

$servername = "127.0.0.1:3306";
$username = "root";
$password = "root";
$database = "hangman";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error){
    die("Errore durante la connessione col database");
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //per far vedere gli errori delle funzioni che riguardano il database
$conn->set_charset("utf8mb4");

define('ER_DUP_KEY', 1062); //costante che indica l'errore di chiave duplicata

//unsetting because this script will be included in almost every other script
unset($servername);
unset($username);
unset($password);
unset($database);

session_start(); //inizializza la variabile _SESSION
?>