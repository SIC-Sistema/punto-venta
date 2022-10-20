<?php
//VERIFICAMOS QUE SI NOS ENVIE POR POST EL ID DEL USUARIO
if (isset($_POST['compra']) == false) {
  ?>
  <script>    
    M.toast({html: "Regresando a compras.", classes: "rounded"})
    setTimeout("location.href='compras_punto_venta.php'", 800);
  </script>
  <?php
}else{
?>
  <!DOCTYPE html>
  <html>
    <head>
    	<title>SIC | Detalles Compra</title>
      <?php
      //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
      include('fredyNav.php');
      $id_compra = $_POST['compra'];// POR EL METODO POST RECIBIMOS EL ID DE LA COMPRA
      $id = $_SESSION['user_id'];//  RECIBIMOS EL ID DEL USUARIO LOGEADO
      //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL USUARIO Y ASIGNAMOS EL ARRAY A UNA VARIABLE $datos_user
      $datos_user = mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM users WHERE user_id=$id"));
      if ($datos_user['compras'] == 0) {
        ?>
        <script>    
          M.toast({html: "Permiso denegado.", classes: "rounded"});
          M.toast({html: "Contacta a un Administrador.", classes: "rounded"});
          setTimeout("location.href='compras_punto_venta.php'", 1000);
        </script>
        <?php
      }
      $Compra = mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM punto_venta_compras WHERE id=$id_compra"));
      ?>
    </head>
    <body>
      <!-- DENTRO DE ESTE DIV VA TODO EL CONTENIDO Y HACE QUE SE VEA AL CENTRO DE LA PANTALLA.-->
    	<div class="container">
        <!--    //////    TITULO    ///////   -->
    		<div class="row">
    			<h2 class="hide-on-med-and-down">Compra:</h2>
     			<h4 class="hide-on-large-only">Compra:</h4>
    		</div>
        <!--   //// INFORMACION DEL USUARIO  //// --->
    		<div class="row">
    			<ul class="collection col s12 m8">
                <li class="collection-item avatar">
                  <img src="../img/lista.png" alt="" class="circle">
                  <span class="title"><b>N°: </b><?php echo $id_compra; ?></span>
                  <p><b>N° Factura: </b><?php echo $Compra['factura']; ?><br>
                     <b>N° Proveedor: </b><?php echo $Compra['id_proveedor']; ?><br>
                     <b>Tipo Cambio: </b><?php echo $Compra['tipo_cambio']; ?><br><br>
                     <b><b>TOTAL: </b> $<?php echo sprintf('%.2f', $Compra['total']); ?></b><br>
                  </p>
                </li>
            </ul>		
    		</div>
        <div class="row">
          <h3 class="hide-on-med-and-down">Detalles:</h3>
          <h5 class="hide-on-large-only">Detalles:</h5>
        </div>
        <div class="row">
          <table>
            <thead>
              <tr>
                <th>N°</th>
                <th>Código</th>
                <th>Artículo</th>
                <th>Cantidad</th>
                <th>Precio Compra</th>
                <th>Importe</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql_art =  mysqli_query($conn,"SELECT * FROM punto_venta_detalle_compra WHERE id_compra=$id_compra");
              if (mysqli_num_rows($sql_art) <= 0) {
                echo "<h5> NO SE ENCONTRARON ARTICULOS</h5>";
              }else{
                while($detalle = mysqli_fetch_array($sql_art)){
                  $id_articulo = $detalle['id_articulo'];
                  $articulo = mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM punto_venta_articulos WHERE id=$id_articulo"));
                  ?>
                    <tr>
                      <td><?php echo $detalle['id']; ?></td>
                      <td><?php echo $articulo['codigo']; ?></td>
                      <td><?php echo $articulo['nombre']; ?></td>
                      <td><?php echo $detalle['cantidad'].' '.$articulo['unidad']; ?></td>
                      <td>$<?php echo sprintf('%.2f', $detalle['precio_compra_u']); ?></td>
                      <td>$<?php echo sprintf('%.2f', $detalle['importe']); ?></td>
                    </tr>
                  <?php
                }//FIN while
              }// FIN else
              ?>
              <tr><td></td><td></td><td></td><td></td><td><b>TOTAL</b></td><td><b>$<?php echo sprintf('%.2f', $Compra['total']); ?></b></td></tr>
            </tbody>            
          </table>          
        </div>
    	</div><!--DIV DEL CONTAINER-->
    </body>
  </html>
<?php 
}
?>