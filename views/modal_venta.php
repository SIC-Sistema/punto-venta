<?php
  //ARCHIVO QUE CONTIENE LA VARIABLE CON LA CONEXION A LA BASE DE DATOS
  include('../php/conexion.php');
  // POR EL METODO POST ¿RECIBIMOS EL ID DEL ARTICULO DESDE EL ARCHIVO views/almacen_punto_venta.php
  $id = $conn->real_escape_string($_POST['id']);
  $Venta = $conn->real_escape_string($_POST['id_venta']);
  // REALIZAMOS LA CONSULTA A LA BASE DE DATOS Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
  $consulta = mysqli_fetch_array (mysqli_query($conn, "SELECT count(id_articulo) AS num, sum(importe) AS suma FROM `tmp_pv_detalle_venta` WHERE id_venta = $Venta"));
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
                <li class="collection-item indigo lighten-5 "><b class="indigo-text">VENTA - Folio N° <?php echo substr(str_repeat(0, 5).$Venta, - 6); ?></b></li>
            </ul>
            <div class="col s12 m5">
                <hr>
                <h6 class="center"><b>Número de Artículos <?php echo $consulta['num'];?> </b></h6><br>
                <h6 class="indigo-text"><b>Formas de pago:</b></h6>
                <b class="col s5">Efectivo</b><div class="col s7"><input type="number" id="efectivo" value="0.00"></div>
                <b class="col s5">A Credito</b><div class="col s7"><input type="number" id="credito" value="0.00"></div>
                <b class="col s5">A Banco</b><div class="col s7"><input type="number" id="banco" value="0.00"></div>
                <br><br><br><br><br><br><br><hr>
            </div>
            <div class="col s12 m7">
                <font face="courier new">
                <hr>
                <h6 class="indigo-text center"><b>Total a pagar:</b></h6>
                <hr>
                <h2 class="indigo-text center" ><b>$<?php echo sprintf('%.2f', $consulta['suma']);?> </b></h2>
                <hr>
                <hr>
                <h6 class="green-text center"><b>Cambio:</b></h6>
                <hr>
                <h2 class="green-text center" ><b>$<?php echo sprintf('%.2f', $consulta['suma']);?> </b></h2>
                </font>
                <hr>
            </div>
            <ul class="collection center">
                <li class="collection-item indigo lighten-5 "></li>
            </ul>            
        </div>
    </div>
</div>