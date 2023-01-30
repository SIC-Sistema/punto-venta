<?php
//ARCHIVO QUE CONTIENE LA VARIABLE CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
//ARCHIVO QUE CONDICIONA QUE TENGAMOS ACCESO A ESTE ARCHIVO SOLO SI HAY SESSION INICIADA Y NOS PREMITE TOMAR LA INFORMACION DE ESTA
include('is_logged.php');
//DEFINIMOS LA ZONA  HORARIA
date_default_timezone_set('America/Mexico_City');
$id_user = $_SESSION['user_id'];// ID DEL USUARIO LOGEADO
$Fecha_hoy = date('Y-m-d');// FECHA ACTUAL

//CON METODO POST TOMAMOS UN VALOR DEL 0 AL 5 PARA VER QUE ACCION HACER (factura temporal = 0, crear factura = 1, borrar venta = 2, guardar cambios = 3, facturas tmp = 4)
$Accion = $conn->real_escape_string($_POST['accion']);

//UN SWITCH EL CUAL DECIDIRA QUE ACCION REALIZA DEL CRUD (factura temporal = 0, crear factura = 1, borrar venta = 2, guardar cambios = 3, facturas tmp = 4)
switch ($Accion) {
    case 0:  ///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 0 realiza:

        //CON POST RECIBIMOS TODAS LAS VARIABLES DEL FORMULARIO QUE NESECITAMOS PARA INSERTAR
        $id_venta = $conn->real_escape_string($_POST['venta']);    
        $nueva = $conn->real_escape_string($_POST['nueva']);    
        if ($id_venta == 0) {
            $sql = "INSERT INTO `tmp_pv_factura` (usuario) VALUES ($id_user)";
        }else{
            $Venta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_ventas` WHERE id = $id_venta"));
            $cliente = $Venta['id_cliente']; $total = $Venta['total'];
            $sql = "INSERT INTO `tmp_pv_factura` (cliente, total, usuario) VALUES ('$cliente', '$total', $id_user)";
        }
        if ($nueva == 0) {            
            //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
            if(mysqli_query($conn, $sql)){
                echo '<script >M.toast({html:"Nueva Factura.", classes: "rounded"})</script>';
                $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(folio) AS id FROM tmp_pv_factura WHERE usuario = $id_user"));       
                $factura = $ultimo['id'];//TOMAMOS EL ID DEL ULTIMO CORTE
            }
        }else{
            $factura = $nueva;
        }            
        if ($id_venta != 0) {
            $info_factura = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM tmp_pv_factura WHERE folio = $factura")); 
            if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `tmp_pv_detalle_factura` WHERE id_venta = $id_venta"))>0) {
                echo '<script >M.toast({html:"Error venta no agregada.", classes: "rounded"})</script>';
                echo '<script >M.toast({html:"Esta venta ya se encuentra en otra factura.", classes: "rounded"})</script>';         
            }else if ($cliente != $info_factura['cliente'] AND $info_factura['cliente'] != 0) {
                echo '<script >M.toast({html:"Error venta no agregada.", classes: "rounded"})</script>';
                echo '<script >M.toast({html:"El cliente de la venta no coincide.", classes: "rounded"})</script>';
            }else{
                mysqli_query($conn, "INSERT INTO `tmp_pv_detalle_factura` (id_factura, id_venta) VALUES ($factura, $id_venta)");
                echo '<script >M.toast({html:"Venta agregada con exito.", classes: "rounded"})</script>';    
            }
        }
        ?>
        <script>
            setTimeout("location.href='../views/add_factura.php?id=<?php echo $factura; ?>'", 1000);
        </script>
        <?php 
        break;
    case 1:  ///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 1 realiza:

        //CON POST RECIBIMOS UN TEXTO DEL BUSCADOR VACIO O NO de "almacenes_punto_venta.php"
        $folio = $conn->real_escape_string($_POST['folio']);
        //ESTA CONSULTA SE HARA SIEMPRE QUE NO ALLA NADA EN EL BUSCADOR Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql...
		$sql = "SELECT * FROM `tmp_pv_detalle_factura` WHERE id_factura = $folio";

        // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
		$consulta = mysqli_query($conn, $sql);		
		$contenido = '';//CREAMOS UNA VARIABLE VACIA PARA IR LLENANDO CON LA INFORMACION EN FORMATO
        ?>
        <table class="bordered centered highlight">
            <thead>
                <tr>
                  <th>CLAVE PRODUCTO/SERVICIO</th>
                  <th>CANTIDAD</th>
                  <th>CLAVE UNIDAD</th>
                  <th>UNIDAD</th>            
                  <th>DESCRIPCION</th>
                  <th>VALOR UNITARIO</th>
                  <th>IMPORTE</th>
                  <th>ACCION</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $Total = 0;
                //VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
                if (mysqli_num_rows($consulta) == 0) {
                        echo '<script>M.toast({html:"No se encontraron ventas.", classes: "rounded"})</script>';
                } else {
                    //SI NO ESTA EN == 0 SI TIENE INFORMACION
                    //RECORREMOS UNO A UNO LOS ALMACENES CON EL WHILE
                    while($venta = mysqli_fetch_array($consulta)) {
                        $id_venta = $venta['id_venta'];
                        $detalle_venta = mysqli_query($conn, "SELECT * FROM `punto_venta_detalle_venta` WHERE id_venta=$id_venta");
                        ?>
                        <tr>
                            <td colspan = "7"><b>Venta N°<?php echo $id_venta; ?></b></td>
                            <td><a onclick="borrar_venta(<?php echo $id_venta; ?>)" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                        </tr>
                        <?php
                        if (mysqli_num_rows($detalle_venta)) {
                            while ($articulo = mysqli_fetch_array($detalle_venta)){
                                $id_articulo = $articulo['id_producto'];
                                $art = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM `punto_venta_articulos` WHERE id = $id_articulo"));
                                //Output
                                ?>        
                                  <tr>
                                    <td><?php echo $art['codigo']; ?></td>
                                    <td><?php echo $articulo['cantidad']; ?></td>
                                    <td><?php echo $art['codigo_unidad']; ?></td>
                                    <td><?php echo $art['unidad']; ?></td>
                                    <td><?php echo $art['nombre']; ?></td>
                                    <td>$ <?php echo sprintf('%.2f', $articulo['precio_venta']-($articulo['precio_venta']*0.16)); ?></td>
                                    <td>$ <?php echo sprintf('%.2f', $articulo['importe']-($articulo['importe']*0.16)); ?></td>                      
                                  </tr>
                                <?php
                                $Total += $articulo['importe'];
                            }
                        }//FIN IF DETALLE VENTA
                    }//FIN while
                }//FIN else
            ?>
            </tbody>
        </table>
        <h5 class="blue-text"><b>  Totales</b></h5>
        <hr>
        <div class="row col s12">
            <div class="col s1 m6"><br></div>
            <div class="col s4 m3">
                <b>*Total<br>
                *SubTotal<br>
                Impuestos Traslados<br></b>
            </div>           
            <div class="col s6 m3">
                <input type="hidden" value="<?php echo $Total; ?>" id="total" />
                <input type="" value="$ <?php echo sprintf('%.4f', $Total); ?>"  /><br>
                <input type="" value="$ <?php echo sprintf('%.4f', $Total-($Total*0.16)); ?>" id="subtotal" /><br>
                <input type="" value="$ <?php echo sprintf('%.4f', $Total*0.16); ?>" id="impuestos" /><br>
            </div>           
        </div>
	    <?php
        break;
    case 2:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 2 realiza:
        //CON POST RECIBIMOS EL ID DEL PROVEEDOR DEL FORMULARIO POR EL SCRIPT "add_factura.php" QUE NESECITAMOS PARA BORRAR
        $id = $conn->real_escape_string($_POST['id']);    
        $folio = $conn->real_escape_string($_POST['folio']);
        
        //VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
        if ($id != 0) {
            if(mysqli_query($conn, "DELETE FROM `tmp_pv_detalle_factura` WHERE `tmp_pv_detalle_factura`.`id_venta` = $id")){
                #SI ES ELIMINADO MANDAR MSJ CON ALERTA
                echo '<script >M.toast({html:"Venta borrada con exito.", classes: "rounded"})</script>';
                ?>
                <script>
                    setTimeout("location.href='../views/add_factura.php?id=<?php echo $folio; ?>'", 700);
                </script>
                <?php 
            }
        }	
        break;
    case 3:
        // $Accion es igual a 3 realiza:                  
    
        //RECIBIMOS TODAS LAS VARIABLES DES DE EL ARCHIVO add_factura.php
        $folio = $conn->real_escape_string($_POST['folio']);
        $regimen = $conn->real_escape_string($_POST['regimen']);
        $cdfi = $conn->real_escape_string($_POST['cdfi']);
        $metodo_pago = $conn->real_escape_string($_POST['metodo_pago']);
        $forma_pago = $conn->real_escape_string($_POST['forma_pago']);
        $total = $conn->real_escape_string($_POST['total']);
        $id_cliente = $conn->real_escape_string($_POST['id_cliente']);

        //CREAMOS LA SENTENCIA SQL PARA HACER LA ACTUALIZACION DE LA INFORMACION DEL ALMACEN Y LA GUARDAMOS EN UNA VARIABLE
        $sql_update = "UPDATE `tmp_pv_factura` SET metodo_pago = '$metodo_pago', forma_pago = '$forma_pago', total = '$total', cliente = '$id_cliente', regimen_fiscal = '$regimen', uso_cdfi = '$cdfi' WHERE folio = $folio";        
        
        if(mysqli_query($conn, $sql_update)){
                echo '<script >M.toast({html:"Los datos se actualizarón con exito.", classes: "rounded"})</script>';
                ?>
                <script>
                    // REDIRECCIONAMOS 
                    setTimeout("location.href='../views/add_factura.php?id=<?php echo $folio; ?>'", 700);
                </script>
                <?php
        }else{
            echo '<script >M.toast({html:"Ha ocurrido un error UPDATE...", classes: "rounded"})</script>';
        }     
        break;
    case 4:
        // $Accion es igual a 4 realiza:

        //CON POST RECIBIMOS UN TEXTO DEL BUSCADOR VACIO O NO de "facturas_punto_venta.php"
        $Texto = $conn->real_escape_string($_POST['texto']);
        //VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
        if ($Texto != "") {
            //MOSTRARA LOS ARTICULOS QUE SE ESTAN BUSCANDO Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql....
            $sql = "SELECT * FROM `tmp_pv_factura` WHERE folio LIKE '$Texto%' OR cliente = '$Texto')";   
        }else{//ESTA CONSULTA SE HARA SIEMPRE QUE NO ALLA NADA EN EL BUSCADOR Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql...
            $sql = "SELECT * FROM `tmp_pv_factura`";
        }//FIN else $Texto VACIO O NO

        // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
        $consulta = mysqli_query($conn, $sql);      
        $contenido = '';//CREAMOS UNA VARIABLE VACIA PARA IR LLENANDO CON LA INFORMACION EN FORMATO

        //VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
        if (mysqli_num_rows($consulta) == 0) {
                echo '<script>M.toast({html:"No se encontraron facturas pendientes", classes: "rounded"})</script>';
        } else {
            //SI NO ESTA EN == 0 SI TIENE INFORMACION
            //La variable $contenido contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
            //RECORREMOS UNO A UNO LOS ARTICULOS CON EL WHILE
            while($factura = mysqli_fetch_array($consulta)) {
                $id_cliente = $factura['cliente'];
                $sql = mysqli_query($conn, "SELECT * FROM `punto-venta_clientes` WHERE id=$id_cliente");
                if (mysqli_num_rows($sql)==0) {
                    $cliente['nombre'] = '';
                }else{
                    $cliente = mysqli_fetch_array($sql);
                }
                //Output
                $contenido .= '         
                  <tr>
                    <td>'.$factura['folio'].'</td>
                    <td>'.$cliente['nombre'].'</td>
                    <td>'.$factura['uso_cdfi'].'</td>
                    <td>'.$factura['regimen_fiscal'].'</td>
                    <td>'.$factura['metodo_pago'].'</td>
                    <td>'.$factura['forma_pago'].'</td>
                    <td>$'.sprintf('%.2f', $factura['total']).'</td>
                    <td>'.$factura['usuario'].'</td>
                    <td><a href = "add_factura.php?id='.$factura['folio'].'" class="btn-small waves-effect waves-light pink"><i class="material-icons">visibility</i></a></td>
                    <td><a onclick="cancelar_factura('.$factura['folio'].')" class="btn-small red darken-1 waves-effect waves-light"><i class="material-icons">cancel</i></a></td>
                  </tr>';
            }//FIN while
        }//FIN else
        echo $contenido;// MOSTRAMOS LA INFORMACION HTML
        // code...
        break;
    case 5:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 5 realiza:

        //RECIBIMOS TODAS LAS VARIABLES DES DE EL ARCHIVO modal_almacen.php
        $id_articulo = $conn->real_escape_string($_POST['id_articulo']);
        $almacen = $conn->real_escape_string($_POST['almacen']);
        $DesCambio = $conn->real_escape_string($_POST['descripcion_cambio']);
        $Cantidad = $conn->real_escape_string($_POST['cantidadCambiar']);

        //CREAMOS LA SENTENCIA SQL PARA HACER LA ACTUALIZACION DE LA INFORMACION DEL ALMACEN Y LA GUARDAMOS EN UNA VARIABLE
        $sql_update = "UPDATE `punto_venta_almacen_general` SET cantidad = '$Cantidad' WHERE id_articulo = $id_articulo AND id_almacen = $almacen";        
        //VERIFICAMOS QUE LAS SENTECIAS SON EJECUTADAS CON EXITO!
        if(mysqli_query($conn, $sql_update)){
            $sql_insert = "INSERT INTO `punto_venta_modificaciones_mi_almacen` (descripcion_cambio, producto, almacen, usuario, fecha) VALUES('$DesCambio',$id_articulo,$almacen,$id_user,'$Fecha_hoy')";
            if(mysqli_query($conn, $sql_insert)){
                echo 'Los datos se actualizarón con exito.';    
                ?>
                <script>
                    // REDIRECCIONAMOS 
                    setTimeout("location.href='../views/facturas_punto_venta.php'", 700);
                </script>
                <?php
            }else{
                echo 'Ha ocurrido un error INSERT...';
            }     
        }else{
            echo 'Ha ocurrio un error UPDATE...';   
        }//FIN else DE ERROR
        break;
    case 6:///////////////           IMPORTANTE               ///////////////
        // $Accion es igual a 6 realiza:

        //RECIBIMOS TODAS LAS VARIABLES DES DE EL ARCHIVO facturas_punto_venta.php
        $folio = $conn->real_escape_string($_POST['folio']);

        //CREAMOS LA SENTENCIA SQL PARA HACER LA ACTUALIZACION DE LA INFORMACION DEL ALMACEN Y LA GUARDAMOS EN UNA VARIABLE
        $sql_delete = "DELETE FROM `tmp_pv_factura` WHERE folio = $folio";        
        //VERIFICAMOS QUE LAS SENTECIAS SON EJECUTADAS CON EXITO!
        if(mysqli_query($conn, $sql_delete)){
            $sql_delete2 = "DELETE FROM `tmp_pv_detalle_factura` WHERE id_factura = $folio";  
            if(mysqli_query($conn, $sql_delete2)){
                echo '<script>M.toast({html:"Factura N° '.$folio.' Cancelada exitosamente.", classes: "rounded"})</script>';
                ?>
                <script>
                    // REDIRECCIONAMOS 
                    setTimeout("location.href='../views/facturas_punto_venta.php'", 700);
                </script>
                <?php
            }else{
               echo '<script>M.toast({html:"Ocurrio un error al borrar los detalles.", classes: "rounded"})</script>';
            }     
        }else{
           echo '<script>M.toast({html:"Ocurrio un error al borrar la factura.", classes: "rounded"})</script>';
        }//FIN else DE ERROR
        break;
    case 7:///////////////           IMPORTANTE               ///////////////
        //CON POST RECIBIMOS UN TEXTO DEL BUSCADOR VACIO O NO de "reservacion.php"
        $rfc = $conn->real_escape_string($_POST['rfc']);
        $razon = $conn->real_escape_string($_POST['razon']);
        //VERIFICAMOS SI CONTIENE ALGO DE TEXTO LA VARIABLE
        if ($rfc != "") {
            //MOSTRARA LOS ARTICULOS QUE SE ESTAN BUSCANDO Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql......
            $sql = mysqli_query($conn,"SELECT * FROM `punto-venta_clientes` WHERE  rfc LIKE '%$rfc%' LIMIT 1");
        if ($razon != "") {
            $sql = mysqli_query($conn,"SELECT * FROM `punto-venta_clientes` WHERE  nombre LIKE '%$razon%' LIMIT 1");
        }
        if (mysqli_num_rows($sql) == 0) {
                echo '<br><br>';
        } else {
                $clienteIgual = mysqli_fetch_array($sql);
                echo '<b>'.$clienteIgual['nombre'].'  '.$clienteIgual['rfc'].'</b>';
                ?>
                <a onclick="mostrarCliente(<?php echo $clienteIgual['id'] ?>)" class="btn-small green waves-effect waves-light">Elegir</a></th>
                <?php
            }   
        }else{//ESTA CONSULTA SE HARA SIEMPRE QUE NO ALLA NADA EN EL BUSCADOR Y GUARDAMOS LA CONSULTA SQL EN UNA VARIABLE $sql...
            echo '<br><br>';
        }//FIN else $Text
        break;
    case 8:
        //CON POST RECIBIMOS EL ID DEL CLIENTE DEL FORMULARIO POR EL SCRIPT "reservacion.php" QUE NESECITAMOS PARA BUSCAR
        $id_cliente = $conn->real_escape_string($_POST['id_cliente']);  
        if ($id_cliente != 0) {
            //HACEMOS LA CONSULTA DEL CLIENTE Y MOSTRAMOS LA INFOR EN FORMATO HTML
            $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto-venta_clientes` WHERE id=$id_cliente"));
        }
        ?>
        <div class="row col s12" id="infoCliente">
            <div class="col s5 m4">
              <b class="right">*RFC: </b><br>
              <b class="right">Razón Social: </b><br><br>
              <b class="right">*Uso de CFDI: </b><br>
              <b class="right">Correo Electronico:</b><br>
            </div>
            <div class="col s7 m8">
              <?php
              $rfc = $cliente['rfc']; $razon_social = $cliente['nombre']; $correo = $cliente['email'];
              ?>
              <input class="col s12 m5" type="" id = "rfc" value="<?php echo $rfc; ?>" onkeyup="buscarClientes()"/><div class="col s12 m7" id="buscarClientes" align="right"><br></div>
              <input type="hidden" id="id_cliente" value="<?php echo $id_cliente; ?>" />
              <input class="col s12 m7" type="" id = "razon_social" value="<?php echo $razon_social; ?>"  onkeyup="buscarClientes()"/><br>
              <select class="browser-default col s12 m9" id="cdfi">
                <option value="" selected>Seleccone un uso CDFI</option>
                <option value="G01">G01-Adquisición de mercancia</option>
                <option value="G02">G02-Devoluciones, descuentos o bonificaciones</option>
                <option value="G03">G03-Gastos en general</option>
                <option value="I01">I01-Construcciones</option>
                <option value="I02">I02-Moviliario y equipo de oficina por inverciones</option>
                <option value="I03">I03-Equipo de transporte</option>
                <option value="I04">I04-Equipo de computo y accesorios</option>
                <option value="I05">I05-Dados, troqueles, moldes, matrices y herramientas</option>
                <option value="I06">I06-Comunicaciones telefonicas</option>
                <option value="I07">I07-Comunicaciones satelitales</option>
                <option value="I08">I08-Otra maquinaria y equipo</option>
                <option value="D01">D01-Honorario médicos, dentales y gastos hospitalarios</option>
                <option value="D02">D02-Gastos médicos por incapacidad o discapacidad</option>
                <option value="D03">D03-Gastos funerales</option>
                <option value="D04">D04-Donativos</option>
                <option value="D05">D05-Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)</option>
                <option value="D06">D06-Aportaciones voluntarias al SAR</option>
                <option value="D07">D07-Primas por seguros de gastos medicos</option>
                <option value="D08">D08-Gastos de transportación escolar obligatoria</option>
                <option value="D09">D09-Depositos de cuentas para el ahorro, primas que tengan como base plabes de pensiones</option>
                <option value="D10">D10-Pagos por servicios educativos (colegiaturas)</option>
                <option value="P01">P01-Por definir</option>
              </select>
              <input class="col s12 m8" type="" id="correo" value="<?php echo $correo; ?>"><br>
            </div>
           </div>
        <?php
        // code...
        break;


}// FIN switch

mysqli_close($conn);
    
?>