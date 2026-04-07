<?php
require_once 'conexion.php';
$conn = conectarBD();
$result = $conn->query('SELECT COUNT(*) as total FROM paises_del_mundo');
$row = $result->fetch_assoc();
echo 'Total países: ' . $row['total'];
?>