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
//echo "hola aqui estoy";
switch ($Accion) {
    case 0:  ///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 0 realiza:

        //CON POST RECIBIMOS TODAS LAS VARIABLES DEL FORMULARIO POR EL SCRIPT "add_proveedor.php" QUE NESECITAMOS PARA INSERTAR
        $nombre = $conn->real_escape_string($_POST['valorNombre']);
        $direccion = $conn->real_escape_string($_POST['valorDireccion']);
        $colonia = $conn->real_escape_string($_POST['valorColonia']);
        $cp = $conn->real_escape_string($_POST['valorCP']);
        $rfc = $conn->real_escape_string($_POST['valorRFC']);
        $email = $conn->real_escape_string($_POST['valorEmail']);
        $telefono = $conn->real_escape_string($_POST['valorTelefono']);
        $dias_credito = $conn->real_escape_string($_POST['valorDias_Credito']);
        
        //VERIFICAMOS QUE NO HALLA UN PROVEEDOR CON LOS MISMOS DATOS
		if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `punto_venta_proveedores` WHERE nombre='$nombre' AND direccion='$direccion' AND colonia='$colonia' AND cp='$cp' AND rfc='$rfc' AND email='$email'"))>0){
            echo '<script >M.toast({html:"Ya se encuentra un cliente con los mismos datos registrados.", classes: "rounded"})</script>';
        }else{
            // SI NO HAY NUNGUNO IGUAL CREAMOS LA SENTECIA SQL  CON LA INFORMACION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
            $sql = "INSERT INTO `punto_venta_proveedores` (nombre, direccion, colonia, cp, rfc, email, telefono, dias_c, usuario, fecha) 
               VALUES('$nombre', '$direccion', '$colonia', '$cp', '$rfc', '$email', '$telefono', '$dias_credito','$id_user','$Fecha_hoy')";
            //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
			if(mysqli_query($conn, $sql)){
				echo '<script >M.toast({html:"El proveedor se dió de alta satisfactoriamente.", classes: "rounded"})</script>';	
			}else{
                echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';	
            }//FIN else DE ERROR
            echo '<script>recargar_proveedores()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
        }// FIN else DE BUSCAR PROVEEDOR IGUAL

        break;
    case 1:  ///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 1 realiza:

        //CON POST RECIBIMOS UN TEXTO DEL BUSCADOR VACIO O NO
        $Texto = $conn->real_escape_string($_POST['texto']);
        //VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
		if ($Texto != "") {
			//MOSTRARA LOS CLIENTES QUE SE ESTAN BUSCANDO Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql......
			$sql = "SELECT * FROM `punto_venta_proveedores` WHERE  nombre LIKE '%$Texto%'  OR id = '$Texto' OR rfc = '$Texto' OR colonia LIKE '%$Texto%' OR direccion LIKE '%$Texto%' ORDER BY id";	
		}else{//ESTA CONSULTA SE HARA SIEMPRE QUE NO ALLA NADA EN EL BUSCADOR Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql...
			$sql = "SELECT * FROM `punto_venta_proveedores`";
		}//FIN else $Texto VACIO O NO

        // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
		$consulta = mysqli_query($conn, $sql);		
		$contenido = '';//CREAMOS UNA VARIABLE VACIA PARA IR LLENANDO CON LA INFORMACION EN FORMATO

		//VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
		if (mysqli_num_rows($consulta) == 0) {
				echo '<script>M.toast({html:"No se encontraron proveedores.", classes: "rounded"})</script>';
        } else {
            //SI NO ESTA EN == 0 SI TIENE INFORMACION
            //La variable $contenido contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
            //RECORREMOS UNO A UNO LOS CLIENTES CON EL WHILE
            while($proveedor = mysqli_fetch_array($consulta)) {
                $id_user = $proveedor['usuario'];
				$user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `users` WHERE user_id=$id_user"));
				//Output
                $contenido .= '			
		          <tr>
		            <td>'.$proveedor['id'].'</td>
		            <td>'.$proveedor['nombre'].'</td>
		            <td>'.$proveedor['direccion'].'</td>
		            <td>'.$proveedor['colonia'].'</td>
		            <td>'.$proveedor['cp'].'</td>
		            <td>'.$proveedor['rfc'].'</td>
		            <td>'.$proveedor['email'].'</td>
		            <td>'.$proveedor['telefono'].'</td>
		            <td>'.$proveedor['dias_c'].'</td>
		            <td>'.$proveedor['usuario'].'</td>
		            <td>'.$proveedor['fecha'].'</td>
		            <td><form method="post" action="../views/editar_proveedor_pv.php"><input id="id" name="id" type="hidden" value="'.$proveedor['id'].'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
		            <td><a onclick="verificar_eliminar('.$proveedor['id'].')" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
		          </tr>';

			}//FIN while
        }//FIN else

        echo $contenido;// MOSTRAMOS LA INFORMACION HTML

        break;
    case 2:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 2 realiza:

        //CON POST RECIBIMOS TODAS LAS VARIABLES DEL FORMULARIO POR EL SCRIPT "editar_proveedor_pv.php" QUE NESECITAMOS PARA ACTUALIZAR
    	$id = $conn->real_escape_string($_POST['id']);
        $nombre = $conn->real_escape_string($_POST['valorNombre']);
        $direccion = $conn->real_escape_string($_POST['valorDireccion']);
        $colonia = $conn->real_escape_string($_POST['valorColonia']);
        $cp = $conn->real_escape_string($_POST['valorCP']);
        $rfc = $conn->real_escape_string($_POST['valorRFC']);
        $email = $conn->real_escape_string($_POST['valorEmail']);
        $telefono = $conn->real_escape_string($_POST['valorTelefono']);
        $dias_credito = $conn->real_escape_string($_POST['valorDias_Credito']);

        //CREAMOS LA SENTENCIA SQL PARA HACER LA ACTUALIZACION DE LA INFORMACION DEL CLIENTE Y LA GUARDAMOS EN UNA VARIABLE
		$sql = "UPDATE `punto_venta_proveedores` SET nombre = '$nombre', direccion = '$direccion', colonia = '$colonia', cp = '$cp', rfc = '$rfc', email = '$email', telefono = '$telefono', dias_c = '$dias_credito', usuario = '$id_user', fecha= '$Fecha_hoy' WHERE id = '$id'";
        //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
		if(mysqli_query($conn, $sql)){
			echo '<script >M.toast({html:"El proveedor se actualizo con exito.", classes: "rounded"})</script>';	
		}else{
			echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';	
		}//FIN else DE ERROR
		echo '<script>recargar_proveedores()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
        break;
    case 3:
        // $Accion es igual a 3 realiza:
        break;

}// FIN switch
mysqli_close($conn);
    
?>
