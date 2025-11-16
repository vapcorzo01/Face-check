<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "123456";
$db = "asistencia";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row["password"])) {
            $_SESSION["usuario"] = $usuario;
            $_SESSION["rol"] = $row["rol"]; // Asegúrate de obtener el campo rol de la base de datos
            header("Location: bienvenido.php?msg=Inicio de sesión exitoso");
            $stmt->close();
            exit;
        } else {
            header("Location: index.php?msg=Contraseña incorrecta.");
            $stmt->close();
            exit;
        }
    } else {
        header("Location: index.php?msg=Usuario no encontrado.");
        $stmt->close();
        exit;
    }
}
$conn->close();
?>