<?php

session_start();


require_once __DIR__ .
    "/../Validaciones/validarFormulario.php";

require_once __DIR__ .
    "/../Config/conexionBDD.php";

require_once __DIR__ .
    "/../Datos/Curso/CursoDAO.php";


/* =====================================================
   COMPROBAR SESIÓN
===================================================== */

if (!isset($_SESSION["id_usuario"])) {

    header(
        "Location: ../Vistas/login.php?mensaje=no_autorizado"
    );

    exit;
}


/* =====================================================
   OBTENER ACCIÓN
===================================================== */

$accion = $_POST["accion"] ?? "";


/* =====================================================
   CREAR CURSO
===================================================== */

if ($accion === "crear") {


    if (
        !isset($_SESSION["rol"]) ||
        $_SESSION["rol"] !== "profesor"
    ) {

        header(
            "Location: ../Vistas/cursos.php?mensaje=no_autorizado"
        );

        exit;
    }


    $nombre = $_POST["nombre"] ?? "";

    $descripcion = $_POST["descripcion"] ?? "";


    $nombre = limpiarTexto($nombre);

    $descripcion = limpiarTexto($descripcion);


    if (!validarCampoObligatorio($nombre)) {

        header(
            "Location: ../Vistas/cursos.php?mensaje=nombre_obligatorio"
        );

        exit;
    }


    if (strlen($nombre) > 150) {

        header(
            "Location: ../Vistas/cursos.php?mensaje=nombre_largo"
        );

        exit;
    }


    $profesorId = $_SESSION["id_usuario"];


    $resultado = crearCurso(
        $conexion,
        $nombre,
        $descripcion,
        $profesorId
    );


    if (!$resultado) {

        header(
            "Location: ../Vistas/cursos.php?mensaje=error"
        );

        exit;
    }


    header(
        "Location: ../Vistas/cursos.php?mensaje=curso_creado"
    );

    exit;
}


/* =====================================================
   ASIGNAR ESTUDIANTE
===================================================== */

if ($accion === "inscribir") {


    if (
        !isset($_SESSION["rol"]) ||
        $_SESSION["rol"] !== "profesor"
    ) {

        header(
            "Location: ../Vistas/cursos.php?mensaje=no_autorizado"
        );

        exit;
    }


    $estudianteId = $_POST["estudiante_id"] ?? "";

    $cursoId = $_POST["curso_id"] ?? "";


    if (!validarId($estudianteId)) {

        header(
            "Location: ../Vistas/cursos.php?mensaje=estudiante_invalido"
        );

        exit;
    }


    if (!validarId($cursoId)) {

        header(
            "Location: ../Vistas/cursos.php?mensaje=curso_invalido"
        );

        exit;
    }


    $estudianteId = (int) $estudianteId;

    $cursoId = (int) $cursoId;


    $curso = obtenerCursoPorId(
        $conexion,
        $cursoId
    );


    if (!$curso) {

        header(
            "Location: ../Vistas/cursos.php?mensaje=curso_no_existe"
        );

        exit;
    }


    if (
        (int) $curso["profesor_id"] !==
        (int) $_SESSION["id_usuario"]
    ) {

        header(
            "Location: ../Vistas/cursos.php?mensaje=no_autorizado"
        );

        exit;
    }


    if (
        estudianteEstaInscrito(
            $conexion,
            $estudianteId,
            $cursoId
        )
    ) {

        header(
            "Location: ../Vistas/curso.php?id=" .
            $cursoId .
            "&mensaje=ya_inscrito"
        );

        exit;
    }


    $resultado = inscribirEstudiante(
        $conexion,
        $estudianteId,
        $cursoId
    );


    if (!$resultado) {

        header(
            "Location: ../Vistas/curso.php?id=" .
            $cursoId .
            "&mensaje=error_inscripcion"
        );

        exit;
    }


    header(
        "Location: ../Vistas/curso.php?id=" .
        $cursoId .
        "&mensaje=estudiante_inscrito"
    );

    exit;
}


/* =====================================================
   ACCIÓN NO VÁLIDA
===================================================== */

header(
    "Location: ../Vistas/cursos.php?mensaje=accion_invalida"
);

exit;