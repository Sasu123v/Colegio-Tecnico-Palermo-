<?php

session_start();

require_once __DIR__ . "/../Config/conexionBDD.php";
require_once __DIR__ . "/../Datos/Tarea/TareaDAO.php";
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
   VERIFICAR QUE SEA PROFESOR
===================================================== */

if ($_SESSION["rol"] !== "profesor") {

    header(
        "Location: ../Vistas/cursos.php?mensaje=no_autorizado"
    );

    exit;
}


/* =====================================================
   RECIBIR DATOS
===================================================== */

$cursoId = $_POST["curso_id"] ?? "";

$titulo = trim(
    $_POST["titulo"] ?? ""
);

$descripcion = trim(
    $_POST["descripcion"] ?? ""
);

$fechaEntrega = $_POST["fecha_entrega"] ?? "";


/* =====================================================
   VALIDAR CURSO
===================================================== */

if (!validarId($cursoId)) {

    header(
        "Location: ../Vistas/cursos.php?mensaje=curso_invalido"
    );

    exit;
}


/* =====================================================
   VALIDAR TÍTULO
===================================================== */

if ($titulo === "") {

    header(
        "Location: ../Vistas/curso.php?id=" .
        $cursoId .
        "&mensaje=titulo_vacio"
    );

    exit;
}


if (strlen($titulo) > 200) {

    header(
        "Location: ../Vistas/curso.php?id=" .
        $cursoId .
        "&mensaje=titulo_largo"
    );

    exit;
}


/* =====================================================
   VALIDAR FECHA
===================================================== */

if ($fechaEntrega === "") {

    header(
        "Location: ../Vistas/curso.php?id=" .
        $cursoId .
        "&mensaje=fecha_vacia"
    );

    exit;
}


/* =====================================================
   VERIFICAR CURSO
===================================================== */

$curso = obtenerCursoPorId(
    $conexion,
    $cursoId
);

if ($curso === null) {

    header(
        "Location: ../Vistas/cursos.php?mensaje=curso_inexistente"
    );

    exit;
}


/* =====================================================
   VERIFICAR QUE EL PROFESOR SEA EL DUEÑO DEL CURSO
===================================================== */

if (
    $curso["profesor_id"]
    !=
    $_SESSION["id_usuario"]
) {

    header(
        "Location: ../Vistas/cursos.php?mensaje=no_autorizado"
    );

    exit;
}


/* =====================================================
   ARCHIVO
===================================================== */

$idArchivo = null;


/*
 * Solo procesamos archivo si el profesor seleccionó uno.
 */

if (
    isset($_FILES["archivo"])
    &&
    $_FILES["archivo"]["error"] !== UPLOAD_ERR_NO_FILE
) {

    $archivo = $_FILES["archivo"];


    /* ---------------------------------------------
       ERROR DE SUBIDA
    --------------------------------------------- */

    if ($archivo["error"] !== UPLOAD_ERR_OK) {

        header(
            "Location: ../Vistas/curso.php?id=" .
            $cursoId .
            "&mensaje=error_archivo"
        );

        exit;
    }


    /* ---------------------------------------------
       TAMAÑO MÁXIMO: 10 MB
    --------------------------------------------- */

    $maximo = 10 * 1024 * 1024;

    if ($archivo["size"] > $maximo) {

        header(
            "Location: ../Vistas/curso.php?id=" .
            $cursoId .
            "&mensaje=archivo_grande"
        );

        exit;
    }


    /* ---------------------------------------------
       EXTENSIONES PERMITIDAS
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
            "Location: ../Vistas/curso.php?id=" .
            $cursoId .
            "&mensaje=tipo_archivo_no_permitido"
        );

        exit;
    }


    /* ---------------------------------------------
       CREAR NOMBRE ÚNICO
    --------------------------------------------- */

    $nombreGuardado =
        uniqid(
            "tarea_",
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
        "/../Archivos/materiales/";


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
            "Location: ../Vistas/curso.php?id=" .
            $cursoId .
            "&mensaje=error_guardado_archivo"
        );

        exit;
    }


    /* ---------------------------------------------
       RUTA PARA BASE DE DATOS
    --------------------------------------------- */

    $rutaBDD =
        "Archivos/materiales/"
        .
        $nombreGuardado;


    /* ---------------------------------------------
       DATOS DEL ARCHIVO
    --------------------------------------------- */

    $idUsuario =
        $_SESSION["id_usuario"];

    $tipo =
        $archivo["type"];

    $tamano =
        $archivo["size"];


    /* ---------------------------------------------
       GUARDAR EN BDD
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


    /* ---------------------------------------------
       ERROR BDD
    --------------------------------------------- */

    if (!$idArchivo) {

        if (
            file_exists($rutaFisica)
        ) {

            unlink($rutaFisica);
        }


        header(
            "Location: ../Vistas/curso.php?id=" .
            $cursoId .
            "&mensaje=error_archivo_bdd"
        );

        exit;
    }
}


/* =====================================================
   CREAR TAREA
===================================================== */

$resultado = crearTarea(

    $conexion,

    $cursoId,

    $titulo,

    $descripcion,

    $fechaEntrega,

    $idArchivo

);


/* =====================================================
   RESULTADO
===================================================== */

if ($resultado) {

    header(
        "Location: ../Vistas/curso.php?id=" .
        $cursoId .
        "&mensaje=tarea_creada"
    );

    exit;
}


/* =====================================================
   SI FALLÓ LA TAREA
===================================================== */


/*
 * Si la tarea no pudo crearse pero el archivo
 * sí se guardó, eliminamos el archivo para
 * no dejar archivos huérfanos.
 */

if (
    $idArchivo !== null
) {

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
    "Location: ../Vistas/curso.php?id=" .
    $cursoId .
    "&mensaje=error_tarea"
);

exit;

?>