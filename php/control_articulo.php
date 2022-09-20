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

        //CON POST RECIBIMOS TODAS LAS VARIABLES DEL FORMULARIO POR EL SCRIPT "add_articulo.php" QUE NESECITAMOS PARA INSERTAR
        $codigo = $conn->real_escape_string($_POST['valorCodigo']);
        $Nombre = $conn->real_escape_string($_POST['valorNombre']);
        $descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
        $precio = $conn->real_escape_string($_POST['valorPrecio']);
        $unidad = $conn->real_escape_string($_POST['valorUnidad']);        
        $CFiscal = $conn->real_escape_string($_POST['valorCFiscal']);        
        //VERIFICAMOS QUE NO HALLA UN ARTICULO CON LOS MISMOS DATOS
		if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `punto_venta_articulos` WHERE codigo='$codigo' OR codigo_fiscal='$CFiscal'"))>0){
            echo '<script >M.toast({html:"Ya se encuentra un articulo con el mismo Codigo.", classes: "rounded"})</script>';
        }else{
            // SI NO HAY NUNGUNO IGUAL CREAMOS LA SENTECIA SQL  CON LA INFORMACION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
            $sql = "INSERT INTO `punto_venta_articulos` (codigo, nombre, descripcion, precio, unidad, codigo_fiscal, usuario, fecha) 
               VALUES('$codigo', '$Nombre', '$descripcion', '$precio', '$unidad', '$CFiscal','$id_user','$Fecha_hoy')";
            //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
			if(mysqli_query($conn, $sql)){
				echo '<script >M.toast({html:"El artículo se dió de alta satisfactoriamente.", classes: "rounded"})</script>';	
                echo '<script>recargar_articulo()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
			}else{
                echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';	
            }//FIN else DE ERROR
            
        }// FIN else DE BUSCAR ARTICULO IGUAL

        break;
    case 1:  ///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 1 realiza:

        //CON POST RECIBIMOS UN TEXTO DEL BUSCADOR VACIO O NO de "punto_venta_articulos.php"
        $Texto = $conn->real_escape_string($_POST['texto']);
        //VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
		if ($Texto != "") {
			//MOSTRARA LOS ARTICULOS QUE SE ESTAN BUSCANDO Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql......
			$sql = "SELECT * FROM `punto_venta_articulos` WHERE  codigo LIKE '%$Texto%'  OR id = '$Texto' OR descripcion LIKE '%$Texto%' OR precio LIKE '%$Texto%' OR unidad LIKE '%$Texto%' ORDER BY id";	
		}else{//ESTA CONSULTA SE HARA SIEMPRE QUE NO ALLA NADA EN EL BUSCADOR Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql...
			$sql = "SELECT * FROM `punto_venta_articulos`";
		}//FIN else $Texto VACIO O NO

        // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
		$consulta = mysqli_query($conn, $sql);		
		$contenido = '';//CREAMOS UNA VARIABLE VACIA PARA IR LLENANDO CON LA INFORMACION EN FORMATO

		//VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
		if (mysqli_num_rows($consulta) == 0) {
				echo '<script>M.toast({html:"No se encontraron artículos.", classes: "rounded"})</script>';
        } else {
            //SI NO ESTA EN == 0 SI TIENE INFORMACION
            //La variable $contenido contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
            //RECORREMOS UNO A UNO LOS ARTICULOS CON EL WHILE
            while($articulo = mysqli_fetch_array($consulta)) {
                $id_user = $articulo['usuario'];
				$user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `users` WHERE user_id=$id_user"));
				//Output
                $contenido .= '			
		          <tr>
		            <td>'.$articulo['codigo'].'</td>
                    <td>'.$articulo['nombre'].'</td>
		            <td>'.$articulo['descripcion'].'</td>
		            <td>$'.$articulo['precio'].'</td>
                    <td>'.$articulo['unidad'].'</td>
		            <td>'.$articulo['codigo_fiscal'].'</td>
		            <td>'.$user['firstname'].'</td>
		            <td>'.$articulo['fecha'].'</td>
		            <td><form method="post" action="../views/editar_articulo_pv.php"><input id="id" name="id" type="hidden" value="'.$articulo['id'].'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
		            <td><a onclick="borrar_articulo_pv('.$articulo['id'].')" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
		          </tr>';

			}//FIN while
        }//FIN else

        echo $contenido;// MOSTRAMOS LA INFORMACION HTML

        break;
    case 2:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 2 realiza:

        //CON POST RECIBIMOS TODAS LAS VARIABLES DEL FORMULARIO POR EL SCRIPT "editar_articulo_pv.php" QUE NESECITAMOS PARA ACTUALIZAR
    	$id = $conn->real_escape_string($_POST['id']);
        $codigo = $conn->real_escape_string($_POST['valorCodigo']);
        $Nombre = $conn->real_escape_string($_POST['valorNombre']);
        $descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
        $precio = $conn->real_escape_string($_POST['valorPrecio']);
        $unidad = $conn->real_escape_string($_POST['valorUnidad']);        
        $CFiscal = $conn->real_escape_string($_POST['valorCFiscal']);        
        //VERIFICAMOS QUE NO HALLA UN ARTICULO CON LOS MISMOS DATOS
        if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `punto_venta_articulos` WHERE (codigo='$codigo' OR codigo_fiscal='$CFiscal') AND id != $id"))>0){
            echo '<script >M.toast({html:"Ya se encuentra un articulo con el mismo Codigo.", classes: "rounded"})</script>';
        }else{
            //CREAMOS LA SENTENCIA SQL PARA HACER LA ACTUALIZACION DE LA INFORMACION DEL CLIENTE Y LA GUARDAMOS EN UNA VARIABLE
    		$sql = "UPDATE `punto_venta_articulos` SET codigo = '$codigo', nombre = '$Nombre', descripcion = '$descripcion', precio = '$precio', unidad = '$unidad', codigo_fiscal = '$CFiscal', usuario = '$id_user', fecha= '$Fecha_hoy' WHERE id = '$id'";
            //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
    		if(mysqli_query($conn, $sql)){
    			echo '<script >M.toast({html:"El artículo se actualizo con exito.", classes: "rounded"})</script>';	
                echo '<script>recargar_articulo()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
    		}else{
    			echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';	
    		}//FIN else DE ERROR
    	}// fin else verificacion	
        break;
    case 3:
        // $Accion es igual a 3 realiza:
        //CON POST RECIBIMOS LA VARIABLE DEL BOTON POR EL SCRIPT DE "articulos_punto_venta.php" QUE NESECITAMOS PARA BORRAR
        $id = $conn->real_escape_string($_POST['id']);
    	//Obtenemos la informacion del Usuario
        $User = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `users` WHERE user_id = $id_user"));
        //SE VERIFICA SI EL USUARIO LOGEADO TIENE PERMISO DE BORRAR CLIENTES
        if ($User['b_articulos'] == 1) {
        #VERIFICAMOS QUE SE BORRE CORRECTAMENTE EL CLIENTE DE `punto_venta_articulos`
        if(mysqli_query($conn, "DELETE FROM `punto_venta_articulos` WHERE `punto_venta_articulos`.`id` = $id")){
        #SI ES ELIMINADO MANDAR MSJ CON ALERTA
            echo '<script >M.toast({html:"Articulo borrado con exito.", classes: "rounded"})</script>';
        }else{
        #SI NO ES BORRADO MANDAR UN MSJ CON ALERTA
            echo "<script >M.toast({html: 'Ha ocurrido un error.', classes: 'rounded'});/script>";
        }
        echo '<script>recargar_articulo()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
        }else{
            echo '<script >M.toast({html:"Permiso denegado.", classes: "rounded"});
            M.toast({html:"Comunicate con un administrador.", classes: "rounded"});</script>';
        }   
        break;
}// FIN switch
mysqli_close($conn);
    
?>