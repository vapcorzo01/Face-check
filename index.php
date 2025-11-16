<?php
// filepath: c:\Lista-de-asistencia-con-reconocimiento-facial-usando-Python-main\index.php
session_start();
$msg = isset($_GET['msg']) ? $_GET['msg'] : "";

if (isset($_SESSION["usuario"])) {
    header("Location: bienvenido.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Asistencia Facial</title>
    <!-- Login Interactivo Mejorado -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Poppins:400,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<style>
.login-bg {
    min-height: 100vh;
    background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Poppins', sans-serif;
}
.login-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 6px 32px #0002;
    padding: 40px 32px 32px 32px;
    max-width: 400px;
    width: 100%;
    transition: box-shadow 0.3s;
}
.login-card:hover {
    box-shadow: 0 12px 48px #0ea5e955;
}
.login-title {
    font-size: 2rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 8px;
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
}
.login-sub {
    text-align: center;
    color: #555;
    margin-bottom: 24px;
}
.input-group-text {
    background: #f1f5f9;
    border: none;
    color: #0ea5e9;
    font-size: 1.1rem;
}
.form-control {
    border-radius: 12px !important;
    border: 1px solid #e2e8f0;
    transition: box-shadow 0.3s, border-color 0.3s;
}
.form-control:focus {
    box-shadow: 0 0 0 2px #38bdf8;
    border-color: #38bdf8;
}
.btn-login {
    background: linear-gradient(90deg, #0ea5e9 0%, #38bdf8 100%);
    color: #fff;
    font-weight: 600;
    border-radius: 12px;
    width: 100%;
    padding: 12px 0;
    font-size: 1.1rem;
    box-shadow: 0 2px 8px #0ea5e933;
    transition: background 0.3s, box-shadow 0.3s;
}
.btn-login:hover {
    background: linear-gradient(90deg, #0284c7 0%, #0ea5e9 100%);
    box-shadow: 0 4px 16px #0ea5e955;
}
.btn-crear {
    background: #f1f5f9;
    color: #0284c7;
    font-weight: 600;
    border-radius: 12px;
    width: 100%;
    padding: 12px 0;
    font-size: 1.1rem;
    border: none;
    margin-top: 10px;
    transition: background 0.3s, color 0.3s;
}
.btn-crear:hover {
    background: #e0f2fe;
    color: #0ea5e9;
}
.divider {
    display: flex;
    align-items: center;
    text-align: center;
    margin: 28px 0 18px 0;
}
.divider::before, .divider::after {
    content: '';
    flex: 1;
    border-bottom: 1.5px solid #e0e0e0;
}
.divider:not(:empty)::before {
    margin-right: .75em;
}
.divider:not(:empty)::after {
    margin-left: .75em;
}
.btn-facial {
    background: linear-gradient(90deg, #06b6d4 0%, #0ea5e9 100%);
    color: #fff;
    font-weight: 600;
    border-radius: 14px;
    width: 100%;
    padding: 14px 0;
    font-size: 1.1rem;
    margin-top: 10px;
    box-shadow: 0 2px 12px #06b6d433;
    transition: background 0.3s, box-shadow 0.3s;
}
.btn-facial:hover {
    background: linear-gradient(90deg, #0284c7 0%, #06b6d4 100%);
    box-shadow: 0 4px 24px #06b6d455;
}
#face-login-video {
    display: none;
    margin: 20px auto 0 auto;
    border: 2px solid #0ea5e9;
    border-radius: 12px;
    box-shadow: 0 2px 12px #06b6d433;
    background: #e0f7fa;
    transition: border-color 0.3s, box-shadow 0.3s;
    width: 100%;
    max-width: 320px;
}
#face-login-status {
    margin-top: 10px;
    font-size: 1rem;
    color: #0284c7;
    min-height: 32px;
}
@media (max-width: 600px) {
    .login-card { padding: 20px 8px 24px 8px; }
    #face-login-video { max-width: 100%; }
}
</style>
</head>
<body>
<div class="login-bg">
    <div class="login-card">
        <div class="login-title">
            <i class="fa-solid fa-user-circle"></i> Iniciar sesión
        </div>
        <div class="login-sub">Accede a tu cuenta</div>
        <?php if ($msg) echo "<div class='msg'>$msg</div>"; ?>

        <!-- Formulario de login con usuario y contraseña -->
        <form method="post" action="procesa_login.php">
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                <input type="text" name="usuario" class="form-control" placeholder="Usuario" required>
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
            </div>
            <div class="d-flex gap-2 mb-2">
                <button type="submit" class="btn btn-login">
                    <i class="fa-solid fa-sign-in-alt"></i> Entrar
                </button>
                <a href="registro.php" class="btn btn-crear">
                    <i class="fa-solid fa-user-plus"></i> Crear cuenta
                </a>
            </div>
        </form>
        <div class="divider">O usa tu rostro para iniciar sesión</div>
        <button id="activar-facial" class="btn btn-facial" type="button">
            <i class="fa-solid fa-camera"></i> Activar reconocimiento facial
        </button>
        <video id="face-login-video" width="320" height="240" autoplay></video>
        <div id="face-login-status" class="text-center text-secondary"></div>
    </div>
</div>

<script>
let video = document.getElementById('face-login-video');
let statusDiv = document.getElementById('face-login-status');
let streaming = false;
let intervalId = null;
let facialActivo = false;

document.getElementById('activar-facial').onclick = function() {
    if (facialActivo) return;
    facialActivo = true;
    video.style.display = "block";
    statusDiv.innerText = "Apunta tu rostro a la cámara para iniciar sesión...";
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            video.srcObject = stream;
            streaming = true;
            startFaceLogin();
        })
        .catch(err => {
            statusDiv.innerText = "No se pudo acceder a la cámara.";
        });
};

function startFaceLogin() {
    let canvas = document.createElement('canvas');
    canvas.width = 320;
    canvas.height = 240;

    function sendFrame() {
        if (!streaming) return;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        let dataURL = canvas.toDataURL('image/jpeg');

        // Enviar la imagen al backend usando la API Flask
        fetch('http://localhost:5000/detectar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ face_image: dataURL })
        })
        .then(res => res.json())
        .then(res => {
            if (res.usuario === "so" && res.rol === "maestra") {
                // Hacer login de sesión PHP antes de redirigir
                fetch('login_facial.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'usuario=so'
                })
                .then(r => r.text())
                .then(r => {
                    if (r.trim() === "ok") {
                        window.location.href = "bienvenido.php";
                    } else {
                        statusDiv.innerHTML = "<span class='text-danger'>No se pudo crear la sesión.</span>";
                    }
                });
                clearInterval(intervalId);
            } else if (res.rol === "estudiante" && res.estado.includes("asistencia registrada")) {
                statusDiv.innerHTML = "<span class='text-success'>Asistencia registrada correctamente</span>";
                clearInterval(intervalId);
            } else if (res.error) {
                statusDiv.innerHTML = "<span class='text-danger'>" + res.error + "</span>";
            }
        })
        .catch(() => {
            statusDiv.innerHTML = "<span class='text-danger'>Error al conectar con la API facial</span>";
        });
    }

    intervalId = setInterval(sendFrame, 2000); // cada 2 segundos
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>