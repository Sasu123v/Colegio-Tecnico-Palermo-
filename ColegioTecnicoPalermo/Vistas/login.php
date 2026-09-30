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

    <title>Iniciar sesión | Colegio Técnico Palermo</title>

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

            <a href="registro.php">
                Registrarse
            </a>

        </div>

        

    </nav>


    <!-- =====================================================
         LOGIN
    ====================================================== -->

    <main>

        <div class="form-card">

            <h1>
                Iniciar sesión
            </h1>

            <p>
                Ingresa a tu cuenta del Colegio Técnico Palermo.
            </p>


            <!-- =================================================
                 MENSAJES
            ================================================== -->

            <?php if ($mensaje === "registro_exitoso"): ?>

                <div class="mensaje mensaje-exito">
                    ¡Cuenta creada correctamente!
                    Ahora puedes iniciar sesión.
                </div>

            <?php elseif ($mensaje === "correo_vacio"): ?>

                <div class="mensaje mensaje-error">
                    Ingresa tu correo electrónico.
                </div>

            <?php elseif ($mensaje === "correo_invalido"): ?>

                <div class="mensaje mensaje-error">
                    Ingresa un correo electrónico válido.
                </div>

            <?php elseif ($mensaje === "password_vacia"): ?>

                <div class="mensaje mensaje-error">
                    Ingresa tu contraseña.
                </div>

            <?php elseif ($mensaje === "datos_incorrectos"): ?>

                <div class="mensaje mensaje-error">
                    El correo o la contraseña son incorrectos.
                </div>

            <?php elseif ($mensaje === "sesion_cerrada"): ?>

                <div class="mensaje mensaje-exito">
                    Has cerrado sesión correctamente.
                </div>

            <?php elseif ($mensaje === "no_autorizado"): ?>

                <div class="mensaje mensaje-error">
                    Debes iniciar sesión para acceder.
                </div>

            <?php endif; ?>


            <!-- =================================================
                 FORMULARIO
            ================================================== -->

            <form
                action="../Procesos/iniciarSesion.php"
                method="POST"
            >

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
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="form-button"
                >
                    Iniciar sesión
                </button>

            </form>


            <p style="margin-top: 25px;">

                ¿No tienes una cuenta?

                <a href="registro.php">
                    Regístrate
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