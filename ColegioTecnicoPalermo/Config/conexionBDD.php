<?php

$servidor = "localhost";
$usuario = "root";
$password = "";
$baseDatos = "colegio_tecnico_palermo";


$conexion = new mysqli(
    $servidor,
    $usuario,
    $password,
    $baseDatos
);


if ($conexion->connect_error) {

    die(
        "Error de conexión con la base de datos: "
        . $conexion->connect_error
    );

}


$conexion->set_charset("utf8mb4");

?>