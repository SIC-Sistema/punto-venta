<?php
//ARCHIVO QUE CONTIENE LA VARIABLE CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
//ARCHIVO QUE CONDICIONA QUE TENGAMOS ACCESO A ESTE ARCHIVO SOLO SI HAY SESSION INICIADA Y NOS PREMITE TOMAR LA INFORMACION DE ESTA
include('is_logged.php');
//DEFINIMOS LA ZONA  HORARIA
date_default_timezone_set('America/Mexico_City');
$id_user = $_SESSION['user_id'];// ID DEL USUARIO LOGEADO
$Fecha_hoy = date('Y-m-d');// FECHA ACTUAL
$Hora = date('H:i:s');
$datos_user = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM users WHERE user_id=$id_user"));
$almacen = $datos_user['almacen'];

//CON METODO POST TOMAMOS UN VALOR DEL 0 AL 3 PARA VER QUE ACCION HACER (Insertar = 0, , InfoCliente = 2, Borrar Venta = 3, Consulta Articulos TMP = 4, pausar venta = 5, buscar articulo y mostrar = 6, borrar listado TMP = 7, borrar todo TMP usuario = 8)
$Accion = $conn->real_escape_string($_POST['accion']);

//UN SWITCH EL CUAL DECIDIRA QUE ACCION REALIZA DEL CRUD (Insertar = 0, , InfoCliente = 2, Borrar Venta = 3, Consulta Articulos TMP = 4, pausar venta = 5, buscar articulo y mostrar = 6, borrar listado TMP = 7, borrar todo TMP usuario = 8)
//echo "hola aqui estoy";
switch ($Accion) {
    case 0:  ///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 0 realiza:

        //CON POST RECIBIMOS TODAS LAS VARIABLES DEL FORMULARIO QUE NESECITAMOS PARA INSERTAR LA INFO DE LA VENTA
        $id_venta = $conn->real_escape_string($_POST['id_venta']);   
        $cliente = $conn->real_escape_string($_POST['cliente']);   
        $tipo_cambio = $conn->real_escape_string($_POST['tipo_cambio']);  
        $sql_total = mysqli_fetch_array(mysqli_query($conn, "SELECT sum(importe) AS Total FROM `tmp_pv_detalle_venta` WHERE usuario = $id_user AND id_venta = $id_venta"));
        $Total = $sql_total['Total'];
        $pago = $conn->real_escape_string($_POST['pago']);  
        
        //SÍ LA FORMA DE PAGO ES A CREDITO Y NO HAY CLIENTE, NO SE PUEDE HACER LA VENTA
        if ($tipo_cambio == 'Credito' AND $cliente == 0){
            echo '<script >M.toast({html:"Debe seleccionar un cliente sí quiere registrar a crédito.", classes: "rounded"})</script>'; 
        }else{
            $sql = "UPDATE `punto_venta_ventas` SET id_cliente = $cliente, fecha= '$Fecha_hoy', hora = '$Hora', tipo_cambio = '$tipo_cambio', total = '$Total', usuario = $id_user, estatus = 2, pagada = $pago   WHERE id = $id_venta";
  
            //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
            if(mysqli_query($conn, $sql)){
                echo '<script >M.toast({html:"La venta se termino exitosamente.", classes: "rounded"})</script>';
                //REGISTRAMOS LOS ARTICULOS EN punto_venta_detalle_venta
                //REALIZAMOS LA CONSULTA A LA BASE DE DATOS Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
                $consulta = mysqli_query($conn, "SELECT * FROM `tmp_pv_detalle_venta` WHERE usuario = $id_user AND id_venta = $id_venta"); 
                //VERIFICAMOS SI HAY ARTICULOS POR AGREGAR
                if(mysqli_num_rows($consulta)>0){
                    //RECORREMOS CON UN WHILE UNO POR UNO LOS ARTICULOS
                    while($detalle_articulo = mysqli_fetch_array($consulta)){
                        $id_articulo = $detalle_articulo['id_articulo'];
                        $cantidad = $detalle_articulo['cantidad'];
                        $precio_venta = $detalle_articulo['precio_venta'];
                        $importe = $detalle_articulo['importe'];
                        // CREAMOS EL SQL INSERT DEL ARTICULO EN TURNO EN punto_venta_detalle_venta
                        $sql = "INSERT INTO `punto_venta_detalle_venta` (id_venta, id_producto, cantidad, precio_venta, importe) VALUES($id_venta, $id_articulo, '$cantidad', '$precio_venta','$importe')";
                        
                        // VERIFICAMOS SI SE HIZO LA INSERCION
                        if (mysqli_query($conn, $sql)) {
                            // VERIFICAMOS SI EL ARTICULO YA ESTA EN ALMACEN Y SOLO MODIFICAMOS LA CANTIDAD -
                            if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `punto_venta_almacen_general` WHERE id_articulo = '$id_articulo' AND id_almacen = '$almacen'"))>0) {
                                mysqli_query($conn, "UPDATE `punto_venta_almacen_general` SET cantidad = cantidad-$cantidad, modifico = $id_user, fecha_modifico = '$Fecha_hoy' WHERE id_articulo = '$id_articulo' AND id_almacen = '$almacen'");
                            }//FIN if esta en ALMACEN
                            // SI SE INSERTO BORRAMOS EL ARTICULO DE tmp_pv_detalle_venta
                            mysqli_query($conn, "DELETE FROM `tmp_pv_detalle_venta` WHERE `tmp_pv_detalle_venta`.`id_articulo` = $id_articulo AND id_venta = $id_venta");
                        }//FIN if insert              
                    }//FIN while
                    $pago = $conn->real_escape_string($_POST['pago']);  
                    if ($pago) {
                        $descripcion = 'Venta N°'.$id_venta;

                        if ($tipo_cambio == 'Credito') {
                            $cliente_punto_venta = $cliente + 10000;
                            $mysql_deudas = "INSERT INTO deudas(id_cliente, cantidad, fecha_deuda, hasta, tipo, descripcion, usuario) VALUES ($cliente_punto_venta, $Total, '$Fecha_hoy', NULL, '$Tipo', '$descripcion', $id_user)";  
                            mysqli_query($conn,$mysql_deudas);
                            //SE LE SUMA 10,000 AL id DEL CLIENTE DEL PUNTO DE VENTA
                            $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_deuda) AS id FROM deudas WHERE id_cliente = $cliente_punto_venta"));            
                            $id_deuda = $ultimo['id'];
                            $sql = "INSERT INTO pagos(id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio, id_deuda, Cotejado) VALUES ($cliente_punto_venta, '$Descripcion', $Total, '$Fecha_hoy', '$Hora', '$Tipo', $id_user, 0, 0, '$Tipo_Cambio', $id_deuda, 0)";
                            //SE AÑADE EL ID DE LA DEUDA A LA VENTA
                            $esecuele = "UPDATE `punto_venta_ventas` SET id_deuda = $id_deuda WHERE id = $id_venta";
                            // CREAMOS LA DEUDA DE CREDITO AL CLIENTE
                            $sql_credito = mysqli_query($conn,"INSERT INTO `punto_venta_credito` (id_cliente, id_venta, fecha, hora, tipo_cambio, id_deuda, total, usuario) VALUES($cliente, $id_venta, '$Fecha_hoy', '$Hora', '$tipo_cambio', $id_deuda, $Total, $id_user)");
                            if(mysqli_query($conn, $mysql_deuda)){
                                echo '<script >M.toast({html:"Se agrego una nueva deuda.", classes: "rounded"})</script>';  
                            }
                        }

                        $cliente = ($cliente == 0)? $cliente: $cliente+10000;
                        #--- CREAMOS EL SQL PARA LA INSERCION ---
                        $sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, tipo_cambio) VALUES ($cliente, '$descripcion', '$Total', '$Fecha_hoy', '$Hora', 'Punto Venta', $id_user, 0, '$tipo_cambio')";
                        #--- SE INSERTA EL PAGO -----------
                        if(mysqli_query($conn, $sql)){
                            $cantidadPago = $conn->real_escape_string($_POST['cantidadPago']);  
                            ?>
                            <script>
                                var a = document.createElement("a");
                                a.href = "../php/ticket_venta.php?p="+<?php echo $cantidadPago; ?>+"&v="+<?php echo $id_venta; ?>;
                                a.target = "blank";
                                a.click();
                            </script>
                            <?php
                            echo '<script>M.toast({html:"El pago se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
                        }// FIN if pago
                    }// FIN IF CONDICION SE HACE PAGO
                }//FIN if consulta
                echo '<script>recargar_venta();</script>';
            }else{
                echo '<script >M.toast({html:"Ha ocurrio un error.", classes: "rounded"})</script>';            
            }// FIN else error
        }//FIN else COMPROBACION DE CLIENTE EN CREDITO
        break;
    case 1:  ///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 1 realiza:

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

        //CON POST RECIBIMOS LA VARIABLE DEL BOTON POR EL SCRIPT DE "ventas_punto_venta.php" QUE NESECITAMOS PARA BORRAR
        $id = $conn->real_escape_string($_POST['id']);
    	//Obtenemos la informacion del Usuario
        $User = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `users` WHERE user_id = $id_user"));
        //SE VERIFICA SI EL USUARIO LOGEADO TIENE PERMISO DE BORRAR ventas
        if ($User['ventas'] == 1) {
            #SELECCIONAMOS LA INFORMACION A BORRAR
            $venta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_ventas` WHERE id = $id"));
            #CREAMOS EL SQL DE LA INSERCION A LA TABLA  `pv_borrar_compras` PARA NO PERDER INFORMACION
            $sql = "INSERT INTO `pv_borrar_ventas` (id_venta, id_cliente, fecha, hora, tipo_cambio, total, registro, borro, fecha_borro) VALUES($id, '".$venta['id_cliente']."', '".$venta['fecha']."', '".$venta['hora']."', '".$venta['tipo_cambio']."', '".$venta['total']."', '".$venta['usuario']."', '$id_user','$Fecha_hoy')";
            //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
            if(mysqli_query($conn, $sql)){
                //SÍ SE BORRA UNA VENTA QUE ES A CREDITO ENTONCES SE BORRA LA DEUDA Y EL CREDITO CORRESPONDIENTE
                if($venta['tipo_cambio']='credito'){
                    #SI ES ELIMINADO MANDAR MSJ CON ALERTA
                    if(mysqli_query($conn, "DELETE FROM `punto_venta_credito` WHERE 'id_venta' = $id")){
                        echo '<script >M.toast({html:"Crédito borrado con exito.", classes: "rounded"})</script>';
                    }
                    $cliente_punto_venta = $venta['id_cliente'];
                    $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_deuda) AS id FROM deudas WHERE id_cliente = $cliente_punto_venta"));            
                    $id_deuda = $ultimo['id'];
                    if(mysqli_query($conn, "DELETE FROM `deudas` WHERE 'id_deuda' = $id_deuda")){
                        #SI ES ELIMINADO MANDAR MSJ CON ALERTA
                        echo '<script >M.toast({html:"Deuda borrada con exito.", classes: "rounded"})</script>';
                    }
                }
                /////////////////////////////////////////////////////////////////////////////////////////////////
                //SI DE CREA LA INSERCION PROCEDEMOS A BORRRAR DE LA TABLA `punto_venta_ventas`
                #VERIFICAMOS QUE SE BORRE CORRECTAMENTE LA COMPRA DE `punto_venta_ventas`
                if(mysqli_query($conn, "DELETE FROM `punto_venta_ventas` WHERE `punto_venta_ventas`.`id` = $id")){
                    #SI ES ELIMINADO MANDAR MSJ CON ALERTA
                    echo '<script >M.toast({html:"Venta borrada con exito.", classes: "rounded"})</script>';

                    #HAY QUE RECORRER LOS ARTICULOS DE DETALLE PARA ELIMINAR LA CANTIDAD DEL ALMACEN DE CADA UNO
                    $sql_art =  mysqli_query($conn,"SELECT * FROM punto_venta_detalle_venta WHERE id_venta=$id");
                    if (mysqli_num_rows($sql_art) <= 0) {
                      echo '<script>M.toast({html:"No se encontraron articulos en la venta.", classes: "rounded"})</script>';
                    }else{
                      $almacen = $User['almacen']; //ID DE ALMACEN
                      while($detalle = mysqli_fetch_array($sql_art)){
                        $cantidad = $detalle['cantidad'];// CANTIDAD QUE HAY QUE RESTAR
                        $id_articulo = $detalle['id_producto'];//ID DE ARTICULO EN TURNO
                        mysqli_query($conn, "UPDATE `punto_venta_almacen_general` SET cantidad = cantidad+$cantidad, modifico = $id_user, fecha_modifico = '$Fecha_hoy' WHERE id_articulo = '$id_articulo' AND id_almacen = '$almacen'");            
                      }//FIN while
                    }// FIN else
                    echo '<script>recargar_venta()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
                }else{
                    #SI NO ES BORRADO MANDAR UN MSJ CON ALERTA
                    echo '<script >M.toast({html:"Hubo un error...", classes: "rounded"})</script>';
                }
            } 
        }else{
            echo '<script >M.toast({html:"Permiso denegado, no tienes permiso para borrar ventas", classes: "rounded"});
            M.toast({html:"Comunicate con un administrador.", classes: "rounded"});</script>';
        }   
        break;
    case 4:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 4 realiza:

        //CON POST RECIBIMOS UN ID DEL MODAL O AL INICIAR EL DOCUMENTO "add_venta.php"
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
        $consulta = mysqli_query($conn, "SELECT * FROM `tmp_pv_detalle_venta` WHERE id_venta = $id_venta");      
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
                    $mayor = false;
                    while($detalle_articulo = mysqli_fetch_array($consulta)){
                        $id_art = $detalle_articulo['id_articulo'];
                        $articulo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_articulos` WHERE id = $id_art"));
                        $total += $detalle_articulo['importe'];
                        $existe = mysqli_fetch_array(mysqli_query($conn, "SELECT cantidad FROM `punto_venta_almacen_general` WHERE id_almacen = $almacen AND id_articulo = $id_art"));
                        if (!$existe) {  $existe['cantidad'] = 0;  }
                        $color = ($detalle_articulo['cantidad']>$existe['cantidad'])? 'class = "red-text"':'';
                        if ($detalle_articulo['cantidad']>$existe['cantidad']){        $mayor = true;           }
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
                    <a onclick="modal_venta()" class="waves-effect waves-light btn-large indigo lighten-5 teal-text right"><b>Cobrar<i class="material-icons left">local_atm</i></b></a>
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
        // $Accion es igual a 5 realiza:

        //CON POST RECIBIMOS TODAS LAS VARIABLES DEL FORMULARIO QUE NESECITAMOS PARA ACTUALIOZAR LA INFO DE LA VENTA
        $id_venta = $conn->real_escape_string($_POST['id_venta']);  

        $sql = "UPDATE `punto_venta_ventas` SET estatus = 1  WHERE id = $id_venta";

        //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
        if(mysqli_query($conn, $sql)){
            echo '<script >M.toast({html:"La venta se pauso exitosamente.", classes: "rounded"})</script>';
            echo '<script>recargar_venta();</script>';
        }else{
            echo '<script >M.toast({html:"Ocurrio un error.", classes: "rounded"})</script>';
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
        $consulta = mysqli_query($conn, "SELECT * FROM tmp_pv_detalle_venta WHERE  id_venta = $id_venta");      
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
                    $mayor = false;
                    while($detalle_articulo = mysqli_fetch_array($consulta)){
                        $id_art = $detalle_articulo['id_articulo'];
                        $articulo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_articulos` WHERE id = $id_art"));
                        $total += $detalle_articulo['importe'];
                        $existe = mysqli_fetch_array(mysqli_query($conn, "SELECT cantidad FROM `punto_venta_almacen_general` WHERE id_almacen = $almacen AND id_articulo = $id_art"));
                        if (!$existe) {   $existe['cantidad'] = 0;  }
                        $color = ($detalle_articulo['cantidad']>$existe['cantidad'])? 'class = "red-text"':'';
                        if ($detalle_articulo['cantidad']>$existe['cantidad']){    $mayor = true;     }
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
                    <a onclick="modal_venta()" class="waves-effect waves-light btn-large indigo lighten-5 teal-text right"><b>Cobrar<i class="material-icons left">add_shopping_cart</i></b></a>
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
        // $Accion es igual a 9 realiza:
        //CON POST RECIBIMOS UN TEXTO DEL BUSCADOR VACIO O NO de "almacen_punto_venta.php"
        $Texto = $conn->real_escape_string($_POST['texto']);
        //RECIBE UN ID IMPORTANTE

        //VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
        if ($Texto != "") {
            //MOSTRARA LOS ARTICULOS QUE SE ESTAN BUSCANDO Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql...... Codigo, Nombre, Descripcion
            $sql = "SELECT * FROM `punto_venta_ventas` WHERE estatus = 2 AND pagada = 1 AND (id = '$Texto' OR id_cliente = '$Texto' OR fecha LIKE '$Texto%')";   
        }else{//ESTA CONSULTA SE HARA SIEMPRE QUE NO ALLA NADA EN EL BUSCADOR Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE 
            $sql = "SELECT * FROM `punto_venta_ventas` WHERE estatus = 2 AND pagada = 1 LIMIT 50";
        }//FIN else $Texto VACIO O NO

        // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
        $consulta = mysqli_query($conn, $sql);      
        $contenido = '';//CREAMOS UNA VARIABLE VACIA PARA IR LLENANDO CON LA INFORMACION EN FORMATO

        //VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
        if (mysqli_num_rows($consulta) == 0) {
                echo '<script>M.toast({html:"No se encontraron ventas.", classes: "rounded"})</script>';
        } else {
            //SI NO ESTA EN == 0 SI TIENE INFORMACION
            //La variable $contenido contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
            //RECORREMOS UNO A UNO LOS ARTICULOS CON EL WHILE
            while($venta = mysqli_fetch_array($consulta)) {
                $id_cliente = $venta['id_cliente'];
                if ($id_cliente == 0) {
                    $cliente['nombre'] = 'Venta Publico';
                }else{
                    $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM  `punto-venta_clientes` WHERE id=$id_cliente"));
                }
                $id_usuario = $venta['usuario'];
                $user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_usuario"));
                //Output
                $contenido .= '         
                  <tr>
                    <td>'.$venta['id'].'</td>
                    <td>'.$cliente['nombre'].'</td>                    
                    <td>'.$venta['fecha'].' '.$venta['hora'].'</td>
                    <td>'.$venta['tipo_cambio'].'</td>
                    <td><b>$'.sprintf('%.2f', $venta['total']).'</b></td>
                    <td>'.$user['firstname'].'</td>
                    <td><form method="post" action="../views/detalles_venta_pv.php"><input id="venta" name="venta" type="hidden" value="'.$venta['id'].'"><br><button class="btn-small waves-effect waves-light pink"><i class="material-icons">list</i></button></form></td>
                    <td><a onclick="facturar('.$venta['id'].')" class="btn-small blue darken-1 waves-effect waves-light"><i class="material-icons">note</i></a></td>
                    <td><a onclick="devolucion_venta_pv('.$venta['id'].')" class="btn-small grey darken-4 waves-effect waves-light"><i class="material-icons">reply</i></a></td>
                    <td><a onclick="borrar_venta_pv('.$venta['id'].')" class="btn-small red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                    <td><a href = "../php/ticket_venta.php?p=0&v='.$venta['id'].'" target = "blank" class="btn-small pink darken-1 waves-effect waves-light"><i class="material-icons">print</i></a></td>
                  </tr>';
            }//FIN while
        }//FIN else
        echo $contenido;// MOSTRAMOS LA INFORMACION HTML
        break;
    case 10:///////////////           IMPORTANTE               //////////////
        // $Accion es igual a 10 realiza:

        //CON POST RECIBIMOS UN TEXTO DEL BUSCADOR VACIO O NO de "almacen_punto_venta.php"
        $Texto = $conn->real_escape_string($_POST['texto']);
        //RECIBE UN ID IMPORTANTE

        //VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
        if ($Texto != "") {
            //MOSTRARA LOS ARTICULOS QUE SE ESTAN BUSCANDO Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql...... Codigo, Nombre, Descripcion
            $sql = "SELECT * FROM `punto_venta_ventas` WHERE estatus !=2 AND (id = '$Texto' OR id_cliente = '$Texto' OR fecha LIKE '$Texto%')";   
        }else{//ESTA CONSULTA SE HARA SIEMPRE QUE NO ALLA NADA EN EL BUSCADOR Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE 
            $sql = "SELECT * FROM `punto_venta_ventas` WHERE estatus != 2 LIMIT 50";
        }//FIN else $Texto VACIO O NO

        // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
        $consulta = mysqli_query($conn, $sql);      
        $contenido = '';//CREAMOS UNA VARIABLE VACIA PARA IR LLENANDO CON LA INFORMACION EN FORMATO

        //VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
        if (mysqli_num_rows($consulta) == 0) {
                echo '<script>M.toast({html:"No se encontraron ventas.", classes: "rounded"})</script>';
        } else {
            //SI NO ESTA EN == 0 SI TIENE INFORMACION
            //La variable $contenido contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
            //RECORREMOS UNO A UNO LOS ARTICULOS CON EL WHILE
            while($venta = mysqli_fetch_array($consulta)) {
                $id_cliente = $venta['id_cliente'];
                if ($id_cliente == 0) {
                    $cliente['nombre'] = 'Venta Publico';
                }else{
                    $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM  `punto-venta_clientes` WHERE id=$id_cliente"));
                }
                $id_usuario = $venta['usuario'];
                $user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_usuario"));
                $estatus = ($venta['estatus'] == 1)? '<span class="new badge blue" data-badge-caption="Pausada"></span>': '<span class="new badge green" data-badge-caption="En Proceso"></span>';
                //Output
                $contenido .= '         
                  <tr>
                    <td>'.$venta['id'].'</td>
                    <td>'.$cliente['nombre'].'</td>                    
                    <td>'.$venta['fecha'].' '.$venta['hora'].'</td>
                    <td>'.$venta['tipo_cambio'].'</td>
                    <td>$'.sprintf('%.2f', $venta['total']).'</td>
                    <td>'.$user['firstname'].'</td>
                    <td>'.$estatus.'</td>
                    <td><a href = "add_venta.php?id='.$venta['id'].'" class="btn-small waves-effect waves-light pink"><i class="material-icons">visibility</i></a></td>
                    <td><a onclick="borrar_lista_all('.$venta['id'].')" class="btn-small red darken-1 waves-effect waves-light"><i class="material-icons">remove_shopping_cart</i></a></td>
                  </tr>';
            }//FIN while
        }//FIN else
        echo $contenido;// MOSTRAMOS LA INFORMACION HTML
        break;
        break;
        break;
    case 11:///////////////           IMPORTANTE               //////////////
        //CON POST RECIBIMOS TODAS LAS VARIABLES DEL MODAL DEVOLUCIONES
        $id_venta = $conn->real_escape_string($_POST['id_venta']);  

        //CREAMOS EL SQL PARA CREAR LA DEVOLUCION
        $sql = "INSERT INTO `punto_venta_devoluciones_articulos`(id_venta, fecha, hora, usuario) VALUES($id_venta, '$Fecha_hoy', '$Hora', $id_user)";
        //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
        if(mysqli_query($conn, $sql)){
            //SI SE CREA LA DEVOLUCION CREAMOS LOS DETALLES
            $array = $conn->real_escape_string($_POST['array']);
            $articulos = explode(", ", $array); // SEPARAMOS EL ARRAY EN ARTICULOS
            $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id) AS id FROM punto_venta_devoluciones_articulos WHERE id_venta = $id_venta AND usuario = $id_user"));  
            $id_devolucion = $ultimo['id'];
            //MEDIANTE UN CICLO RECORREMOS UNO POR UNO LOS ARTICULOS
            for ($i = 0; $i <= count($articulos)-1; $i++) {
                $articulo = explode("-", $articulos[$i]);
                $id_art = $articulo[0];
                $cantidad = $articulo[1];
                //echo "Venta N° $id_venta id: $id_art cantidad: $cantidad <br>";
                // INSERTAMOS UNO A UNO LOS ARTICULOS EN EL DETALLE DE LA DEVOLUCION
                $sql_devoluciones = "INSERT INTO `pv_detalles_devoluciones` (id_devolucion, id_articulo, cantidad) VALUES($id_devolucion, '$id_art', '$cantidad')";
                //VERIFICAMOS SI SE HACE LA INSERCION
                if (mysqli_query($conn, $sql_devoluciones)) {
                    //AHORA MODIFICAMOS LA EXISTENCIA EN EL ALMACEN
                    mysqli_query($conn, "UPDATE `punto_venta_almacen_general` SET cantidad = cantidad+$cantidad, modifico = $id_user, fecha_modifico = '$Fecha_hoy' WHERE id_articulo = '$id_art' AND id_almacen = '$almacen'");
                }
            }//FIN FOR
            ?>
            <script>
                var a = document.createElement("a");
                    a.href = "../php/ticket_devolucion.php?id="+<?php echo $id_devolucion; ?>;
                    a.target = "blank";
                    a.click();
            </script>
            <?php
            echo '<script>recargar_venta();</script>';
        }else{
            echo '<script >M.toast({html:"Ha ocurrio un error.", classes: "rounded"})</script>';            
        }// FIN else error
       
        break;
}// FIN switch
mysqli_close($conn);
    
?>