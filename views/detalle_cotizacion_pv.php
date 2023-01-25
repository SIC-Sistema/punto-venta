<?php
//VERIFICAMOS QUE SI NOS ENVIE POR POST EL ID DEL USUARIO
if (isset($_POST['cotizacion']) == false) {
  ?>
  <script>    
    M.toast({html: "Regresando a Cotizaciones.", classes: "rounded"})
    setTimeout("location.href='cotizaciones_punto_venta.php'", 800);
  </script>
  <?php
}else{
?>
  <!DOCTYPE html>
  <html>
    <head>
    	<title>SIC | Detalles Cotización</title>
      <?php
      //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
      include('fredyNav.php');
      $id_cotizacion = $_POST['cotizacion'];// POR EL METODO POST RECIBIMOS EL ID DE LA COTIZACIÓN DEL ARCHIVO cotizacion_nueva_punto_venta.php
      $id = $_SESSION['user_id'];//  RECIBIMOS EL ID DEL USUARIO LOGEADO
      //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL USUARIO Y ASIGNAMOS EL ARRAY A UNA VARIABLE $datos_user
      $datos_user = mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM users WHERE user_id=$id"));
      if ($datos_user['ventas'] == 0) {
        ?>
        <script>    
          M.toast({html: "Permiso denegado.", classes: "rounded"});
          M.toast({html: "Contacta a un Administrador.", classes: "rounded"});
          M.toast({html: "No tiene el permiso de Ventas.", classes: "rounded"});
          setTimeout("location.href='cotizaciones_punto_venta.php'", 1000);
        </script>
        <?php
      }
      $Cotizacion = mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM punto_venta_cotizaciones WHERE id=$id_cotizacion"));
      ?>
      <!-- FUNCION PARA ACTIVAR EL MODAL PARA COBRAR LOS ARTICULOS DE LA COTIZACIÓN -->
      <script>
        function modal_venta() {
          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "modal_venta.php" PARA MOSTRAR EL MODAL
          $.post("modal_venta_cotizacion.php", {
            //Cada valor se separa por una ,
            $id_cotizacion: <?php echo $id_cotizacion; ?>,
          }, function(mensaje){
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "modal_venta.php"
            $("#modal").html(mensaje);
          });//FIN post
        }
      </script>
    </head>
    
    <body>
      <!-- DENTRO DE ESTE DIV VA TODO EL CONTENIDO Y HACE QUE SE VEA AL CENTRO DE LA PANTALLA.-->
    	<div class="container">
        <!--    //////    TITULO    ///////   -->
    		<div class="row">
    			<h2 class="hide-on-med-and-down">Cotización:</h2>
     			<h4 class="hide-on-large-only">Cotización:</h4>
    		</div>
        <!--   //// INFORMACION DEL USUARIO  //// --->
    		<div class="row">
    			<ul class="collection col s12">
            <li class="collection-item avatar">
              <img src="../img/lista.png" alt="" class="circle">
              <span class="title"><b>N° COTIZACION: </b><?php echo $id_cotizacion; ?></span><br><br>
              <p>
                <div class="col s12 m6">
                  <b>N° Cliente: </b><?php echo $Cotizacion['id_cliente']; ?><br>
                  <b>Cliente: </b><?php echo $Cotizacion['id_cliente']; ?><br>
                </div>
                <div class="col s12 m6">
                  <b><b>TOTAL: </b> $<?php echo sprintf('%.2f', $Cotizacion['total']); ?></b><br>
                  <a href="../php/imprimir_cotizacion.php?id=<?php echo $id_cotizacion; ?>" target = 'blank' class="waves-effect waves-light btn-small pink right"><i class="material-icons right">print</i>IMPRIMIR</a>
                  <a onclick="modal_venta()" class="waves-effect waves-light btn-small green accent-4 right">COBRAR<i class="material-icons right">monetization_on</i></a>
                  <form method="post" action="../views/editar_cotizacion_pv.php"><input id="cotizacion" name="cotizacion" type="hidden" value="<?php echo $id_cotizacion; ?>"><button class="btn-small waves-effect waves-light indigo"><i class="material-icons right">edit</i>Editar</button></form>
                </div><br>
              </p><br><br>
            </li>
          </ul>		
    		</div>
        <div class="row">
          <h3 class="hide-on-med-and-down">Detalles:</h3>
          <h5 class="hide-on-large-only">Detalles:</h5>
        </div>
        <!-- CREAMOS UN DIV EL CUAL TENGA id = "modal"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
        <div id="modal"></div>
        <div class="row">
          <table>
            <thead>
              <tr>
                <th>Código</th>
                <th>Imagen</th>
                <th>Artículo</th>
                <th>Descripción</th>
                <th>Modelo</th>
                <th>Precio Venta</th>
                <th>Cantidad</th>
                <th>Importe</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql_art =  mysqli_query($conn,"SELECT * FROM `punto_venta_detalle_cotizacion` WHERE id_venta=$id_cotizacion");
              if (mysqli_num_rows($sql_art) <= 0) {
                echo "<h5> NO SE ENCONTRARON ARTICULOS</h5>";
              }else{
                while($detalle = mysqli_fetch_array($sql_art)){
                  $id_articulo = $detalle['id_articulo'];
                  $id_detalle_cotizacion=$detalle['id'];
                  $articulo = mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM punto_venta_articulos WHERE id=$id_articulo"));
                  ?>
                  <!-- Output -->
                  <?php $img = ($articulo['imagen'] != '')? '<td><img class="materialboxed" width="100" src="../Imagenes/Catalogo/'.$articulo['imagen'].'"></td>': '<td></td>'; ?>
                    <tr>
                      <td><?php echo $articulo['codigo']; ?></td>
                      <?php echo $img ?>
                      <td><?php echo $articulo['nombre']; ?></td>
                      <td><?php echo $articulo['descripcion'] ?></td>
                      <td><?php echo $articulo['modelo'] ?></td>
                      <td>$<?php echo sprintf('%.2f', $detalle['importe']); ?></td>
                      <td><?php echo $detalle['cantidad'].' '.$articulo['unidad']; ?></td>
                      <td>$<?php echo sprintf('%.2f', $detalle['importe']); ?></td>                      
                    </tr>
                  <?php
                }//FIN while <td><a onclick="editarCotizacion(<?php echo $detalle['id']);" class="btn btn-floating indigo darken-1 waves-effect waves-light"><i class="material-icons">edit</i></a></td>
              }// FIN else
              ?>
              <tr><td colspan="5"><td colspan="2"><b>TOTAL</b></td><td><b>$<?php echo sprintf('%.2f', $Cotizacion['total']); ?></b></td></tr>
            </tbody>            
          </table>          
        </div>
    	</div><!--DIV DEL CONTAINER-->
    </body>
  </html>
<?php 
}
?>