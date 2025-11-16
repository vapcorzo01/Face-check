<?php
$msg = isset($_GET['msg']) ? $_GET['msg'] : "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Asistencia Facial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f0f0; }
        .login-box { background: #fff; padding: 30px; margin: 100px auto; width: 400px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        .msg { color: #d00; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2 class="mb-4 text-center">Crear cuenta</h2>
        <?php if ($msg) echo "<div class='msg'>$msg</div>"; ?>
        <form method="post" action="procesa_registro.php">
            <input type="text" name="nombre" class="form-control mb-2" placeholder="Nombre" required>
            <input type="text" name="apellido" class="form-control mb-2" placeholder="Apellido" required>
            <input type="email" name="correo" class="form-control mb-2" placeholder="Correo" required>
            <input type="text" name="usuario" class="form-control mb-2" placeholder="Usuario" required>
            <input type="password" name="password" class="form-control mb-2" placeholder="Contraseña" required>
            <div class="d-flex justify-content-between">
                <input type="submit" class="btn btn-success" value="Registrar">
                <a href="index.php" class="btn btn-secondary">Volver</a>
            </div>
        </form>
    </div>
</body>
</html>