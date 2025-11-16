<?php
// filepath: c:\Lista-de-asistencia-con-reconocimiento-facial-usando-Python-main\portal_estudiantil.php

// Datos de ejemplo para cada estudiante
$estudiantes = [
    "ME" => [
        "nombre" => "Melany De Los Angeles Ortiz Ruiz",
        "id" => "EST001",
        "carrera" => "Ingeniería en Sistemas",
        "semestre" => "7mo Semestre",
        "total_clases" => 6,
        "asistencias" => 5,
        "faltas" => 0,
        "tardanzas" => 1,
        "porcentaje" => 83,
        "mensaje" => "¡Buen trabajo, sigue así!",
        "color_porcentaje" => "success",
        "historial" => [
            ["Programación Avanzada", "14/1/2024", "08:00", "Dr. López", "Presente"],
            ["Base de Datos II", "14/1/2024", "10:00", "Ing. Martínez", "Tardanza"],
            ["Redes de Computadoras", "15/1/2024", "14:00", "Dr. Hernández", "Presente"],
            ["Programación Avanzada", "16/1/2024", "08:00", "Dr. López", "Presente"],
            ["Ingeniería de Software", "16/1/2024", "16:00", "Lic. García", "Presente"],
            ["Base de Datos II", "17/1/2024", "10:00", "Ing. Martínez", "Presente"],
        ]
    ],
    "SO" => [
        "nombre" => "Sofia Lopez Yepes",
        "id" => "EST002",
        "carrera" => "Ingeniería en Sistemas",
        "semestre" => "7mo Semestre",
        "total_clases" => 6,
        "asistencias" => 4,
        "faltas" => 1,
        "tardanzas" => 1,
        "porcentaje" => 67,
        "mensaje" => "Puedes mejorar tu asistencia.",
        "color_porcentaje" => "warning",
        "historial" => [
            ["Programación Avanzada", "14/1/2024", "08:00", "Dr. López", "Presente"],
            ["Base de Datos II", "14/1/2024", "10:00", "Ing. Martínez", "Tardanza"],
            ["Redes de Computadoras", "15/1/2024", "14:00", "Dr. Hernández", "Falta"],
            ["Programación Avanzada", "16/1/2024", "08:00", "Dr. López", "Presente"],
            ["Ingeniería de Software", "16/1/2024", "16:00", "Lic. García", "Presente"],
            ["Base de Datos II", "17/1/2024", "10:00", "Ing. Martínez", "Presente"],
        ]
    ],
    "SA" => [
        "nombre" => "Sara Sofia Garzon Fontecha",
        "id" => "EST003",
        "carrera" => "Ingeniería en Sistemas",
        "semestre" => "7mo Semestre",
        "total_clases" => 6,
        "asistencias" => 3,
        "faltas" => 2,
        "tardanzas" => 1,
        "porcentaje" => 50,
        "mensaje" => "Necesitas mejorar tu asistencia",
        "color_porcentaje" => "danger",
        "historial" => [
            ["Programación Avanzada", "14/1/2024", "08:00", "Dr. López", "Presente"],
            ["Base de Datos II", "14/1/2024", "10:00", "Ing. Martínez", "Tardanza"],
            ["Redes de Computadoras", "15/1/2024", "14:00", "Dr. Hernández", "Falta"],
            ["Programación Avanzada", "16/1/2024", "08:00", "Dr. López", "Falta"],
            ["Ingeniería de Software", "16/1/2024", "16:00", "Lic. García", "Presente"],
            ["Base de Datos II", "17/1/2024", "10:00", "Ing. Martínez", "Presente"],
        ]
    ]
];

// Obtener el id del estudiante por GET
$id = isset($_GET['id']) ? strtoupper($_GET['id']) : "ME";
$datos = isset($estudiantes[$id]) ? $estudiantes[$id] : $estudiantes["ME"];

// Definir el icono según el color_porcentaje
$icono_porcentaje = "";
switch ($datos["color_porcentaje"]) {
    case "success":
        $icono_porcentaje = "✅";
        break;
    case "warning":
        $icono_porcentaje = "⚠️";
        break;
    case "danger":
        $icono_porcentaje = "❌";
        break;
    default:
        $icono_porcentaje = "";
        break;
}

