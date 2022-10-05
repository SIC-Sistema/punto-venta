<?php
//ARCHIVO QUE CONTIENE LA VARIABLE CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
//ARCHIVO QUE CONDICIONA QUE TENGAMOS ACCESO A ESTE ARCHIVO SOLO SI HAY SESSION INICIADA Y NOS PREMITE TIMAR LA INFORMACION DE ESTA
include('is_logged.php');
//DEFINIMOS LA ZONA  HORARIA
date_default_timezone_set('America/Mexico_City');
$id_user = $_SESSION['user_id'];// ID DEL USUARIO LOGEADO
$Fecha_hoy = date('Y-m-d');// FECHA ACTUAL

//CON METODO POST TOMAMOS UN VALOR DEL 0 AL 3 PARA VER QUE ACCION HACER (Para Insertar = 0, Consultar compras = 1, InfoProveedor = 2, Borrar compra = 3, Buscar e Insertar Articulos TMP = 4, Actualizar Cant. o Costo = 5, Consulta articulos Modal = 6, borrar listado TMP = 7)
$Accion = $conn->real_escape_string($_POST['accion']);

//UN SWITCH EL CUAL DECIDIRA QUE ACCION REALIZA DEL CRUD (Para Insertar = 0, Consultar = 1, Actualizar = 2, Borar = 3)
//echo "hola aqui estoy";
switch ($Accion) {
    case 0:  ///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 0 realiza:

        //CON POST RECIBIMOS TODAS LAS VARIABLES DEL FORMULARIO QUE NESECITAMOS PARA INSERTAR
        $Nombre = $conn->real_escape_string($_POST['valorNombre']);     
        //VERIFICAMOS QUE NO HALLA UN ARTICULO CON LOS MISMOS DATOS
		if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `punto_venta_almacenes` WHERE nombre='$Nombre' "))>0){
            echo '<script >M.toast({html:"Ya se encuentra una categoria con el mismo Nombre.", classes: "rounded"})</script>';
        }else{
            // SI NO HAY NUNGUNO IGUAL CREAMOS LA SENTECIA SQL  CON LA INFORMACION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
            $sql = "INSERT INTO `punto_venta_almacenes` (nombre, usuario, fecha) 
               VALUES('$Nombre','$id_user','$Fecha_hoy')";
            //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
			if(mysqli_query($conn, $sql)){
				echo '<script >M.toast({html:"El almacen  se registró exitosamente.", classes: "rounded"})</script>';	
                echo '<script>recargar_almacen_lista()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
			}else{
                echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';	
            }//FIN else DE ERROR
            
        }// FIN else DE BUSCAR CATEGORIA IGUAL

        break;
    case 1:  ///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 1 realiza:

        //CON POST RECIBIMOS UN TEXTO DEL BUSCADOR VACIO O NO de "almacenes_punto_venta.php"
        $Texto = $conn->real_escape_string($_POST['texto']);
        //VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
		if ($Texto != "") {
			//MOSTRARA LOS ALMACENES QUE SE ESTAN BUSCANDO Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql......
			$sql = "SELECT * FROM `punto_venta_almacenes` WHERE  nombre LIKE '%$Texto%' ORDER BY id";	
		}else{//ESTA CONSULTA SE HARA SIEMPRE QUE NO ALLA NADA EN EL BUSCADOR Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql...
			$sql = "SELECT * FROM `punto_venta_almacenes`";
		}//FIN else $Texto VACIO O NO

        // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
		$consulta = mysqli_query($conn, $sql);		
		$contenido = '';//CREAMOS UNA VARIABLE VACIA PARA IR LLENANDO CON LA INFORMACION EN FORMATO

		//VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
		if (mysqli_num_rows($consulta) == 0) {
				echo '<script>M.toast({html:"No se encontraron almacenes.", classes: "rounded"})</script>';
        } else {
            //SI NO ESTA EN == 0 SI TIENE INFORMACION
            //La variable $contenido contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
            //RECORREMOS UNO A UNO LOS ALMACENES CON EL WHILE
            while($almacen = mysqli_fetch_array($consulta)) {
                $id_user = $almacen['usuario'];
				$user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `users` WHERE user_id=$id_user"));
				//Output
                $contenido .= '			
		          <tr>
		            <td>'.$almacen['id'].'</td>
                    <td>'.$almacen['nombre'].'</td>
		            <td>'.$user['firstname'].'</td>
		            <td>'.$almacen['fecha'].'</td>
		            <td><form method="post" action="../views/editar_almacen_pv.php"><input id="id" name="id" type="hidden" value="'.$almacen['id'].'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
		            <td><a onclick="borrar_almacen('.$almacen['id'].')" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
		          </tr>';

			}//FIN while
        }//FIN else
        echo $contenido;// MOSTRAMOS LA INFORMACION HTML
        break;
    case 2:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 2 realiza:

        //CON POST RECIBIMOS EL ID DEL PROVEEDOR DEL FORMULARIO POR EL SCRIPT "add_compra.php" QUE NESECITAMOS PARA BUSCAR
    	$id = $conn->real_escape_string($_POST['proveedor']);    
        $contenido = '';//CREAMOS UNA VARIABLE VACIA PARA IR LLENANDO CON LA INFORMACION EN FORMATO
        //VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
        if ($id != 0) {
            //HACEMOS LA CONSULTA DEL PROVEEDOR Y MOSTRAMOS LA INFOR EN FORMATO HTML
            $proveedor = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_proveedores` WHERE id=$id"));
            $contenido .= '<h6 class = "col s12 m4 l4"><b>RFC: </b>'.$proveedor['rfc'].'</h6>  <h6 class = "col s12 m4 l4"><b>Telefono: </b>'.$proveedor['telefono'].' </h6> <h6 class = "col s12 m4 l4"><b>Dias Credito:</b>'.$proveedor['dias_c'].' </h6>';
        }
        echo $contenido;// IMPRIMIMOS EL CONTENDIO QUE PUEDE IR VACIO SI ES $id = 0
        break;
    case 3:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 3 realiza:

        //CON POST RECIBIMOS LA VARIABLE DEL BOTON POR EL SCRIPT DE "almacenes_punto_venta.php" QUE NESECITAMOS PARA BORRAR
        $id = $conn->real_escape_string($_POST['id']);
    	//Obtenemos la informacion del Usuario
        $User = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `users` WHERE user_id = $id_user"));
        //SE VERIFICA SI EL USUARIO LOGEADO TIENE PERMISO DE BORRAR ALMACENES
        if ($User['b_almacenes'] == 1) {
            #SELECCIONAMOS LA INFORMACION A BORRAR
            $almacen = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_almacenes` WHERE id = $id"));
            #CREAMOS EL SQL DE LA INSERCION A LA TABLA  `pv_borrar_almacenes` PARA NO PERDER INFORMACION
            $sql = "INSERT INTO `pv_borrar_almacenes` (id_almacen, nombre, registro, borro, fecha_borro) 
                    VALUES($id, '".$almacen['nombre']."', '".$almacen['usuario']."', '$id_user','$Fecha_hoy')";
            //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
            if(mysqli_query($conn, $sql)){
                //SI DE CREA LA INSERCION PROCEDEMOS A BORRRAR DE LA TABLA `punto_venta_almacenes`
                #VERIFICAMOS QUE SE BORRE CORRECTAMENTE EL ALMACEN DE `punto_venta_almacenes`
                if(mysqli_query($conn, "DELETE FROM `punto_venta_almacenes` WHERE `punto_venta_almacenes`.`id` = $id")){
                #SI ES ELIMINADO MANDAR MSJ CON ALERTA
                    echo '<script >M.toast({html:"Almacen borrado con exito.", classes: "rounded"})</script>';
                    echo '<script>recargar_almacen_lista()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
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

        //CON POST RECIBIMOS UN ID DEL MODAL O AL INICIAR EL DOCUMENTO "add_compra.php"
        $user_id = $conn->real_escape_string($_POST['id']);
        $insert = $conn->real_escape_string($_POST['insert']);
        //VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
        if ($insert) {
            //SE HACE LA INSERCION A TMP
            $id_art = $conn->real_escape_string($_POST['id_art']);
            if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `tmp_pv_detalle_compra` WHERE id_articulo = $id_art"))>0) {
                echo '<script >M.toast({html:"No se pueden repetir los articulos en la lista.", classes: "rounded"})</script>';   

            }else{
                #SELECCIONAMOS LA INFORMACION DEL ARTICULO
                $articulo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_articulos` WHERE id = $id_art"));
                $precio = $articulo['precio'];
                //CREAMOS EL SQL PARA INSERTAR
                $sql = "INSERT INTO `tmp_pv_detalle_compra` (id_articulo, cantidad, precio_compra_u, importe, usuario) 
                   VALUES($id_art,'1','$precio','$precio','$user_id')";
                if(mysqli_query($conn, $sql)){
                    echo '<script >M.toast({html:"El articulo se registró exitosamente.", classes: "rounded"})</script>';   
                    echo '<script>recargar_add_compra()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
                }else{
                    echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>'; 
                }//FIN else DE ERROR
            }
        }
        // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
        $consulta = mysqli_query($conn, "SELECT * FROM tmp_pv_detalle_compra WHERE usuario = $user_id");      
        ?>
        <div class="row" id="articulosCompra">
            <div class="hide-on-small-only col s1"><br></div>
            <table class="col s12 m10 l10">
              <thead>
                <tr>
                  <th>Código</th>
                  <th>Cantidad</th>
                  <th>Artículo</th>
                  <th>Costo U.</th>
                  <th>Importe</th>
                </tr>
              </thead>
              <tbody>
               <?php
               $aux = mysqli_num_rows($consulta);
               $total = 0;
               //VERIFICAMOS SI HA ARRTICULOS EN LA TABLA
               if(mysqli_num_rows($consulta)>0){
                while($detalle_articulo = mysqli_fetch_array($consulta)){
                    $id_art = $detalle_articulo['id_articulo'];
                    $articulo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_articulos` WHERE id = $id_art"));
                    $total += $detalle_articulo['importe'];
                    ?>
                        <tr>
                            <td><?php echo $articulo['codigo'] ?></td>
                            <td class="row col s10"><input id="cantidadA<?php echo $id_art; ?>" type="number" class="validate col s6 m4 l4" value="<?php echo $detalle_articulo['cantidad'];?>" onchange= 'totales(<?php echo $id_art.', '.$user_id;?>);'><br><?php echo $articulo['unidad'] ?></td>
                            <td><?php echo $articulo['nombre'] ?></td>
                            <td class="row col s12"><p class="col s2">$</p><input id="precio_compra<?php echo $id_art; ?>" type="number" class="validate col s10 m7 l6" value="<?php echo sprintf('%.2f', $detalle_articulo['precio_compra_u']); ?>" onchange= 'totales(<?php echo $id_art.', '.$user_id;?>);'></td>
                            <td><div class="col s2">$</div><input class="col s10 m7 l6" type="" id="importe<?php echo $id_art; ?>" value = "<?php echo sprintf('%.2f', $detalle_articulo['importe']); ?>"></td>
                            <td><a onclick="borrar_lista_articulo(<?php echo $id_art; ?>);" class="waves-effect waves-light btn-small red right"><i class="material-icons">delete</i></a></td>
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
        <div class="row">
            <div class="hide-on-small-only col s1"><br></div>
            <div class="col s12 m10 l10">
                <div class="col s6 m6 l6 ">
                    <h5 class="right"><b>Número de Artículos <?php echo $aux;?></b></h5><br><br><br><br><br><br>
                    <a href="#" class="waves-effect waves-light btn-small red right">Cancelar<i class="material-icons left">close</i></a>
                    <a href="#" class="waves-effect waves-light btn-small indigo right">Registrar<i class="material-icons left">done</i></a>
                </div>
                <div class="hide-on-small-only col s2"><br></div>
                <div class="col s6 m4 l4 row">
                    <h6 class="right" ><b><div class="col s3 m5 l4">SubTotal</div><div class="col s1">$</div> <input class="col s8 m6 l5" type="" id="subtotal" value="<?php echo sprintf('%.2f', $total-($total*0.16)); ?>"></b></h6>
                    <h6 class="right" ><b><div class="col s3 m5 l4">Impuesto</div><div class="col s1">$</div> <input class="col s8 m6 l5" type="" id="impuesto" value="<?php echo sprintf('%.2f', $total*0.16); ?>"></b></h6><br><br><br><br>
                    <hr>
                    <h5 class="right" ><b><div class="col s3 m5 l4">Total </div><div class="col s1">$</div> <input class="col s8 m7 l6" type="" id="totalCompra" value = "<?php echo sprintf('%.2f', $total); ?>"></td></b></h5>
                </div>
            </div>            
        </div>
        <hr><br>
        <?php
        break;
    case 5:
        // $Accion es igual a 6 realiza:
        break;
    case 6:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 6 realiza:
        
        //CON POST RECIBIMOS UN TEXTO DEL BUSCADOR VACIO O NO de "articulos_punto_venta.php"
        $Texto = $conn->real_escape_string($_POST['texto']);
        //VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
        if ($Texto != "") {
            //MOSTRARA LOS ARTICULOS QUE SE ESTAN BUSCANDO Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql......
            $sql = "SELECT * FROM `punto_venta_articulos` WHERE  codigo LIKE '%$Texto%' OR nombre LIKE '%$Texto%' OR descripcion LIKE '%$Texto%' LIMIT 5 "; 
        }else{//ESTA CONSULTA SE HARA SIEMPRE QUE NO ALLA NADA EN EL BUSCADOR Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql...
            $sql = "SELECT * FROM `punto_venta_articulos` LIMIT 5";
        }//FIN else $Texto VACIO O NO

         // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
        $consulta = mysqli_query($conn, $sql);      
        ?>
        <div class="row">
            <div class="hide-on-small-only col s1"><br></div>
            <table class="col s12 m10 l10">
              <thead>
                <tr>
                  <th>Código</th>
                  <th>Nombre</th>
                  <th>Descripcion</th>
                  <th>Costo U.</th>
                </tr>
              </thead>
              <tbody>
               <?php
               //VERIFICAMOS SI HA ARRTICULOS EN LA TABLA
               if(mysqli_num_rows($consulta)>0){
                    while($articulo = mysqli_fetch_array($consulta)){
                    ?>
                        <tr>
                            <td><?php echo $articulo['codigo'] ?></td>
                            <td><?php echo $articulo['nombre'] ?></td>
                            <td><?php echo $articulo['descripcion'] ?></td>
                            <td><?php echo $articulo['precio'] ?></td>
                            <td><a onclick="tmp_articulos(<?php echo $id_user ?>, 1,<?php echo $articulo['id'] ?>);" class="waves-effect waves-light btn-small indigo right">Agregar</a></td>
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
        break;
    case 7:///////////////           IMPORTANTE               //////////////
        //$Accion es igual a 7 realizar:

        //CON POST RECIBIMOS LA VARIABLE DEL BOTON POR EL SCRIPT DE "add_compra.php" QUE NESECITAMOS PARA BORRAR
        $id = $conn->real_escape_string($_POST['id']);
        //Obtenemos la informacion del Usuario
        
        #VERIFICAMOS QUE SE BORRE CORRECTAMENTE EL ALMACEN DE `tmp_pv_detalle_compra`
        if(mysqli_query($conn, "DELETE FROM `tmp_pv_detalle_compra` WHERE `tmp_pv_detalle_compra`.`id_articulo` = $id")){
            #SI ES ELIMINADO MANDAR MSJ CON ALERTA
            echo '<script >M.toast({html:"Articulo borrado con exito.", classes: "rounded"})</script>';
            echo '<script>recargar_add_compra()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
        }else{
            #SI NO ES BORRADO MANDAR UN MSJ CON ALERTA
            echo "<script >M.toast({html: 'Ha ocurrido un error.', classes: 'rounded'});/script>";
        }

        break;
}// FIN switch
mysqli_close($conn);
    
?>