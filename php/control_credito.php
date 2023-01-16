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
		if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `punto_venta_proveedores` WHERE (nombre='$nombre' AND direccion='$direccion' AND colonia='$colonia' AND cp='$cp') OR rfc='$rfc' OR email='$email'"))>0){
            echo '<script >M.toast({html:"Ya se encuentra un cliente con los mismos datos registrados.", classes: "rounded"})</script>';
        }else{
            // SI NO HAY NUNGUNO IGUAL CREAMOS LA SENTECIA SQL  CON LA INFORMACION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
            $sql = "INSERT INTO `punto_venta_proveedores` (nombre, direccion, colonia, cp, rfc, email, telefono, dias_c, usuario, fecha) 
               VALUES('$nombre', '$direccion', '$colonia', '$cp', '$rfc', '$email', '$telefono', '$dias_credito','$id_user','$Fecha_hoy')";
            //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
			if(mysqli_query($conn, $sql)){
				echo '<script >M.toast({html:"El proveedor se dió de alta satisfactoriamente.", classes: "rounded"})</script>';	
                echo '<script>recargar_proveedores()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
			}else{
                echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';	
            }//FIN else DE ERROR
        }// FIN else DE BUSCAR PROVEEDOR IGUAL

        break;
    case 1:  ///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 1 realiza:

        //CON POST RECIBIMOS UN TEXTO DEL BUSCADOR VACIO O NO de "credito.php"
        $Texto = $conn->real_escape_string($_POST['texto']);
        //VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
		if ($Texto != "") {
			//MOSTRARA LOS PROVEEDORES QUE SE ESTAN BUSCANDO Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql......
			$sql = "SELECT * FROM `punto_venta_credito` WHERE  id = '$Texto' OR id_cliente LIKE '%$Texto%' OR id_venta LIKE '%$Texto%' OR fecha LIKE '%$Texto%' OR total LIKE '%$Texto%' OR usuario LIKE '%$Texto%' ORDER BY id";	
		}else{//ESTA CONSULTA SE HARA SIEMPRE QUE NO ALLA NADA EN EL BUSCADOR Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql...
			$sql = "SELECT * FROM `punto_venta_credito`";
		}//FIN else $Texto VACIO O NO

        // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
		$consulta = mysqli_query($conn, $sql);		
		$contenido = '';//CREAMOS UNA VARIABLE VACIA PARA IR LLENANDO CON LA INFORMACION EN FORMATO

		//VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
		if (mysqli_num_rows($consulta) == 0) {
				echo '<script>M.toast({html:"No se encontraron creditos.", classes: "rounded"})</script>';
        } else {
            //SI NO ESTA EN == 0 SI TIENE INFORMACION
            //La variable $contenido contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
            //RECORREMOS UNO A UNO LOS PROVEEDORES CON EL WHILE
            while($credito = mysqli_fetch_array($consulta)) {
                $id_user = $credito['usuario'];
                $id_cliente = $credito['id_cliente'];
				$user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `users` WHERE user_id=$id_user"));
                $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto-venta_clientes` WHERE id=$id_cliente"));
                $a1=$credito['id_cliente'];
                if($a1 == 0 OR $a1 == "" ){
                    $a1 = 'Venta Público';
                }
				//Output
                $contenido .= '			
		          <tr>
		            <td>'.$credito['id'].'</td>
		            <td>'.$cliente['nombre'].' ['.$a1.']</td>
		            <td>'.$credito['id_venta'].'</td>
		            <td>'.$credito['fecha'].'</td>
		            <td>'.$credito['hora'].'</td>
		            <td>'."$".$credito['total'].'</td>
		            <td>'.$user['firstname'].'</td>
		            <td><form method="post" action="../views/credito_abono_pv.php"><input id="no_cliente" name="no_cliente" type="hidden" value="'.$id_cliente.'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">credit_card</i></button></form></td>
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
         //VERIFICAMOS QUE NO HALLA UN PROVEEDOR RFC o CORREO IGUAL Y NO SE REPITA
        if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `punto_venta_proveedores` WHERE (rfc='$rfc' OR email='$email') AND id != $id"))>0){
            echo '<script >M.toast({html:"RFC o Email repetido.", classes: "rounded"})</script>';
        }else{
            //CREAMOS LA SENTENCIA SQL PARA HACER LA ACTUALIZACION DE LA INFORMACION DEL PROVEEDOR Y LA GUARDAMOS EN UNA VARIABLE
    		$sql = "UPDATE `punto_venta_proveedores` SET nombre = '$nombre', direccion = '$direccion', colonia = '$colonia', cp = '$cp', rfc = '$rfc', email = '$email', telefono = '$telefono', dias_c = '$dias_credito', usuario = '$id_user', fecha= '$Fecha_hoy' WHERE id = '$id'";
            //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
    		if(mysqli_query($conn, $sql)){
    			echo '<script >M.toast({html:"El proveedor se actualizo con exito.", classes: "rounded"})</script>';
                echo '<script>recargar_proveedores()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
	
    		}else{
    			echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';	
    		}//FIN else DE ERROR
    	}//FIN else VALIDAR
        break;
    case 3:
        // $Accion es igual a 3 realiza:
        //CON POST RECIBIMOS LA VARIABLE DEL BOTON POR EL SCRIPT DE "verificar_eliminar_credito.php" QUE NESECITAMOS PARA BORRAR
        $id = $conn->real_escape_string($_POST['valorIdPago']);
        $motivo = $conn->real_escape_string($_POST['valorMotivo']);
        #SELECCIONAMOS LA INFORMACION A BORRAR
        $credito = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_credito` WHERE id = $id"));
        
        //OBTENEMOS LAS VARIABLES QUE NECECITAMOS
        $id_cliente = $credito['id_cliente'];
        $id_venta = $credito['id_venta'];
        $fecha_credito = $credito['fecha'];
        $hora_credito = $credito['hora'];
        $cambio = $credito['tipo_cambio'];
        $total = $credito['total'];
        $borro = $credito['usuario'];

        #CREAMOS EL SQL DE LA INSERCION A LA TABLA  `pv_borrar_cliente` PARA NO PERDER INFORMACION
        $sql = "INSERT INTO `pv_borrar_credito` (id_cliente, id_venta, fecha, hora, tipo_cambio, total, registro, borro, fecha_borro, motivo) VALUES($id_cliente, $id_venta, '$fecha_credito', '$hora_credito', '$cambio', $total, $borro, $id_user,'$Fecha_hoy', '$motivo')";
        //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
        if(mysqli_query($conn, $sql)){
            //SI DE CREA LA INSERCION PROCEDEMOS A BORRRAR DE LA TABLA `punto_venta_credito`
        	#VERIFICAMOS QUE SE BORRE CORRECTAMENTE EL CREDITO DE `punto_venta_credito`
            if(mysqli_query($conn, "DELETE FROM `punto_venta_credito` WHERE `punto_venta_credito`.`id` = $id")){
                #SI ES ELIMINADO MANDAR MSJ CON ALERTA
                echo '<script >M.toast({html:"Crédito borrado con exito de la tabla creditos.", classes: "rounded"})</script>';
                
                if(mysqli_query($conn, "DELETE FROM `punto_venta_ventas` WHERE `punto_venta_ventas`.`id_venta` = $id_venta")){
                    echo '<script >M.toast({html:"Crédito borrado con exito de la tabla ventas.", classes: "rounded"})</script>';
                    
                    if(mysqli_query($conn, "DELETE FROM `punto_venta_detalle_venta` WHERE `punto_venta_detalle_venta`.`id_venta` = $id_venta")){
                        echo '<script >M.toast({html:"Crédito borrado con exito de la tabla de detalle venta.", classes: "rounded"})</script>';
                    }
                }  
                echo '<script>recargar_credito()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
            }else{
                #SI NO ES BORRADO MANDAR UN MSJ CON ALERTA
    		    echo "<script >M.toast({html: 'Ha ocurrido un error al borrar crédito.', classes: 'rounded'});/script>";
            }
        }else{
            echo "<script >M.toast({html: 'Error al insertar en la tabla de pv_borrar_credito.', classes: 'rounded'});/script>";
        }
        break;
}// FIN switch
mysqli_close($conn);
    
?>