function estado_icono($estado) {
    if ($estado === "Presente") return "<span class='text-success fw-bold'>🟢 Presente</span>";
    if ($estado === "Falta") return "<span class='text-danger fw-bold'>🔴 Falta</span>";
    if ($estado === "Tardanza") return "<span class='text-warning fw-bold'>🟡 Tardanza</span>";
    return $estado;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Portal Estudiantil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f6f8fa; }
        .dashboard-box { background: #fff; border-radius: 16px; box-shadow: 0 2px 12px #0001; padding: 32px; margin: 40px auto; max-width: 900px; }
        .dashboard-header { display: flex; justify-content: space-between; align-items: center; }
        .dashboard-title { font-size: 2rem; font-weight: 700; }
        .dashboard-subtitle { font-size: 1.2rem; color: #555; margin-bottom: 24px; }
        .btn-top { margin-left: 10px; }
        .info-personal { background: #f8f9fa; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
        .resumen-cards { display: flex; gap: 20px; margin-bottom: 24px; }
        .resumen-card { flex: 1; background: #f8f9fa; border-radius: 12px; padding: 18px; text-align: center; box-shadow: 0 1px 6px #0001; }
        .resumen-card h4 { margin: 0; font-size: 2rem; }
        .resumen-card span { font-size: 1.1rem; color: #555; }
        .table thead { background: #e9ecef; }
        .porcentaje-block { background: #fff3cd; border-radius: 12px; padding: 18px; margin-top: 24px; text-align: center; box-shadow: 0 1px 6px #0001; }
        .porcentaje-block .display-5 { font-weight: 700; }
        @media (max-width: 700px) {
            .dashboard-box { padding: 10px; }
            .resumen-cards { flex-direction: column; gap: 10px; }
        }
    </style>
</head>
<body>
    <div class="dashboard-box">
        <div class="dashboard-header mb-3">
            <div class="dashboard-title">Portal Estudiantil</div>
            <div>
                <a href="seleccionar_estudiante.php" class="btn btn-outline-secondary btn-top">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
                <a href="bienvenido.php" class="btn btn-outline-primary btn-top"><i class="bi bi-camera"></i> Nuevo Escaneo</a>
                <a href="logout.php" class="btn btn-outline-danger btn-top"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
            </div>
        </div>
        <div class="dashboard-subtitle">Bienvenido, <b><?php echo $datos["nombre"]; ?></b></div>
        <div class="info-personal mb-4">
            <div class="row">
                <div class="col-md-3"><b>ID Estudiante:</b> <?php echo $datos["id"]; ?></div>
                <div class="col-md-4"><b>Nombre Completo:</b> <?php echo $datos["nombre"]; ?></div>
                <div class="col-md-3"><b>Carrera:</b> <?php echo $datos["carrera"]; ?></div>
                <div class="col-md-2"><b>Semestre:</b> <?php echo $datos["semestre"]; ?></div>
            </div>
        </div>
        <div class="resumen-cards">
            <div class="resumen-card">
                <h4><?php echo $datos["total_clases"]; ?></h4>
                <span>Total Clases</span>
            </div>
            <div class="resumen-card">
                <h4><?php echo $datos["asistencias"]; ?></h4>
                <span>Asistencias</span>
            </div>
            <div class="resumen-card">
                <h4><?php echo $datos["faltas"]; ?></h4>
                <span>Faltas</span>
            </div>
            <div class="resumen-card">
                <h4><?php echo $datos["tardanzas"]; ?></h4>
                <span>Tardanzas</span>
            </div>
        </div>
        <h5 class="mb-3 mt-4">Historial de Asistencias</h5>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Materia</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Profesor</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datos["historial"] as $fila): ?>
                    <tr>
                        <td><?php echo $fila[0]; ?></td>
                        <td><?php echo $fila[1]; ?></td>
                        <td><?php echo $fila[2]; ?></td>
                        <td><?php echo $fila[3]; ?></td>
                        <td><?php echo estado_icono($fila[4]); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="porcentaje-block mt-4">
            <div class="display-5 text-<?php echo $datos["color_porcentaje"]; ?>">
                <?php echo $datos["porcentaje"]; ?>% <?php echo $icono_porcentaje; ?>
            </div>
            <div class="fw-bold"><?php echo $datos["mensaje"]; ?></div>
        </div>
    </div>
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</body>
</html>