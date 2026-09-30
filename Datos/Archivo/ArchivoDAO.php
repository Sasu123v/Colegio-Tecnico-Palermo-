<?php

function guardarArchivo(
    $conexion,
    $id_usuario,
    $nombre_original,
    $nombre_guardado,
    $ruta,
    $tipo,
    $tamano
) {

    $sql = "
        INSERT INTO archivos (
            id_usuario,
            nombre_original,
            nombre_guardado,
            ruta,
            tipo,
            tamano
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "issssi",
        $id_usuario,
        $nombre_original,
        $nombre_guardado,
        $ruta,
        $tipo,
        $tamano
    );

    $resultado = $stmt->execute();

    if (!$resultado) {
        $stmt->close();
        return false;
    }

    $idArchivo = $conexion->insert_id;

    $stmt->close();

    return $idArchivo;
}


/*
 * Obtiene un archivo por su ID.
 */

function obtenerArchivoPorId(
    $conexion,
    $idArchivo
) {

    $sql = "
        SELECT *
        FROM archivos
        WHERE id_archivo = ?
    ";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param(
        "i",
        $idArchivo
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $archivo = $resultado->fetch_assoc();

    $stmt->close();

    return $archivo;
}