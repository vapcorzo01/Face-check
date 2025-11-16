<?php
$host = "localhost";
$user = "root";
$pass = "123456";
$db = "asistencia";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $correo = $_POST["correo"];
    $usuario = $_POST["usuario"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        header("Location: registro.php?msg=El usuario ya existe.");
        exit;
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (usuario, password, nombre, apellido, correo) VALUES (?, ?, ?, ?, ?)";
        $stmt2 = $conn->prepare($sql);
        $stmt2->bind_param("sssss", $usuario, $hash, $nombre, $apellido, $correo);
        if ($stmt2->execute()) {
            header("Location: index.php?msg=Cuenta creada correctamente. Ahora puedes iniciar sesión.");
        } else {
            header("Location: registro.php?msg=Error al crear la cuenta.");
        }
        $stmt2->close();
    }
    $stmt->close();
}
$conn->close();
?>