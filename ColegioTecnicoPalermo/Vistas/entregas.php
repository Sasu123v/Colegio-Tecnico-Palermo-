<?php

session_start();

require_once __DIR__ . "/../Config/conexionBDD.php";
require_once __DIR__ . "/../Datos/Tarea/TareaDAO.php";
require_once __DIR__ . "/../Datos/Entrega/EntregaDAO.php";
require_once __DIR__ . "/../Datos/Curso/CursoDAO.php";
require_once __DIR__ . "/../Validaciones/validarFormulario.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php?mensaje=no_autorizado");
    exit;
}

if ($_SESSION["rol"] !== "profesor") {
    header("Location: cursos.php?mensaje=no_autorizado");
    exit;
}

$tareaId = $_GET["tarea_id"] ?? "";

if (!validarId($tareaId)) {
    header("Location: cursos.php?mensaje=tarea_invalida");
    exit;
}

$tarea = obtenerTareaPorId(
    $conexion,
    $tareaId
);

if ($tarea === null) {
    header("Location: cursos.php?mensaje=tarea_inexistente");
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
    header("Location: cursos.php?mensaje=no_autorizado");
    exit;
}

$entregas = obtenerEntregasPorTarea(
    $conexion,
    $tareaId
);

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

    <title>Entregas | Colegio Técnico Palermo</title>

    <link
        rel="stylesheet"
        href="../CSS/estilos.css?v=6"
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

        <a href="inicio.php">
            Inicio
        </a>

        <a href="cursos.php">
            Cursos
        </a>
    </div>
</nav>


<main>

<div class="curso-detalle">

    <div class="curso-detalle-header">

        <p>
            Curso:
            <?php
            echo htmlspecialchars(
                $curso["nombre"]
            );
            ?>
        </p>

        <h1>
            Entregas
        </h1>

        <p>
            Tarea:
            <?php
            echo htmlspecialchars(
                $tarea["titulo"]
            );
            ?>
        </p>

    </div>


    <?php if ($mensaje === "calificacion_exitosa"): ?>

        <div class="mensaje mensaje-exito">
            Calificación guardada correctamente.
        </div>

    <?php elseif ($mensaje === "calificacion_invalida"): ?>

        <div class="mensaje mensaje-error">
            La calificación debe estar entre 0 y 100.
        </div>

    <?php elseif ($mensaje === "error_calificacion"): ?>

        <div class="mensaje mensaje-error">
            No se pudo guardar la calificación.
        </div>

    <?php endif; ?>


    <div class="curso-detalle-header">

        <h2>
            Entregas de estudiantes
        </h2>


        <?php if ($entregas && $entregas->num_rows > 0): ?>


            <?php while ($entrega = $entregas->fetch_assoc()): ?>

                <div class="estudiante-item">


                    <!-- =========================================
                         DATOS DEL ESTUDIANTE
                    ========================================== -->

                    <strong>

                        <?php

                        echo htmlspecialchars(
                            $entrega["nombre"] .
                            " " .
                            $entrega["apellido"]
                        );

                        ?>

                    </strong>


                    <span>

                        <?php

                        echo htmlspecialchars(
                            $entrega["correo"]
                        );

                        ?>

                    </span>


                    <!-- =========================================
                         RESPUESTA DEL ESTUDIANTE
                    ========================================== -->

                    <p>

                        <strong>
                            Respuesta:
                        </strong>

                    </p>


                    <p>

                        <?php

                        echo nl2br(
                            htmlspecialchars(
                                $entrega["respuesta"]
                            )
                        );

                        ?>

                    </p>


                    <!-- =========================================
                         ARCHIVO ADJUNTO
                    ========================================== -->

                    <?php if (!empty($entrega["id_archivo"])): ?>


                        <div class="archivo-entrega">


                            <div class="archivo-entrega-icono">
                                📎
                            </div>


                            <div class="archivo-entrega-info">

                                <strong>
                                    Archivo adjunto
                                </strong>

                                <span>

                                    <?php

                                    echo htmlspecialchars(
                                        $entrega["nombre_original"]
                                    );

                                    ?>

                                </span>

                            </div>


                            <div class="archivo-entrega-acciones">

                                <a
                                    href="../<?php
                                        echo htmlspecialchars(
                                            $entrega["ruta"]
                                        );
                                    ?>"
                                    target="_blank"
                                    class="archivo-ver-btn"
                                >
                                    Ver archivo
                                </a>

                            </div>


                        </div>


                    <?php else: ?>


                        <div class="archivo-agregar-card">

                            <div class="archivo-agregar-icono">
                                📄
                            </div>

                            <div>

                                <strong>
                                    Sin archivo adjunto
                                </strong>

                                <span>
                                    El estudiante no adjuntó ningún archivo.
                                </span>

                            </div>

                        </div>


                    <?php endif; ?>


                    <!-- =========================================
                         CALIFICACIÓN
                    ========================================== -->

                    <p>

                        Calificación actual:

                        <?php

                        if ($entrega["calificacion"] !== null) {

                            echo htmlspecialchars(
                                $entrega["calificacion"]
                            );

                        } else {

                            echo "Sin calificar";

                        }

                        ?>

                    </p>


                    <form
                        action="../Procesos/calificarEntrega.php"
                        method="POST"
                    >

                        <input
                            type="hidden"
                            name="entrega_id"
                            value="<?php
                                echo $entrega["id_entrega"];
                            ?>"
                        >

                        <input
                            type="hidden"
                            name="tarea_id"
                            value="<?php
                                echo $tareaId;
                            ?>"
                        >


                        <div class="form-group">

                            <label>
                                Calificación (0 - 100)
                            </label>

                            <input
                                type="number"
                                name="calificacion"
                                min="0"
                                max="100"
                                step="0.01"
                                value="<?php
                                    echo $entrega["calificacion"] ?? "";
                                ?>"
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            class="form-button"
                        >
                            Guardar calificación
                        </button>

                    </form>


                </div>


            <?php endwhile; ?>


        <?php else: ?>


            <div class="mensaje">
                Todavía no hay entregas.
            </div>


        <?php endif; ?>


    </div>

</div>

</main>


<footer>

    <p>
        © Colegio Técnico Palermo
    </p>

</footer>

</body>

</html>