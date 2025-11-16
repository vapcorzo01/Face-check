<?php
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== "admin") {
    header("Location: index.php");
    exit;
}
// Aquí va el panel de administración
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Panel de Administración</h2>
    <a href="bienvenido.php" class="btn btn-secondary mb-3">Volver</a>
    <!-- Aquí puedes poner botones para crear, modificar y eliminar asistencias -->
    <a href="crear_asistencia.php" class="btn btn-success mb-2">Crear asistencia</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Conexión y consulta
        $conn = new mysqli("localhost", "root", "123456", "asistencia");
        $res = $conn->query("SELECT id, nombre, fecha, hora FROM registro ORDER BY fecha DESC, hora DESC");
        while ($row = $res->fetch_assoc()) {
            echo "<tr>
                <td>{$row['nombre']}</td>
                <td>{$row['fecha']}</td>
                <td>{$row['hora']}</td>
                <td>
                    <a href='editar_asistencia.php?id={$row['id']}' class='btn btn-warning btn-sm'>Editar</a>
                    <a href='eliminar_asistencia.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('¿Eliminar asistencia?')\">Eliminar</a>
                </td>
            </tr>";
        }
        $conn->close();
        ?>
        </tbody>
    </table>
</div>
</body>
</html>