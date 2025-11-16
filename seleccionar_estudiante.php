<?php
// filepath: c:\Lista-de-asistencia-con-reconocimiento-facial-usando-Python-main\seleccionar_estudiante.php
// Datos de ejemplo (puedes reemplazar por consulta a la BD)
$estudiantes = [
    ["id"=>"EST001", "nombre"=>"Melany De Los Angeles Ortiz Ruiz", "carrera"=>"Ingeniería en Sistemas", "semestre"=>"7mo Semestre", "asistencia"=>80, "estado"=>"Excelente"],
    ["id"=>"EST002", "nombre"=>"Sofia Lopez Yepes", "carrera"=>"Ingeniería en Sistemas", "semestre"=>"7mo Semestre", "asistencia"=>75, "estado"=>"Excelente"],
    ["id"=>"EST003", "nombre"=>"Sara Sofia Garzon Fontecha", "carrera"=>"Ingeniería en Sistemas", "semestre"=>"7mo Semestre", "asistencia"=>70, "estado"=>"Bajo"],
    ["id"=>"EST004", "nombre"=>"Luis Martín Fernández", "carrera"=>"Ingeniería en Sistemas", "semestre"=>"7mo Semestre", "asistencia"=>88, "estado"=>"Excelente"],
    ["id"=>"EST005", "nombre"=>"Sofía Chen Wang", "carrera"=>"Ingeniería en Sistemas", "semestre"=>"7mo Semestre", "asistencia"=>58, "estado"=>"Bajo"],
    ["id"=>"EST006", "nombre"=>"Diego Rojas Vargas", "carrera"=>"Ingeniería en Sistemas", "semestre"=>"7mo Semestre", "asistencia"=>94, "estado"=>"Excelente"],
];
function estado_icono($estado) {
    if ($estado === "Excelente") return "<span class='badge bg-success'><i class='bi bi-emoji-smile'></i> Excelente</span>";
    if ($estado === "Bajo") return "<span class='badge bg-warning text-dark'><i class='bi bi-exclamation-triangle'></i> Bajo</span>";
    return $estado;
}
function iniciales($nombre) {
    $parts = explode(" ", $nombre);
    $ini = "";
    foreach ($parts as $p) {
        if ($p !== "") $ini .= strtoupper($p[0]);
        if (strlen($ini) == 2) break;
    }
    return $ini;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Estudiante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background: #f6f8fa; }
        .panel-box { background: #fff; border-radius: 16px; box-shadow: 0 2px 12px #0001; padding: 32px; margin: 40px auto; max-width: 900px; }
        .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .search-box { margin-bottom: 12px; }
        .student-card { display: flex; align-items: center; background: #f8f9fa; border-radius: 12px; padding: 12px; margin-bottom: 12px; box-shadow: 0 1px 6px #0001; }
        .student-initials { width: 38px; height: 38px; border-radius: 50%; background: #007bff; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; font-weight: bold; margin-right: 14px; }
        .student-info { flex: 1; }
        .student-actions { margin-left: 10px; }
        .student-name { font-weight: 600; font-size: 1rem; }
        .student-meta { color: #555; font-size: 0.85rem; }
        .btn-portal { font-size: 0.9rem; padding: 4px 10px; }
        .btn-buscar { font-size: 0.95rem; padding: 6px 18px; }
        .search-row { display: flex; gap: 8px; }
        @media (max-width: 700px) {
            .panel-box { padding: 10px; }
            .student-card { flex-direction: column; align-items: flex-start; }
            .student-actions { margin-left: 0; margin-top: 10px; }
            .search-row { flex-direction: column; gap: 6px; }
        }
    </style>
</head>
<body>
    <div class="panel-box">
        <div class="panel-header">
            <div>
                <a href="admin.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver</a>
            </div>
        </div>
        <form id="form-buscar" autocomplete="off">
            <div class="search-box">
                <div class="search-row">
                    <input type="text" id="buscador" class="form-control form-control-sm" placeholder="Ejemplo: EST001 o María González" aria-label="Buscar estudiante">
                    <button type="submit" class="btn btn-primary btn-buscar"><i class="bi bi-search"></i> Seleccionar Estudiante</button>
                </div>
                <small class="text-muted">Busca por ID de estudiante o nombre completo para acceder a su portal.</small>
            </div>
        </form>
        <div id="lista-estudiantes">
            <?php foreach ($estudiantes as $est): ?>
            <div class="student-card" data-nombre="<?php echo strtolower($est["nombre"]); ?>" data-id="<?php echo strtolower($est["id"]); ?>">
                <div class="student-initials"><?php echo iniciales($est["nombre"]); ?></div>
                <div class="student-info">
                    <div class="student-name"><?php echo $est["nombre"]; ?> (<?php echo $est["id"]; ?>)</div>
                    <div class="student-meta">
                        <?php if ($est["carrera"]) echo $est["carrera"] . " — "; ?>
                        <?php if ($est["semestre"]) echo $est["semestre"] . " — "; ?>
                        <?php echo $est["asistencia"]; ?>% Asistencia —
                        <?php echo estado_icono($est["estado"]); ?>
                    </div>
                </div>
                <div class="student-actions">
                    <a href="portal_estudiantil.php?id=<?php echo $est["id"]; ?>" class="btn btn-outline-info btn-portal">
                        <i class="bi bi-journal-check"></i> Portal asistencias
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
    // Buscador funcional
    const estudiantes = document.querySelectorAll('.student-card');
    const buscador = document.getElementById('buscador');
    const formBuscar = document.getElementById('form-buscar');

    function filtrarEstudiantes() {
        const valor = buscador.value.trim().toLowerCase();
        estudiantes.forEach(card => {
            const nombre = card.getAttribute('data-nombre');
            const id = card.getAttribute('data-id');
            if (valor === "" || nombre.includes(valor) || id.includes(valor)) {
                card.style.display = "";
            } else {
                card.style.display = "none";
            }
        });
    }

    buscador.addEventListener('input', filtrarEstudiantes);

    formBuscar.addEventListener('submit', function(e) {
        e.preventDefault();
        filtrarEstudiantes();
    });
    </script>
</body>
</html>