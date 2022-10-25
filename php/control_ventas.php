<?php
//ARCHIVO QUE CONTIENE LA VARIABLE CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
//ARCHIVO QUE CONDICIONA QUE TENGAMOS ACCESO A ESTE ARCHIVO SOLO SI HAY SESSION INICIADA Y NOS PREMITE TOMAR LA INFORMACION DE ESTA
include('is_logged.php');
//DEFINIMOS LA ZONA  HORARIA
date_default_timezone_set('America/Mexico_City');
$id_user = $_SESSION['user_id'];// ID DEL USUARIO LOGEADO
$Fecha_hoy = date('Y-m-d');// FECHA ACTUAL
$datos_user = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM users WHERE user_id=$id_user"));
$almacen = $datos_user['almacen'];


//CON METODO POST TOMAMOS UN VALOR DEL 0 AL 3 PARA VER QUE ACCION HACER (Insertar = 0, Consultar compras = 1, InfoCliente = 2, Borrar compra = 3, Consulta Articulos TMP = 4, Actualizar Cant. o Costo = 5, buscararticulo y mostrar = 6, borrar listado TMP = 7, borrar todo TMP usuario = 8)
$Accion = $conn->real_escape_string($_POST['accion']);

//UN SWITCH EL CUAL DECIDIRA QUE ACCION REALIZA DEL CRUD (Insertar = 0, Consultar compras = 1, InfoCliente = 2, Borrar compra = 3, Consulta Articulos TMP = 4, Actualizar Cant. o Costo = 5, buscararticulo y mostrar = 6, borrar listado TMP = 7, borrar todo TMP usuario = 8)
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
            $sql_total = mysqli_fetch_array(mysqli_query($conn, "SELECT sum(importe) AS Total FROM tmp_pv_detalle_venta WHERE usuario = $id_user"));
            $Total = $sql_total['Total'];

            // SI NO HAY NUNGUNO IGUAL CREAMOS LA SENTECIA SQL  CON LA INFORMACION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
            $sql = "INSERT INTO `punto_venta_compras` (factura, id_proveedor, total, tipo_cambio, usuario, fecha) 
               VALUES('$Factura', '$Proveedor', '$Total', '$TipoCambio','$id_user','$Fecha_hoy')";
            //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
			if(mysqli_query($conn, $sql)){
				echo '<script >M.toast({html:"La compra se registró exitosamente.", classes: "rounded"})</script>';
                #SELECCIONAMOS EL ULTIMO CORTE CREADO
                $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id) AS id FROM punto_venta_compras WHERE usuario=$id_user"));           
                $compra = $ultimo['id'];//TOMAMOS EL ID DEL ULTIMO CORTE

                //REGISTRAMOS LOS ARTICULOS EN punto_venta_detalle_compra
                //REALIZAMOS LA CONSULTA A LA BASE DE DATOS Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
                $consulta = mysqli_query($conn, "SELECT * FROM tmp_pv_detalle_venta WHERE usuario = $id_user"); 
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
                            $UtilidadCincuenta = $articulo['precio']*$CincuentaP;//LIMITE PARA LA DIFERENCIA
                            $Diferencia = abs($articulo['precio']-$precio_compra_u); //DIFERENCIA DE PRECIOS
                            //COMPARAMOS PRECIOS
                            if ($Diferencia >= $UtilidadCincuenta) {
                                $LISTA .= '<tr><td>'.$articulo['codigo'].'</td><td>'.$articulo['nombre'].'</td><td>$'.sprintf('%.2f', $articulo['precio']).'</td><td>$'.sprintf('%.2f', $precio_compra_u).'</td><td><div class = "col s1"><br>$</div> <input id="precioCambio'.$id_articulo.'" type="number" class="validate col s10"></td><td><a onclick="cambiar_precio('.$id_articulo.')" class="btn-small indigo waves-effect waves-light">Cambiar</a></td></tr>';
                            }         

                            // SI SE INSERTO BORRAMOS EL ARTICULO DE tmp_pv_detalle_venta
                            mysqli_query($conn, "DELETE FROM `tmp_pv_detalle_venta` WHERE `tmp_pv_detalle_venta`.`id_articulo` = $id_articulo");
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
			}else{
                echo '<script >M.toast({html:"Ha ocurrio un error.", classes: "rounded"})</script>';   
            }//FIN else DE ERROR            
        }// FIN else DE BUSCAR CATEGORIA IGUAL
        break;
    case 1:  ///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 1 realiza:

        //CON POST RECIBIMOS UN TEXTO DEL BUSCADOR VACIO O NO de "compras_punto_venta.php"
        $Texto = $conn->real_escape_string($_POST['texto']);
        //VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
		if ($Texto != "") {
			//MOSTRARA LOS ALMACENES QUE SE ESTAN BUSCANDO Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql......
			$sql = "SELECT * FROM `punto_venta_compras`  WHERE  factura LIKE '$Texto%' OR id LIKE '$Texto%' OR id_proveedor LIKE '$Texto%' LIMIT 30";	
		}else{//ESTA CONSULTA SE HARA SIEMPRE QUE NO ALLA NADA EN EL BUSCADOR Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql...
			$sql = "SELECT * FROM `punto_venta_compras` LIMIT 30";
		}//FIN else $Texto VACIO O NO

        // REALIZAMOS LA CONSULTA A LA BASE DE DATOS Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
		$consulta = mysqli_query($conn, $sql);		
		$contenido = '';//CREAMOS UNA VARIABLE VACIA PARA IR LLENANDO CON LA INFORMACION EN FORMATO

		//VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
		if (mysqli_num_rows($consulta) == 0) {
				echo '<script>M.toast({html:"No se encontraron compras.", classes: "rounded"})</script>';
        } else {
            //SI NO ESTA EN == 0 SI TIENE INFORMACION
            //La variable $contenido contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
            //RECORREMOS UNO A UNO LAS COMPRAS CON EL WHILE
            while($compra = mysqli_fetch_array($consulta)) {
                $id_user = $compra['usuario'];// ID DEL USUARIO
                $id_proveedor = $compra['id_proveedor']; //ID DEL PROVEEDOR
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
    	$id = $conn->real_escape_string($_POST['cliente']);    
        $contenido = '';//CREAMOS UNA VARIABLE VACIA PARA IR LLENANDO CON LA INFORMACION EN FORMATO
        //VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
        if ($id != 0) {
            //HACEMOS LA CONSULTA DEL cliente Y MOSTRAMOS LA INFOR EN FORMATO HTML
            $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto-venta_clientes` WHERE id=$id"));
            $contenido .= '<br><h6 class = "col s12 m3 l3"><b>RFC: </b>'.$cliente['rfc'].'</h6>  <h6 class = "col s12 m3 l3"><b>Telefono: </b>'.$cliente['telefono'].' </h6> <h6 class = "col s12 m3 l3"><b>CD.:</b>'.$cliente['localidad'].' </h6> <h6 class = "col s12 m3 l3"><b>C.P.:</b>'.$cliente['cp'].' </h6>';
        }else{
            echo '<h5><b>VENTA AL PUBLICO</b></h5>';
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
                        $cantidad = $detalle['cantidad'];// CANTIDAD QUE HAY QUE RESTAR
                        $id_articulo = $detalle['id_articulo'];//ID DE ARTICULO EN TURNO
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
        $insert = $conn->real_escape_string($_POST['insert']);
        $id_venta = $conn->real_escape_string($_POST['id_venta']);

        //VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE insert = 1
        if ($insert) {
            //SE HACE LA INSERCION A TMP
            $id_art = $conn->real_escape_string($_POST['id_art']);
            if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `tmp_pv_detalle_venta` WHERE id_articulo = $id_art AND id_venta = $id_venta"))>0) {
                echo '<script >M.toast({html:"No se pueden repetir los articulos en la lista de la venta.", classes: "rounded"})</script>';   
            }else{
                #Tomamos los valores a insertar POST
                $cantidad = $conn->real_escape_string($_POST['cantidad']);
                $precio_venta = $conn->real_escape_string($_POST['precio_venta']);
                $importe = $cantidad*$precio_venta;

                //CREAMOS EL SQL PARA INSERTAR
                $sql = "INSERT INTO `tmp_pv_detalle_venta` (id_venta, id_articulo, cantidad, precio_venta, importe, usuario) 
                   VALUES($id_venta, $id_art,'$cantidad','$precio_venta','$importe','$id_user')";
                if(mysqli_query($conn, $sql)){
                    echo '<script >M.toast({html:"El articulo se registró exitosamente.", classes: "rounded"})</script>';   
                }else{
                    echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>'; 
                }//FIN else DE ERROR
            }
        }
        // REALIZAMOS LA CONSULTA A LA BASE DE DATOS Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
        $consulta = mysqli_query($conn, "SELECT * FROM `tmp_pv_detalle_venta` WHERE usuario = $id_user AND id_venta = $id_venta");      
        ?>
        <div class="row"><br><br>
            <div class="hide-on-small-only col s1"><br></div>
            <table class="col s12 m10 l10">
              <thead>
                <tr>
                  <th>Código</th>
                  <th>Descripción</th>
                  <th>Precio Venta</th>
                  <th>Cantidad</th>
                  <th>Importe</th>
                  <th>Exist.</th>
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
                        $existe = mysqli_fetch_array(mysqli_query($conn, "SELECT cantidad FROM `punto_venta_almacen_general` WHERE id_almacen = $almacen AND id_articulo = $id_art"));
                        if (!$existe) {
                            $existe['cantidad'] = 0;
                        }
                        $color = ($detalle_articulo['cantidad']>$existe['cantidad'])? 'class = "red-text"':'';
                        $mayor = ($detalle_articulo['cantidad']>$existe['cantidad'])? true:false;
                        ?>
                        <tr>
                            <td><?php echo $articulo['codigo'] ?></td>
                            <td><?php echo $articulo['nombre'] ?></td>
                            <td>$<?php echo sprintf('%.2f',$detalle_articulo['precio_venta']) ?></td>
                            <td <?php echo $color ?>><?php echo $detalle_articulo['cantidad'].' '.$articulo['unidad'] ?></td>
                            <td>$<?php echo sprintf('%.2f',$detalle_articulo['importe']) ?></td>
                            <td><?php echo $existe['cantidad'].' '.$articulo['unidad'] ?></td>
                            <td><a onclick="borrar_lista_articulo(<?php echo $id_art; ?>);" class="waves-effect waves-light btn-small red right"><i class="material-icons">delete</i></a></td>
                        </tr>
                    <?php
                    }//FIN WHILE
                    echo '<input type="hidden" id="mayor_exist" value="'.$mayor.'">';
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
                <div class="col s6 m6">
                    <h5 class="right"><b>Número de Artículos:  <?php echo $aux;?> </b></h5><br><br><br><br>
                    <a onclick="borrar_lista_all()" class="waves-effect waves-light btn-large indigo lighten-5 red-text right"><b>Cancelar<i class="material-icons left">remove_shopping_cart</i></b></a>
                    <a class="right white-text"> <br>_ _ _ _</a>
                    <a onclick="insert_venta()" class="waves-effect waves-light btn-large indigo lighten-5 teal-text right"><b>Cobrar<i class="material-icons left">add_shopping_cart</i></b></a>
                </div>
                <div class="hide-on-small-only col s2"><br></div>
                <div class="col s6 m4 row">
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
        //CREAMOS LA SENTENCIA SQL PARA HACER LA ACTUALIZACION DE LA INFORMACION DE LOS ARTICULOS Y LA GUARDAMOS EN UNA VARIABLE
        $sql = "UPDATE `tmp_pv_detalle_venta` SET cantidad = '$CantidadA', precio_compra_u = '$PrecioU', importe= '$Importe' WHERE id_articulo = $id_articulo AND usuario = $id_usuario";
        //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
        if(mysqli_query($conn, $sql)){
            #echo '<script >M.toast({html:"Las cantidades se actualizaron con exito.", classes: "rounded"})</script>';    
        }else{
            #echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>'; 
        }//FIN else DE ERROR
        break;
    case 6:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 6 realiza:
        
        //CON POST RECIBIMOS UN TEXTO DEL BUSCADOR VACIO O NO de "add_venta.php" INPUTS ARTICULO
        $Nombre = $conn->real_escape_string($_POST['valorNombre']);
        $Codigo = $conn->real_escape_string($_POST['valorCodigo']);
        
        if ($Codigo != '') {
            $sql = mysqli_query($conn,"SELECT id, codigo, nombre,precio  FROM `punto_venta_articulos` WHERE  codigo LIKE '$Codigo%' LIMIT 1");
        }else if ($Nombre != '') {
            $sql = mysqli_query($conn,"SELECT id, codigo, nombre,precio  FROM `punto_venta_articulos` WHERE  nombre LIKE '%$Nombre%' OR descripcion LIKE '%$Nombre%' LIMIT 1");
        }

         // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
        $consulta = mysqli_fetch_array($sql); 
        if (!$consulta) {
            $consulta['codigo'] =''; $consulta['nombre'] =''; $consulta['precio'] = 0; $consulta['id'] = '';
        } 

        $config = mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM punto_venta_config"));
        $utilidad = $config['utilidad'];
        $precio_compra = $consulta['precio'];
        $precio_venta = $precio_compra +($precio_compra*($utilidad/100));
        ?>
            <div class="row">
                <div class="input-field col s6 m3">
                  <i class="material-icons prefix">local_offer</i>
                  <input id="codigoP" type="text" value="<?php echo $consulta['codigo']; ?>">
                  <label for="codigoP">Código Producto:</label>
              </div>
              <div class="input-field col s6 m3">
                  <i class="material-icons prefix">edit</i>
                  <input id="nombreP" type="text" value="<?php echo $consulta['nombre']; ?>">
                  <label for="nombreP">Descripción:</label>
              </div>
              <div class="input-field col s4 m2">
                  <i class="material-icons prefix">filter_2</i>
                  <input id="cantidadP" type="number" value="1">
                  <label for="cantidadP">Cantidad:</label>
              </div>
              <div class="input-field col s4 m2">
                  <i class="material-icons prefix">monetization_on</i>
                  <input id="precio_venta" type="number" value="<?php echo sprintf('%.2f', $precio_venta); ?>">
                  <label for="precio_venta">Precio Venta:</label>
              </div>
              <input type="hidden" id="id_articulo" value="<?php echo $consulta['id']; ?>">
              <div class="col s4 m2"><br>
                <!-- BOTON QUE MANDA LLAMAR EL SCRIPT PARA QUE EL SCRIPT HAGA LO QUE LA FUNCION CONTENGA -->
                <a onclick="buscar();" class="waves-effect waves-light btn indigo lighten-5 indigo-text right"><b><i class="material-icons right">search</i>Buscar</b></a>
              </div>            
            </div>
        <?php
        break;
    case 7:///////////////           IMPORTANTE               //////////////
        //$Accion es igual a 7 realizar:

        //CON POST RECIBIMOS LA VARIABLE DEL BOTON POR EL SCRIPT DE "add_compra.php" QUE NESECITAMOS PARA BORRAR
        $id = $conn->real_escape_string($_POST['id']);
        $id_venta = $conn->real_escape_string($_POST['id_venta']);
        //Obtenemos la informacion del Usuario
        
        #VERIFICAMOS QUE SE BORRE CORRECTAMENTE EL ARTICULO DE TMP `tmp_pv_detalle_venta`
        if(mysqli_query($conn, "DELETE FROM `tmp_pv_detalle_venta` WHERE `tmp_pv_detalle_venta`.`id_articulo` = $id AND id_venta = $id_venta")){
            #SI ES ELIMINADO MANDAR MSJ CON ALERTA
            echo '<script >M.toast({html:"Articulo borrado con exito.", classes: "rounded"})</script>';
        }else{
            #SI NO ES BORRADO MANDAR UN MSJ CON ALERTA
            echo "<script >M.toast({html: 'Ha ocurrido un error.', classes: 'rounded'});/script>";
        }
        // REALIZAMOS LA CONSULTA A LA BASE DE DATOS Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
        $consulta = mysqli_query($conn, "SELECT * FROM tmp_pv_detalle_venta WHERE usuario = $id_user AND id_venta = $id_venta");      
        ?>
        <div class="row"><br><br>
            <div class="hide-on-small-only col s1"><br></div>
            <table class="col s12 m10 l10">
              <thead>
                <tr>
                  <th>Código</th>
                  <th>Descripción</th>
                  <th>Precio Venta</th>
                  <th>Cantidad</th>
                  <th>Importe</th>
                  <th>Exist.</th>
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
                        $existe = mysqli_fetch_array(mysqli_query($conn, "SELECT cantidad FROM `punto_venta_almacen_general` WHERE id_almacen = $almacen AND id_articulo = $id_art"));
                        if (!$existe) {
                            $existe['cantidad'] = 0;
                        }
                        $color = ($detalle_articulo['cantidad']>$existe['cantidad'])? 'class = "red-text"':'';
                        $mayor = ($detalle_articulo['cantidad']>$existe['cantidad'])? true:false;
                        ?>
                        <tr>
                            <td><?php echo $articulo['codigo'] ?></td>
                            <td><?php echo $articulo['nombre'] ?></td>
                            <td>$<?php echo sprintf('%.2f',$detalle_articulo['precio_venta']) ?></td>
                            <td <?php echo $color ?>><?php echo $detalle_articulo['cantidad'].' '.$articulo['unidad'] ?></td>
                            <td>$<?php echo sprintf('%.2f',$detalle_articulo['importe']) ?></td>
                            <td><?php echo $existe['cantidad'].' '.$articulo['unidad'] ?></td>
                            <td><a onclick="borrar_lista_articulo(<?php echo $id_art; ?>);" class="waves-effect waves-light btn-small red right"><i class="material-icons">delete</i></a></td>
                        </tr>
                    <?php
                    }//FIN WHILE
                    echo '<input type="hidden" id="mayor_exist" value="'.$mayor.'">';
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
                    <h5 class="right"><b>Número de Artículos:  <?php echo $aux;?> </b></h5><br><br><br><br>
                    <a onclick="borrar_lista_all()" class="waves-effect waves-light btn-large indigo lighten-5 red-text right"><b>Cancelar<i class="material-icons left">remove_shopping_cart</i></b></a>
                    <a class="right white-text"> <br>_ _ _ _</a>
                    <a onclick="insert_venta()" class="waves-effect waves-light btn-large indigo lighten-5 teal-text right"><b>Cobrar<i class="material-icons left">add_shopping_cart</i></b></a>
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

        $id_venta = $conn->real_escape_string($_POST['id_venta']);
        #VERIFICAMOS QUE SE BORRE CORRECTAMENTE TODOS LAS ARTICULOS QUE REGITRO EL USUARIO EN `tmp_pv_detalle_venta`
        if(mysqli_query($conn, "DELETE FROM `tmp_pv_detalle_venta` WHERE `usuario` = $id_user AND id_venta = $id_venta")){
            #SI ES ELIMINADO MANDAR MSJ CON ALERTA
            if (mysqli_query($conn, "DELETE FROM `punto_venta_ventas` WHERE id = $id_venta")) {
                echo '<script >M.toast({html:"Venta Cancelada.", classes: "rounded"})</script>';
            }
            echo '<script >M.toast({html:"Si hay articulos en la lista fueron borrados con exito.", classes: "rounded"})</script>';
            echo '<script>recargar_venta()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
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
}// FIN switch
mysqli_close($conn);
    
?>