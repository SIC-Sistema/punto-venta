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

        //CON POST RECIBIMOS TODAS LAS VARIABLES DEL FORMULARIO QUE NESECITAMOS PARA INSERTAR
        $Nombre = $conn->real_escape_string($_POST['valorNombre']);     
        //VERIFICAMOS QUE NO HALLA UNA CATEGORIA CON LOS MISMOS DATOS
		if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `punto_venta_categorias` WHERE nombre='$Nombre' "))>0){
            echo '<script >M.toast({html:"Ya se encuentra una categoria con el mismo Nombre.", classes: "rounded"})</script>';
        }else{
            // SI NO HAY NUNGUNO IGUAL CREAMOS LA SENTECIA SQL  CON LA INFORMACION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
            $sql = "INSERT INTO `punto_venta_categorias` (nombre, usuario, fecha) 
               VALUES('$Nombre','$id_user','$Fecha_hoy')";
            //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
			if(mysqli_query($conn, $sql)){
				echo '<script >M.toast({html:"La categoria se registró exitosamente.", classes: "rounded"})</script>';	
                echo '<script>recargar_categoria()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
			}else{
                echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';	
            }//FIN else DE ERROR            
        }// FIN else DE BUSCAR CATEGORIA IGUAL

        break;
    case 1:  ///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 1 realiza:

        //CON POST RECIBIMOS UN TEXTO DEL BUSCADOR VACIO O NO de "categorias_punto_venta.php"
        $Texto = $conn->real_escape_string($_POST['texto']);
        //VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
		if ($Texto != "") {
			//MOSTRARA LAS CATEGORIAS QUE SE ESTAN BUSCANDO Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql......
			$sql = "SELECT * FROM `punto_venta_categorias` WHERE  nombre LIKE '%$Texto%' ORDER BY id";	
		}else{//ESTA CONSULTA SE HARA SIEMPRE QUE NO ALLA NADA EN EL BUSCADOR Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql...
			$sql = "SELECT * FROM `punto_venta_categorias`";
		}//FIN else $Texto VACIO O NO

        // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
		$consulta = mysqli_query($conn, $sql);		
		$contenido = '';//CREAMOS UNA VARIABLE VACIA PARA IR LLENANDO CON LA INFORMACION EN FORMATO

		//VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
		if (mysqli_num_rows($consulta) == 0) {
			echo '<script>M.toast({html:"No se encontraron categorias.", classes: "rounded"})</script>';
        } else {
            //SI NO ESTA EN == 0 SI TIENE INFORMACION
            //La variable $contenido contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
            //RECORREMOS UNO A UNO LOS ARTICULOS CON EL WHILE
            while($categoria = mysqli_fetch_array($consulta)) {
                $id_user = $categoria['usuario'];
				$user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `users` WHERE user_id=$id_user"));
				//Output
                $contenido .= '			
		          <tr>
		            <td>'.$categoria['id'].'</td>
                    <td>'.$categoria['nombre'].'</td>
		            <td>'.$user['firstname'].'</td>
		            <td>'.$categoria['fecha'].'</td>
		            <td><form method="post" action="../views/editar_categoria_pv.php"><input id="id" name="id" type="hidden" value="'.$categoria['id'].'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
		            <td><a onclick="borrar_categoria_pv('.$categoria['id'].')" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
		          </tr>';

			}//FIN while
        }//FIN else
        echo $contenido;// MOSTRAMOS LA INFORMACION HTML

        break;
    case 2:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 2 realiza:

        //CON POST RECIBIMOS TODAS LAS VARIABLES DEL FORMULARIO POR EL SCRIPT "editar_categoria_pv.php" QUE NESECITAMOS PARA ACTUALIZAR
    	$id = $conn->real_escape_string($_POST['id']);
        $Nombre = $conn->real_escape_string($_POST['valorNombre']);    
        //VERIFICAMOS QUE NO HALLA UNA CATEGORIA CON LOS MISMOS DATOS
        if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `punto_venta_categorias` WHERE (nombre='$Nombre') AND id != $id"))>0){
            echo '<script >M.toast({html:"Ya se encuentra una categoria con el mismo nombre.", classes: "rounded"})</script>';
        }else{
            //CREAMOS LA SENTENCIA SQL PARA HACER LA ACTUALIZACION DE LA INFORMACION DE LA CATEGORIA Y LA GUARDAMOS EN UNA VARIABLE
    		$sql = "UPDATE `punto_venta_categorias` SET nombre = '$Nombre', usuario = '$id_user', fecha= '$Fecha_hoy' WHERE id = '$id'";
            //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
    		if(mysqli_query($conn, $sql)){
    			echo '<script >M.toast({html:"La categoria se actualizó con exito.", classes: "rounded"})</script>';	
                echo '<script>recargar_categoria()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
    		}else{
    			echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';	
    		}//FIN else DE ERROR
    	}// fin else verificacion	
        break;
    case 3:
        // $Accion es igual a 3 realiza:
        //CON POST RECIBIMOS LA VARIABLE DEL BOTON POR EL SCRIPT DE "categorias_punto_venta.php" QUE NESECITAMOS PARA BORRAR
        $id = $conn->real_escape_string($_POST['id']);
    	#SELECCIONAMOS LA INFORMACION A BORRAR
        $categoria = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_categorias` WHERE id = $id"));
        #CREAMOS EL SQL DE LA INSERCION A LA TABLA  `pv_borrar_categorias` PARA NO PERDER INFORMACION
        $sql = "INSERT INTO `pv_borrar_categorias` (id_categoria, nombre, registro, borro, fecha_borro) 
                VALUES($id, '".$categoria['nombre']."', '".$categoria['usuario']."', '$id_user','$Fecha_hoy')";
        //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
        if(mysqli_query($conn, $sql)){
        //SI DE CREA LA INSERCION PROCEDEMOS A BORRRAR DE LA TABLA `punto_venta_categorias`
        #VERIFICAMOS QUE SE BORRE CORRECTAMENTE LA CATEGORIA DE `punto_venta_categorias`
        if(mysqli_query($conn, "DELETE FROM `punto_venta_categorias` WHERE `punto_venta_categorias`.`id` = $id")){
            #SI ES ELIMINADO MANDAR MSJ CON ALERTA
                echo '<script >M.toast({html:"Categoria borrada con exito.", classes: "rounded"})</script>';
                echo '<script>recargar_categoria()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
        }else{
            #SI NO ES BORRADO MANDAR UN MSJ CON ALERTA
                echo "<script >M.toast({html: 'Ha ocurrido un error.', classes: 'rounded'});/script>";
            }
        } 
        break;
}// FIN switch
mysqli_close($conn);
    
?>