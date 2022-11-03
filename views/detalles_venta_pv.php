<?php
//VERIFICAMOS QUE SI NOS ENVIE POR POST EL ID DEL USUARIO
if (isset($_POST['venta']) == false) {
  ?>
  <script>    
    M.toast({html: "Regresando a ventas.", classes: "rounded"})
    setTimeout("location.href='ventas_punto_venta.php.php'", 800);
  </script>
  <?php
}else{
?>
  <!DOCTYPE html>
  <html>
    <head>
      <title>SIC | Detalles Venta</title>
      <?php
      //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
      include('fredyNav.php');
      $id_venta = $_POST['venta'];// POR EL METODO POST RECIBIMOS EL ID DE LA COMPRA
      
      $Venta = mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM punto_venta_ventas WHERE id=$id_venta"));

      $id_cliente = $Venta['id_cliente'];//  RECIBIMOS EL ID DEL USUARIO LOGEADO
      if ($id_cliente == 0) {
        $datos_cliente['nombre'] = 'Venta Publico';
      }else{
        //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL USUARIO Y ASIGNAMOS EL ARRAY A UNA VARIABLE $datos_cliente
        $datos_cliente = mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM `punto-venta_clientes` WHERE id=$id_cliente"));
      }
      ?>
    </head>
    <body>
      <!-- DENTRO DE ESTE DIV VA TODO EL CONTENIDO Y HACE QUE SE VEA AL CENTRO DE LA PANTALLA.-->
      <div class="container">
        <!--    //////    TITULO    ///////   -->
        <div class="row">
          <h2 class="hide-on-med-and-down">Venta:</h2>
          <h4 class="hide-on-large-only">Venta:</h4>
        </div>
        <!--   //// INFORMACION DEL USUARIO  //// --->
        <div class="row">
            <ul class="collection col s12 m9">
                <li class="collection-item avatar">
                  <img src="../img/lista.png" alt="" class="circle">
                  <span class="title"><b>DETALLES DE VENTA</b></span><br><br>
                  <p class="row col s12"><b>
                    <div class="col s12 m6">
                      <div class="col s12"><b class="indigo-text">N° VENTA: </b><?php echo $id_venta;?></div>
                      <div class="col s12"><b class="indigo-text">N° CLIENTE: </b><?php echo $Venta['id_cliente'];?></div>
                      <div class="col s12"><b class="indigo-text">CLIENTE: </b><?php echo $datos_cliente['nombre'];?></div>           
                    </div>
                    <div class="col s12 m6">
                      <div class="col s12"><b class="indigo-text">FECHA Y HORA: </b><?php echo $Venta['fecha'].' '.$Venta['hora'];?></div>         
                      <div class="col s12"><b class="indigo-text">TIPO CAMBIO: </b><?php echo $Venta['tipo_cambio'];?></div>         
                      <div class="col s12"><b class="indigo-text">TOTAL: </b><?php echo '$'.sprintf('%.2f', $Venta['total']);?></div>         
                    </div>
                  </b></p><br><br><br><br>
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
              $sql_art =  mysqli_query($conn,"SELECT * FROM `punto_venta_detalle_venta` WHERE id_venta=$id_venta");
              if (mysqli_num_rows($sql_art) <= 0) {
                echo "<h5> NO SE ENCONTRARON ARTICULOS</h5>";
              }else{
                while($detalle = mysqli_fetch_array($sql_art)){
                  $id_articulo = $detalle['id_producto'];
                  $articulo = mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM punto_venta_articulos WHERE id=$id_articulo"));
                  ?>
                    <tr>
                      <td><?php echo $detalle['id']; ?></td>
                      <td><?php echo $articulo['codigo']; ?></td>
                      <td><?php echo $articulo['nombre']; ?></td>
                      <td><?php echo $detalle['cantidad'].' '.$articulo['unidad']; ?></td>
                      <td>$<?php echo sprintf('%.2f', $detalle['precio_venta']); ?></td>
                      <td>$<?php echo sprintf('%.2f', $detalle['importe']); ?></td>
                    </tr>
                  <?php
                }//FIN while
              }// FIN else
              ?>
              <tr><td></td><td></td><td></td><td></td><td><b>TOTAL</b></td><td><b>$<?php echo sprintf('%.2f', $Venta['total']); ?></b></td></tr>
            </tbody>            
          </table>          
        </div>
      </div><!--DIV DEL CONTAINER-->
    </body>
  </html>
<?php 
}
?>