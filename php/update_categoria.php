<?php 
include('../php/conexion.php');

$Nombre = $conn->real_escape_string($_POST['valorNombre']);
$id = $conn->real_escape_string($_POST['id']);
//o $consultaBusqueda sea igual a nombre + (espacio) + apellido
$sql = "UPDATE punto_venta_categorias SET nombre='$Nombre' WHERE id= $id";
if(mysqli_query($conn, $sql)){
	echo '<script>M.toast({html:"La categoria se actualizó correctamente.", classes: "rounded"})</script>';
	?>
	<script>
	    setTimeout("location.href='../views/categorias.php'", 800);
	</script>
	<?php
}else{
	echo '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
}

mysqli_close($conn);
?>