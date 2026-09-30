<?php

session_start();

require_once __DIR__ . "/../Config/conexionBDD.php";
require_once __DIR__ . "/../Datos/Tarea/TareaDAO.php";
require_once __DIR__ . "/../Datos/Curso/CursoDAO.php";
require_once __DIR__ . "/../Validaciones/validarFormulario.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php?mensaje=no_autorizado");
    exit;
}

$cursoId = $_GET["curso_id"] ?? "";

if (!validarId($cursoId)) {
    header("Location: cursos.php?mensaje=curso_invalido");
    exit;
}

$curso = obtenerCursoPorId(
    $conexion,
    $cursoId
);

if ($curso === null) {
    header("Location: cursos.php?mensaje=curso_inexistente");
    exit;
}

$esProfesor = $_SESSION["rol"] === "profesor";

if ($esProfesor) {

    if ($curso["profesor_id"] != $_SESSION["id_usuario"]) {
        header("Location: cursos.php?mensaje=no_autorizado");
        exit;
    }

} else {

    if (!estudianteEstaInscrito(
        $conexion,
        $_SESSION["id_usuario"],
        $cursoId
    )) {
        header("Location: cursos.php?mensaje=no_autorizado");
        exit;
    }
}

$tareas = obtenerTareasPorCurso(
    $conexion,
    $cursoId
);

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Tareas | Colegio Técnico Palermo</title>

    <link
        rel="stylesheet"
        href="../CSS/estilos.css"
    >

</head>

<body>

<nav class="navbar">

    <a href="inicio.php" class="logo">

        <span class="logo-mark">CTP</span>

        <span class="logo-text">
            Colegio Técnico Palermo
        </span>

    </a>

    <div class="nav-links">

        <a href="inicio.php">Inicio</a>
        <a href="cursos.php">Cursos</a>
        <a href="perfil.php">Mi perfil</a>

    </div>


</nav>


<main>

<section class="cursos-container">

    <div class="cursos-header">

        <h1>
            Tareas
        </h1>

        <p>
            Curso:
            <?php echo htmlspecialchars($curso["nombre"]); ?>
        </p>

    </div>


    <?php if ($tareas && $tareas->num_rows > 0): ?>

        <div class="cursos-grid">

            <?php while ($tarea = $tareas->fetch_assoc()): ?>

                <div class="curso-card">

                    <div class="curso-icon">
                        T
                    </div>

                    <h2>
                        <?php
                        echo htmlspecialchars(
                            $tarea["titulo"]
                        );
                        ?>
                    </h2>

                    <p>
                        <?php
                        echo htmlspecialchars(
                            $tarea["descripcion"] ?? ""
                        );
                        ?>
                    </p>

                    <div class="curso-info">

                        Fecha de entrega:

                        <?php
                        echo htmlspecialchars(
                            $tarea["fecha_entrega"]
                        );
                        ?>

                    </div>

                    <a
                        href="tarea.php?id=<?php echo $tarea["id_tarea"]; ?>"
                        class="curso-btn"
                    >
                        Ver tarea
                    </a>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="mensaje">
            Este curso todavía no tiene tareas.
        </div>

    <?php endif; ?>

</section>

</main>

<footer>

    <p>
        © Colegio Técnico Palermo
    </p>

</footer>

</body>

</html>