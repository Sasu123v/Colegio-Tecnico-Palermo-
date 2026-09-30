<?php

session_start();

require_once __DIR__ . "/../Config/conexionBDD.php";
require_once __DIR__ . "/../Datos/Tarea/TareaDAO.php";
require_once __DIR__ . "/../Datos/Curso/CursoDAO.php";
require_once __DIR__ . "/../Datos/Entrega/EntregaDAO.php";
require_once __DIR__ . "/../Validaciones/validarFormulario.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php?mensaje=no_autorizado");
    exit;
}

$idTarea = $_GET["id"] ?? "";

if (!validarId($idTarea)) {
    header("Location: cursos.php?mensaje=tarea_invalida");
    exit;
}

$tarea = obtenerTareaPorId(
    $conexion,
    $idTarea
);

if ($tarea === null) {
    header("Location: cursos.php?mensaje=tarea_inexistente");
    exit;
}

$cursoId = $tarea["curso_id"];

$curso = obtenerCursoPorId(
    $conexion,
    $cursoId
);

if ($curso === null) {
    header("Location: cursos.php?mensaje=curso_inexistente");
    exit;
}


/* =====================================================
   VERIFICAR ACCESO
===================================================== */

if ($_SESSION["rol"] === "profesor") {

    if ($curso["profesor_id"] != $_SESSION["id_usuario"]) {

        header(
            "Location: cursos.php?mensaje=no_autorizado"
        );

        exit;
    }

} else {

    if (!estudianteEstaInscrito(
        $conexion,
        $_SESSION["id_usuario"],
        $cursoId
    )) {

        header(
            "Location: cursos.php?mensaje=no_autorizado"
        );

        exit;
    }
}


/* =====================================================
   OBTENER ENTREGA DEL ESTUDIANTE
===================================================== */

$entrega = null;

if ($_SESSION["rol"] === "estudiante") {

    $entrega = obtenerEntregaEstudiante(
        $conexion,
        $idTarea,
        $_SESSION["id_usuario"]
    );
}


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
        <?php
        echo htmlspecialchars(
            $tarea["titulo"]
        );
        ?>
    </title>

    <link
        rel="stylesheet"
        href="../CSS/estilos.css?v=6"
    >

</head>

<body>


