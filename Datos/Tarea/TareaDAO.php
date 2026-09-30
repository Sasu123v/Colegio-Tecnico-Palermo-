<?php


/* =====================================================
   CREAR TAREA
===================================================== */

function crearTarea(
    $conexion,
    $cursoId,
    $titulo,
    $descripcion,
    $fechaEntrega,
    $idArchivo = null
) {

    $sql = "
        INSERT INTO tareas (
            curso_id,
            titulo,
            descripcion,
            fecha_entrega,
            id_archivo
        )
        VALUES (?, ?, ?, ?, ?)
    ";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "isssi",
        $cursoId,
        $titulo,
        $descripcion,
        $fechaEntrega,
        $idArchivo
    );

    $resultado = $stmt->execute();

    $stmt->close();

    return $resultado;
}


/* =====================================================
   OBTENER TAREAS DE UN CURSO
===================================================== */

function obtenerTareasPorCurso(
    $conexion,
    $cursoId
) {

    $sql = "
        SELECT
            tareas.*,
            archivos.nombre_original AS archivo_nombre
        FROM tareas

        LEFT JOIN archivos
            ON tareas.id_archivo = archivos.id_archivo

        WHERE tareas.curso_id = ?

        ORDER BY tareas.fecha_entrega ASC
    ";

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


/* =====================================================
   OBTENER UNA TAREA
===================================================== */

function obtenerTareaPorId(
    $conexion,
    $idTarea
) {

    $sql = "
        SELECT
            tareas.*,

            cursos.nombre AS nombre_curso,

            archivos.nombre_original,
            archivos.nombre_guardado,
            archivos.ruta,
            archivos.tipo,
            archivos.tamano

        FROM tareas

        INNER JOIN cursos
            ON tareas.curso_id = cursos.id_curso

        LEFT JOIN archivos
            ON tareas.id_archivo = archivos.id_archivo

        WHERE tareas.id_tarea = ?
    ";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param(
        "i",
        $idTarea
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $tarea = $resultado->fetch_assoc();

    $stmt->close();

    return $tarea;
}