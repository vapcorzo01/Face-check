<?php
// filepath: c:\Lista-de-asistencia-con-reconocimiento-facial-usando-Python-main\bienvenido.php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit;
}
$nombre = $_SESSION["usuario"];
$msg = isset($_GET['msg']) ? $_GET['msg'] : "";

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "123456";
$db = "asistencia";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener listado de asistencia del día actual
$fecha_hoy = date("Y-m-d");
$sql = "SELECT nombre, hora FROM registro WHERE fecha = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $fecha_hoy);
$stmt->execute();
$result = $stmt->get_result();

$asistencias = [];
while ($row = $result->fetch_assoc()) {
    $asistencias[$row['nombre']] = $row['hora'];
}
$stmt->close();

// Lista de estudiantes esperados (puedes cambiar esto según tu base de datos)
$estudiantes = ["So", "Sa", "Me", "Va"];

// Determinar estado de cada estudiante
$listado = [];
foreach ($estudiantes as $est) {
    if (isset($asistencias[$est])) {
        $hora = $asistencias[$est];
        // Cambia aquí la lógica de puntualidad según tu franja horaria
        $estado = ($hora <= "07:10") ? "A tiempo" : "Tarde";
    } else {
        $hora = "";
        $estado = "No asistió";
    }
    $listado[] = ["nombre" => $est, "hora" => $hora, "estado" => $estado];
}

