<?php

session_start();

require_once __DIR__ . "/../Config/conexionBDD.php";
require_once __DIR__ . "/../Datos/Entrega/EntregaDAO.php";
require_once __DIR__ . "/../Datos/Tarea/TareaDAO.php";
require_once __DIR__ . "/../Datos/Curso/CursoDAO.php";
require_once __DIR__ . "/../Validaciones/validarFormulario.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../Vistas/login.php?mensaje=no_autorizado");
    exit;
}

if ($_SESSION["rol"] !== "profesor") {
    header("Location: ../Vistas/cursos.php?mensaje=no_autorizado");
    exit;
}

$entregaId = $_POST["entrega_id"] ?? "";
$tareaId = $_POST["tarea_id"] ?? "";
$calificacion = $_POST["calificacion"] ?? "";

if (
    !validarId($entregaId) ||
    !validarId($tareaId)
) {
    header("Location: ../Vistas/cursos.php?mensaje=datos_invalidos");
    exit;
}

if (
    !is_numeric($calificacion) ||
    $calificacion < 0 ||
    $calificacion > 100
) {
    header(
        "Location: ../Vistas/entregas.php?tarea_id=" .
        $tareaId .
        "&mensaje=calificacion_invalida"
    );

    exit;
}

$tarea = obtenerTareaPorId(
    $conexion,
    $tareaId
);

if ($tarea === null) {
    header("Location: ../Vistas/cursos.php?mensaje=tarea_inexistente");
    exit;
}

$curso = obtenerCursoPorId(
    $conexion,
    $tarea["curso_id"]
);

if (
    $curso === null ||
    $curso["profesor_id"] != $_SESSION["id_usuario"]
) {
    header("Location: ../Vistas/cursos.php?mensaje=no_autorizado");
    exit;
}

$resultado = calificarEntrega(
    $conexion,
    $entregaId,
    $calificacion
);

if ($resultado) {

    header(
        "Location: ../Vistas/entregas.php?tarea_id=" .
        $tareaId .
        "&mensaje=calificacion_exitosa"
    );

    exit;
}

header(
    "Location: ../Vistas/entregas.php?tarea_id=" .
    $tareaId .
    "&mensaje=error_calificacion"
);

exit;