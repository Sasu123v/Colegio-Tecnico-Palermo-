<?php


/* =====================================================
   CREAR CURSO
===================================================== */

function crearCurso(
    $conexion,
    $nombre,
    $descripcion,
    $profesorId
) {

    $sql = "INSERT INTO cursos
            (nombre, descripcion, profesor_id)
            VALUES (?, ?, ?)";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "ssi",
        $nombre,
        $descripcion,
        $profesorId
    );

    $resultado = $stmt->execute();

    $stmt->close();

    return $resultado;
}


/* =====================================================
   OBTENER CURSOS DEL PROFESOR
===================================================== */

function obtenerCursosPorProfesor(
    $conexion,
    $profesorId
) {

    $sql = "SELECT
                id_curso,
                nombre,
                descripcion,
                profesor_id,
                fecha_creacion
            FROM cursos
            WHERE profesor_id = ?
            ORDER BY fecha_creacion DESC";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "i",
        $profesorId
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $stmt->close();

    return $resultado;
}


/* =====================================================
   OBTENER CURSOS DEL ESTUDIANTE
===================================================== */

function obtenerCursosPorEstudiante(
    $conexion,
    $estudianteId
) {

    $sql = "SELECT
                c.id_curso,
                c.nombre,
                c.descripcion,
                c.fecha_creacion,
                u.nombre AS profesor_nombre,
                u.apellido AS profesor_apellido
            FROM cursos c

            INNER JOIN estudiantes_cursos ec
                ON c.id_curso = ec.curso_id

            INNER JOIN usuarios u
                ON c.profesor_id = u.id_usuario

            WHERE ec.estudiante_id = ?

            ORDER BY c.fecha_creacion DESC";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "i",
        $estudianteId
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $stmt->close();

    return $resultado;
}


/* =====================================================
   OBTENER CURSO POR ID
===================================================== */

function obtenerCursoPorId(
    $conexion,
    $idCurso
) {

    $sql = "SELECT
                c.id_curso,
                c.nombre,
                c.descripcion,
                c.fecha_creacion,
                c.profesor_id,
                u.nombre AS profesor_nombre,
                u.apellido AS profesor_apellido
            FROM cursos c

            INNER JOIN usuarios u
                ON c.profesor_id = u.id_usuario

            WHERE c.id_curso = ?";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "i",
        $idCurso
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $curso = $resultado->fetch_assoc();

    $stmt->close();

    return $curso;
}


/* =====================================================
   INSCRIBIR ESTUDIANTE
===================================================== */

function inscribirEstudiante(
    $conexion,
    $estudianteId,
    $cursoId
) {

    $sql = "INSERT INTO estudiantes_cursos
            (estudiante_id, curso_id)
            VALUES (?, ?)";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "ii",
        $estudianteId,
        $cursoId
    );

    $resultado = $stmt->execute();

    $stmt->close();

    return $resultado;
}


/* =====================================================
   COMPROBAR INSCRIPCIÓN
===================================================== */

function estudianteEstaInscrito(
    $conexion,
    $estudianteId,
    $cursoId
) {

    $sql = "SELECT
                id_estudiante_curso
            FROM estudiantes_cursos
            WHERE estudiante_id = ?
            AND curso_id = ?";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "ii",
        $estudianteId,
        $cursoId
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $estaInscrito = $resultado->num_rows > 0;

    $stmt->close();

    return $estaInscrito;
}


/* =====================================================
   OBTENER ESTUDIANTES DE UN CURSO
===================================================== */

function obtenerEstudiantesCurso(
    $conexion,
    $cursoId
) {

    $sql = "SELECT
                u.id_usuario,
                u.nombre,
                u.apellido,
                u.correo

            FROM usuarios u

            INNER JOIN estudiantes_cursos ec
                ON u.id_usuario = ec.estudiante_id

            WHERE ec.curso_id = ?
            AND u.rol = 'estudiante'

            ORDER BY u.apellido, u.nombre";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return false;
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