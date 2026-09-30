<?php

session_start();

require_once __DIR__ . "/../Config/conexionBDD.php";
require_once __DIR__ . "/../Datos/Usuario/UsuarioDAO.php";
require_once __DIR__ . "/../Validaciones/validarFormulario.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../Vistas/login.php");
    exit;
}

$correo = trim($_POST["correo"] ?? "");
$password = $_POST["password"] ?? "";

/*
|--------------------------------------------------------------------------
| VALIDACIONES
|--------------------------------------------------------------------------
*/

if (!validarCampoObligatorio($correo)) {
    header("Location: ../Vistas/login.php?mensaje=correo_vacio");
    exit;
}

if (!validarCorreo($correo)) {
    header("Location: ../Vistas/login.php?mensaje=correo_invalido");
    exit;
}

if (!validarCampoObligatorio($password)) {
    header("Location: ../Vistas/login.php?mensaje=password_vacia");
    exit;
}

/*
|--------------------------------------------------------------------------
| BUSCAR USUARIO
|--------------------------------------------------------------------------
*/

$usuario = obtenerUsuarioPorCorreo(
    $conexion,
    $correo
);

if ($usuario === null) {
    header("Location: ../Vistas/login.php?mensaje=datos_incorrectos");
    exit;
}

/*
|--------------------------------------------------------------------------
| COMPROBAR CONTRASEÑA
|--------------------------------------------------------------------------
*/

if (!password_verify($password, $usuario["password"])) {
    header("Location: ../Vistas/login.php?mensaje=datos_incorrectos");
    exit;
}

/*
|--------------------------------------------------------------------------
| CREAR SESIÓN
|--------------------------------------------------------------------------
*/

$_SESSION["id_usuario"] = $usuario["id_usuario"];
$_SESSION["nombre"] = $usuario["nombre"];
$_SESSION["apellido"] = $usuario["apellido"];
$_SESSION["correo"] = $usuario["correo"];
$_SESSION["rol"] = $usuario["rol"];

/*
|--------------------------------------------------------------------------
| REDIRECCIÓN
|--------------------------------------------------------------------------
*/

header("Location: ../Vistas/inicio.php");
exit;