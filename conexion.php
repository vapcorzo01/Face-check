<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Probando PHP<br>"; // 👈 Agrega esto temporalmente

$host = "localhost";
$user = "root";   
$pass = "123456";   // revisa si tu MySQL usa password
$db   = "asistencia";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
} else {
    echo "✅ Conexión exitosa a la base de datos";
}
?>

