<?php
//ARCHIVO QUE CONTIENE LA VARIABLE CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
//ARCHIVO QUE CONDICIONA QUE TENGAMOS ACCESO A ESTE ARCHIVO SOLO SI HAY SESSION INICIADA Y NOS PREMITE TIMAR LA INFORMACION DE ESTA
include('is_logged.php');

// BUSCAMOS TODAS LAS VENTAS EN ESTATUS = 0 en proceso y seran BORRADAS CUANDO SE EJECUETE ESTE SCRÏP
$consulta = mysqli_query($conn, "SELECT * FROM `punto_venta_ventas` WHERE estatus = 0"); 
//VERIFICAMOS SI HAY VENTAS POR BORRAR
if(mysqli_num_rows($consulta)>0){
    //RECORREMOS CON UN WHILE UNA POR UNA LAS VENTAS
    while($venta = mysqli_fetch_array($consulta)){  
    	$id_venta = $venta['id'];
    	if (mysqli_query($conn, "DELETE FROM `punto_venta_ventas` WHERE id = $id_venta")) {
    		echo "Venta N° $id_venta Borrada! <br>";
    	}else{
    		echo "Error al Borrar Venta N° $id_venta";
    	}
    }// FIN WHILE
}else{
	echo 'No Encontro Ventas (estatus = 0)';
}// FIN IF
?>