<?php
//ARCHIVO QUE CONTIENE LA VARIABLE CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
//ARCHIVO QUE CONDICIONA QUE TENGAMOS ACCESO A ESTE ARCHIVO SOLO SI HAY SESSION INICIADA Y NOS PREMITE TIMAR LA INFORMACION DE ESTA
include('is_logged.php');
//DEFINIMOS LA ZONA  HORARIA
date_default_timezone_set('America/Mexico_City');
$id_user = $_SESSION['user_id'];// ID DEL USUARIO LOGEADO
$Fecha_hoy = date('Y-m-d');// FECHA ACTUAL

//CON METODO POST TOMAMOS UN VALOR DEL 0 AL 3 PARA VER QUE ACCION HACER (Para Insertar = 0, Consultar compras = 1, InfoProveedor = 2, Borrar compra = 3, Buscar e Insertar Articulos TMP = 4, Actualizar Cant. o Costo = 5, Consulta articulos Modal = 6, borrar listado TMP = 7, borrar todo TMP usuario = 8)
$Accion = $conn->real_escape_string($_POST['accion']);

//UN SWITCH EL CUAL DECIDIRA QUE ACCION REALIZA DEL CRUD (Para Insertar = 0, Consultar = 1, Actualizar = 2, Borar = 3)
//echo "hola aqui estoy";
switch ($Accion) {
    case 0:  ///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 0 realiza:

        //CON POST RECIBIMOS TODAS LAS VARIABLES DEL FORMULARIO QUE NESECITAMOS PARA INSERTAR
        $Proveedor = $conn->real_escape_string($_POST['valorProveedor']);   
        $Factura = $conn->real_escape_string($_POST['valorFactura']);   
        $TipoCambio = $conn->real_escape_string($_POST['valorTipoCambio']); 

        //VERIFICAMOS QUE NO HALLA UNA COMPRA CON LOS MISMOS DATOS
		if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `punto_venta_compras` WHERE factura='$Factura' "))>0){
            echo '<script >M.toast({html:"Ya se encuentra una compra con el mismo numero de Factura.", classes: "rounded"})</script>';
            echo '<script>recargar_compra()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
        }else{
            $sql_total = mysqli_fetch_array(mysqli_query($conn, "SELECT sum(importe) AS Total FROM tmp_pv_detalle_compra WHERE usuario = $id_user"));
            $Total = $sql_total['Total'];
            // SI NO HAY NUNGUNO IGUAL CREAMOS LA SENTECIA SQL  CON LA INFORMACION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE

            $sql = "INSERT INTO `punto_venta_compras` (factura, id_proveedor, total, tipo_cambio, usuario, fecha) 
               VALUES('$Factura', '$Proveedor', '$Total', '$TipoCambio','$id_user','$Fecha_hoy')";
            //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
			if(mysqli_query($conn, $sql)){
				echo '<script >M.toast({html:"La compra se registró exitosamente.", classes: "rounded"})</script>';
                // HAY QUE SACAR EL ID DE LA COMPRA
                #SELECCIONAMOS EL ULTIMO CORTE CREADO
                $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id) AS id FROM punto_venta_compras WHERE usuario=$id_user"));           
                $compra = $ultimo['id'];//TOMAMOS EL ID DEL ULTIMO CORTE

                //REGISTRAMOS LOS ARTICULOS EN punto_venta_detalle_compra
                // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
                $consulta = mysqli_query($conn, "SELECT * FROM tmp_pv_detalle_compra WHERE usuario = $id_user"); 
                //VERIFICAMOS SI HAY ARTICULOS POR AGREGAR
                if(mysqli_num_rows($consulta)>0){
                    $LISTA = '';
                    $almacen = $conn->real_escape_string($_POST['almacen']); //ID DE ALMACEN
                    //RECORREMOS CON UN WHILE UNO POR UNO LOS ARTICULOS
                    while($detalle_articulo = mysqli_fetch_array($consulta)){
                        $id_articulo = $detalle_articulo['id_articulo'];
                        $cantidad = $detalle_articulo['cantidad'];
                        $precio_compra_u = $detalle_articulo['precio_compra_u'];
                        $importe = $detalle_articulo['importe'];
                        // CREAMOS EL SQL INSERT DEL ARTICULO EN TURNO EN punto_venta_detalle_compra
                        $sql = "INSERT INTO `punto_venta_detalle_compra` (id_compra, id_articulo, cantidad, precio_compra_u, importe) VALUES($compra, $id_articulo, '$cantidad', '$precio_compra_u','$importe')";

                        // VERIFICAMOS SI SE HIZO LA INSERCION
                        if (mysqli_query($conn, $sql)) {
                            // VERIFICAMOS SI EL ARTICULO YA ESTA EN ALMACEN Y SOLO MODIFICAMOS LA CANTIDAD +
                            if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `punto_venta_almacen_general` WHERE id_articulo = '$id_articulo' AND id_almacen = '$almacen'"))>0) {

                                mysqli_query($conn, "UPDATE `punto_venta_almacen_general` SET cantidad = cantidad+$cantidad, modifico = $id_user, fecha_modifico = '$Fecha_hoy' WHERE id_articulo = '$id_articulo' AND id_almacen = '$almacen'");
                            }else{
                                // SI NO REGISTRAMOS EL ARTICULO Y CANTIDAD
                                mysqli_query($conn, "INSERT INTO `punto_venta_almacen_general` (id_articulo, cantidad, id_almacen, modifico, fecha_modifico) VALUES ($id_articulo, $cantidad, $almacen, $id_user, '$Fecha_hoy')");
                            }
                            

                            // HAY QUE CREAR UN LISTADO DE ARTICULOS QUE CAMBIARON EL PRECION CON DIFERENCIA DEL 50% DE LA UTILIDAD Y PREGUNTAR MODAL SI CAMBIAR EL PRECIO (CAMBIAR EN ARTICULOS)
                            $CincuentaP = (20/2)/100; //($utilidad/2)/100 porcentaje
                            // SELECCIONAMOS EL PRECIO DEL ARTICULO EN TURNO
                            $articulo =  mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM punto_venta_articulos WHERE id=$id_articulo")); 
                            $UtilidadCincuenta = $articulo['precio']*$CincuentaP;
                            $Diferencia = abs($articulo['precio']-$precio_compra_u); 
                            //COMPARAMOS PRECIOS
                            if ($Diferencia >= $UtilidadCincuenta) {
                                $LISTA .= '<tr><td>'.$articulo['codigo'].'</td><td>'.$articulo['nombre'].'</td><td>$'.sprintf('%.2f', $articulo['precio']).'</td><td>$'.sprintf('%.2f', $precio_compra_u).'</td><td><div class = "col s1"><br>$</div> <input id="precioCambio'.$id_articulo.'" type="number" class="validate col s10"></td><td><a onclick="cambiar_precio('.$id_articulo.')" class="btn-small indigo waves-effect waves-light">Cambiar</a></td></tr>';
                            }         

                            // SI SE INSERTO BORRAMOS EL ARTICULO DE tmp_pv_detalle_compra
                             #VERIFICAMOS QUE SE BORRE CORRECTAMENTE EL ARTICULO DE `tmp_pv_detalle_compra`
                            mysqli_query($conn, "DELETE FROM `tmp_pv_detalle_compra` WHERE `tmp_pv_detalle_compra`.`id_articulo` = $id_articulo");
                        }//FIN IF                      
                    }//FIN WHILE
                    // SI LA LISTA NO ESTA VACIA MOSTRAR MODAL
                    if ($LISTA != '') {                    
                    ?>
                    <div class="row">
                          <div class="row"><br><br><hr>
                            <h4 class="red-text center">¡ADVERTENCIA!</h4><br>
                            <h5 class="blue-text">Articulos que se tomaron a consideracion para cambio de precio:</h5>
                          </div>
                          <div id="cambio"></div>
                          <form class="row">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Precio Actual</th>
                                        <th>Precio Compra</th>
                                        <th>Precio Fijado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php echo $LISTA; ?>
                                </tbody>
                            </table><br><br><br>
                            <a onclick="recargar_compra()" class="btn waves-effect waves-light pink right">Aceptar y Continuar<i class="material-icons right">done</i></a>
                          </form>
                    </div><hr>
                    <?php }else{
                        //SI NO HAY NADA POR CAMBIAR SOLO REDIRECCIONAMOS
                        echo '<script>recargar_compra()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
                    }
                }else{
                    echo '<script >M.toast({html:"No se encontraron articulos por agregar.", classes: "rounded"})</script>';  
                }//FIN ELSE
                #echo '<script>recargar_compra()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
			}else{
                echo '<script >M.toast({html:"Ha ocurrio un error.", classes: "rounded"})</script>';   
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
			$sql = "SELECT * FROM `punto_venta_compras`  WHERE  factura LIKE '$Texto%' OR id LIKE '$Texto%' OR id_proveedor LIKE '$Texto%' LIMIT 30";	
		}else{//ESTA CONSULTA SE HARA SIEMPRE QUE NO ALLA NADA EN EL BUSCADOR Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql...
			$sql = "SELECT * FROM `punto_venta_compras` LIMIT 30";
		}//FIN else $Texto VACIO O NO

        // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
		$consulta = mysqli_query($conn, $sql);		
		$contenido = '';//CREAMOS UNA VARIABLE VACIA PARA IR LLENANDO CON LA INFORMACION EN FORMATO

		//VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
		if (mysqli_num_rows($consulta) == 0) {
				echo '<script>M.toast({html:"No se encontraron compras.", classes: "rounded"})</script>';
        } else {
            //SI NO ESTA EN == 0 SI TIENE INFORMACION
            //La variable $contenido contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
            //RECORREMOS UNO A UNO LOS ALMACENES CON EL WHILE
            while($compra = mysqli_fetch_array($consulta)) {
                $id_user = $compra['usuario'];
                $id_proveedor = $compra['id_proveedor'];
                $user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `users` WHERE user_id=$id_user"));
				$proveedor = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_proveedores` WHERE id=$id_proveedor"));
				//Output
                $contenido .= '			
		          <tr>
		            <td>'.$compra['id'].'</td>
                    <td>'.$compra['factura'].'</td>
                    <td>'.$id_proveedor.' - '.$proveedor['nombre'].'</td>
                    <td>'.$compra['tipo_cambio'].'</td>
                    <td>$'.sprintf('%.2f', $compra['total']).'</td>
		            <td>'.$user['firstname'].'</td>
		            <td>'.$compra['fecha'].'</td>
		            <td><form method="post" action="../views/detalle_compra_pv.php"><input id="compra" name="compra" type="hidden" value="'.$compra['id'].'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">list</i></button></form></td>
		            <td><a onclick="borrar_compra_pv('.$compra['id'].')" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
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

        //CON POST RECIBIMOS LA VARIABLE DEL BOTON POR EL SCRIPT DE "compras_punto_venta.php" QUE NESECITAMOS PARA BORRAR
        $id = $conn->real_escape_string($_POST['id']);
    	//Obtenemos la informacion del Usuario
        $User = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `users` WHERE user_id = $id_user"));
        //SE VERIFICA SI EL USUARIO LOGEADO TIENE PERMISO DE BORRAR COMPRAS
        if ($User['compras'] == 1) {
            #SELECCIONAMOS LA INFORMACION A BORRAR
            $compra = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_compras` WHERE id = $id"));
            #CREAMOS EL SQL DE LA INSERCION A LA TABLA  `pv_borrar_compras` PARA NO PERDER INFORMACION
            $sql = "INSERT INTO `pv_borrar_compras` (id_compra, factura, id_proveedor, tipo_cambio, total, registro, borro, fecha_borro) 
                    VALUES($id, '".$compra['factura']."', '".$compra['id_proveedor']."', '".$compra['tipo_cambio']."', '".$compra['total']."', '".$compra['usuario']."', '$id_user','$Fecha_hoy')";
            //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
            if(mysqli_query($conn, $sql)){
                //SI DE CREA LA INSERCION PROCEDEMOS A BORRRAR DE LA TABLA `punto_venta_compras`
                #VERIFICAMOS QUE SE BORRE CORRECTAMENTE LA COMPRA DE `punto_venta_compras`
                if(mysqli_query($conn, "DELETE FROM `punto_venta_compras` WHERE `punto_venta_compras`.`id` = $id")){
                    #SI ES ELIMINADO MANDAR MSJ CON ALERTA
                    echo '<script >M.toast({html:"Compra borrada con exito.", classes: "rounded"})</script>';

                    #HAY QUE RECORRER LOS ARTICULOS DE DETALLE PARA ELIMINAR LA CANTIDAD DEL ALMACEN DE CADA UNO
                    $sql_art =  mysqli_query($conn,"SELECT * FROM punto_venta_detalle_compra WHERE id_compra=$id");
                    if (mysqli_num_rows($sql_art) <= 0) {
                      echo '<script>M.toast({html:"No se encontraron articulos en la compra.", classes: "rounded"})</script>';
                    }else{
                      $almacen = $User['almacen']; //ID DE ALMACEN
                      while($detalle = mysqli_fetch_array($sql_art)){
                        $cantidad = $detalle['cantidad'];
                        $id_articulo = $detalle['id_articulo'];
                        mysqli_query($conn, "UPDATE `punto_venta_almacen_general` SET cantidad = cantidad-$cantidad, modifico = $id_user, fecha_modifico = '$Fecha_hoy' WHERE id_articulo = '$id_articulo' AND id_almacen = '$almacen'");                        
                      }//FIN while
                    }// FIN else

                    echo '<script>recargar_compra()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
                }else{
                #SI NO ES BORRADO MANDAR UN MSJ CON ALERTA
                    echo '<script >M.toast({html:"Hubo un error...", classes: "rounded"})</script>';
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
                }else{
                    echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>'; 
                }//FIN else DE ERROR
            }
        }
        // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
        $consulta = mysqli_query($conn, "SELECT * FROM tmp_pv_detalle_compra WHERE usuario = $user_id");      
        ?>
        <div class="row">
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
                    <h5 class="right"><b>Número de Artículos <?php echo $aux;?></b></h5><br><br><br><br>
                    <!-- Switch -->
                    <div class="switch right">
                        <label>
                          Al Contado
                          <input type="checkbox" id="cambio">
                          <span class="lever"></span>
                          Credito
                        </label>
                    </div><br><br><br>
                    <a onclick="borrar_lista_all(<?php echo $user_id; ?>)" class="waves-effect waves-light btn-small red right">Cancelar<i class="material-icons left">close</i></a>
                    <a onclick="insert_compra()" class="waves-effect waves-light btn-small indigo right">Registrar<i class="material-icons left">done</i></a>
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
    case 5:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 6 realiza:

        sleep(1);// HACE UNA PAUSA DE 1 segundo para hace el cambio
        //CON POST RECIBIMOS TODAS LAS VARIABLES DEL FORMULARIO QUE NESECITAMOS PARA CAMBIAR LAS CANTIDADES
        $id_articulo = $conn->real_escape_string($_POST['valorIdArt']);
        $id_usuario = $conn->real_escape_string($_POST['valorIdUs']);
        $CantidadA = $conn->real_escape_string($_POST['valorCantidadA']);
        $PrecioU = $conn->real_escape_string($_POST['valorPrecioU']);
        $Importe = $CantidadA*$PrecioU;
        //CREAMOS LA SENTENCIA SQL PARA HACER LA ACTUALIZACION DE LA INFORMACION DE LA CATEGORIA Y LA GUARDAMOS EN UNA VARIABLE
        $sql = "UPDATE `tmp_pv_detalle_compra` SET cantidad = '$CantidadA', precio_compra_u = '$PrecioU', importe= '$Importe' WHERE id_articulo = $id_articulo AND usuario = $id_usuario";
        //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
        if(mysqli_query($conn, $sql)){
            #echo '<script >M.toast({html:"Las cantidades se actualizaron con exito.", classes: "rounded"})</script>';    
        }else{
            #echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>'; 
        }//FIN else DE ERROR

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
        }else{
            #SI NO ES BORRADO MANDAR UN MSJ CON ALERTA
            echo "<script >M.toast({html: 'Ha ocurrido un error.', classes: 'rounded'});/script>";
        }
        // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
        $consulta = mysqli_query($conn, "SELECT * FROM tmp_pv_detalle_compra WHERE usuario = $id_user");      
        ?>
        <div class="row">
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
                    <h5 class="right"><b>Número de Artículos <?php echo $aux;?></b></h5><br><br><br><br>
                    <!-- Switch -->
                    <div class="switch right">
                        <label>
                          Al Contado
                          <input type="checkbox" id="cambio">
                          <span class="lever"></span>
                          Credito
                        </label>
                    </div><br><br><br>
                    <a onclick="borrar_lista_all(<?php echo $id_user; ?>)" class="waves-effect waves-light btn-small red right">Cancelar<i class="material-icons left">close</i></a>
                    <a onclick="insert_compra()" class="waves-effect waves-light btn-small indigo right">Registrar<i class="material-icons left">done</i></a>
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
    case 8:///////////////           IMPORTANTE               //////////////

        $id_usuario = $conn->real_escape_string($_POST['usuario']);
        #VERIFICAMOS QUE SE BORRE CORRECTAMENTE EL ALMACEN DE `tmp_pv_detalle_compra`
        if(mysqli_query($conn, "DELETE FROM `tmp_pv_detalle_compra` WHERE `usuario` = $id_usuario")){
            #SI ES ELIMINADO MANDAR MSJ CON ALERTA
            echo '<script >M.toast({html:"Articulos borrados con exito.", classes: "rounded"})</script>';
            echo '<script>recargar_compra()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
        }else{
            #SI NO ES BORRADO MANDAR UN MSJ CON ALERTA
            echo "<script >M.toast({html: 'Ha ocurrido un error.', classes: 'rounded'});/script>";
        }
        break;
    case 9:///////////////           IMPORTANTE               //////////////

        $id_articulo = $conn->real_escape_string($_POST['id_articulo']);
        $precio = $conn->real_escape_string($_POST['precio']);

        //CREAMOS LA SENTENCIA SQL PARA HACER LA ACTUALIZACION DE LA INFORMACION DE LA CATEGORIA Y LA GUARDAMOS EN UNA VARIABLE
        $sql = "UPDATE `punto_venta_articulos` SET precio = '$precio' WHERE id = $id_articulo";
        //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
        if(mysqli_query($conn, $sql)){
            echo '<script >M.toast({html:"El precio fue actualizado a: $'.sprintf('%.2f', $precio).'", classes: "rounded"})</script>';    
        }else{
            echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>'; 
        }//FIN else DE ERROR

        break;
}// FIN switch
mysqli_close($conn);
    
?>