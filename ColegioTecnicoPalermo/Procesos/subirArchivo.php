<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {

    header(
        "Location: ../Vistas/login.php?mensaje=no_autorizado"
    );

    exit;
}


require_once __DIR__ .
    "/../Config/conexionBDD.php";

require_once __DIR__ .
    "/../Datos/Archivo/ArchivoDAO.php";



/*
|--------------------------------------------------------------------------
| Verificar método
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header(
        "Location: ../Vistas/subirArchivo.php"
    );

    exit;
}



/*
|--------------------------------------------------------------------------
| Verificar archivo
|--------------------------------------------------------------------------
*/

if (!isset($_FILES["archivo"])) {

    header(
        "Location: ../Vistas/subirArchivo.php?mensaje=no_archivo"
    );

    exit;
}


$archivo = $_FILES["archivo"];



/*
|--------------------------------------------------------------------------
| Verificar error de subida
|--------------------------------------------------------------------------
*/

if ($archivo["error"] !== UPLOAD_ERR_OK) {

    header(
        "Location: ../Vistas/subirArchivo.php?mensaje=error_subida"
    );

    exit;
}



/*
|--------------------------------------------------------------------------
| Tamaño máximo: 10 MB
|--------------------------------------------------------------------------
*/

$maximo = 10 * 1024 * 1024;


if ($archivo["size"] > $maximo) {

    header(
        "Location: ../Vistas/subirArchivo.php?mensaje=archivo_grande"
    );

    exit;
}



/*
|--------------------------------------------------------------------------
| Extensiones permitidas
|--------------------------------------------------------------------------
*/

$extensionesPermitidas = [

    "pdf",
    "doc",
    "docx",
    "xls",
    "xlsx",
    "ppt",
    "pptx",
    "jpg",
    "jpeg",
    "png"

];



/*
|--------------------------------------------------------------------------
| Nombre original
|--------------------------------------------------------------------------
*/

$nombreOriginal = basename(
    $archivo["name"]
);



/*
|--------------------------------------------------------------------------
| Obtener extensión
|--------------------------------------------------------------------------
*/

$extension = strtolower(
    pathinfo(
        $nombreOriginal,
        PATHINFO_EXTENSION
    )
);



/*
|--------------------------------------------------------------------------
| Verificar extensión
|--------------------------------------------------------------------------
*/

if (!in_array(
    $extension,
    $extensionesPermitidas
)) {

    header(
        "Location: ../Vistas/subirArchivo.php?mensaje=tipo_no_permitido"
    );

    exit;
}



/*
|--------------------------------------------------------------------------
| Crear nombre único
|--------------------------------------------------------------------------
*/

$nombreGuardado =
    uniqid(
        "archivo_",
        true
    )
    . "."
    . $extension;



/*
|--------------------------------------------------------------------------
| Carpeta donde se guardará
|--------------------------------------------------------------------------
*/

$carpeta =
    __DIR__
    . "/../Archivos/entregas/";



/*
|--------------------------------------------------------------------------
| Crear carpeta si no existe
|--------------------------------------------------------------------------
*/

if (!is_dir($carpeta)) {

    mkdir(
        $carpeta,
        0777,
        true
    );

}



/*
|--------------------------------------------------------------------------
| Ruta física
|--------------------------------------------------------------------------
*/

$rutaFisica =
    $carpeta
    . $nombreGuardado;



/*
|--------------------------------------------------------------------------
| Mover archivo
|--------------------------------------------------------------------------
*/

if (!move_uploaded_file(
    $archivo["tmp_name"],
    $rutaFisica
)) {

    header(
        "Location: ../Vistas/subirArchivo.php?mensaje=error_guardado"
    );

    exit;
}



/*
|--------------------------------------------------------------------------
| Ruta que se guarda en la base de datos
|--------------------------------------------------------------------------
*/

$rutaBDD =
    "Archivos/entregas/"
    . $nombreGuardado;



/*
|--------------------------------------------------------------------------
| Datos adicionales
|--------------------------------------------------------------------------
*/

$idUsuario =
    $_SESSION["id_usuario"];

$tipo =
    $archivo["type"];

$tamano =
    $archivo["size"];



/*
|--------------------------------------------------------------------------
| Guardar información en la base de datos
|--------------------------------------------------------------------------
*/

$guardado = guardarArchivo(

    $conexion,

    $idUsuario,

    $nombreOriginal,

    $nombreGuardado,

    $rutaBDD,

    $tipo,

    $tamano

);



/*
|--------------------------------------------------------------------------
| Si falla la base de datos,
| eliminar el archivo físico
|--------------------------------------------------------------------------
*/

if (!$guardado) {

    if (file_exists($rutaFisica)) {

        unlink($rutaFisica);

    }


    header(
        "Location: ../Vistas/subirArchivo.php?mensaje=error_bdd"
    );

    exit;
}



/*
|--------------------------------------------------------------------------
| Todo salió correctamente
|--------------------------------------------------------------------------
*/

header(
    "Location: ../Vistas/subirArchivo.php?mensaje=exito"
);

exit;

?>
