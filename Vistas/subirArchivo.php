<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php?mensaje=no_autorizado");
    exit;
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

    <title>Subir archivo | Colegio Técnico Palermo</title>

    <link
    rel="stylesheet"
    href="../CSS/estilos.css?v=3"
    >

</head>

<body>


    <!-- =====================================================
         NAVBAR
    ====================================================== -->

    <nav class="navbar">

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


        <div class="nav-links">

            <a href="inicio.php">
                Inicio
            </a>

            <a href="cursos.php">
                Cursos
            </a>

            <a href="perfil.php">
                Mi perfil
            </a>

        </div>


        <div class="auth-buttons">

            <a
                href="../Procesos/cerrarSesion.php"
                class="nav-logout"
            >
                Cerrar sesión
            </a>

        </div>

    </nav>



    <!-- =====================================================
         CONTENIDO
    ====================================================== -->

    <main>

        <section class="subir-archivo-container">


            <!-- ENCABEZADO -->

            <div class="subir-archivo-header">

                <span class="subir-archivo-etiqueta">
                    ARCHIVOS
                </span>

                <h1>
                    Subir archivo
                </h1>

                <p>
                    Adjunta documentos, imágenes o materiales
                    para compartirlos dentro de la plataforma.
                </p>

            </div>



            <!-- =================================================
                 MENSAJES
            ================================================== -->

            <?php if ($mensaje === "exito"): ?>

                <div class="subir-mensaje subir-mensaje-exito">

                    <strong>
                        Archivo subido correctamente
                    </strong>

                    <span>
                        El archivo se guardó correctamente en la plataforma.
                    </span>

                </div>

            <?php elseif ($mensaje === "archivo_grande"): ?>

                <div class="subir-mensaje subir-mensaje-error">

                    <strong>
                        El archivo es demasiado grande
                    </strong>

                    <span>
                        El tamaño máximo permitido es de 10 MB.
                    </span>

                </div>

            <?php elseif ($mensaje === "tipo_no_permitido"): ?>

                <div class="subir-mensaje subir-mensaje-error">

                    <strong>
                        Tipo de archivo no permitido
                    </strong>

                    <span>
                        Selecciona un formato compatible con la plataforma.
                    </span>

                </div>

            <?php elseif ($mensaje === "no_archivo"): ?>

                <div class="subir-mensaje subir-mensaje-error">

                    <strong>
                        No seleccionaste ningún archivo
                    </strong>

                    <span>
                        Selecciona un archivo antes de continuar.
                    </span>

                </div>

            <?php elseif ($mensaje === "error_subida"): ?>

                <div class="subir-mensaje subir-mensaje-error">

                    <strong>
                        No se pudo subir el archivo
                    </strong>

                    <span>
                        Ocurrió un problema durante la subida.
                    </span>

                </div>

            <?php elseif ($mensaje === "error_guardado"): ?>

                <div class="subir-mensaje subir-mensaje-error">

                    <strong>
                        No se pudo guardar el archivo
                    </strong>

                    <span>
                        Intenta nuevamente.
                    </span>

                </div>

            <?php elseif ($mensaje === "error_bdd"): ?>

                <div class="subir-mensaje subir-mensaje-error">

                    <strong>
                        No se pudo registrar el archivo
                    </strong>

                    <span>
                        El archivo no pudo registrarse en la base de datos.
                    </span>

                </div>

            <?php endif; ?>



            <!-- =================================================
                 TARJETA
            ================================================== -->

            <div class="subir-archivo-card">


                <div class="subir-archivo-icono">
                    ↑
                </div>


                <h2>
                    Selecciona tu archivo
                </h2>


                <p>
                    Puedes subir documentos, imágenes y presentaciones.
                </p>



                <!-- FORMULARIO -->

                <form
                    action="../Procesos/subirArchivo.php"
                    method="POST"
                    enctype="multipart/form-data"
                >


                    <label
                        for="archivo"
                        class="archivo-selector"
                    >

                        <div class="archivo-selector-icono">
                            +
                        </div>


                        <span class="archivo-selector-texto">
                            Haz clic para seleccionar un archivo
                        </span>


                        <span
                            class="archivo-nombre"
                            id="nombre-archivo"
                        >
                            Ningún archivo seleccionado
                        </span>

                    </label>


                    <input
                        type="file"
                        id="archivo"
                        name="archivo"
                        required
                    >



                    <!-- INFORMACIÓN -->

                    <div class="archivo-info">

                        <div>

                            <strong>
                                Tamaño máximo
                            </strong>

                            <span>
                                10 MB por archivo
                            </span>

                        </div>


                        <div>

                            <strong>
                                Formatos permitidos
                            </strong>

                            <span>
                                PDF, Word, Excel, PowerPoint, JPG y PNG
                            </span>

                        </div>

                    </div>



                    <!-- BOTÓN -->

                    <button
                        type="submit"
                        class="subir-archivo-btn"
                    >

                        <span>
                            ↑
                        </span>

                        Subir archivo

                    </button>


                </form>

            </div>

        </section>

    </main>



    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <footer>

        <p>
            © Colegio Técnico Palermo
        </p>

    </footer>



    <!-- =====================================================
         JAVASCRIPT
    ====================================================== -->

    <script>

        const inputArchivo =
            document.getElementById("archivo");

        const nombreArchivo =
            document.getElementById("nombre-archivo");


        inputArchivo.addEventListener(
            "change",
            function () {

                if (this.files.length > 0) {

                    nombreArchivo.textContent =
                        this.files[0].name;

                    nombreArchivo.classList.add(
                        "archivo-seleccionado"
                    );

                } else {

                    nombreArchivo.textContent =
                        "Ningún archivo seleccionado";

                    nombreArchivo.classList.remove(
                        "archivo-seleccionado"
                    );

                }

            }
        );

    </script>


</body>

</html>