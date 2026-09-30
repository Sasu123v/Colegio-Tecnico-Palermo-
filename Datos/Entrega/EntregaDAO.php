<?php


/* =====================================================
   CREAR ENTREGA
===================================================== */

function crearEntrega(
    $conexion,
    $tareaId,
    $estudianteId,
    $respuesta,
    $idArchivo = null
) {

    $sql = "INSERT INTO entregas
            (
                tarea_id,
                estudiante_id,
                respuesta,
                id_archivo
            )
            VALUES (?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "iisi",
        $tareaId,
        $estudianteId,
        $respuesta,
        $idArchivo
    );

    $resultado = $stmt->execute();

    $stmt->close();

    return $resultado;
}


/* =====================================================
   OBTENER ENTREGA DEL ESTUDIANTE
===================================================== */

function obtenerEntregaEstudiante(
    $conexion,
    $tareaId,
    $estudianteId
) {

    $sql = "SELECT
                entregas.*,

                archivos.nombre_original,
                archivos.nombre_guardado,
                archivos.ruta,
                archivos.tipo,
                archivos.tamano

            FROM entregas

            LEFT JOIN archivos
                ON entregas.id_archivo =
                   archivos.id_archivo

            WHERE entregas.tarea_id = ?
            AND entregas.estudiante_id = ?

            ORDER BY entregas.fecha_entrega DESC

            LIMIT 1";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param(
        "ii",
        $tareaId,
        $estudianteId
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $entrega = $resultado->fetch_assoc();

    $stmt->close();

    return $entrega;
}


/* =====================================================
   OBTENER ENTREGAS POR TAREA
===================================================== */

function obtenerEntregasPorTarea(
    $conexion,
    $tareaId
) {

    $sql = "SELECT
                entregas.*,

                usuarios.nombre,
                usuarios.apellido,
                usuarios.correo,

                archivos.nombre_original,
                archivos.nombre_guardado,
                archivos.ruta,
                archivos.tipo,
                archivos.tamano

            FROM entregas

            INNER JOIN usuarios
                ON entregas.estudiante_id =
                   usuarios.id_usuario

            LEFT JOIN archivos
                ON entregas.id_archivo =
                   archivos.id_archivo

            WHERE entregas.tarea_id = ?

            ORDER BY entregas.fecha_entrega DESC";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param(
        "i",
        $tareaId
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $stmt->close();

    return $resultado;
}


/* =====================================================
   ACTUALIZAR ARCHIVO DE UNA ENTREGA
===================================================== */

function actualizarArchivoEntrega(
    $conexion,
    $idEntrega,
    $idArchivo
) {

    $sql = "UPDATE entregas
            SET id_archivo = ?
            WHERE id_entrega = ?";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "ii",
        $idArchivo,
        $idEntrega
    );

    $resultado = $stmt->execute();

    $stmt->close();

    return $resultado;
}


/* =====================================================
   ELIMINAR ARCHIVO DE UNA ENTREGA
===================================================== */

function eliminarArchivoEntrega(
    $conexion,
    $idEntrega
) {

    $sql = "UPDATE entregas
            SET id_archivo = NULL
            WHERE id_entrega = ?";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "i",
        $idEntrega
    );

    $resultado = $stmt->execute();

    $stmt->close();

    return $resultado;
}


/* =====================================================
   CALIFICAR ENTREGA
===================================================== */

function calificarEntrega(
    $conexion,
    $idEntrega,
    $calificacion
) {

    $sql = "UPDATE entregas
            SET calificacion = ?
            WHERE id_entrega = ?";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "di",
        $calificacion,
        $idEntrega
    );

    $resultado = $stmt->execute();

    $stmt->close();

    return $resultado;
}