<?php
require_once 'conexion.php';
$conn = conectarBD();
$conn->query("UPDATE paises_del_mundo SET bandera = '' WHERE bandera NOT LIKE 'http%' AND bandera != ''");
echo 'Banderas inválidas limpiadas.';
?>