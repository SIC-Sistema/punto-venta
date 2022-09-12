<?php 
include('../php/conexion.php');


$Accion = $conn->real_escape_string($_POST['accion']);
$Nombre = $conn->real_escape_string($_POST['valorNombre']);

echo $Accion;
echo $Nombre;