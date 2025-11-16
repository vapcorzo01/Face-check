<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['face_image'])) {
    $img = $_POST['face_image'];
    $img = str_replace('data:image/jpeg;base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    $data = base64_decode($img);
    $file = "temp_face.jpg";
    file_put_contents($file, $data);

    // Ejecuta el script Python y obtiene el nombre reconocido
    $output = shell_exec("python facerec_login.py $file");
    $usuario = trim($output);

    // Borra la imagen temporal
    unlink($file);

    // Solo permite registrar asistencia si el rostro coincide con el usuario logueado
    if (strcasecmp($usuario, $_SESSION["usuario"]) == 0) {
        $nombre = $_SESSION["usuario"];
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");

        $host = "localhost";
        $user = "root";
        $pass = "123456";
        $db = "asistencia";
        $conn = new mysqli($host, $user, $pass, $db);

        if ($conn->connect_error) {
            die("Error de conexión: " . $conn->connect_error);
        }

        $sql = "INSERT INTO registro (fecha, nombre, hora) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Error en prepare: " . $conn->error);
        }
        $stmt->bind_param("sss", $fecha, $nombre, $hora);
        if ($stmt->execute()) {
            header("Location: bienvenido.php?msg=¡Asistencia registrada correctamente!");
        } else {
            header("Location: bienvenido.php?msg=Error al registrar la asistencia.");
        }
        $stmt->close();
        $conn->close();
        exit;
    } else {
        header("Location: bienvenido.php?msg=Rostro no coincide con el usuario logueado.");
        exit;
    }
} else {
    header("Location: bienvenido.php?msg=No se recibió imagen.");
    exit;
}
?>