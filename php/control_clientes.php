<?php 
//ARCHIVO QUE CONTIENE LA VARIABLE CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
//ARCHIVO QUE CONDICIONA QUE TENGAMOS ACCESO A ESTE ARCHIVO SOLO SI HAY SESSION INICIADA Y NOS PREMITE TIMAR LA INFORMACION DE ESTA
include('is_logged.php');
//DEFINIMOS LA ZONA  HORARIA
date_default_timezone_set('America/Mexico_City');
$id_user = $_SESSION['user_id'];// ID DEL USUARIO LOGEADO
$Fecha_hoy = date('Y-m-d');// FECHA ACTUAL

//CON METODO POST TOMAMOS UN VALOR DEL 0 AL 3 PARA VER QUE ACCION HACER (Para Insertar = 0, Consultar = 1, Actualizar = 2, Borar = 3)
$Accion = $conn->real_escape_string($_POST['accion']);

//UN SWITCH EL CUAL DECIDIRA QUE ACCION REALIZA DEL CRUD (Para Insertar = 0, Consultar = 1, Actualizar = 2, Borar = 3)
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
		if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `punto-venta_clientes` WHERE nombre='$Nombre' AND telefono='$Telefono' AND email='$Email' AND rfc='$RFC' AND cp='$CP'"))>0){
	 		echo '<script >M.toast({html:"Ya se encuentra un cliente con los mismos datos registrados.", classes: "rounded"})</script>';
	 	}else{
	 		// SI NO HAY NUNGUNO IGUAL CREAMOS LA SENTECIA SQL  CON LA INFORMACION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
	 		$sql = "INSERT INTO `punto-venta_clientes` (nombre, telefono, direccion, colonia, cp, rfc, email, localidad, usuario, fecha) 
				VALUES('$Nombre', '$Telefono', '$Direccion', '$Colonia', '$CP', '$RFC', '$Email', '$Localidad', '$id_user','$Fecha_hoy')";
			//VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
			if(mysqli_query($conn, $sql)){
				echo '<script >M.toast({html:"El cliente se dió de alta satisfactoriamente.", classes: "rounded"})</script>';	
			}else{
				echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';	
			}//FIN else DE ERROR
			echo '<script>recargar_clientes()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
	 	}// FIN else DE BUSCAR CLIENTE IGUAL

        break;
    case 1:
        // $Accion es igual a 1 realiza:
    	//CON POST RECIBIMOS UN TEXTO DEL BUSCADOR VACIO O NO
    	$Texto = $conn->real_escape_string($_POST['texto']);

    	//VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
		if ($Texto != "") {
			//MOSTRARA LOS CLIENTES QUE SE ESTAN BUSCANDO Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql......
			$sql = "SELECT * FROM `punto-venta_clientes` WHERE  nombre LIKE '%$Texto%'  OR id = '$Texto' OR rfc = '$Texto' OR colonia LIKE '%$Texto%' OR localidad LIKE '%$Texto%' ORDER BY id";	
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
		            <td>'.$cliente['usuario'].'</td>
		            <td>'.$cliente['fecha'].'</td>
		            <td><form method="post" action="../views/editar_cliente.php"><input id="no_cliente" name="no_cliente" type="hidden" value="'.$cliente['id'].'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
		            <td><a onclick="verificar_eliminar('.$cliente['id'].')" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
		          </tr>';

			}//FIN while
		}//FIN else

		echo $contenido;// MOSTRAMOS LA INFORMACION HTML


        break;
    case 2:
        // $Accion es igual a 2 realiza:
        break;
    case 3:
        // $Accion es igual a 3 realiza:
        break;
}// FIN switch
mysqli_close($conn);