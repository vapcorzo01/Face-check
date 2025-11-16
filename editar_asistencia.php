<?php
// filepath: c:\Lista-de-asistencia-con-reconocimiento-facial-usando-Python-main\editar_asistencia.php
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== "admin") {
    header("Location: index.php");
    exit;
}

$conn = new mysqli("localhost", "root", "123456", "asistencia");
$msg = "";

if (isset($_GET["id"])) {
    $id = intval($_GET["id"]);
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nombre = $_POST["nombre"];
        $fecha = $_POST["fecha"];
        $hora = $_POST["hora"];
        $stmt = $conn->prepare("UPDATE registro SET nombre=?, fecha=?, hora=? WHERE id=?");
        $stmt->bind_param("sssi", $nombre, $fecha, $hora, $id);
        if ($stmt->execute()) {
            $msg = "Asistencia actualizada correctamente.";
        } else {
            $msg = "Error al actualizar asistencia.";
        }
        $stmt->close();
    }
    $res = $conn->query("SELECT * FROM registro WHERE id=$id");
    $row = $res->fetch_assoc();
} else {
    header("Location: admin.php");
    exit;
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Asistencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Editar Asistencia</h2>
    <?php if ($msg) echo "<div class='alert alert-info'>$msg</div>"; ?>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Nombre del estudiante</label>
            <input type="text" name="nombre" class="form-control" required value="<?php echo htmlspecialchars($row['nombre']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Fecha</label>
            <input type="date" name="fecha" class="form-control" required value="<?php echo $row['fecha']; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Hora</label>
            <input type="time" name="hora" class="form-control" required value="<?php echo $row['hora']; ?>">
        </div>
        <button type="submit" class="btn btn-warning">Actualizar</button>
        <a href="admin.php" class="btn btn-secondary">Volver</a>
    </form>
</div>
</body>
</html>