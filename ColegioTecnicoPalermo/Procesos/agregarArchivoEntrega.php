<?php

session_start();

require_once __DIR__ . "/../Config/conexionBDD.php";
require_once __DIR__ . "/../Datos/Archivo/ArchivoDAO.php";
require_once __DIR__ . "/../Datos/Entrega/EntregaDAO.php";


/* =====================================================
   VERIFICAR SESIÓN
===================================================== */

if (!isset($_SESSION["id_usuario"])) {

    header(
        "Location: ../Vistas/login.php?mensaje=no_autorizado"
    );

    exit;
}


/* =====================================================
   VERIFICAR QUE SEA ESTUDIANTE
===================================================== */

if ($_SESSION["rol"] !== "estudiante") {

    header(
        "Location: ../Vistas/cursos.php?mensaje=no_autorizado"
    );

    exit;
}


/* =====================================================
   RECIBIR ENTREGA
===================================================== */

$idEntrega = $_POST["entrega_id"] ?? "";


if (!is_numeric($idEntrega) || $idEntrega <= 0) {

    header(
        "Location: ../Vistas/cursos.php?mensaje=entrega_invalida"
    );

    exit;
}


/* =====================================================
   OBTENER ENTREGA
===================================================== */

$sql = "
    SELECT *
    FROM entregas
    WHERE id_entrega = ?
    AND estudiante_id = ?
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


/* =====================================================
   VERIFICAR QUE EXISTA
===================================================== */

if ($entrega === null) {

    header(
        "Location: ../Vistas/cursos.php?mensaje=no_autorizado"
    );

    exit;
}


/* =====================================================
   VERIFICAR SI YA TIENE ARCHIVO
===================================================== */

if (!empty($entrega["id_archivo"])) {

    header(
        "Location: ../Vistas/tarea.php?id=" .
        $entrega["tarea_id"] .
        "&mensaje=archivo_ya_existe"
    );

    exit;
}


/* =====================================================
   VERIFICAR ARCHIVO
===================================================== */

if (
    !isset($_FILES["archivo"])
    ||
    $_FILES["archivo"]["error"] === UPLOAD_ERR_NO_FILE
) {

    header(
        "Location: ../Vistas/tarea.php?id=" .
        $entrega["tarea_id"] .
        "&mensaje=no_archivo"
    );

    exit;
}


$archivo = $_FILES["archivo"];


/* =====================================================
   ERROR DE SUBIDA
===================================================== */

if ($archivo["error"] !== UPLOAD_ERR_OK) {

    header(
        "Location: ../Vistas/tarea.php?id=" .
        $entrega["tarea_id"] .
        "&mensaje=error_archivo"
    );

    exit;
}


/* =====================================================
   TAMAÑO MÁXIMO
===================================================== */

$maximo = 10 * 1024 * 1024;


if ($archivo["size"] > $maximo) {

    header(
        "Location: ../Vistas/tarea.php?id=" .
        $entrega["tarea_id"] .
        "&mensaje=archivo_grande"
    );

    exit;
}


/* =====================================================
   EXTENSIONES PERMITIDAS
===================================================== */

$extensionesPermitidas = [
    "pdf",
    "doc",
    "docx",
    "xls",
    "xlsx",
    "ppt",
    "pptx",
    "jpg",
    "jpeg",
    "png"
];


$nombreOriginal = basename(
    $archivo["name"]
);


$extension = strtolower(
    pathinfo(
        $nombreOriginal,
        PATHINFO_EXTENSION
    )
);


if (
    !in_array(
        $extension,
        $extensionesPermitidas
    )
) {

    header(
        "Location: ../Vistas/tarea.php?id=" .
        $entrega["tarea_id"] .
        "&mensaje=tipo_archivo_no_permitido"
    );

    exit;
}


/* =====================================================
   CREAR NOMBRE ÚNICO
===================================================== */

$nombreGuardado =
    uniqid(
        "entrega_",
        true
    )
    .
    "."
    .
    $extension;


/* =====================================================
   CARPETA
===================================================== */

$carpeta =
    __DIR__
    .
    "/../Archivos/entregas/";


if (!is_dir($carpeta)) {

    mkdir(
        $carpeta,
        0777,
        true
    );
}


/* =====================================================
   RUTA FÍSICA
===================================================== */

$rutaFisica =
    $carpeta
    .
    $nombreGuardado;


/* =====================================================
   MOVER ARCHIVO
===================================================== */

if (
    !move_uploaded_file(
        $archivo["tmp_name"],
        $rutaFisica
    )
) {

    header(
        "Location: ../Vistas/tarea.php?id=" .
        $entrega["tarea_id"] .
        "&mensaje=error_guardado_archivo"
    );

    exit;
}


/* =====================================================
   RUTA PARA BDD
===================================================== */

$rutaBDD =
    "Archivos/entregas/"
    .
    $nombreGuardado;


/* =====================================================
   GUARDAR ARCHIVO EN BDD
===================================================== */

$idArchivo = guardarArchivo(

    $conexion,

    $_SESSION["id_usuario"],

    $nombreOriginal,

    $nombreGuardado,

    $rutaBDD,

    $archivo["type"],

    $archivo["size"]

);


if (!$idArchivo) {

    if (file_exists($rutaFisica)) {
        unlink($rutaFisica);
    }

    header(
        "Location: ../Vistas/tarea.php?id=" .
        $entrega["tarea_id"] .
        "&mensaje=error_archivo_bdd"
    );

    exit;
}


/* =====================================================
   VINCULAR ARCHIVO CON ENTREGA
===================================================== */

$resultado = actualizarArchivoEntrega(

    $conexion,

    $idEntrega,

    $idArchivo

);


/* =====================================================
   SI FALLÓ LA VINCULACIÓN
===================================================== */

if (!$resultado) {

    if (file_exists($rutaFisica)) {
        unlink($rutaFisica);
    }

    header(
        "Location: ../Vistas/tarea.php?id=" .
        $entrega["tarea_id"] .
        "&mensaje=error_archivo"
    );

    exit;
}


/* =====================================================
   ÉXITO
===================================================== */

header(
    "Location: ../Vistas/tarea.php?id=" .
    $entrega["tarea_id"] .
    "&mensaje=archivo_agregado"
);

exit;

?>