<nav class="navbar">

    <a href="inicio.php" class="logo">

        <span class="logo-mark">
            CTP
        </span>

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


    <!-- =================================================
         INFORMACIÓN DE LA TAREA
    ================================================== -->

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

            <?php
            echo htmlspecialchars(
                $tarea["titulo"]
            );
            ?>

        </h1>


        <p>

            <?php
            echo nl2br(
                htmlspecialchars(
                    $tarea["descripcion"] ?? ""
                )
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


        <!-- =================================================
             MATERIAL DEL PROFESOR
        ================================================== -->

        <?php if (!empty($tarea["id_archivo"])): ?>

            <div class="archivo-tarea">

                <div class="archivo-tarea-icono">
                    📎
                </div>


                <div class="archivo-tarea-info">

                    <strong>
                        Material de la tarea
                    </strong>

                    <span>

                        <?php
                        echo htmlspecialchars(
                            $tarea["nombre_original"]
                        );
                        ?>

                    </span>

                </div>


                <a
                    href="../<?php echo htmlspecialchars($tarea["ruta"]); ?>"
                    target="_blank"
                    class="archivo-tarea-btn"
                >
                    Ver archivo
                </a>

            </div>

        <?php endif; ?>

    </div>


    <!-- =================================================
         MENSAJES
    ================================================== -->

    <?php if ($mensaje === "entrega_exitosa"): ?>

        <div class="mensaje mensaje-exito">
            ¡Tu entrega fue enviada correctamente!
        </div>


    <?php elseif ($mensaje === "error_entrega"): ?>

        <div class="mensaje mensaje-error">
            No se pudo realizar la entrega.
        </div>


    <?php elseif ($mensaje === "respuesta_vacia"): ?>

        <div class="mensaje mensaje-error">
            Escribe una respuesta antes de entregar la tarea.
        </div>


    <?php elseif ($mensaje === "ya_entregada"): ?>

        <div class="mensaje mensaje-error">
            Ya realizaste una entrega para esta tarea.
        </div>


    <?php elseif ($mensaje === "archivo_grande"): ?>

        <div class="mensaje mensaje-error">
            El archivo es demasiado grande. El máximo permitido es de 10 MB.
        </div>


    <?php elseif ($mensaje === "tipo_archivo_no_permitido"): ?>

        <div class="mensaje mensaje-error">
            El tipo de archivo seleccionado no está permitido.
        </div>


    <?php elseif ($mensaje === "error_archivo"): ?>

        <div class="mensaje mensaje-error">
            No se pudo subir el archivo.
        </div>


    <?php elseif ($mensaje === "error_guardado_archivo"): ?>

        <div class="mensaje mensaje-error">
            No se pudo guardar el archivo.
        </div>


    <?php elseif ($mensaje === "error_archivo_bdd"): ?>

        <div class="mensaje mensaje-error">
            No se pudo registrar el archivo.
        </div>


    <?php elseif ($mensaje === "archivo_agregado"): ?>

        <div class="mensaje mensaje-exito">
            Archivo adjuntado correctamente.
        </div>


    <?php elseif ($mensaje === "archivo_eliminado"): ?>

        <div class="mensaje mensaje-exito">
            Archivo eliminado correctamente.
        </div>


    <?php elseif ($mensaje === "archivo_ya_existe"): ?>

        <div class="mensaje mensaje-error">
            Esta entrega ya tiene un archivo adjunto.
        </div>


    <?php elseif ($mensaje === "error_eliminar_archivo"): ?>

        <div class="mensaje mensaje-error">
            No se pudo eliminar el archivo.
        </div>

    <?php endif; ?>


    <!-- =================================================
         ENTREGA DEL ESTUDIANTE
    ================================================== -->

    <?php if ($_SESSION["rol"] === "estudiante"): ?>

        <div class="form-card">

            <h2>
                Entregar tarea
            </h2>


            <?php if ($entrega): ?>


                <!-- =============================================
                     ENTREGA YA REALIZADA
                ============================================== -->

                <div class="mensaje mensaje-exito">

                    Ya realizaste una entrega
                    para esta tarea.

                </div>


                <div class="entrega-estudiante">

                    <h3>
                        Tu respuesta
                    </h3>

                    <p>

                        <?php
                        echo nl2br(
                            htmlspecialchars(
                                $entrega["respuesta"]
                            )
                        );
                        ?>

                    </p>

                </div>


                <!-- =============================================
                     CALIFICACIÓN
                ============================================== -->

                <div class="calificacion-estudiante">

                    <h3>
                        Calificación
                    </h3>


                    <?php if ($entrega["calificacion"] !== null): ?>

                        <div class="calificacion-valor">

                            <?php
                            echo htmlspecialchars(
                                $entrega["calificacion"]
                            );
                            ?>

                            <span>
                                / 100
                            </span>

                        </div>


                        <p>
                            Tu profesor ya calificó esta entrega.
                        </p>


                    <?php else: ?>

                        <div class="calificacion-pendiente">

                            ⏳ Pendiente de calificación

                        </div>


                        <p>
                            Tu profesor todavía no ha calificado esta entrega.
                        </p>

                    <?php endif; ?>

                </div>


                <!-- =============================================
                     ARCHIVO DEL ESTUDIANTE
                ============================================== -->

                <?php if (!empty($entrega["id_archivo"])): ?>

                    <div class="archivo-entrega">

                        <div class="archivo-entrega-icono">
                            📎
                        </div>


                        <div class="archivo-entrega-info">

                            <strong>
                                Tu archivo
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
                                href="../<?php echo htmlspecialchars($entrega["ruta"]); ?>"
                                target="_blank"
                                class="archivo-ver-btn"
                            >
                                Ver
                            </a>


                            <a
                                href="../Procesos/eliminarArchivoEntrega.php?id=<?php echo $entrega["id_entrega"]; ?>"
                                class="archivo-eliminar-btn"
                                onclick="return confirm('¿Seguro que quieres eliminar este archivo?');"
                            >
                                Eliminar
                            </a>

                        </div>

                    </div>


                <?php else: ?>


                    <!-- =============================================
                         AGREGAR ARCHIVO DESPUÉS DE ENTREGAR
                    ============================================== -->

                    <div class="archivo-agregar-card">

                        <div class="archivo-agregar-icono">
                            📎
                        </div>


                        <div>

                            <strong>
                                ¿Quieres adjuntar un archivo?
                            </strong>

                            <p>
                                Puedes agregar un archivo a tu entrega.
                            </p>

                        </div>


                        <form
                            action="../Procesos/agregarArchivoEntrega.php"
                            method="POST"
                            enctype="multipart/form-data"
                        >

                            <input
                                type="hidden"
                                name="entrega_id"
                                value="<?php echo $entrega["id_entrega"]; ?>"
                            >


                            <label
                                for="archivo_entrega_extra"
                                class="archivo-selector-btn"
                            >
                                Seleccionar archivo
                            </label>


                            <input
                                type="file"
                                id="archivo_entrega_extra"
                                name="archivo"
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png"
                                required
                            >


                            <span
                                id="nombre-extra"
                                class="archivo-nombre-extra"
                            >
                                Ningún archivo seleccionado
                            </span>


                            <button
                                type="submit"
                                class="form-button"
                            >
                                Adjuntar archivo
                            </button>

                        </form>

                    </div>

                <?php endif; ?>


            <?php else: ?>


                <!-- =============================================
                     FORMULARIO DE ENTREGA
                ============================================== -->

                <form
                    action="../Procesos/entregarTarea.php"
                    method="POST"
                    enctype="multipart/form-data"
                >

                    <input
                        type="hidden"
                        name="tarea_id"
                        value="<?php echo $idTarea; ?>"
                    >


                    <div class="form-group">

                        <label for="respuesta">
                            Respuesta
                        </label>

                        <textarea
                            id="respuesta"
                            name="respuesta"
                            required
                        ></textarea>

                    </div>


                    <!-- =============================================
                         ARCHIVO
                    ============================================== -->

                    <div class="form-group">

                        <label for="archivo">
                            Archivo adjunto
                        </label>


                        <input
                            type="file"
                            id="archivo"
                            name="archivo"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png"
                        >


                        <small>
                            Opcional. Máximo 10 MB.
                            PDF, Word, Excel, PowerPoint, JPG y PNG.
                        </small>


                        <span
                            id="nombre-archivo-entrega"
                            class="archivo-nombre-extra"
                        >
                            Ningún archivo seleccionado
                        </span>

                    </div>


                    <button
                        type="submit"
                        class="form-button"
                    >
                        Entregar tarea
                    </button>

                </form>


            <?php endif; ?>

        </div>

    <?php endif; ?>


    <!-- =================================================
         REVISAR ENTREGAS
    ================================================== -->

    <?php if ($_SESSION["rol"] === "profesor"): ?>

        <div class="form-card">

            <h2>
                Revisar entregas
            </h2>

            <p>
                Consulta las respuestas de los estudiantes
                y asigna sus calificaciones.
            </p>

            <a
                href="entregas.php?tarea_id=<?php echo $idTarea; ?>"
                class="curso-btn"
            >
                Ver entregas
            </a>

        </div>

    <?php endif; ?>


</div>

</main>


<footer>

    <p>
        © Colegio Técnico Palermo
    </p>

</footer>


<script>

const archivoEntrega =
    document.getElementById("archivo");

const nombreArchivoEntrega =
    document.getElementById(
        "nombre-archivo-entrega"
    );


if (archivoEntrega) {

    archivoEntrega.addEventListener(
        "change",
        function () {

            if (this.files.length > 0) {

                nombreArchivoEntrega.textContent =
                    this.files[0].name;

                nombreArchivoEntrega.classList.add(
                    "archivo-seleccionado"
                );

            } else {

                nombreArchivoEntrega.textContent =
                    "Ningún archivo seleccionado";

                nombreArchivoEntrega.classList.remove(
                    "archivo-seleccionado"
                );

            }

        }
    );

}


const archivoExtra =
    document.getElementById(
        "archivo_entrega_extra"
    );

const nombreExtra =
    document.getElementById(
        "nombre-extra"
    );


if (archivoExtra) {

    archivoExtra.addEventListener(
        "change",
        function () {

            if (this.files.length > 0) {

                nombreExtra.textContent =
                    this.files[0].name;

                nombreExtra.classList.add(
                    "archivo-seleccionado"
                );

            } else {

                nombreExtra.textContent =
                    "Ningún archivo seleccionado";

                nombreExtra.classList.remove(
                    "archivo-seleccionado"
                );

            }

        }
    );

}

</script>


</body>

</html>