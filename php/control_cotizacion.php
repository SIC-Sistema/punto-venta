<?php
//ARCHIVO QUE CONTIENE LA VARIABLE CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
//ARCHIVO QUE CONDICIONA QUE TENGAMOS ACCESO A ESTE ARCHIVO SOLO SI HAY SESSION INICIADA Y NOS PREMITE TOMAR LA INFORMACION DE ESTA
include('is_logged.php');
//DEFINIMOS LA ZONA  HORARIA
date_default_timezone_set('America/Mexico_City');
$id_user = $_SESSION['user_id'];// ID DEL USUARIO LOGEADO
$Fecha_hoy = date('Y-m-d');// FECHA ACTUAL

//CON METODO POST TOMAMOS UN VALOR DEL 0 AL 3 PARA VER QUE ACCION HACER (Insertar = 0, Consultar compras = 1, InfoProveedor = 2, Borrar compra = 3, Buscar e Insertar Articulos TMP = 4, Actualizar Cant. o Costo = 5, Consulta articulos Modal = 6, borrar listado TMP = 7, borrar todo TMP usuario = 8)
$Accion = $conn->real_escape_string($_POST['accion']);

//UN SWITCH EL CUAL DECIDIRA QUE ACCION REALIZA DEL CRUD (Insertar = 0, Consultar compras = 1, InfoProveedor = 2, Borrar compra = 3, Buscar e Insertar Articulos TMP = 4, Actualizar Cant. o Costo = 5, Consulta articulos Modal = 6, borrar listado TMP = 7, borrar todo TMP usuario = 8)
//echo "hola aqui estoy";
switch ($Accion) {
    case 0:  ///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 0 realiza:

        //CON POST RECIBIMOS TODAS LAS VARIABLES DEL FORMULARIO QUE NESECITAMOS PARA INSERTAR
        $Cliente = $conn->real_escape_string($_POST['valorCliente']);   
        $Cotizacion = $conn->real_escape_string($_POST['valorCotizacion']);   
        $TipoCambio = $conn->real_escape_string($_POST['valorTipoCambio']); 

        //VERIFICAMOS QUE NO HALLA UNA COMPRA CON LOS MISMOS DATOS
		if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `punto_venta_cotizaciones` WHERE cotizacion='$Cotizacion' "))>0){
            echo '<script >M.toast({html:"Ya se encuentra una Cotización con el mismo número de Cotización.", classes: "rounded"})</script>';
            echo '<script>recargar_cotizaciones()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
        }else{
            $sql_total = mysqli_fetch_array(mysqli_query($conn, "SELECT sum(importe) AS Total FROM tmp_pv_detalle_cotizacion WHERE usuario = $id_user"));
            $Total = $sql_total['Total'];

            // SI NO HAY NUNGUNO IGUAL CREAMOS LA SENTECIA SQL  CON LA INFORMACION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
            $sql = "INSERT INTO `punto_venta_cotizaciones` (cotizacion, id_cliente, total, tipo_cambio, usuario, fecha) 
               VALUES('$Cotizacion', '$Cliente', '$Total', '$TipoCambio','$id_user','$Fecha_hoy')";
            //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
			if(mysqli_query($conn, $sql)){
				echo '<script >M.toast({html:"La cotizacion se registró exitosamente.", classes: "rounded"})</script>';
                #SELECCIONAMOS EL ULTIMO CORTE CREADO
                $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id) AS id FROM `punto_venta_cotizaciones` WHERE usuario=$id_user"));           
                $venta = $ultimo['id'];//TOMAMOS EL ID DEL ULTIMO CORTE

                //REGISTRAMOS LOS ARTICULOS EN tmp_pv_detalle_cotizacion
                //REALIZAMOS LA CONSULTA A LA BASE DE DATOS Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
                $consulta = mysqli_query($conn, "SELECT * FROM tmp_pv_detalle_cotizacion WHERE usuario = $id_user"); 
                //VERIFICAMOS SI HAY ARTICULOS POR AGREGAR
                if(mysqli_num_rows($consulta)>0){
                    $LISTA = '';
                    $almacen = $conn->real_escape_string($_POST['almacen']); //ID DE ALMACEN
                    //RECORREMOS CON UN WHILE UNO POR UNO LOS ARTICULOS
                    while($detalle_articulo = mysqli_fetch_array($consulta)){
                        $id_articulo = $detalle_articulo['id_articulo'];
                        $cantidad = $detalle_articulo['cantidad'];
                        $precio_venta_u = $detalle_articulo['precio_venta_u'];
                        $importe = $detalle_articulo['importe'];
                        // CREAMOS EL SQL INSERT DEL ARTICULO EN TURNO EN punto_venta_detalle_cotizacion
                        $sql = "INSERT INTO `punto_venta_detalle_cotizacion` (id_venta, id_articulo, cantidad, precio_venta_u, importe) VALUES($venta, $id_articulo, '$cantidad', '$precio_venta_u','$importe')";

                        // VERIFICAMOS SI SE HIZO LA INSERCION
                        if (mysqli_query($conn, $sql)) {
                            
                            // HAY QUE CREAR UN LISTADO DE ARTICULOS QUE CAMBIARON EL PRECION CON DIFERENCIA DEL 50% DE LA UTILIDAD Y PREGUNTAR MODAL SI CAMBIAR EL PRECIO (CAMBIAR EN ARTICULOS)
                            $CincuentaP = (20/2)/100; //($utilidad/2)/100 porcentaje
                            // SELECCIONAMOS EL PRECIO DEL ARTICULO EN TURNO
                            $articulo =  mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM punto_venta_articulos WHERE id=$id_articulo")); 
                            $UtilidadCincuenta = $articulo['precio']*$CincuentaP;//LIMITE PARA LA DIFERENCIA
                            $Diferencia = abs($articulo['precio']-$precio_venta_u); //DIFERENCIA DE PRECIOS
                            //COMPARAMOS PRECIOS
                            if ($Diferencia >= $UtilidadCincuenta) {
                                $LISTA .= '<tr><td>'.$articulo['codigo'].'</td><td>'.$articulo['nombre'].'</td><td>$'.sprintf('%.2f', $articulo['precio']).'</td><td>$'.sprintf('%.2f', $precio_venta_u).'</td><td><div class = "col s1"><br>$</div> <input id="precioCambio'.$id_articulo.'" type="number" class="validate col s10"></td><td><a onclick="cambiar_precio('.$id_articulo.')" class="btn-small indigo waves-effect waves-light">Cambiar</a></td></tr>';
                            }         

                            // SI SE INSERTO BORRAMOS EL ARTICULO DE tmp_pv_detalle_cotizacion
                            mysqli_query($conn, "DELETE FROM `tmp_pv_detalle_cotizacion` WHERE `tmp_pv_detalle_cotizacion`.`id_articulo` = $id_articulo");
                        }//FIN IF                   
                    }//FIN WHILE 
                    // SI LA LISTA NO ESTA VACIA MOSTRAMOS LA VISTA:
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
                                            <th>Precio Venta</th>
                                            <th>Precio Fijado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php echo $LISTA; ?>
                                    </tbody>
                                </table><br><br><br>
                                <a onclick="recargar_cotizaciones()" class="btn waves-effect waves-light pink right">Aceptar y Continuar<i class="material-icons right">done</i></a>
                            </form>
                        </div><hr>
                        <?php 
                    }else{
                        //SI NO HAY NADA POR CAMBIAR SOLO REDIRECCIONAMOS
                        echo '<script>recargar_cotizaciones()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
                    }
                }else{
                    echo '<script >M.toast({html:"No se encontraron articulos por agregar.", classes: "rounded"})</script>';  
                }//FIN ELSE
			}else{
                echo '<script >M.toast({html:"Ha ocurrio un error.", classes: "rounded"})</script>';   
            }//FIN else DE ERROR            
        }// FIN else DE BUSCAR CATEGORIA IGUAL
        break;
    case 1:  ///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 1 realiza:

        //CON POST RECIBIMOS UN TEXTO DEL BUSCADOR VACIO O NO de "cotizaciones_punto_venta.php"
        $Texto = $conn->real_escape_string($_POST['texto']);
        //VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
		if ($Texto != "") {
			//MOSTRARA LOS PRODUCTOS QUE SE ESTAN BUSCANDO Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql......
			$sql = "SELECT * FROM `punto_venta_cotizaciones`  WHERE  cotizacion LIKE '$Texto%' OR id LIKE '$Texto%' OR id_cliente LIKE '$Texto%' LIMIT 30";	
		}else{//ESTA CONSULTA SE HARA SIEMPRE QUE NO ALLA NADA EN EL BUSCADOR Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql...
			$sql = "SELECT * FROM `punto_venta_cotizaciones` LIMIT 30";
		}//FIN else $Texto VACIO O NO

        // REALIZAMOS LA CONSULTA A LA BASE DE DATOS Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
		$consulta = mysqli_query($conn, $sql);		
		$contenido = '';//CREAMOS UNA VARIABLE VACIA PARA IR LLENANDO CON LA INFORMACION EN FORMATO

		//VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
		if (mysqli_num_rows($consulta) == 0) {
				echo '<script>M.toast({html:"No se encontraron cotizaciones.", classes: "rounded"})</script>';
        } else {
            //SI NO ESTA EN == 0 SI TIENE INFORMACION
            //La variable $contenido contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
            //RECORREMOS UNO A UNO LAS COMPRAS CON EL WHILE
            while($cotizacion = mysqli_fetch_array($consulta)) {
                $id_user = $cotizacion['usuario'];// ID DEL USUARIO
                $id_cliente = $cotizacion['id_cliente']; //ID DEL CLIENTE
                $user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `users` WHERE user_id=$id_user"));
				$cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto-venta_clientes` WHERE id=$id_cliente"));
				//Output
                $contenido .= '			
		          <tr>
		            <td>'.$cotizacion['id'].'</td>
                    <td>'.$cotizacion['cotizacion'].'</td>
                    <td>'.$id_cliente.' - '.$cliente['nombre'].'</td>
                    <td>'.$cotizacion['tipo_cambio'].'</td>
                    <td>$'.sprintf('%.2f', $cotizacion['total']).'</td>
		            <td>'.$user['firstname'].'</td>
		            <td>'.$cotizacion['fecha'].'</td>
		            <td><form method="post" action="../views/detalle_cotizacion_pv.php"><input id="cotizacion" name="cotizacion" type="hidden" value="'.$cotizacion['id'].'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">list</i></button></form></td>
		            <td><a onclick="borrar_compra_pv('.$cotizacion['id'].')" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
		          </tr>';

			}//FIN while
        }//FIN else
        echo $contenido;// MOSTRAMOS LA INFORMACION HTML
        break;
    case 2:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 2 realiza:

        //CON POST RECIBIMOS EL ID DEL CLIENTE DEL FORMULARIO POR EL SCRIPT "cotizacion_nueva_punto_venta.php" QUE NESECITAMOS PARA BUSCAR
    	$id = $conn->real_escape_string($_POST['cliente']);    
        $contenido = '';//CREAMOS UNA VARIABLE VACIA PARA IR LLENANDO CON LA INFORMACION EN FORMATO
        //VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
        if ($id != 0) {
            //HACEMOS LA CONSULTA DEL CLIENTE Y MOSTRAMOS LA INFORMACIÓN EN FORMATO HTML
            $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto-venta_clientes` WHERE id=$id"));
            $contenido .= '<h6 class = "col s12 m4 l4"><b>RFC: </b>'.$cliente['rfc'].'</h6>  <h6 class = "col s12 m4 l4"><b>Telefono: </b>'.$cliente['telefono'].' </h6>';
        }
        echo $contenido;// IMPRIMIMOS EL CONTENDIO QUE PUEDE IR VACIO SI ES $id = 0
        break;
    case 3:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 3 realiza:

        //CON POST RECIBIMOS LA VARIABLE DEL BOTON POR EL SCRIPT DE "cotizaciones_punto_venta.php" QUE NESECITAMOS PARA BORRAR
        $id = $conn->real_escape_string($_POST['id']);
    	//Obtenemos la informacion del Usuario
        $User = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `users` WHERE user_id = $id_user"));
        //SE VERIFICA SI EL USUARIO LOGEADO TIENE ALGÚN ALMACEN ASIGNADO PARA PODER BORRAR
        if ($User['almacen'] > 0) {
            #SELECCIONAMOS LA INFORMACION A BORRAR
            $cotizacion = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_cotizaciones` WHERE id = $id"));
            #CREAMOS EL SQL DE LA INSERCION A LA TABLA  `pv_borrar_cotizaciones` PARA NO PERDER INFORMACION
            $sql = "INSERT INTO `pv_borrar_cotizaciones` (id_venta, cotizacion, id_cliente, tipo_cambio, total, registro, borro, fecha_borro) 
                    VALUES($id, '".$cotizacion['cotizacion']."', '".$cotizacion['id_cliente']."', '".$cotizacion['tipo_cambio']."', '".$cotizacion['total']."', '".$cotizacion['usuario']."', '$id_user','$Fecha_hoy')";
            //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
            if(mysqli_query($conn, $sql)){
                //SI DE CREA LA INSERCION PROCEDEMOS A BORRRAR DE LA TABLA `punto_venta_cotizaciones`
                #VERIFICAMOS QUE SE BORRE CORRECTAMENTE LA COMPRA DE `punto_venta_cotizaciones`
                if(mysqli_query($conn, "DELETE FROM `punto_venta_cotizaciones` WHERE `punto_venta_cotizaciones`.`id` = $id")){
                    #SI ES ELIMINADO MANDAR MSJ CON ALERTA
                    echo '<script >M.toast({html:"Cotizacion borrada con exito.", classes: "rounded"})</script>';
                    echo '<script>recargar_cotizaciones()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
                }else{
                    #SI NO ES BORRADO MANDAR UN MSJ CON ALERTA
                    echo '<script >M.toast({html:"Hubo un error...", classes: "rounded"})</script>';
                }
            } 
        }else{
            echo '<script >M.toast({html:"Permiso denegado nececitas un almacen para poder borrar.", classes: "rounded"});
            M.toast({html:"Comunicate con un administrador.", classes: "rounded"});</script>';
        }   
        break;
    case 4:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 4 realiza:

        //CON POST RECIBIMOS UN ID DEL MODAL O AL INICIAR EL DOCUMENTO "cotizacion_nueva_punto_venta.php"
        $user_id = $conn->real_escape_string($_POST['id']);
        $insert = $conn->real_escape_string($_POST['insert']);

        //VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE insert = 1
        if ($insert) {
            //SE HACE LA INSERCION A TMP
            $id_art = $conn->real_escape_string($_POST['id_art']);
            if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `tmp_pv_detalle_cotizacion` WHERE id_articulo = $id_art"))>0) {
                echo '<script >M.toast({html:"No se pueden repetir los articulos en la lista.", classes: "rounded"})</script>';   
            }else{
                #SELECCIONAMOS LA INFORMACION DEL ARTICULO
                $articulo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_articulos` WHERE id = $id_art"));
                $precio = $articulo['precio'];
                //CREAMOS EL SQL PARA INSERTAR
                $sql = "INSERT INTO `tmp_pv_detalle_cotizacion` (id_articulo, cantidad, precio_venta_u, importe, usuario) 
                   VALUES($id_art,'1','$precio','$precio','$user_id')";
                if(mysqli_query($conn, $sql)){
                    echo '<script >M.toast({html:"El articulo se registró exitosamente.", classes: "rounded"})</script>';   
                }else{
                    echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>'; 
                }//FIN else DE ERROR
            }
        }
        // REALIZAMOS LA CONSULTA A LA BASE DE DATOS Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
        $consulta = mysqli_query($conn, "SELECT * FROM tmp_pv_detalle_cotizacion WHERE usuario = $user_id");      
        ?>
        <div class="row">
            <div class="hide-on-small-only col s1"><br></div>
            <table class="col s12 m10 l10">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Imagen</th>
                        <th>Cantidad</th>
                        <th>Artículo</th>
                        <th>Costo Unitario</th>
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
                        <!-- OBTENEMOS LA IMAGEN -->
                        <?php $img = ($articulo['imagen'] != '')? '<td><img class="materialboxed" width="100" src="../Imagenes/Catalogo/'.$articulo['imagen'].'"></td>': '<td></td>'; ?>
                        <tr>
                            <td><?php echo $articulo['codigo'] ?></td>
                            <?php echo $img ?>
                            <td class="row col s10"><input id="cantidadA<?php echo $id_art; ?>" type="number" class="validate col s6 m4 l4" value="<?php echo $detalle_articulo['cantidad'];?>" onchange= 'totales(<?php echo $id_art.', '.$user_id;?>);'><br><?php echo $articulo['unidad'] ?></td>
                            <td><?php echo $articulo['nombre'] ?></td>
                            <td class="row col s12"><p class="col s2">$</p><input id="precio_compra<?php echo $id_art; ?>" type="number" class="validate col s10 m7 l6" value="<?php echo sprintf('%.2f', $detalle_articulo['precio_venta_u']); ?>" onchange= 'totales(<?php echo $id_art.', '.$user_id;?>);'></td>
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
        // $Accion es igual a 5 realiza:

        sleep(1);// HACE UNA PAUSA DE 1 segundo para hace el cambio
        //CON POST RECIBIMOS TODAS LAS VARIABLES DEL FORMULARIO QUE NESECITAMOS PARA CAMBIAR LAS CANTIDADES
        $id_articulo = $conn->real_escape_string($_POST['valorIdArt']);
        $id_usuario = $conn->real_escape_string($_POST['valorIdUs']);
        $CantidadA = $conn->real_escape_string($_POST['valorCantidadA']);
        $PrecioU = $conn->real_escape_string($_POST['valorPrecioU']);
        $Importe = $CantidadA*$PrecioU;
        //CREAMOS LA SENTENCIA SQL PARA HACER LA ACTUALIZACION DE LA INFORMACION DE LOS ARTICULOS Y LA GUARDAMOS EN UNA VARIABLE
        $sql = "UPDATE `tmp_pv_detalle_cotizacion` SET cantidad = '$CantidadA', precio_venta_u = '$PrecioU', importe= '$Importe' WHERE id_articulo = $id_articulo AND usuario = $id_usuario";
        //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
        if(mysqli_query($conn, $sql)){
            echo '<script >M.toast({html:"Las cantidades se actualizaron con exito.", classes: "rounded"})</script>';    
        }else{
            echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>'; 
        }//FIN else DE ERROR
        break;
    case 6:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 6 realiza:
        
        //CON POST RECIBIMOS UN TEXTO DEL BUSCADOR VACIO O NO de "add_compra.php" MODAL
        $Texto = $conn->real_escape_string($_POST['texto']);
        //VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
        if ($Texto != "") {
            //MOSTRARA LOS ARTICULOS QUE SE ESTAN BUSCANDO Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql......
            $sql = "SELECT * FROM `punto_venta_articulos` WHERE  codigo LIKE '%$Texto%' OR nombre LIKE '%$Texto%' OR descripcion LIKE '%$Texto%' LIMIT 3 "; 
        }else{//ESTA CONSULTA SE HARA SIEMPRE QUE NO ALLA NADA EN EL BUSCADOR Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql...
            $sql = "SELECT * FROM `punto_venta_articulos` LIMIT 3";
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
                  <th>Imagen</th>
                  <th>Nombre</th>
                  <th>Descripcion</th>
                  <th>Modelo</th>
                  <th>Unidad</th>
                  <th>Costo U.</th>
                  <th>C.Unidad</th>
                  <th>Codigo Fiscal</th>
                </tr>
              </thead>
              <tbody>
               <?php
               //VERIFICAMOS SI HA ARRTICULOS EN LA TABLA
               if(mysqli_num_rows($consulta)>0){
                    while($articulo = mysqli_fetch_array($consulta)){
                        ?>
                        <!-- Output -->
                        <?php $img = ($articulo['imagen'] != '')? '<td><img class="materialboxed" width="100" src="../Imagenes/Catalogo/'.$articulo['imagen'].'"></td>': '<td></td>'; ?>
                        <tr>
                            <td><?php echo $articulo['codigo'] ?></td>
                            <?php echo $img ?>
                            <td><?php echo $articulo['nombre'] ?></td>
                            <td><?php echo $articulo['descripcion'] ?></td>
                            <td><?php echo $articulo['modelo'] ?></td>
                            <td><?php echo $articulo['unidad'] ?></td>
                            <td><?php echo $articulo['precio'] ?></td>
                            <td><?php echo $articulo['codigo_unidad'] ?></td>
                            <td><?php echo $articulo['codigo_fiscal'] ?></td>
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

        //CON POST RECIBIMOS LA VARIABLE DEL BOTON POR EL SCRIPT DE "cotizacion_nueva_punto_venta.php" QUE NESECITAMOS PARA BORRAR
        $id = $conn->real_escape_string($_POST['id']);
        //Obtenemos la informacion del Usuario
        
        #VERIFICAMOS QUE SE BORRE CORRECTAMENTE EL ARTICULO DE TMP `tmp_pv_detalle_compra`
        if(mysqli_query($conn, "DELETE FROM `tmp_pv_detalle_cotizacion` WHERE `tmp_pv_detalle_cotizacion`.`id_articulo` = $id")){
            #SI ES ELIMINADO MANDAR MSJ CON ALERTA
            echo '<script >M.toast({html:"Articulo borrado con exito.", classes: "rounded"})</script>';
        }else{
            #SI NO ES BORRADO MANDAR UN MSJ CON ALERTA
            echo "<script >M.toast({html: 'Ha ocurrido un error.', classes: 'rounded'});/script>";
        }
        // REALIZAMOS LA CONSULTA A LA BASE DE DATOS Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
        $consulta = mysqli_query($conn, "SELECT * FROM tmp_pv_detalle_cotizacion WHERE usuario = $id_user");      
        ?>
        <div class="row">
            <div class="hide-on-small-only col s1"><br></div>
            <table class="col s12 m10 l10">
              <thead>
                <tr>
                  <th>Código</th>
                  <th>Imagen</th>
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
                        <!-- OBTENEMOS LA IMAGEN -->
                        <?php $img = ($articulo['imagen'] != '')? '<td><img class="materialboxed" width="100" src="../Imagenes/Catalogo/'.$articulo['imagen'].'"></td>': '<td></td>'; ?>
                        <tr>
                            <td><?php echo $articulo['codigo'] ?></td>
                            <?php echo $img ?>
                            <td class="row col s10"><input id="cantidadA<?php echo $id_art; ?>" type="number" class="validate col s6 m4 l4" value="<?php echo $detalle_articulo['cantidad'];?>" onchange= 'totales(<?php echo $id_art.', '.$user_id;?>);'><br><?php echo $articulo['unidad'] ?></td>
                            <td><?php echo $articulo['nombre'] ?></td>
                            <td class="row col s12"><p class="col s2">$</p><input id="precio_compra<?php echo $id_art; ?>" type="number" class="validate col s10 m7 l6" value="<?php echo sprintf('%.2f', $detalle_articulo['precio_venta_u']); ?>" onchange= 'totales(<?php echo $id_art.', '.$user_id;?>);'></td>
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
        #VERIFICAMOS QUE SE BORRE CORRECTAMENTE TODOS LAS ARTICULOS QUE REGITRO EL USUARIO EN `tmp_pv_detalle_cotizacion`
        if(mysqli_query($conn, "DELETE FROM `tmp_pv_detalle_cotizacion` WHERE `usuario` = $id_usuario")){
            #SI ES ELIMINADO MANDAR MSJ CON ALERTA
            echo '<script >M.toast({html:"Si hay articulos en la lsita fueron borrados con exito.", classes: "rounded"})</script>';
            echo '<script>recargar_cotizaciones()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
        }else{
            #SI NO ES BORRADO MANDAR UN MSJ CON ALERTA
            echo "<script >M.toast({html: 'Ha ocurrido un error.', classes: 'rounded'});/script>";
        }
        break;
    case 9:///////////////           IMPORTANTE               //////////////

        $id_articulo = $conn->real_escape_string($_POST['id_articulo']);
        $precio = $conn->real_escape_string($_POST['precio']);

        //CREAMOS LA SENTENCIA SQL PARA HACER LA ACTUALIZACION DE LA INFORMACION DEL PRECIO DEL ARTICULO (EN CATALOGO) Y LA GUARDAMOS EN UNA VARIABLE
        $sql = "UPDATE `punto_venta_articulos` SET precio = '$precio' WHERE id = $id_articulo";
        //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
        if(mysqli_query($conn, $sql)){
            echo '<script >M.toast({html:"El precio fue actualizado a: $'.sprintf('%.2f', $precio).'", classes: "rounded"})</script>';    
        }else{
            echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>'; 
        }//FIN else DE ERROR
        break;

    case 10:///////////////           IMPORTANTE               //////////////

        // $Accion es igual a 10 realiza:
        $informacion_usuario = "SELECT area FROM `users` WHERE user_id = $id_user";

        if($informacion_usuario ='Administrador'){

            //RECIBIMOS TODAS LAS VARIABLES DES DE EL ARCHIVO modal_almacen.php
            $id_cotizacion = $conn->real_escape_string($_POST['id_cotizacion']);
            $DesCambio = $conn->real_escape_string($_POST['descripcion_cambio']);
            $Cantidad = $conn->real_escape_string($_POST['cantidadCambiar']);
            $Precio = $conn->real_escape_string($_POST['precioCambiar']);


            //DECLARAMOS LAS VARIABLES QUE NECECITAMOS

            //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL ARTICULO EN LA BD `punto_venta_detalle_cotizacion` Y ASIGNAMOS EL ARRAY A UNA VARIABLE $cotizacion
            $detalle_cotizacion = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_detalle_cotizacion` WHERE id=$id_cotizacion LIMIT 1"));
            //CON LA VARIABLE $id_articulo DECIMOS NOS TRAEMOS LA INFORMACION DEL ID DEL ARTICULO PARA SER UTILIZADA 
            $id_articulo=$detalle_cotizacion['id_articulo'];
            $id_detalle=$detalle_cotizacion['id'];

            //CREAMOS LA SENTENCIA SQL PARA HACER LA ACTUALIZACION DE LA INFORMACION DEL ALMACEN Y LA GUARDAMOS EN UNA VARIABLE
            $sql_update1 = "UPDATE `punto_venta_articulos` SET precio = '$Precio' WHERE id = $id_articulo";
            $up_1 = "UPDATE `punto_venta_detalle_cotizacion` SET precio_venta_u = '$Precio' WHERE id = $id_detalle";
            $sql_update2 = "UPDATE `punto_venta_detalle_cotizacion` SET cantidad = '$Cantidad' WHERE id = $id_detalle";
            // $sql_update_importe = "UPDATE `punto_venta_detalle_cotizacion` SET importe = ('$Cantidad'*'$Precio')WHERE id_articulo = $id_articulo";

            //VERIFICAMOS QUE LAS SENTECIAS SON EJECUTADAS CON EXITO!
            if(mysqli_query($conn, $up_1)){
                if(mysqli_query($conn, $sql_update1)){
                    if(mysqli_query($conn, $sql_update2)){
                        $sql_insert = "INSERT INTO `punto_venta_modificaciones_cotizacion` (descripcion_cambio, producto, codigo_cotizacion, usuario, fecha) VALUES('$DesCambio',$id_articulo,10,$id_user,'$Fecha_hoy')";
                        if(mysqli_query($conn, $sql_insert)){
                            echo 'Los datos se actualizarón con exito.';
                            ?>
                            <script>
                                // REDIRECCIONAMOS 
                                setTimeout("location.href='../views/cotizaciones_punto_venta.php'", 500);
                            </script>
                            <?php
                        }else{
                            echo 'Ha ocurrido un error en el INSERT...';
                        }
                    
                    }else{
                        echo 'Ha ocurrio un error UPDATE2...';
                    }             
                }else{
                    echo 'Ha ocurrio un error UPDATE1...';	
                }//FIN else DE ERROR
            }else{
                echo '<script >M.toast({html:"Solo los Administradores pueden editar...", classes: "rounded"})</script>';
                ?>
                <script>
                    // REDIRECCIONAMOS 
                    setTimeout("location.href='../views/cotizaciones_punto_venta.php'", 500);
                </script>
                <?php
            }
                
            }else{
                echo 'Ha ocurrido un error en el INSERT precio en detalle...';
            }
            
    break;
}// FIN switch
mysqli_close($conn);
?>

        

        