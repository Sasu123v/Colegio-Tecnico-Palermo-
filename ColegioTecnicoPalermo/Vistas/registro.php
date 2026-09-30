<?php

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

    <title>Registro | Colegio Técnico Palermo</title>

    <link
        rel="stylesheet"
        href="../CSS/estilos.css"
    >

</head>

<body>

    <!-- =====================================================
         NAVBAR
    ====================================================== -->

    <nav class="navbar">

        <a
            href="../index.php"
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

            <a href="../index.php">
                Inicio
            </a>

            <a href="login.php">
                Iniciar sesión
            </a>

        </div>

    </nav>


    <!-- =====================================================
         REGISTRO
    ====================================================== -->

    <main>

        <div class="form-card">

            <h1>
                Crear cuenta
            </h1>

            <p>
                Regístrate para comenzar a utilizar la plataforma.
            </p>


            <?php if ($mensaje === "nombre_vacio"): ?>

                <div class="mensaje mensaje-error">
                    Ingresa tu nombre.
                </div>

            <?php elseif ($mensaje === "apellido_vacio"): ?>

                <div class="mensaje mensaje-error">
                    Ingresa tu apellido.
                </div>

            <?php elseif ($mensaje === "correo_vacio"): ?>

                <div class="mensaje mensaje-error">
                    Ingresa tu correo electrónico.
                </div>

            <?php elseif ($mensaje === "correo_invalido"): ?>

                <div class="mensaje mensaje-error">
                    Ingresa un correo electrónico válido.
                </div>

            <?php elseif ($mensaje === "password_corta"): ?>

                <div class="mensaje mensaje-error">
                    La contraseña debe tener mínimo 6 caracteres.
                </div>

            <?php elseif ($mensaje === "rol_invalido"): ?>

                <div class="mensaje mensaje-error">
                    Selecciona un rol válido.
                </div>

            <?php elseif ($mensaje === "correo_existente"): ?>

                <div class="mensaje mensaje-error">
                    Ya existe una cuenta registrada con ese correo.
                </div>

            <?php elseif ($mensaje === "error_registro"): ?>

                <div class="mensaje mensaje-error">
                    Ocurrió un error al registrar la cuenta.
                </div>

            <?php endif; ?>


            <!-- =================================================
                 FORMULARIO
            ================================================== -->

            <form
                action="../Procesos/registrarUsuario.php"
                method="POST"
            >

                <div class="form-group">

                    <label for="nombre">
                        Nombre
                    </label>

                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        maxlength="100"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="apellido">
                        Apellido
                    </label>

                    <input
                        type="text"
                        id="apellido"
                        name="apellido"
                        maxlength="100"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="correo">
                        Correo electrónico
                    </label>

                    <input
                        type="email"
                        id="correo"
                        name="correo"
                        maxlength="150"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="password">
                        Contraseña
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        minlength="6"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="rol">
                        Tipo de usuario
                    </label>

                    <select
                        id="rol"
                        name="rol"
                        required
                    >

                        <option value="">
                            Selecciona una opción
                        </option>

                        <option value="estudiante">
                            Estudiante
                        </option>

                        <option value="profesor">
                            Profesor
                        </option>

                    </select>

                </div>


                <button
                    type="submit"
                    class="form-button"
                >
                    Crear cuenta
                </button>

            </form>


            <p style="margin-top: 25px;">

                ¿Ya tienes una cuenta?

                <a href="login.php">
                    Inicia sesión
                </a>

            </p>

        </div>

    </main>


    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <footer>

        <p>
            © Colegio Técnico Palermo
        </p>

    </footer>

</body>

</html>