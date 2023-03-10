<?php
//ARCHIVO QUE CONTIENE LA VARIABLE CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
//ARCHIVO QUE CONDICIONA QUE TENGAMOS ACCESO A ESTE ARCHIVO SOLO SI HAY SESSION INICIADA Y NOS PREMITE TIMAR LA INFORMACION DE ESTA
include('is_logged.php');
//DEFINIMOS LA ZONA  HORARIA
date_default_timezone_set('America/Mexico_City');
$id_user = $_SESSION['user_id'];// ID DEL USUARIO LOGEADO
$Fecha_hoy = date('Y-m-d');// FECHA ACTUAL

//CON METODO POST TOMAMOS UN VALOR DEL 0 AL 3 PARA VER QUE ACCION HACER (Para Insertar = 0, Consultar = 1, Actualizar = 2, Borrar = 3)
$Accion = $conn->real_escape_string($_POST['accion']);

//UN SWITCH EL CUAL DECIDIRA QUE ACCION REALIZA DEL CRUD (Para Insertar = 0, Consultar = 1, Actualizar = 2, Borrar = 3)
switch ($Accion) {
    case 0:  ///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 0 realiza:

    	//CON POST RECIBIMOS TODAS LAS VARIABLES DEL FORMULARIO POR EL SCRIPT "add_cliente.php" QUE NESECITAMOS PARA INSERTAR
    	$Nombre = $conn->real_escape_string($_POST['valorNombre']);
		$Telefono = $conn->real_escape_string($_POST['valorTelefono']);
		$Email = $conn->real_escape_string($_POST['valorEmail']);
		$RFC = $conn->real_escape_string($_POST['valorRFC']);
		$Direccion = $conn->real_escape_string($_POST['valorDireccion']);
		$Colonia = $conn->real_escape_string($_POST['valorColonia']);
		$Localidad = $conn->real_escape_string($_POST['valorLocalidad']);
		$CP = $conn->real_escape_string($_POST['valorCP']);

		//VERIFICAMOS QUE NO HALLA UN CLIENTE CON LOS MISMOS DATOS
		if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `punto-venta_clientes` WHERE(nombre='$Nombre' AND direccion='$Direccion' AND colonia='$Colonia' AND cp='$CP') OR rfc='$RFC' OR email='$Email'"))>0){
	 		echo '<script >M.toast({html:"Ya se encuentra un cliente con los mismos datos registrados.", classes: "rounded"})</script>';
	 	}else{
	 		// SI NO HAY NUNGUNO IGUAL CREAMOS LA SENTECIA SQL  CON LA INFORMACION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
	 		$sql = "INSERT INTO `punto-venta_clientes` (nombre, telefono, direccion, colonia, cp, rfc, email, localidad, usuario, fecha) 
				VALUES('$Nombre', '$Telefono', '$Direccion', '$Colonia', '$CP', '$RFC', '$Email', '$Localidad', '$id_user','$Fecha_hoy')";
			//VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
			if(mysqli_query($conn, $sql)){
				echo '<script >M.toast({html:"El cliente se dió de alta satisfactoriamente.", classes: "rounded"})</script>';	
				echo '<script>recargar_clientes()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
			}else{
				echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';	
			}//FIN else DE ERROR
	 	}// FIN else DE BUSCAR CLIENTE IGUAL

        break;
    case 1:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 1 realiza:

    	//CON POST RECIBIMOS UN TEXTO DEL BUSCADOR VACIO O NO DE "clientes_punto_venta.php"
    	$Texto = $conn->real_escape_string($_POST['texto']);

    	//VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
		if ($Texto != "") {
			//MOSTRARA LOS CLIENTES QUE SE ESTAN BUSCANDO Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql......
			$sql = "SELECT * FROM `punto-venta_clientes` WHERE  nombre LIKE '%$Texto%' OR id = '$Texto' OR rfc LIKE '%$Texto%' OR colonia LIKE '%$Texto%' OR localidad LIKE '%$Texto%' ORDER BY id";	
		}else{
			//ESTA CONSULTA SE HARA SIEMPRE QUE NO ALLA NADA EN EL BUSCADOR Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql...
			$sql = "SELECT * FROM `punto-venta_clientes`";
		}//FIN else $Texto VACIO O NO

		// REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
		$consulta = mysqli_query($conn, $sql);		
		$contenido = '';//CREAMOS UNA VARIABLE VACIA PARA IR LLENANDO CON LA INFORMACION EN FORMATO

		//VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
		if (mysqli_num_rows($consulta) == 0) {
				echo '<script>M.toast({html:"No se encontraron clientes.", classes: "rounded"})</script>';
			
		} else {
			//SI NO ESTA EN == 0 SI TIENE INFORMACION
			//La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
			//RECORREMOS UNO A UNO LOS CLIENTES CON EL WHILE	
			while($cliente = mysqli_fetch_array($consulta)) {
				$id_user = $cliente['usuario'];
				$user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `users` WHERE user_id=$id_user"));
				//Output
				$contenido .= '			
		          <tr>
		            <td>'.$cliente['id'].'</td>
		            <td>'.$cliente['nombre'].'</td>
		            <td>'.$cliente['telefono'].'</td>
		            <td>'.$cliente['rfc'].'</td>
		            <td>'.$cliente['email'].'</td>
		            <td>'.$cliente['direccion'].'</td>
		            <td>'.$cliente['colonia'].'</td>
		            <td>'.$cliente['localidad'].'</td>
		            <td>'.$cliente['cp'].'</td>
		            <td>'.$user['firstname'].'</td>
		            <td>'.$cliente['fecha'].'</td>
					<td><form method="post" action="../views/credito_abono_pv.php"><input id="no_cliente" name="no_cliente" type="hidden" value="'.$cliente['id'].'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">credit_card</i></button></form></td>
		            <td><form method="post" action="../views/editar_cliente_pv.php"><input id="id" name="id" type="hidden" value="'.$cliente['id'].'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
		            <td><a onclick="borrar_cliente_pv('.$cliente['id'].')" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
		          </tr>';
			}//FIN while
		}//FIN else
		echo $contenido;// MOSTRAMOS LA INFORMACION HTML
        break;
    case 2:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 2 realiza:

    	//CON POST RECIBIMOS TODAS LAS VARIABLES DEL FORMULARIO POR EL SCRIPT "editar_cliente_pv.php" QUE NESECITAMOS PARA ACTUALIZAR
    	$id = $conn->real_escape_string($_POST['id']);
    	$Nombre = $conn->real_escape_string($_POST['valorNombre']);
		$Telefono = $conn->real_escape_string($_POST['valorTelefono']);
		$Email = $conn->real_escape_string($_POST['valorEmail']);
		$RFC = $conn->real_escape_string($_POST['valorRFC']);
		$Direccion = $conn->real_escape_string($_POST['valorDireccion']);
		$Colonia = $conn->real_escape_string($_POST['valorColonia']);
		$Localidad = $conn->real_escape_string($_POST['valorLocalidad']);
		$CP = $conn->real_escape_string($_POST['valorCP']);

		//VERIFICAMOS QUE NO HALLA UN CLIENTE CON LOS MISMOS DATOS
		if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `punto-venta_clientes` WHERE (telefono = '$Telefono' OR rfc='$RFC' OR email='$Email') AND id != $id"))>0){
	 		echo '<script >M.toast({html:"El RFC, Telefono o Email ya se encuentra registrados en la BD.", classes: "rounded"})</script>';
	 	}else{
			//CREAMO LA SENTENCIA SQL PARA HACER LA ACTUALIZACION DE LA INFORMACION DEL CLIENTE Y LA GUARDAMOS EN UNA VARIABLE
			$sql = "UPDATE `punto-venta_clientes` SET nombre = '$Nombre', telefono = '$Telefono', email = '$Email', rfc = '$RFC', direccion = '$Direccion', colonia = '$Colonia', localidad = '$Localidad', cp = '$CP' WHERE id = '$id'";
			//VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
			if(mysqli_query($conn, $sql)){
				echo '<script >M.toast({html:"El cliente se actualizo con exito.", classes: "rounded"})</script>';	
				echo '<script>recargar_clientes()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
			}else{
				echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';	
			}//FIN else DE ERROR
		}// FIn else Validacion
        break;
    case 3:
        // $Accion es igual a 3 realiza:
    	//CON POST RECIBIMOS LA VARIABLE DEL BOTON POR EL SCRIPT DE "clientes_punto_venta.php" QUE NESECITAMOS PARA BORRAR
    	$id = $conn->real_escape_string($_POST['id']);
    	//Obtenemos la informacion del Usuario
    	$User = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `users` WHERE user_id = $id_user"));
    	//SE VERIFICA SI EL USUARIO LOGEADO TIENE PERMISO DE BORRAR CLIENTES
    	if ($User['b_clientes'] == 1) {
    		#SELECCIONAMOS LA INFORMACION A BORRAR
    		$cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto-venta_clientes` WHERE id = $id"));
    		#CREAMOS EL SQL DE LA INSERCION A LA TABLA  `pv_borrar_cliente` PARA NO PERDER INFORMACION
			$sql = "INSERT INTO `pv_borrar_cliente` (id_cliente, nombre, telefono, direccion, colonia, cp, rfc, email, localidad, registro, borro, fecha_borro) 
				VALUES($id, '".$cliente['nombre']."', '".$cliente['telefono']."', '".$cliente['direccion']."', '".$cliente['colonia']."', '".$cliente['cp']."', '".$cliente['rfc']."', '".$cliente['email']."', '".$cliente['localidad']."', '".$cliente['usuario']."', '$id_user','$Fecha_hoy')";
			//VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
			if(mysqli_query($conn, $sql)){
				//SI DE CREA LA INSERCION PROCEDEMOS A BORRRAR DE LA TABLA `punto-venta_clientes`
	    		#VERIFICAMOS QUE SE BORRE CORRECTAMENTE EL CLIENTE DE `punto-venta_clientes`
				if(mysqli_query($conn, "DELETE FROM `punto-venta_clientes` WHERE `punto-venta_clientes`.`id` = $id")){
				  #SI ES ELIMINADO MANDAR MSJ CON ALERTA
				  echo '<script >M.toast({html:"Cliente borrado con exito.", classes: "rounded"})</script>';
				}else{
				  #SI NO ES BORRADO MANDAR UN MSJ CON ALERTA
				  echo "<script >M.toast({html: 'Ha ocurrido un error.', classes: 'rounded'});/script>";
				}
				echo '<script>recargar_clientes()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
			}
	    }else{
			echo '<script >M.toast({html:"Permiso denegado.", classes: "rounded"});
			M.toast({html:"Comunicate con un administrador.", classes: "rounded"});</script>';
	    }   
    	break;
	case 4:
		// $Accion es igual a 4 realiza:

    	//CON POST RECIBIMOS UN TEXTO DEL BUSCADOR VACIO O NO DE "add_venta.php"
    	$Texto = $conn->real_escape_string($_POST['texto']);

    	//VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
		if ($Texto != "") {
			//MOSTRARA LOS CLIENTES QUE SE ESTAN BUSCANDO Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql......
			$sql = "SELECT * FROM `punto-venta_clientes` WHERE  nombre LIKE '%$Texto%' OR id = '$Texto' OR rfc LIKE '%$Texto%' OR colonia LIKE '%$Texto%' OR localidad LIKE '%$Texto%' ORDER BY id";	
		}else{
			//ESTA CONSULTA SE HARA SIEMPRE QUE NO ALLA NADA EN EL BUSCADOR Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql...
			$sql = "SELECT * FROM `punto-venta_clientes`";
		}//FIN else $Texto VACIO O NO

		// REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
		$consulta = mysqli_query($conn, $sql);		

		//VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
		if (mysqli_num_rows($consulta) == 0) {
			echo '<script>M.toast({html:"No se encontraron clientes.", classes: "rounded"})</script>';
		} else{
			?>
			<?php
			//SI NO ESTA EN == 0 SI TIENE INFORMACION
			// REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
			$consulta = mysqli_query($conn, $sql);      
			?>
			<div class="row">
				<div class="hide-on-small-only col s1"><br></div>
				<table class="col s12 m10 l10">
					<thead>
						<tr>
						<th>Id</th>
						<th>Nombre</th>
						<th>Teléfono</th>
						<th>RFC</th>
						<th>E-mail</th>
						<th>Dirección</th>
						<th>Colonia</th>
						<th>Localidad</th>
						<th>C.P.</th>
						</tr>
					</thead>
				  	<tbody>
						<?php
						//VERIFICAMOS SI HAY ARRTICULOS EN LA TABLA
						if(mysqli_num_rows($consulta)>0){
							while($cliente = mysqli_fetch_array($consulta)){
								?>
								<!-- Output -->
								<tr>
									<td><?php echo $cliente['id'] ?></td>
									<td><?php echo $cliente['nombre'] ?></td>
									<td><?php echo $cliente['telefono'] ?></td>
									<td><?php echo $cliente['rfc'] ?></td>
									<td><?php echo $cliente['email'] ?></td>
									<td><?php echo $cliente['direccion'] ?></td>
									<td><?php echo $cliente['colonia'] ?></td>
									<td><?php echo $cliente['localidad'] ?></td>
									<td><?php echo $cliente['cp'] ?></td>
									<!-- <td><a href="add_venta.php?id=<?php //echo $cliente['id'] ?>" class="waves-effect waves-light btn-small indigo right">Agregar</a></td> -->
									<td><a onclick="MostrarCliente(<?php echo $cliente['id'] ?>);" class="waves-effect waves-light btn-small indigo right">Agregar</a></td>
								</tr>
								<?php
							}//FIN WHILE
						}else{
							echo '<tr><td></td><td></td><td><h6> Sin Artículos </h6></td></tr>';
						}//FIN ELSE
				   		?>                
				  	</tbody>
				</table>
			</div>
			<?php
		}//FIN else
    break;
}// FIN switch

mysqli_close($conn);
?>