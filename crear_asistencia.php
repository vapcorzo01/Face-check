<?php
// filepath: c:\Lista-de-asistencia-con-reconocimiento-facial-usando-Python-main\crear_asistencia.php
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== "admin") {
    header("Location: index.php");
    exit;
}

$msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST["nombre"];
    $fecha = $_POST["fecha"];
    $hora = $_POST["hora"];
    $conn = new mysqli("localhost", "root", "123456", "asistencia");
    $stmt = $conn->prepare("INSERT INTO registro (nombre, fecha, hora) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $fecha, $hora);
    if ($stmt->execute()) {
        $msg = "Asistencia creada correctamente.";
    } else {
        $msg = "Error al crear asistencia.";
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Asistencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Crear Asistencia</h2>
    <?php if ($msg) echo "<div class='alert alert-info'>$msg</div>"; ?>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Nombre del estudiante</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Fecha</label>
            <input type="date" name="fecha" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Hora</label>
            <input type="time" name="hora" class="form-control" required value="<?php echo date('H:i'); ?>">
        </div>
        <button type="submit" class="btn btn-success">Crear</button>
        <a href="admin.php" class="btn btn-secondary">Volver</a>
    </form>
</div>
</body>
</html>