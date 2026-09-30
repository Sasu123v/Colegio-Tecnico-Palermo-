<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {

    header("Location: login.php?mensaje=no_autorizado");

    exit;
}

$nombre = $_SESSION["nombre"];
$apellido = $_SESSION["apellido"];
$rol = $_SESSION["rol"];

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
        Inicio | Colegio Técnico Palermo
    </title>

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

            <a
                href="inicio.php"
                class="active"
            >
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


        <!-- =================================================
             HERO
        ================================================== -->

        <section class="hero">


            <div class="hero-content">


                <span class="section-label">
                    COLEGIO TÉCNICO PALERMO
                </span>


                <h1>

                    ¡Hola,
                    <?php
                    echo htmlspecialchars($nombre);
                    ?>!

                </h1>


                <p>

                    Has iniciado sesión correctamente.
                    Desde aquí puedes acceder a tus cursos,
                    tareas y demás funciones de la plataforma.

                </p>


                <div class="hero-buttons">


                    <a
                        href="cursos.php"
                        class="primary-btn"
                    >
                        Ver cursos
                    </a>


                    <a
                        href="perfil.php"
                        class="secondary-btn"
                    >
                        Mi perfil
                    </a>


                </div>


            </div>


            <!-- =================================================
                 TARJETA LATERAL
            ================================================== -->

            <div class="hero-card">


                <div class="hero-card-top">

                    <span>
                        PLATAFORMA EDUCATIVA
                    </span>

                    <span class="status-dot">
                        ●
                    </span>

                </div>


                <h2>
                    Tu espacio académico
                </h2>


                <p>

                    Consulta tus cursos, revisa tus actividades
                    y mantén tu información académica organizada.

                </p>


                <!-- CURSO -->

                <div class="mini-course">

                    <div class="mini-icon">
                        📚
                    </div>

                    <div>

                        <strong>
                            Mis cursos
                        </strong>

                        <span>
                            Consulta tus cursos
                        </span>

                    </div>

                </div>


                <!-- TAREAS -->

                <div class="mini-course">

                    <div class="mini-icon blue-dark">
                        ✓
                    </div>

                    <div>

                        <strong>
                            Actividades
                        </strong>

                        <span>
                            Revisa tus tareas
                        </span>

                    </div>

                </div>


                <!-- PERFIL -->

                <div class="mini-course">

                    <div class="mini-icon red">
                        👤
                    </div>

                    <div>

                        <strong>
                            Mi perfil
                        </strong>

                        <span>
                            Consulta tu información
                        </span>

                    </div>

                </div>


            </div>


        </section>



        <!-- =================================================
             INFORMACIÓN DEL USUARIO
        ================================================== -->

        <section class="features">


            <div class="section-heading">

                <span class="section-label">
                    TU PLATAFORMA
                </span>

                <h2>
                    Todo lo que necesitas.
                </h2>

                <p>
                    Accede rápidamente a las principales
                    funciones de tu cuenta.
                </p>

            </div>


            <div class="feature-grid">


                <!-- =================================================
                     CUENTA
                ================================================== -->

                <div class="feature-card">

                    <span class="feature-number">
                        01 — CUENTA
                    </span>


                    <div class="feature-icon">
                        👤
                    </div>


                    <h3>
                        Tu cuenta
                    </h3>


                    <p>

                        <strong>Nombre:</strong>

                        <?php

                        echo htmlspecialchars(
                            $nombre . " " . $apellido
                        );

                        ?>

                    </p>


                    <p>

                        <strong>Rol:</strong>

                        <?php

                        echo htmlspecialchars($rol);

                        ?>

                    </p>

                </div>



                <!-- =================================================
                     CURSOS
                ================================================== -->

                <div class="feature-card">

                    <span class="feature-number">
                        02 — CURSOS
                    </span>


                    <div
                        class="feature-icon"
                        style="background: var(--azul-oscuro);"
                    >
                        📚
                    </div>


                    <h3>
                        Cursos
                    </h3>


                    <p>

                        Accede a los cursos en los que
                        participas y consulta su contenido.

                    </p>


                    <a
                        href="cursos.php"
                        style="
                            display: inline-block;
                            margin-top: 18px;
                            color: var(--azul-oscuro);
                            font-weight: 700;
                            font-size: 14px;
                        "
                    >
                        Ver cursos →
                    </a>

                </div>



                <!-- =================================================
                     PERFIL
                ================================================== -->

                <div class="feature-card">

                    <span
                        class="feature-number"
                        style="color: var(--rojo);"
                    >
                        03 — PERFIL
                    </span>


                    <div
                        class="feature-icon"
                        style="background: var(--rojo);"
                    >
                        ✓
                    </div>


                    <h3>
                        Perfil
                    </h3>


                    <p>

                        Consulta la información asociada
                        a tu cuenta.

                    </p>


                    <a
                        href="perfil.php"
                        style="
                            display: inline-block;
                            margin-top: 18px;
                            color: var(--rojo);
                            font-weight: 700;
                            font-size: 14px;
                        "
                    >
                        Ver perfil →
                    </a>

                </div>


            </div>


        </section>


    </main>



    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <footer>


        <p>
            © 2026 Colegio Técnico Palermo
        </p>


    </footer>


</body>

</html>