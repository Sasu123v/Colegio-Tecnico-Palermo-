<?php

function crearAnuncio(
    $conexion,
    $cursoId,
    $profesorId,
    $titulo,
    $contenido
) {

    $sql = "INSERT INTO anuncios
            (curso_id, profesor_id, titulo, contenido)
            VALUES (?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "iiss",
        $cursoId,
        $profesorId,
        $titulo,
        $contenido
    );

    $resultado = $stmt->execute();

    $stmt->close();

    return $resultado;
}


function obtenerAnunciosPorCurso(
    $conexion,
    $cursoId
) {

    $sql = "SELECT
                anuncios.*,
                usuarios.nombre,
                usuarios.apellido
            FROM anuncios
            INNER JOIN usuarios
                ON anuncios.profesor_id =
                   usuarios.id_usuario
            WHERE anuncios.curso_id = ?
            ORDER BY anuncios.fecha DESC";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param(
        "i",
        $cursoId
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $stmt->close();

    return $resultado;
}