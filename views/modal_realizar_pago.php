<?php
  //ARCHIVO QUE CONTIENE LA VARIABLE CON LA CONEXION A LA BASE DE DATOS
  include('../php/conexion.php');
  // POR EL METODO POST ¿RECIBIMOS EL ID DEL ARTICULO DESDE EL ARCHIVO views/add_venta.php
  $Venta = $conn->real_escape_string($_POST['id_venta']);
  // REALIZAMOS LA CONSULTA A LA BASE DE DATOS Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
  $consulta = mysqli_fetch_array (mysqli_query($conn, "SELECT count(id_producto) AS num, sum(importe) AS suma FROM `punto_venta_detalle_venta` WHERE id_venta = $Venta"));
?>
<script>
	$(document).ready(function(){
	    $('#modalRpago').modal();
	    $('#modalRpago').modal('open'); 
	 });
</script>

<!-- MODALES DE ALMACEN -->
<!-- Modal EditarAlmacen Structure -->
<div id="modalRpago" class="modal">
    <div class="modal-content"> 
        <div class="row">
            <ul class="collection center">
                <li class="collection-item indigo lighten-5 "><b class="indigo-text">VENTA - Folio N° <?php echo substr(str_repeat(0, 5).$Venta, - 6); ?></b></li>
            </ul>
            <div class="col s12 m5">
                <hr>
                <h6 class="center"><b>Número de Artículos <?php echo $consulta['num'];?> </b></h6><br>
                <h6 class="indigo-text"><b>Formas de pago:</b></h6>
                <b class="col s5">Efectivo</b><div class="col s7"><input type="number" id="efectivoV" value="0.00" onchange="cambio();"></div>
                <b class="col s5">A Credito</b><div class="col s7"><input type="number" id="creditoV" value="0.00" onchange="cambio();"></div>
                <b class="col s5">A Banco</b><div class="col s7"><input type="number" id="bancoV" value="0.00" onchange="cambio();"></div>
                <br><br><br><br><br><br><br><hr>
            </div>
            <div class="col s12 m7">
                <font face="courier new">
                <hr>
                <h6 class="indigo-text center"><b>Total a pagar:</b></h6>
                <hr>
                <h2 class="indigo-text center" ><b>$<?php echo sprintf('%.2f', $consulta['suma']);?> </b></h2>
                <input type="hidden" id="total" value="<?php echo $consulta['suma'];?>">
                <hr>
                <hr>
                <h6 class="green-text center"><b>Cambio:</b></h6>
                <hr>
                <h2 class="green-text center" ><b><input type="" id="cambio" class="green-text center col s12" value="<?php echo '$-'.sprintf('%.2f', $consulta['suma']);?>"  id="cambio"/></b></h2>
                </font>
                <br> <br> <br><hr>
            </div>
            <ul class="collection center">
                <li class="collection-item indigo lighten-5 "></li>
            </ul>  
            <a class="modal-action modal-close waves-effect waves-light btn-large indigo lighten-5 indigo-text right"><b>Regresar<i class="material-icons left">close</i></b></a>
            <a class="right white-text"> <br>_ _ _ _</a>
            <a onclick="insert_pago(<?php echo $Venta;?>)" class="waves-effect waves-green btn-large indigo lighten-5 teal-text right"><b>REALIZAR<i class="material-icons left">local_atm</i></b></a>          
        </div>
    </div>
</div>