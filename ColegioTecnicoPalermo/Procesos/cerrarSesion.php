<?php

session_start();

$_SESSION = [];

session_destroy();

header("Location: ../Vistas/login.php?mensaje=sesion_cerrada");
exit;