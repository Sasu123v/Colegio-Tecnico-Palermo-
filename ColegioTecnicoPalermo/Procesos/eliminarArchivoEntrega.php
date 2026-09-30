<?php

session_start();

require_once __DIR__ . "/../Config/conexionBDD.php";
require_once __DIR__ . "/../Datos/Archivo/ArchivoDAO.php";
require_once __DIR__ . "/../Datos/Entrega/EntregaDAO.php";


// =====================================================
// VERIFICAR SESIÓN
// =====================================================

if (!isset($_SESSION["id_usuario"])) {

    header(
        "Location: ../Vistas/login.php?mensaje=no_autorizado"
    );

    exit;
}


// =====================================================
// SOLO ESTUDIANTES
// =====================================================

if ($_SESSION["rol"] !== "estudiante") {

    header(
        "Location: ../Vistas/cursos.php?mensaje=no_autorizado"
    );

    exit;
}


// =====================================================
// OBTENER ID DE LA ENTREGA
// =====================================================

$idEntrega = $_GET["id"] ?? "";


// =====================================================
// VALIDAR ID
// =====================================================

if (!is_numeric($idEntrega) || $idEntrega <= 0) {

    header(
        "Location: ../Vistas/cursos.php?mensaje=entrega_invalida"
    );

    exit;
}


// =====================================================
// BUSCAR LA ENTREGA
// =====================================================

$sql = "
    SELECT
        entregas.*,
        tareas.id_tarea
    FROM entregas

    INNER JOIN tareas
        ON entregas.tarea_id = tareas.id_tarea

    WHERE entregas.id_entrega = ?
    AND entregas.estudiante_id = ?
";


$stmt = $conexion->prepare($sql);


if (!$stmt) {

    header(
        "Location: ../Vistas/cursos.php?mensaje=error"
    );

    exit;
}


$stmt->bind_param(
    "ii",
    $idEntrega,
    $_SESSION["id_usuario"]
);


$stmt->execute();


$resultado = $stmt->get_result();


$entrega = $resultado->fetch_assoc();


$stmt->close();


// =====================================================
// VERIFICAR QUE LA ENTREGA EXISTA
// =====================================================

if ($entrega === null) {

    header(
        "Location: ../Vistas/cursos.php?mensaje=no_autorizado"
    );

    exit;
}


// =====================================================
// VERIFICAR QUE TENGA ARCHIVO
// =====================================================

if (empty($entrega["id_archivo"])) {

    header(
        "Location: ../Vistas/tarea.php?id=" .
        $entrega["tarea_id"] .
        "&mensaje=archivo_inexistente"
    );

    exit;
}


// =====================================================
// OBTENER INFORMACIÓN DEL ARCHIVO
// =====================================================

$archivo = obtenerArchivoPorId(
    $conexion,
    $entrega["id_archivo"]
);


if ($archivo === null) {

    header(
        "Location: ../Vistas/tarea.php?id=" .
        $entrega["tarea_id"] .
        "&mensaje=archivo_inexistente"
    );

    exit;
}


// =====================================================
// QUITAR EL ARCHIVO DE LA ENTREGA
// =====================================================

$resultado = eliminarArchivoEntrega(
    $conexion,
    $idEntrega
);


if (!$resultado) {

    header(
        "Location: ../Vistas/tarea.php?id=" .
        $entrega["tarea_id"] .
        "&mensaje=error_eliminar_archivo"
    );

    exit;
}


// =====================================================
// ELIMINAR ARCHIVO FÍSICO
// =====================================================

$rutaFisica =
    __DIR__
    .
    "/../"
    .
    $archivo["ruta"];


if (file_exists($rutaFisica)) {

    unlink($rutaFisica);
}


// =====================================================
// ELIMINAR REGISTRO DE LA BASE DE DATOS
// =====================================================

$sqlEliminar = "
    DELETE FROM archivos
    WHERE id_archivo = ?
";


$stmtEliminar = $conexion->prepare(
    $sqlEliminar
);


if ($stmtEliminar) {

    $stmtEliminar->bind_param(
        "i",
        $entrega["id_archivo"]
    );

    $stmtEliminar->execute();

    $stmtEliminar->close();
}


// =====================================================
// VOLVER A LA TAREA
// =====================================================

header(
    "Location: ../Vistas/tarea.php?id=" .
    $entrega["tarea_id"] .
    "&mensaje=archivo_eliminado"
);

exit;

?>