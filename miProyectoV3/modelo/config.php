<?php
$servername = "localhost";
$username = "root";
$password = "mysql";  
$dbname = "miBase1";

function conectarDB() {
    global $servername, $username, $password, $dbname;
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    // Configurar charset
    $conn->set_charset("utf8");
    
    return $conn;
}
?>