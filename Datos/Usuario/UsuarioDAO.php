<?php


/* =====================================================
   REGISTRAR USUARIO
===================================================== */

function registrarUsuario(
    $conexion,
    $nombre,
    $apellido,
    $correo,
    $password,
    $rol
) {

    $sql = "INSERT INTO usuarios
            (nombre, apellido, correo, password, rol)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "sssss",
        $nombre,
        $apellido,
        $correo,
        $password,
        $rol
    );

    $resultado = $stmt->execute();

    $stmt->close();

    return $resultado;
}


/* =====================================================
   BUSCAR USUARIO POR CORREO
===================================================== */

function obtenerUsuarioPorCorreo(
    $conexion,
    $correo
) {

    $sql = "SELECT
                id_usuario,
                nombre,
                apellido,
                correo,
                password,
                rol
            FROM usuarios
            WHERE correo = ?";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "s",
        $correo
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $usuario = $resultado->fetch_assoc();

    $stmt->close();

    return $usuario;
}


/* =====================================================
   OBTENER USUARIO POR ID
===================================================== */

function obtenerUsuarioPorId(
    $conexion,
    $idUsuario
) {

    $sql = "SELECT
                id_usuario,
                nombre,
                apellido,
                correo,
                rol,
                fecha_registro
            FROM usuarios
            WHERE id_usuario = ?";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "i",
        $idUsuario
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $usuario = $resultado->fetch_assoc();

    $stmt->close();

    return $usuario;
}


/* =====================================================
   OBTENER TODOS LOS ESTUDIANTES
===================================================== */

function obtenerEstudiantes($conexion)
{
    $sql = "SELECT
                id_usuario,
                nombre,
                apellido,
                correo
            FROM usuarios
            WHERE rol = 'estudiante'
            ORDER BY apellido, nombre";

    $resultado = $conexion->query($sql);

    if (!$resultado) {
        return false;
    }

    return $resultado;
}