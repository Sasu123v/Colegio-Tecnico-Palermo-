<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php?mensaje=no_autorizado");
    exit;
}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Mi perfil | Colegio Técnico Palermo</title>

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

        <a href="inicio.php">
            Inicio
        </a>

        <a href="cursos.php">
            Cursos
        </a>


    </div>

    <div class="auth-buttons">

            <a
                href="../Procesos/cerrarSesion.php"
                class="nav-logout">
                Cerrar sesión
            </a>

        </div>

</nav>


<main>

<div class="form-card">

    <h1>
        Mi perfil
    </h1>

    <p>
        Información de tu cuenta.
    </p>


    <div class="form-group">

        <label>
            Nombre
        </label>

        <input
            type="text"
            value="<?php
                echo htmlspecialchars(
                    $_SESSION["nombre"]
                );
            ?>"
            disabled
        >

    </div>


    <div class="form-group">

        <label>
            Apellido
        </label>

        <input
            type="text"
            value="<?php
                echo htmlspecialchars(
                    $_SESSION["apellido"]
                );
            ?>"
            disabled
        >

    </div>


    <div class="form-group">

        <label>
            Correo electrónico
        </label>

        <input
            type="text"
            value="<?php
                echo htmlspecialchars(
                    $_SESSION["correo"]
                );
            ?>"
            disabled
        >

    </div>


    <div class="form-group">

        <label>
            Rol
        </label>

        <input
            type="text"
            value="<?php
                echo htmlspecialchars(
                    ucfirst($_SESSION["rol"])
                );
            ?>"
            disabled
        >

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