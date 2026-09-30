<?php

function validarCampoObligatorio($valor)
{
    return trim($valor) !== "";
}


function validarNombre($nombre)
{
    $nombre = trim($nombre);

    return strlen($nombre) >= 2 &&
           strlen($nombre) <= 150;
}


function validarCorreo($correo)
{
    return filter_var(
        $correo,
        FILTER_VALIDATE_EMAIL
    ) !== false;
}


function validarPassword($password)
{
    return strlen($password) >= 6;
}


function validarId($id)
{
    return filter_var(
        $id,
        FILTER_VALIDATE_INT
    ) !== false && $id > 0;
}


function limpiarTexto($texto)
{
    return trim($texto);
}