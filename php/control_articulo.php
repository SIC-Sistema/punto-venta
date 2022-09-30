<?php
//ARCHIVO QUE CONTIENE LA VARIABLE CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
//ARCHIVO QUE CONDICIONA QUE TENGAMOS ACCESO A ESTE ARCHIVO SOLO SI HAY SESSION INICIADA Y NOS PREMITE TIMAR LA INFORMACION DE ESTA
include('is_logged.php');
//DEFINIMOS LA ZONA  HORARIA
date_default_timezone_set('America/Mexico_City');
$id_user = $_SESSION['user_id'];// ID DEL USUARIO LOGEADO
$Fecha_hoy = date('Y-m-d');// FECHA ACTUAL

//CON METODO POST TOMAMOS UN VALOR DEL 0 AL 3 PARA VER QUE ACCION HACER (Para Insertar = 0, Consultar = 1, Actualizar = 2, Borar = 3, Imagen = 4)
$Accion = $conn->real_escape_string($_POST['accion']);

//UN SWITCH EL CUAL DECIDIRA QUE ACCION REALIZA DEL CRUD (Para Insertar = 0, Consultar = 1, Actualizar = 2, Borar = 3)
//echo "hola aqui estoy";
switch ($Accion) {
    case 0:  ///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 0 realiza:

        //CON POST RECIBIMOS TODAS LAS VARIABLES DEL FORMULARIO POR EL SCRIPT "add_articulo.php" QUE NESECITAMOS PARA INSERTAR
        $codigo = $conn->real_escape_string($_POST['valorCodigo']);
        $Nombre = $conn->real_escape_string($_POST['valorNombre']);
        $Modelo = $conn->real_escape_string($_POST['valorModelo']);
        $descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
        $precio = $conn->real_escape_string($_POST['valorPrecio']);
        $unidad = $conn->real_escape_string($_POST['valorUnidad']);        
        $CUnidad = $conn->real_escape_string($_POST['valorCUnidad']);        
        $CFiscal = $conn->real_escape_string($_POST['valorCFiscal']); 
        $Categoria = $conn->real_escape_string($_POST['valorCategoria']);    

        //VERIFICAMOS QUE NO HALLA UN ARTICULO CON LOS MISMOS DATOS
		if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `punto_venta_articulos` WHERE codigo='$codigo' OR codigo_fiscal='$CFiscal'"))>0){
            echo '<script >M.toast({html:"Ya se encuentra un articulo con el mismo Codigo.", classes: "rounded"})</script>';
        }else{
            // SI NO HAY NUNGUNO IGUAL CREAMOS LA SENTECIA SQL  CON LA INFORMACION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
            $sql = "INSERT INTO `punto_venta_articulos` (codigo, nombre, descripcion, precio, unidad, codigo_fiscal, codigo_unidad, modelo, categoria, usuario, fecha) 
               VALUES('$codigo', '$Nombre', '$descripcion', '$precio', '$unidad', '$CFiscal', '$CUnidad', '$Modelo', $Categoria, '$id_user','$Fecha_hoy')";
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

        //CON POST RECIBIMOS UN TEXTO DEL BUSCADOR VACIO O NO de "articulos_punto_venta.php"
        $Texto = $conn->real_escape_string($_POST['texto']);
        //VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
		if ($Texto != "") {
			//MOSTRARA LOS ARTICULOS QUE SE ESTAN BUSCANDO Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql......
			$sql = "SELECT * FROM `punto_venta_articulos` WHERE  codigo LIKE '%$Texto%' OR nombre LIKE '%$Texto%' OR descripcion LIKE '%$Texto%' OR codigo_fiscal LIKE '%$Texto%' ORDER BY id";	
		}else{//ESTA CONSULTA SE HARA SIEMPRE QUE NO ALLA NADA EN EL BUSCADOR Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql...
			$sql = "SELECT * FROM `punto_venta_articulos` LIMIT 50";
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
                $id_categoria = $articulo['categoria'];
				$user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `users` WHERE user_id=$id_user"));
                $categoria_pv = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_categorias` WHERE id=$id_categoria"));
				//Output
                $img = ($articulo['imagen'] != '')? '<td><img class="materialboxed" width="100" src="../Imagenes/Catalogo/'.$articulo['imagen'].'"></td>': '<td></td>';
                $contenido .= '			
		          <tr>
                    <td>'.$articulo['codigo'].'</td>
		            '.$img.'
                    <td>'.$articulo['nombre'].'</td>
		            <td>'.$articulo['descripcion'].'</td>
                    <td>'.$articulo['modelo'].'</td>
		            <td>$'.sprintf('%.2f', $articulo['precio']).'</td>
                    <td>'.$articulo['unidad'].'</td>
                    <td>'.$articulo['codigo_unidad'].'</td>
		            <td>'.$articulo['codigo_fiscal'].'</td>
                    <td>'.$categoria_pv['nombre'].'</td>
		            <td>'.$user['firstname'].'</td>
		            <td>'.$articulo['fecha'].'</td>
		            <td><form method="post" action="../views/editar_articulo_pv.php"><input id="id" name="id" type="hidden" value="'.$articulo['id'].'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
		            <td><a onclick="borrar_articulo_pv('.$articulo['id'].')" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                    <td><a onclick="subirImagen('.$articulo['id'].')" class="btn btn-floating indigo darken-1 waves-effect waves-light"><i class="material-icons">backup</i></a></td>
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
        $Modelo = $conn->real_escape_string($_POST['valorModelo']);
        $descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
        $precio = $conn->real_escape_string($_POST['valorPrecio']);
        $unidad = $conn->real_escape_string($_POST['valorUnidad']);        
        $CUnidad = $conn->real_escape_string($_POST['valorCUnidad']);        
        $CFiscal = $conn->real_escape_string($_POST['valorCFiscal']); 
        $Categoria = $conn->real_escape_string($_POST['valorCategoria']);  

        //VERIFICAMOS QUE NO HALLA UN ARTICULO CON LOS MISMOS DATOS
        if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `punto_venta_articulos` WHERE (codigo='$codigo' OR codigo_fiscal='$CFiscal') AND id != $id"))>0){
            echo '<script >M.toast({html:"Ya se encuentra un articulo con el mismo Codigo.", classes: "rounded"})</script>';
        }else{
            //CREAMOS LA SENTENCIA SQL PARA HACER LA ACTUALIZACION DE LA INFORMACION DEL ARTICULO Y LA GUARDAMOS EN UNA VARIABLE
    		$sql = "UPDATE `punto_venta_articulos` SET codigo = '$codigo', nombre = '$Nombre', modelo = '$Modelo', descripcion = '$descripcion', precio = '$precio', unidad = '$unidad', codigo_unidad = '$CUnidad', codigo_fiscal = '$CFiscal', categoria = '$Categoria', usuario = '$id_user', fecha= '$Fecha_hoy' WHERE id = '$id'";
            //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
    		if(mysqli_query($conn, $sql)){
    			echo '<script >M.toast({html:"El artículo se actualizo con exito.", classes: "rounded"})</script>';	
                echo '<script>recargar_articulo()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
    		}else{
    			echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';	
    		}//FIN else DE ERROR
    	}// fin else verificacion	
        break;
    case 3:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 3 realiza:

        //CON POST RECIBIMOS LA VARIABLE DEL BOTON POR EL SCRIPT DE "articulos_punto_venta.php" QUE NESECITAMOS PARA BORRAR
        $id = $conn->real_escape_string($_POST['id']);
    	//Obtenemos la informacion del Usuario
        $User = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `users` WHERE user_id = $id_user"));
        //SE VERIFICA SI EL USUARIO LOGEADO TIENE PERMISO DE BORRAR ARTICULOS
        if ($User['b_articulos'] == 1) {
            #SELECCIONAMOS LA INFORMACION A BORRAR
            $articulo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_articulos` WHERE id = $id"));
            #CREAMOS EL SQL DE LA INSERCION A LA TABLA  `pv_borrar_articulos` PARA NO PERDER INFORMACION
            $sql = "INSERT INTO `pv_borrar_articulos` (id_articulo, codigo, nombre, descripcion, precio, unidad, codigo_fiscal, codigo_unidad, modelo, categoria, imagen, registro, borro, fecha_borro) 
                VALUES($id, '".$articulo['codigo']."', '".$articulo['nombre']."', '".$articulo['descripcion']."', '".$articulo['precio']."', '".$articulo['unidad']."', '".$articulo['codigo_fiscal']."', '".$articulo['codigo_unidad']."', '".$articulo['modelo']."', '".$articulo['categoria']."', '".$articulo['imagen']."', '".$articulo['usuario']."', '$id_user','$Fecha_hoy')";
            //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
            if(mysqli_query($conn, $sql)){
                //SI DE CREA LA INSERCION PROCEDEMOS A BORRRAR DE LA TABLA `pv_borrar_proveedor`
                #VERIFICAMOS QUE SE BORRE CORRECTAMENTE EL ARTICULO DE `punto_venta_articulos`
                if(mysqli_query($conn, "DELETE FROM `punto_venta_articulos` WHERE `punto_venta_articulos`.`id` = $id")){
                #SI ES ELIMINADO MANDAR MSJ CON ALERTA
                    echo '<script >M.toast({html:"Articulo borrado con exito.", classes: "rounded"})</script>';
                    echo '<script>recargar_articulo()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
                }else{
                #SI NO ES BORRADO MANDAR UN MSJ CON ALERTA
                    echo "<script >M.toast({html: 'Ha ocurrido un error.', classes: 'rounded'});/script>";
                }
            }
        }else{
            echo '<script >M.toast({html:"Permiso denegado.", classes: "rounded"});
            M.toast({html:"Comunicate con un administrador.", classes: "rounded"});</script>';
        }   
        break;
    case 4:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 4 realiza:

        $id = $_POST["id"]; 
        //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL ARTICULO Y ASIGNAMOS EL ARRAY A UNA VARIABLE $datos
        $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_articulos` WHERE id=$id"));

        function generarRandomString($length) { 
          return substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length); 
        }
        $key = generarRandomString(3);

        //CREAR EL NOMBRE DEL ARCHIVO
        $name_img = 'ArticuloNo.'.$id."-".$key.'_img.jpg';//Nombre que tomara la imagen
        $destino = '../Imagenes/Catalogo/'.$name_img;//Destino donde se guardara la imagen ya con nombre modificado
        
        //VERIIFICAMOS QUE LA IMAGEN ESTE SIENDO RECIBIDA CORRECTAMENTE
        if ($_FILES['imagen']['error'] === UPLOAD_ERR_OK) {

            $imagen_original = $_FILES['imagen']['tmp_name']; //RECIBIMOS LA IMAGEN SUBIDA Y SUS DIMENSIONES ORIG.

            if ($datos['imagen'] != '') {
                //--- SI HAY UN ARCHIVO EN LA CARPETA CON ESE NOMBRE LO BORRAMOS---
                if (file_exists("../Imagenes/Catalogo/".$datos['imagen'])) {
                  unlink("../Imagenes/Catalogo/".$datos['imagen']);
                } 
            }

            //Creamos una nueva variable de imagen a partir de la imagen que recibimos
            $img_orig = imagecreatefromjpeg($imagen_original);

            //Creamos una imagen en blanco de la dimencion que deseamos 200x200
            $tmp =imagecreatetruecolor(200, 200);

            //Copiamos a imagen original en esta nueva imagen en blanco que creamos con las dimenciones 200x200
            imagecopyresized($tmp, $img_orig, 0, 0, 0, 0, 200, 200, imagesx($img_orig), imagesy($img_orig));

            //AHORA GUARDAMOS LA IMAGEN
            imagejpeg($tmp,$destino,100);

            //UNA VEZ SUBIDA LA IMAGEN MODIFICAMOS LA TABLA `punto_venta_articulos` CON EL NOMBRE DE LA IMAGEN
            //CREAMOS LA SENTENCIA SQL PARA HACER LA ACTUALIZACION DE LA INFORMACION DEL ARTICULO Y LA GUARDAMOS EN UNA VARIABLE
            $sql = "UPDATE `punto_venta_articulos` SET imagen = '$name_img' WHERE id = '$id'";
            //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
            if(mysqli_query($conn, $sql)){
                echo "El artículo se actualizo con exito"; 
                ?>
                <script>
                    // REDIRECCIONAMOS 
                    setTimeout("location.href='../views/articulos_punto_venta.php'", 800);
                </script>
                <?php
            }else{
                echo "Ocurrio un error.."; 
            }//FIN else DE ERROR
        }
        break; 
}// FIN switch
mysqli_close($conn);  
?>