<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nombre"])) {
    $nombre = $_POST["nombre"];
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

    // Guarda la asistencia
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
    // Formulario de registro de asistencia
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Registrar Asistencia</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <style>
            body { background: #f0f4f8; }
            .asistencia-box {
                background: #fff;
                border-radius: 20px;
                box-shadow: 0 6px 32px #0002;
                padding: 40px 32px 32px 32px;
                max-width: 400px;
                margin: 60px auto;
            }
            .btn-center {
                display: flex;
                justify-content: center;
                gap: 16px;
                margin-top: 18px;
            }
        </style>
    </head>
    <body>
        <div class="asistencia-box">
            <h2 class="text-center mb-4"><i class="fa-solid fa-user-check"></i> Registrar Asistencia</h2>
            <form method="post">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del estudiante</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Ejemplo: Melany De Los Angeles Ortiz Ruiz">
                </div>
                <div class="btn-center">
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-check"></i> Registrar
                    </button>
                    <a href="bienvenido.php" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Volver
                    </a>
                </div>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>