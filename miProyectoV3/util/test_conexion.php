<?php
// test_conexion.php
$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "miBase1";

echo "Probando conexión...<br>";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
} else {
    echo "¡Conexión exitosa!<br>";
    
    // Probar consulta de usuarios
    $sql = "SELECT nombre_usuario, EMAIL FROM Usuarios LIMIT 5";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo "Usuarios encontrados:<br>";
        while($row = $result->fetch_assoc()) {
            echo "Usuario: " . $row["nombre_usuario"] . " - Email: " . $row["EMAIL"] . "<br>";
        }
    } else {
        echo "No se encontraron usuarios";
    }
    
    $conn->close();
}
?>