// Últimos registros de asistencia
$result = $conn->query("SELECT nombre, fecha, hora FROM registro ORDER BY fecha DESC, hora DESC LIMIT 20");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .main-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-top: 40px;
        }
        .panel-derecho {
            min-width: 320px;
            max-width: 350px;
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 8px #ccc;
        }
        .listado-estudiantes {
            flex: 1;
            min-width: 320px;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 8px #ccc;
        }
        .top-bar {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
        }
        @media (max-width: 900px) {
            .main-container { flex-direction: column; }
            .top-bar { justify-content: center; }
        }
        .panel-docente-card {
            font-family: 'Poppins', sans-serif;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 24px #0002;
            padding: 32px 28px;
            max-width: 400px;
            margin: 40px auto;
            transition: box-shadow 0.3s;
        }
        .panel-docente-card:hover {
            box-shadow: 0 8px 32px #0003;
        }
        .panel-docente-title {
            font-size: 1.7rem;
            font-weight: 600;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #1e293b;
        }
        .form-label {
            font-weight: 500;
            color: #1e293b;
        }
        .input-group-text {
            background: #f1f5f9;
            border: none;
            color: #0ea5e9;
            font-size: 1.1rem;
        }
        .form-control, .form-select {
            border-radius: 10px !important;
            border: 1px solid #e2e8f0;
            transition: box-shadow 0.3s, border-color 0.3s;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 2px #38bdf8;
            border-color: #38bdf8;
        }
        .input-group {
            margin-bottom: 18px;
        }
        .btn-guardar {
            background: linear-gradient(90deg, #0ea5e9 0%, #38bdf8 100%);
            color: #fff;
            font-weight: 600;
            border-radius: 10px;
            width: 100%;
            padding: 12px 0;
            font-size: 1.1rem;
            box-shadow: 0 2px 8px #0ea5e933;
            transition: background 0.3s, box-shadow 0.3s;
        }
        .btn-guardar:hover {
            background: linear-gradient(90deg, #0284c7 0%, #0ea5e9 100%);
            box-shadow: 0 4px 16px #0ea5e955;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#asistenciaModal">
                Registrar asistencia con rostro
            </button>
            <a href="logout.php" class="btn btn-danger">Cerrar sesión</a>
            <?php if (isset($_SESSION["rol"]) && $_SESSION["rol"] === "admin"): ?>
                <a href="admin.php" class="btn btn-dark ms-2">Panel de administración</a>
            <?php endif; ?>
        </div>
        <div class="alert alert-success mt-4">
            <?php echo $msg ? $msg : "¡Bienvenido, " . htmlspecialchars($nombre) . "!"; ?>
        </div>
        <div class="main-container">
            <!-- Panel derecho: controles del docente -->
            <div class="panel-derecho ms-auto">
                <h5 class="mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-chalkboard-user"></i> Panel del docente
                </h5>
                <form id="form-docente" autocomplete="off">
                    <label class="form-label">Franja horaria</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text"><i class="fa-regular fa-clock"></i></span>
                        <select class="form-select" id="franja" required>
                            <option value="" disabled selected>Selecciona una franja</option>
                            <option>07:00 - 09:00</option>
                            <option>09:00 - 11:00</option>
                            <option>11:00 - 13:00</option>
                            <option>13:00 - 15:00</option>
                        </select>
                    </div>
                    <label class="form-label">Materia</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text"><i class="fa-solid fa-book"></i></span>
                        <select class="form-select" id="materia" required>
                            <option value="" disabled selected>Selecciona una materia</option>
                            <option>Diseño de ingeniería</option>
                            <option>Programación Avanzada</option>
                            <option>Redes de Computadores</option>
                        </select>
                    </div>
                    <label class="form-label">Salón</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text"><i class="fa-solid fa-building"></i></span>
                        <select class="form-select" id="salon" required>
                            <option value="" disabled selected>Selecciona un salón</option>
                            <option>223</option>
                            <option>101</option>
                            <option>305</option>
                        </select>
                    </div>
                    <label class="form-label">Profesor</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                        <input type="text" class="form-control" id="profesor" placeholder="Escribe tu nombre completo" required>
                    </div>
                    <label class="form-label">Profesión</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa-solid fa-briefcase"></i></span>
                        <input type="text" class="form-control" id="profesion" placeholder="Ejemplo: Ingeniero de Sistemas" required>
                    </div>
                    <button type="submit" class="btn btn-guardar w-100">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar
                    </button>
                </form>
                <div id="datos-fijos" style="display:none; margin-top:18px;">
                    <div class="alert alert-success mb-2 py-2 px-3">
                        <b>Franja:</b> <span id="fijo-franja"></span><br>
                        <b>Materia:</b> <span id="fijo-materia"></span><br>
                        <b>Salón:</b> <span id="fijo-salon"></span><br>
                        <b>Profesor:</b> <span id="fijo-profesor"></span><br>
                        <b>Profesión:</b> <span id="fijo-profesion"></span>
                    </div>
                    <button class="btn btn-secondary w-100" id="editar-datos">
                        <i class="fa-solid fa-pen"></i> Editar datos
                    </button>
                </div>
                <a href="seleccionar_estudiante.php" class="btn btn-info w-100 mt-3">
                    <i class="fa-solid fa-users"></i> Listado estudiantes
                </a>
            </div>
            <!-- Listado de estudiantes -->
            <div class="listado-estudiantes">
                <h5 class="mb-3">Listado de estudiantes</h5>
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Hora de llegada</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listado as $est): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($est["nombre"]); ?></td>
                            <td><?php echo $est["hora"] ? $est["hora"] : "-"; ?></td>
                            <td>
                                <?php
                                if ($est["estado"] === "A tiempo") {
                                    echo "<span class='badge bg-success'>A tiempo</span>";
                                } elseif ($est["estado"] === "Tarde") {
                                    echo "<span class='badge bg-warning text-dark'>Tarde</span>";
                                } else {
                                    echo "<span class='badge bg-danger'>No asistió</span>";
                                }
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Últimos registros de asistencia -->
                <h5 class="mt-4">Últimos registros de asistencia</h5>
                <table class="table table-bordered mt-4">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nombre']) ?></td>
                            <td><?= htmlspecialchars($row['fecha']) ?></td>
                            <td><?= htmlspecialchars($row['hora']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para la cámara -->
    <div class="modal fade" id="asistenciaModal" tabindex="-1" aria-labelledby="asistenciaModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="asistenciaModalLabel">Registrar asistencia</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body text-center">
            <div class="mb-4 text-center">
                <video id="video-asistencia" width="320" height="240" autoplay style="border:1px solid #ccc"></video>
                <div id="asistencia-status" class="mt-2 text-primary"></div>
            </div>
            <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
            <br>
            <button id="capture" class="btn btn-primary mt-2" type="button">Capturar</button>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button id="send-face" type="button" class="btn btn-success" disabled>Registrar asistencia</button>
          </div>
        </div>
      </div>
    </div>
    <form id="asistencia-form" method="post" enctype="multipart/form-data" action="registrar_asistencia_rostro.php">
        <input type="hidden" name="face_image" id="face_image">
    </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    let video = document.getElementById('video-asistencia');
    let statusDiv = document.getElementById('asistencia-status');
    let streaming = false;
    let intervalId = null;
    let ultimaAsistencia = "";

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            video.srcObject = stream;
            streaming = true;
            startFaceRecognition();
        })
        .catch(err => {
            statusDiv.innerText = "No se pudo acceder a la cámara.";
        });

    function startFaceRecognition() {
        let canvas = document.createElement('canvas');
        canvas.width = 320;
        canvas.height = 240;

        function sendFrame() {
            if (!streaming) return;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            let dataURL = canvas.toDataURL('image/jpeg');

            fetch('http://localhost:5000/detectar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ face_image: dataURL })
            })
            .then(res => res.json())
            .then(res => {
                if (res.rol === "estudiante" && res.estado.includes("asistencia registrada")) {
                    statusDiv.innerHTML = `<span class='text-success'>Asistencia registrada para ${res.usuario}</span>`;
                    setTimeout(() => { location.reload(); }, 1500);
                } else if (res.rol === "estudiante" && res.estado === "ya registrada") {
                    statusDiv.innerHTML = `<span class='text-warning'>${res.usuario}: asistencia ya registrada hoy.</span>`;
                } else if (res.error) {
                    statusDiv.innerHTML = `<span class='text-danger'>${res.error}</span>`;
                } else {
                    statusDiv.innerHTML = "<span class='text-danger'>No se reconoció el rostro.</span>";
                }
            })
            .catch(() => {
                statusDiv.innerHTML = "<span class='text-danger'>Error al conectar con la API facial</span>";
            });
        }

        intervalId = setInterval(sendFrame, 2000); // cada 2 segundos
    }
    </script>
</body>
</html>