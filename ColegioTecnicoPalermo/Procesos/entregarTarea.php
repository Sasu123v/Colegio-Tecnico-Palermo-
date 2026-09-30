<?php

session_start();

require_once __DIR__ . "/../Config/conexionBDD.php";
require_once __DIR__ . "/../Datos/Tarea/TareaDAO.php";
require_once __DIR__ . "/../Datos/Entrega/EntregaDAO.php";
require_once __DIR__ . "/../Datos/Archivo/ArchivoDAO.php";
require_once __DIR__ . "/../Datos/Curso/CursoDAO.php";
require_once __DIR__ . "/../Validaciones/validarFormulario.php";


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
   VERIFICAR ESTUDIANTE
===================================================== */

if ($_SESSION["rol"] !== "estudiante") {

    header(
        "Location: ../Vistas/cursos.php?mensaje=no_autorizado"
    );

    exit;
}


/* =====================================================
   DATOS
===================================================== */

$tareaId = $_POST["tarea_id"] ?? "";

$respuesta = trim(
    $_POST["respuesta"] ?? ""
);


/* =====================================================
   VALIDAR TAREA
===================================================== */

if (!validarId($tareaId)) {

    header(
        "Location: ../Vistas/cursos.php?mensaje=tarea_invalida"
    );

    exit;
}


/* =====================================================
   VALIDAR RESPUESTA
===================================================== */

if ($respuesta === "") {

    header(
        "Location: ../Vistas/tarea.php?id=" .
        $tareaId .
        "&mensaje=respuesta_vacia"
    );

    exit;
}


/* =====================================================
   OBTENER TAREA
===================================================== */

$tarea = obtenerTareaPorId(
    $conexion,
    $tareaId
);

if ($tarea === null) {

    header(
        "Location: ../Vistas/cursos.php?mensaje=tarea_inexistente"
    );

    exit;
}


/* =====================================================
   VERIFICAR INSCRIPCIÓN
===================================================== */

if (!estudianteEstaInscrito(
    $conexion,
    $_SESSION["id_usuario"],
    $tarea["curso_id"]
)) {

    header(
        "Location: ../Vistas/cursos.php?mensaje=no_autorizado"
    );

    exit;
}


/* =====================================================
   VERIFICAR SI YA ENTREGÓ
===================================================== */

$entregaExistente = obtenerEntregaEstudiante(
    $conexion,
    $tareaId,
    $_SESSION["id_usuario"]
);

if ($entregaExistente !== null) {

    header(
        "Location: ../Vistas/tarea.php?id=" .
        $tareaId .
        "&mensaje=ya_entregada"
    );

    exit;
}


/* =====================================================
   ARCHIVO
===================================================== */

$idArchivo = null;

if (
    isset($_FILES["archivo"])
    &&
    $_FILES["archivo"]["error"] !== UPLOAD_ERR_NO_FILE
) {

    $archivo = $_FILES["archivo"];


    /* ---------------------------------------------
       ERROR
    --------------------------------------------- */

    if ($archivo["error"] !== UPLOAD_ERR_OK) {

        header(
            "Location: ../Vistas/tarea.php?id=" .
            $tareaId .
            "&mensaje=error_archivo"
        );

        exit;
    }


    /* ---------------------------------------------
       TAMAÑO
    --------------------------------------------- */

    $maximo = 10 * 1024 * 1024;

    if ($archivo["size"] > $maximo) {

        header(
            "Location: ../Vistas/tarea.php?id=" .
            $tareaId .
            "&mensaje=archivo_grande"
        );

        exit;
    }


    /* ---------------------------------------------
       EXTENSIONES
    --------------------------------------------- */

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
            $tareaId .
            "&mensaje=tipo_archivo_no_permitido"
        );

        exit;
    }


    /* ---------------------------------------------
       NOMBRE ÚNICO
    --------------------------------------------- */

    $nombreGuardado =
        uniqid(
            "entrega_",
            true
        )
        .
        "."
        .
        $extension;


    /* ---------------------------------------------
       CARPETA
    --------------------------------------------- */

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


    /* ---------------------------------------------
       RUTA FÍSICA
    --------------------------------------------- */

    $rutaFisica =
        $carpeta
        .
        $nombreGuardado;


    /* ---------------------------------------------
       MOVER ARCHIVO
    --------------------------------------------- */

    if (
        !move_uploaded_file(
            $archivo["tmp_name"],
            $rutaFisica
        )
    ) {

        header(
            "Location: ../Vistas/tarea.php?id=" .
            $tareaId .
            "&mensaje=error_guardado_archivo"
        );

        exit;
    }


    /* ---------------------------------------------
       RUTA BDD
    --------------------------------------------- */

    $rutaBDD =
        "Archivos/entregas/"
        .
        $nombreGuardado;


    /* ---------------------------------------------
       DATOS
    --------------------------------------------- */

    $idUsuario =
        $_SESSION["id_usuario"];

    $tipo =
        $archivo["type"];

    $tamano =
        $archivo["size"];


    /* ---------------------------------------------
       GUARDAR ARCHIVO
    --------------------------------------------- */

    $idArchivo = guardarArchivo(

        $conexion,

        $idUsuario,

        $nombreOriginal,

        $nombreGuardado,

        $rutaBDD,

        $tipo,

        $tamano

    );


    if (!$idArchivo) {

        if (
            file_exists($rutaFisica)
        ) {

            unlink($rutaFisica);
        }

        header(
            "Location: ../Vistas/tarea.php?id=" .
            $tareaId .
            "&mensaje=error_archivo_bdd"
        );

        exit;
    }
}


/* =====================================================
   CREAR ENTREGA
===================================================== */

$resultado = crearEntrega(

    $conexion,

    $tareaId,

    $_SESSION["id_usuario"],

    $respuesta,

    $idArchivo

);


/* =====================================================
   ÉXITO
===================================================== */

if ($resultado) {

    header(
        "Location: ../Vistas/tarea.php?id=" .
        $tareaId .
        "&mensaje=entrega_exitosa"
    );

    exit;
}


/* =====================================================
   SI FALLÓ
===================================================== */

if ($idArchivo !== null) {

    $archivo = obtenerArchivoPorId(
        $conexion,
        $idArchivo
    );

    if ($archivo !== null) {

        $rutaFisica =
            __DIR__
            .
            "/../"
            .
            $archivo["ruta"];

        if (
            file_exists($rutaFisica)
        ) {

            unlink($rutaFisica);
        }
    }
}


header(
    "Location: ../Vistas/tarea.php?id=" .
    $tareaId .
    "&mensaje=error_entrega"
);

exit;

?>