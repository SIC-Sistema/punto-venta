<?php
include ("../php/conexion.php");

$Texto = $conn->real_escape_string($_POST['texto']);

$mensaje = '';
$sql = "SELECT * FROM punto_venta_categorias";
if ($Texto !="") {
	$sql = "SELECT * FROM punto_venta_categorias WHERE nombre LIKE '%$Texto%' OR id = '$Texto'";
}

$consulta =mysqli_query($conn, $sql);
$filas = mysqli_num_rows($consulta);

if ($filas == 0) {
	$mensaje = '<script>M.toast({html:"No se encontraron categorias.", classes: "rounded"})</script>';
}else{
	//La variable $resultados contiene el array que se genera en la consulta, asi que obtenemos los datos y los mostramos en un bucle.
	while($resultados = mysqli_fetch_array($consulta)){

		//Output / Salida

		$mensaje .= '
			<tr>
                <td>'.$resultados['id'].'</td>
		        <td>'.$resultados['nombre'].'</td>
		        <td><form method="post" action="../views/editar_categoria.php"><input name="no_categoria" type="hidden" value="'.$resultados['id'].'"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
		    </tr>';
	}//Fin while $resultados
} //Fin else $filas

echo $mensaje;
mysqli_close($conn);
?>