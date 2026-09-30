<?php

session_start();

require_once __DIR__ . "/../Config/conexionBDD.php";
require_once __DIR__ . "/../Datos/Usuario/UsuarioDAO.php";
require_once __DIR__ . "/../Validaciones/validarFormulario.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../Vistas/registro.php");
    exit;
}

$nombre = trim($_POST["nombre"] ?? "");
$apellido = trim($_POST["apellido"] ?? "");
$correo = trim($_POST["correo"] ?? "");
$password = $_POST["password"] ?? "";
$rol = $_POST["rol"] ?? "";

/*
|--------------------------------------------------------------------------
| VALIDACIONES
|--------------------------------------------------------------------------
*/

if (!validarCampoObligatorio($nombre)) {
    header("Location: ../Vistas/registro.php?mensaje=nombre_vacio");
    exit;
}

if (!validarCampoObligatorio($apellido)) {
    header("Location: ../Vistas/registro.php?mensaje=apellido_vacio");
    exit;
}

if (!validarCampoObligatorio($correo)) {
    header("Location: ../Vistas/registro.php?mensaje=correo_vacio");
    exit;
}

if (!validarCorreo($correo)) {
    header("Location: ../Vistas/registro.php?mensaje=correo_invalido");
    exit;
}

if (!validarPassword($password)) {
    header("Location: ../Vistas/registro.php?mensaje=password_corta");
    exit;
}

if ($rol !== "estudiante" && $rol !== "profesor") {
    header("Location: ../Vistas/registro.php?mensaje=rol_invalido");
    exit;
}

/*
|--------------------------------------------------------------------------
| COMPROBAR SI EL CORREO YA EXISTE
|--------------------------------------------------------------------------
*/

$usuarioExistente = obtenerUsuarioPorCorreo(
    $conexion,
    $correo
);

if ($usuarioExistente !== null) {
    header("Location: ../Vistas/registro.php?mensaje=correo_existente");
    exit;
}

/*
|--------------------------------------------------------------------------
| ENCRIPTAR CONTRASEÑA
|--------------------------------------------------------------------------
*/

$passwordHash = password_hash(
    $password,
    PASSWORD_DEFAULT
);

/*
|--------------------------------------------------------------------------
| REGISTRAR USUARIO
|--------------------------------------------------------------------------
*/

$resultado = registrarUsuario(
    $conexion,
    $nombre,
    $apellido,
    $correo,
    $passwordHash,
    $rol
);

if ($resultado) {

    header(
        "Location: ../Vistas/login.php?mensaje=registro_exitoso"
    );
    exit;

} else {

    header(
        "Location: ../Vistas/registro.php?mensaje=error_registro"
    );
    exit;
}