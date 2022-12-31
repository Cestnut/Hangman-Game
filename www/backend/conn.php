<?php

$servername = "127.0.0.1:3306";
$username = "root";
$password = "";
$database = "hangman";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error){
    die("Errore durante la connessione col database");
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset("utf8mb4");

define('ER_DUP_KEY', 1062);

session_start();
?>