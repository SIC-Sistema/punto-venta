<?php
  //ARCHIVO QUE CONTIENE LA VARIABLE CON LA CONEXION A LA BASE DE DATOS
  include('../php/conexion.php');
  // POR EL METODO POST ¿RECIBIMOS EL ID DEL ARTICULO DESDE EL ARCHIVO views/add_venta.php
  $Venta = $conn->real_escape_string($_POST['id_venta']);
?>
<script>
	$(document).ready(function(){
	    $('#modalVenta').modal();
	    $('#modalVenta').modal('open'); 
	 });
</script>

<!-- MODALES DE ALMACEN -->
<!-- Modal EditarAlmacen Structure -->
<div id="modalVenta" class="modal">
    <div class="modal-content"> 
        <div class="row">
            <ul class="collection center">
                <li class="collection-item indigo lighten-5 "><b class="indigo-text">DEVOLUCION - VENTA - Folio N° <?php echo substr(str_repeat(0, 5).$Venta, - 6);?></b></li>
            </ul>
            <div class="row">
              <h4 class="hide-on-med-and-down">Detalles (Seleccione la cantidad y los articulos a devolver):</h4>
              <h6 class="hide-on-large-only">Detalles (Seleccione la cantidad y los articulos a devolver):</h6>
            </div>
            <div class="row">
              <table>
                <thead>
                  <tr>
                    <th>N°</th>
                    <th>Código</th>
                    <th>Artículo</th>
                    <th>Cantidad a Devolver</th>
                    <th>Seleccionar</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $bandera = 0;// ESTA VARIABLE NOS INDICARA CUANTOS PRODUCTOS ESTAN EL LA LISTA
                  $sql_art =  mysqli_query($conn,"SELECT * FROM `punto_venta_detalle_venta` WHERE id_venta=$Venta");
                  if (mysqli_num_rows($sql_art) <= 0) {
                    echo "<h5> NO SE ENCONTRARON ARTICULOS</h5>";
                  }else{                    
                    while($detalle = mysqli_fetch_array($sql_art)){
                      $bandera ++;//SE INCREMENTE EN UNO PORQUE SALE UN PRODUCTO EN LA LISTA
                      $id_articulo = $detalle['id_producto'];
                      $articulo = mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM punto_venta_articulos WHERE id=$id_articulo"));
                      ?>
                        <tr>
                          <td><?php echo $id_articulo; ?></td>
                          <td><?php echo $articulo['codigo']; ?></td>
                          <td><?php echo $articulo['nombre']; ?></td>
                          <td><?php echo $detalle['cantidad'];?> de <br><input id="cantidadD<?php echo $bandera; ?>" type="number" class="validate col s4 m2 l2" value="<?php echo $detalle['cantidad'];?>"><br><?php echo $articulo['unidad'] ?></td>
                          <td><p><label><input type="checkbox" id="select<?php echo $bandera; ?>" /><span></span></label></p></td>
                          <input  type="hidden"  value="<?php echo $id_articulo; ?>" id = "id<?php echo $bandera; ?>" />
                          <input  type="hidden"  value="<?php echo $detalle['cantidad']; ?>" id = "cantidadA<?php echo $bandera; ?>" />
                        </tr>
                      <?php
                    }//FIN while
                  }// FIN else
                  ?>
                </tbody>            
              </table>          
            </div>
            <ul class="collection center">
                <li class="collection-item indigo lighten-5 "></li>
            </ul>  
            <a class="modal-action modal-close waves-effect waves-light btn-large indigo lighten-5 indigo-text right"><b>Cancelar<i class="material-icons left">close</i></b></a>
            <a class="right white-text"> <br>_ _ _ _</a>
            <a onclick="realizar_devolucion(<?php echo $bandera; ?>, <?php echo $Venta; ?>)" class="waves-effect waves-green btn-large indigo lighten-5 teal-text right"><b>Devolver<i class="material-icons left">reply</i></b></a>          
        </div>
    </div>
</div>