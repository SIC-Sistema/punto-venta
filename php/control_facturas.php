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
//echo "hola aqui estoy";
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

		//VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
		if (mysqli_num_rows($consulta) == 0) {
				echo '<script>M.toast({html:"No se encontraron ventas.", classes: "rounded"})</script>';
        } else {
            //SI NO ESTA EN == 0 SI TIENE INFORMACION
            //La variable $contenido contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle//Output
            
            //RECORREMOS UNO A UNO LOS ALMACENES CON EL WHILE
            while($venta = mysqli_fetch_array($consulta)) {
                $id_venta = $venta['id_venta'];
				$detalle_venta = mysqli_query($conn, "SELECT * FROM `punto_venta_detalle_venta` WHERE id_venta=$id_venta");
                $contenido .= '
                    <tr>
                        <td colspan = "7"><b>Venta N°'.$id_venta.'</b></td>
                        <td><a onclick="borrar_venta('.$id_venta.')" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                    </tr>';
                if (mysqli_num_rows($detalle_venta)) {
                    while ($articulo = mysqli_fetch_array($detalle_venta)){
                        $id_articulo = $articulo['id_producto'];
                        $art = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM `punto_venta_articulos` WHERE id = $id_articulo"));
                        //Output
                        $contenido .= '         
                          <tr>
                            <td>'.$art['codigo'].'</td>
                            <td>'.$articulo['cantidad'].'</td>
                            <td>'.$art['codigo_unidad'].'</td>
                            <td>'.$art['unidad'].'</td>
                            <td>'.$art['nombre'].'</td>
                            <td>$'.sprintf('%.2f', $articulo['precio_venta']).'</td>
                            <td>$'.sprintf('%.2f', $articulo['importe']).'</td>                            
                          </tr>';
                    }
                }//FIN IF DETALLE VENTA
			}//FIN while
        }//FIN else
        echo $contenido;// MOSTRAMOS LA INFORMACION HTML
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

        //CREAMOS LA SENTENCIA SQL PARA HACER LA ACTUALIZACION DE LA INFORMACION DEL ALMACEN Y LA GUARDAMOS EN UNA VARIABLE
        $sql_update = "UPDATE `tmp_pv_factura` SET metodo_pago = '$metodo_pago', forma_pago = '$forma_pago', regimen_fiscal = '$regimen', uso_cdfi = '$cdfi' WHERE folio = $folio";        
        
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

}// FIN switch

mysqli_close($conn);
    
?>