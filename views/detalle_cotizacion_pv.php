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
      $id_cotizacion = $_POST['cotizacion'];// POR EL METODO POST RECIBIMOS EL ID DE LA COTIZACIÓN
      $id = $_SESSION['user_id'];//  RECIBIMOS EL ID DEL USUARIO LOGEADO
      //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL USUARIO Y ASIGNAMOS EL ARRAY A UNA VARIABLE $datos_user
      $datos_user = mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM users WHERE user_id=$id"));
      if ($datos_user['ventas'] == 0) {
        ?>
        <script>    
          M.toast({html: "Permiso denegado.", classes: "rounded"});
          M.toast({html: "Contacta a un Administrador.", classes: "rounded"});
          setTimeout("location.href='cotizaciones_punto_venta.php'", 1000);
        </script>
        <?php
      }
      $Cotizacion = mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM punto_venta_cotizaciones WHERE id=$id_cotizacion"));
      ?>
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
    			<ul class="collection col s12 m8">
                <li class="collection-item avatar">
                  <img src="../img/lista.png" alt="" class="circle">
                  <span class="title"><b>N°: </b><?php echo $id_cotizacion; ?></span>
                  <p><b>Código de Cotizacion: </b><?php echo $Cotizacion['cotizacion']; ?><br>
                     <b>N° Cliente: </b><?php echo $Cotizacion['id_cliente']; ?><br>
                     <b>Tipo Cambio: </b><?php echo $Cotizacion['tipo_cambio']; ?><br><br>
                     <b><b>TOTAL: </b> $<?php echo sprintf('%.2f', $Cotizacion['total']); ?></b><br>
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
                <th>Imagen</th>
                <th>Artículo</th>
                <th>Descripción</th>
                <th>Código Unidad</th>
                <th>Código Fiscal</th>
                <th>Modelo</th>
                <th>Unidad</th>
                <th>Precio Venta</th>
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
                  $articulo = mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM punto_venta_articulos WHERE id=$id_articulo"));
                  ?>
                  <!-- Output -->
                  <?php $img = ($articulo['imagen'] != '')? '<td><img class="materialboxed" width="100" src="../Imagenes/Catalogo/'.$articulo['imagen'].'"></td>': '<td></td>'; ?>
                    <tr>
                      <td><?php echo $detalle['id']; ?></td>
                      <td><?php echo $articulo['codigo']; ?></td>
                      <?php echo $img ?>
                      <td><?php echo $articulo['nombre']; ?></td>
                      <td><?php echo $articulo['descripcion'] ?></td>
                      <td><?php echo $articulo['codigo_unidad'] ?></td>
                      <td><?php echo $articulo['codigo_fiscal'] ?></td>
                      <td><?php echo $articulo['modelo'] ?></td>
                      <td><?php echo $detalle['cantidad'].' '.$articulo['unidad']; ?></td>
                      <td>$<?php echo sprintf('%.2f', $detalle['precio_venta_u']); ?></td>
                      <td>$<?php echo sprintf('%.2f', $detalle['importe']); ?></td>
                    </tr>
                  <?php
                }//FIN while
              }// FIN else
              ?>
              <tr><td></td><td></td><td></td><td></td><td><b>TOTAL</b></td><td><b>$<?php echo sprintf('%.2f', $Cotizacion['total']); ?></b></td></tr>
            </tbody>            
          </table>          
        </div>
    	</div><!--DIV DEL CONTAINER-->
    </body>
  </html>
<?php 
}
?>