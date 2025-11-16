<?php
// filepath: c:\Lista-de-asistencia-con-reconocimiento-facial-usando-Python-main\eliminar_asistencia.php
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== "admin") {
    header("Location: index.php");
    exit;
}

if (isset($_GET["id"])) {
    $id = intval($_GET["id"]);
    $conn = new mysqli("localhost", "root", "123456", "asistencia");
    $conn->query("DELETE FROM registro WHERE id=$id");
    $conn->close();
}
header("Location: admin.php");
exit;
?>