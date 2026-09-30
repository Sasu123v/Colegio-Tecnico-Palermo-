<?php

session_start();


require_once __DIR__ .
    "/../Config/conexionBDD.php";

require_once __DIR__ .
    "/../Datos/Curso/CursoDAO.php";


/* =====================================================
   COMPROBAR SESIÓN
===================================================== */

if (!isset($_SESSION["id_usuario"])) {

    header(
        "Location: login.php?mensaje=no_autorizado"
    );

    exit;
}


$idUsuario = $_SESSION["id_usuario"];

$rol = $_SESSION["rol"];


/* =====================================================
   OBTENER CURSOS
===================================================== */

if ($rol === "profesor") {

    $resultado = obtenerCursosPorProfesor(
        $conexion,
        $idUsuario
    );

} else {

    $resultado = obtenerCursosPorEstudiante(
        $conexion,
        $idUsuario
    );
}


/* =====================================================
   MENSAJE
===================================================== */

$mensaje = $_GET["mensaje"] ?? "";

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Mis cursos - Classroom
    </title>

    <link
        rel="stylesheet"
        href="../CSS/estilos.css"
    >

</head>


<body>


<header class="navbar">


    <a
        href="inicio.php"
        class="logo"
    >

        <span class="logo-mark">
            CTP
        </span>


        <span class="logo-text">
            Colegio Técnico Palermo
        </span>

    </a>


    <nav class="nav-links">

        <a href="inicio.php">
            Inicio
        </a>
    </nav>





</header>


<main>


<section class="cursos-container">


    <div class="curso-detalle-header">

        <h1>
            Mis cursos
        </h1>


        <p>
            Consulta tus cursos y accede rápidamente
            a sus actividades.
        </p>

    </div>


    <?php if ($mensaje === "curso_creado"): ?>

        <div class="mensaje mensaje-exito">

            Curso creado correctamente.

        </div>

    <?php endif; ?>


    <?php if ($mensaje === "nombre_obligatorio"): ?>

        <div class="mensaje mensaje-error">

            El nombre del curso es obligatorio.

        </div>

    <?php endif; ?>


    <?php if ($mensaje === "nombre_largo"): ?>

        <div class="mensaje mensaje-error">

            El nombre del curso no puede superar
            los 150 caracteres.

        </div>

    <?php endif; ?>


    <?php if ($mensaje === "error"): ?>

        <div class="mensaje mensaje-error">

            Ocurrió un error al guardar el curso.

        </div>

    <?php endif; ?>


    <?php if ($mensaje === "no_autorizado"): ?>

        <div class="mensaje mensaje-error">

            No tienes permiso para realizar esta acción.

        </div>

    <?php endif; ?>


    <?php if ($rol === "profesor"): ?>


        <div class="form-card">


            <span class="section-label">
                PROFESOR
            </span>


            <h2>
                Crear nuevo curso
            </h2>


            <br>


            <form
                action="../Procesos/guardarCurso.php"
                method="POST"
            >


                <input
                    type="hidden"
                    name="accion"
                    value="crear"
                >


                <div class="form-group">


                    <label for="nombre">
                        Nombre del curso
                    </label>


                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        maxlength="150"
                        required
                    >


                </div>


                <div class="form-group">


                    <label for="descripcion">
                        Descripción
                    </label>


                    <textarea
                        id="descripcion"
                        name="descripcion"
                    ></textarea>


                </div>


                <button
                    type="submit"
                    class="form-button"
                >
                    Crear curso
                </button>


            </form>


        </div>


    <?php endif; ?>


    <div class="cursos-grid">


        <?php if (
            $resultado &&
            $resultado->num_rows > 0
        ): ?>


            <?php while (
                $curso =
                $resultado->fetch_assoc()
            ): ?>


                <article class="curso-card">


                    <div class="curso-icon">
                        CTP
                    </div>


                    <h2>

                        <?= htmlspecialchars(
                            $curso["nombre"],
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>

                    </h2>


                    <p>

                        <?= htmlspecialchars(
                            $curso["descripcion"] ??
                            "Sin descripción.",
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>

                    </p>


                    <?php if ($rol === "estudiante"): ?>


                        <div class="curso-info">

                            Profesor:

                            <?= htmlspecialchars(
                                $curso["profesor_nombre"] .
                                " " .
                                $curso["profesor_apellido"],
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>

                        </div>


                    <?php endif; ?>


                    <a
                        href="curso.php?id=<?= (int) $curso["id_curso"] ?>"
                        class="curso-btn"
                    >
                        Ver curso
                    </a>


                </article>


            <?php endwhile; ?>


        <?php else: ?>


            <div class="mensaje">


                <?php if ($rol === "profesor"): ?>

                    Todavía no has creado ningún curso.

                <?php else: ?>

                    Todavía no estás inscrito
                    en ningún curso.

                <?php endif; ?>


            </div>


        <?php endif; ?>


    </div>


</section>


</main>


</body>

</html>