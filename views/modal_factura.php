<?php
include('../php/conexion.php');
$venta = $conn->real_escape_string($_POST['venta']);
?>
	<script>
		$(document).ready(function(){
		    $('#nodalFactura').modal();
		    $('#nodalFactura').modal('open'); 
		 });
	</script>
	<div id="nodalFactura" class="modal">
	    <div class="modal-content row">
        <h5 class="blue-text center"><b>Crear Factura</b></h5>
	      <h6><b>Crear nueva factura o agregar a una en proceso elegir:</b></h6>
	      <form class="row">
          <div class="input-field col s12 m6">
            <label>Seleccion una opcion:</label><br><br>
            <select class="browser-default" id="factura">
              <option value="0" selected>Nueva Factura</option>
              <?php 
                // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
                $consulta = mysqli_query($conn,"SELECT * FROM tmp_pv_factura");
                //VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
                if (mysqli_num_rows($consulta) == 0) {
                  echo '<script>M.toast({html:"No se encontraron facturas.", classes: "rounded"})</script>';
                } else {
                  //RECORREMOS UNO A UNO LOS ARTICULOS CON EL WHILE
                  while($factura = mysqli_fetch_array($consulta)) {
                    //Output
                    ?>                      
                    <option value="<?php echo $factura['folio'];?>">Folio N° <?php echo $factura['folio'];// MOSTRAMOS LA INFORMACION HTML?></option>-->
                    <?php
                  }//FIN while
                }//FIN else
              ?>
            </select>
          </div>
	      </form>
        <a onclick="facturar_update(<?php echo $venta ?>);" class="btn waves-effect waves-light grey darken-3 right">Facturar<i class="material-icons right">save</i></a>
        <a href="#" class="modal-action modal-close waves-effect waves-green btn red accent-2 right">Cancelar<i class="material-icons right">close</i></a>
	    </div>
	</div>