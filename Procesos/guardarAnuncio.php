<?php

session_start();

require_once __DIR__ . "/../Config/conexionBDD.php";
require_once __DIR__ . "/../Datos/Anuncio/AnuncioDAO.php";
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

$cursoId = $_POST["curso_id"] ?? "";
$titulo = trim($_POST["titulo"] ?? "");
$contenido = trim($_POST["contenido"] ?? "");

if (!validarId($cursoId)) {
    header("Location: ../Vistas/cursos.php?mensaje=curso_invalido");
    exit;
}

if ($titulo === "" || $contenido === "") {
    header(
        "Location: ../Vistas/curso.php?id=" .
        $cursoId .
        "&mensaje=anuncio_incompleto"
    );

    exit;
}

$curso = obtenerCursoPorId(
    $conexion,
    $cursoId
);

if ($curso === null) {
    header("Location: ../Vistas/cursos.php?mensaje=curso_inexistente");
    exit;
}

if ($curso["profesor_id"] != $_SESSION["id_usuario"]) {
    header("Location: ../Vistas/cursos.php?mensaje=no_autorizado");
    exit;
}

$resultado = crearAnuncio(
    $conexion,
    $cursoId,
    $_SESSION["id_usuario"],
    $titulo,
    $contenido
);

if ($resultado) {

    header(
        "Location: ../Vistas/curso.php?id=" .
        $cursoId .
        "&mensaje=anuncio_creado"
    );

    exit;
}

header(
    "Location: ../Vistas/curso.php?id=" .
    $cursoId .
    "&mensaje=error_anuncio"
);

exit;