<?php

session_start();

require_once __DIR__ . "/../Config/conexionBDD.php";
require_once __DIR__ . "/../Datos/Curso/CursoDAO.php";
require_once __DIR__ . "/../Datos/Usuario/UsuarioDAO.php";
require_once __DIR__ . "/../Datos/Tarea/TareaDAO.php";
require_once __DIR__ . "/../Datos/Anuncio/AnuncioDAO.php";
require_once __DIR__ . "/../Validaciones/validarFormulario.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php?mensaje=no_autorizado");
    exit;
}

$idCurso = $_GET["id"] ?? "";

if (!validarId($idCurso)) {
    header("Location: cursos.php?mensaje=curso_invalido");
    exit;
}

$curso = obtenerCursoPorId(
    $conexion,
    $idCurso
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
        $idCurso
    )) {
        header("Location: cursos.php?mensaje=no_autorizado");
        exit;
    }
}

$estudiantes = obtenerEstudiantesCurso(
    $conexion,
    $idCurso
);

$usuarios = null;

if ($esProfesor) {

    $usuarios = obtenerEstudiantes(
        $conexion
    );
}

$tareas = obtenerTareasPorCurso(
    $conexion,
    $idCurso
);

$anuncios = obtenerAnunciosPorCurso(
    $conexion,
    $idCurso
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

    <title>
        <?php echo htmlspecialchars($curso["nombre"]); ?>
    </title>

    <link
        rel="stylesheet"
        href="../CSS/estilos.css?v=4"
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
         INFORMACIÓN DEL CURSO
    ================================================== -->

    <div class="curso-detalle-header">

        <h1>
            <?php
            echo htmlspecialchars(
                $curso["nombre"]
            );
            ?>
        </h1>

        <p>
            <?php
            echo nl2br(
                htmlspecialchars(
                    $curso["descripcion"] ?? ""
                )
            );
            ?>
        </p>

        <p>
            Profesor:

            <?php
            echo htmlspecialchars(
                $curso["profesor_nombre"] .
                " " .
                $curso["profesor_apellido"]
            );
            ?>
        </p>

    </div>


    <!-- =================================================
         MENSAJES
    ================================================== -->

    <?php if ($mensaje === "tarea_creada"): ?>

        <div class="mensaje mensaje-exito">
            Tarea creada correctamente.
        </div>

    <?php elseif ($mensaje === "anuncio_creado"): ?>

        <div class="mensaje mensaje-exito">
            Anuncio publicado correctamente.
        </div>

    <?php elseif ($mensaje === "anuncio_incompleto"): ?>

        <div class="mensaje mensaje-error">
            Completa todos los campos del anuncio.
        </div>

    <?php elseif ($mensaje === "error_tarea"): ?>

        <div class="mensaje mensaje-error">
            No se pudo crear la tarea.
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

    <?php endif; ?>


    <!-- =================================================
         TAREAS
    ================================================== -->

    <div class="curso-detalle-header">
        <h2>
            Tareas
        </h2>

        <?php if ($tareas && $tareas->num_rows > 0): ?>

            <?php while ($tarea = $tareas->fetch_assoc()): ?>

                <div class="estudiante-item">

                    <strong>

                        <?php
                        echo htmlspecialchars(
                            $tarea["titulo"]
                        );
                        ?>

                    </strong>

                    <span>

                        Entrega:

                        <?php
                        echo htmlspecialchars(
                            $tarea["fecha_entrega"]
                        );
                        ?>

                    </span>

                    <p>

                        <?php
                        echo nl2br(
                            htmlspecialchars(
                                $tarea["descripcion"] ?? ""
                            )
                        );
                        ?>

                    </p>


                    <?php if (!empty($tarea["id_archivo"])): ?>

                        <div class="archivo-tarea-mini">
                            📎 Archivo adjunto
                        </div>

                    <?php endif; ?>


                    <a
                        href="tarea.php?id=<?php echo $tarea["id_tarea"]; ?>"
                        class="curso-btn"
                    >
                        Ver tarea
                    </a>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <p>
                No hay tareas todavía.
            </p>

        <?php endif; ?>

    </div>


    <!-- =================================================
         CREAR TAREA
    ================================================== -->

    <?php if ($esProfesor): ?>

        <div class="form-card">

            <h2>
                Crear tarea
            </h2>

            <p>
                Crea una nueva tarea y, si lo deseas,
                adjunta material para los estudiantes.
            </p>

            <form
                action="../Procesos/guardarTarea.php"
                method="POST"
                enctype="multipart/form-data"
            >

                <input
                    type="hidden"
                    name="curso_id"
                    value="<?php echo $idCurso; ?>"
                >


                <div class="form-group">

                    <label for="titulo">
                        Título
                    </label>

                    <input
                        type="text"
                        id="titulo"
                        name="titulo"
                        maxlength="200"
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


                <div class="form-group">

                    <label for="fecha_entrega">
                        Fecha de entrega
                    </label>

                    <input
                        type="datetime-local"
                        id="fecha_entrega"
                        name="fecha_entrega"
                        required
                    >

                </div>


                <!-- =================================================
                     ARCHIVO ADJUNTO
                ================================================== -->

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

                </div>


                <button
                    type="submit"
                    class="form-button"
                >
                    Crear tarea
                </button>

            </form>

        </div>

    <?php endif; ?>


    <!-- =================================================
         ANUNCIOS
    ================================================== -->

    <div class="curso-detalle-header">

        <h2>
            Anuncios
        </h2>

        <?php if ($anuncios && $anuncios->num_rows > 0): ?>

            <?php while ($anuncio = $anuncios->fetch_assoc()): ?>

                <div class="estudiante-item">

                    <strong>

                        <?php
                        echo htmlspecialchars(
                            $anuncio["titulo"]
                        );
                        ?>

                    </strong>

                    <span>

                        <?php
                        echo htmlspecialchars(
                            $anuncio["nombre"] .
                            " " .
                            $anuncio["apellido"]
                        );
                        ?>

                        ·

                        <?php
                        echo htmlspecialchars(
                            $anuncio["fecha"]
                        );
                        ?>

                    </span>

                    <p>

                        <?php
                        echo nl2br(
                            htmlspecialchars(
                                $anuncio["contenido"]
                            )
                        );
                        ?>

                    </p>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <p>
                No hay anuncios todavía.
            </p>

        <?php endif; ?>

    </div>


    <!-- =================================================
         CREAR ANUNCIO
    ================================================== -->

    <?php if ($esProfesor): ?>

        <div class="form-card">

            <h2>
                Publicar anuncio
            </h2>

            <form
                action="../Procesos/guardarAnuncio.php"
                method="POST"
            >

                <input
                    type="hidden"
                    name="curso_id"
                    value="<?php echo $idCurso; ?>"
                >

                <div class="form-group">

                    <label for="titulo_anuncio">
                        Título
                    </label>

                    <input
                        type="text"
                        id="titulo_anuncio"
                        name="titulo"
                        maxlength="200"
                        required
                    >

                </div>

                <div class="form-group">

                    <label for="contenido">
                        Contenido
                    </label>

                    <textarea
                        id="contenido"
                        name="contenido"
                        required
                    ></textarea>

                </div>

                <button
                    type="submit"
                    class="form-button"
                >
                    Publicar anuncio
                </button>

            </form>

        </div>

    <?php endif; ?>


    <!-- =================================================
         INSCRIBIR ESTUDIANTE
    ================================================== -->

    <?php if ($esProfesor): ?>

        <div class="form-card">

            <h2>
                Inscribir estudiante
            </h2>

            <form
                action="../Procesos/guardarCurso.php"
                method="POST"
            >

                <input
                    type="hidden"
                    name="accion"
                    value="inscribir"
                >

                <input
                    type="hidden"
                    name="curso_id"
                    value="<?php echo $idCurso; ?>"
                >

                <div class="form-group">

                    <label for="estudiante_id">
                        Estudiante
                    </label>

                    <select
                        name="estudiante_id"
                        id="estudiante_id"
                        required
                    >

                        <option value="">
                            Selecciona un estudiante
                        </option>

                        <?php if ($usuarios): ?>

                            <?php while ($usuario = $usuarios->fetch_assoc()): ?>

                                <option
                                    value="<?php echo $usuario["id_usuario"]; ?>"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $usuario["nombre"] .
                                        " " .
                                        $usuario["apellido"] .
                                        " - " .
                                        $usuario["correo"]
                                    );
                                    ?>

                                </option>

                            <?php endwhile; ?>

                        <?php endif; ?>

                    </select>

                </div>

                <button
                    type="submit"
                    class="form-button"
                >
                    Inscribir estudiante
                </button>

            </form>

        </div>

    <?php endif; ?>


    <!-- =================================================
         ESTUDIANTES INSCRITOS
    ================================================== -->

    <div class="curso-detalle-header">

        <h2>
            Estudiantes inscritos
        </h2>

        <?php if ($estudiantes && $estudiantes->num_rows > 0): ?>

            <?php while ($estudiante = $estudiantes->fetch_assoc()): ?>

                <div class="estudiante-item">

                    <strong>

                        <?php
                        echo htmlspecialchars(
                            $estudiante["nombre"] .
                            " " .
                            $estudiante["apellido"]
                        );
                        ?>

                    </strong>

                    <span>

                        <?php
                        echo htmlspecialchars(
                            $estudiante["correo"]
                        );
                        ?>

                    </span>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <p>
                Todavía no hay estudiantes inscritos.
            </p>

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