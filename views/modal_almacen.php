<?php
  //ARCHIVO QUE CONTIENE LA VARIABLE CON LA CONEXION A LA BASE DE DATOS
  include('../php/conexion.php');
  // POR EL METODO POST ¿RECIBIMOS EL ID DEL ARTICULO DESDE EL ARCHIVO views/almacen_punto_venta.php
  $id = $conn->real_escape_string($_POST['id']);
  $id_almacen = $conn->real_escape_string($_POST['almacen']);
  //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL ARTICULO EN LA BD `punto_venta_articulos` Y ASIGNAMOS EL ARRAY A UNA VARIABLE $articulo
  $articulo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_articulos` WHERE id=$id"));
  //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL ARTICULO EN LA BD `punto_venta_almacen_general` Y ASIGNAMOS EL ARRAY A UNA VARIABLE $almacen
  $almacen = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_almacen_general` WHERE id_articulo=$id AND id_almacen = $id_almacen"));
?>
<script>
	$(document).ready(function(){
	    $('#modalAlmacen').modal();
	    $('#modalAlmacen').modal('open'); 
	 });
</script>
<!-- MODALES DE ALMACEN -->
<!-- Modal EditarAlmacen Structure -->
<div id="modalAlmacen" class="modal">
    <div class="modal-content">
        <h5 class="red-text"><b>EDITAR EXISTENCA (CANTIDAD) DEL ARTICULO</b></h5><br>
        <select name="" id="">
            <option value="volvo">Volvo</option>
            <option value="saab">Saab</option>
            <option value="opel">Opel</option>
            <option value="audi">Audi</option>
        </select>
        <h6><b>NOTA:  Cada edición conlleva un motivo para que, se pueda realizar el cambio.</b></h6><br>
        <form class="row" action="../php/control_almacen.php" method="post">
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Articulo</th>
                            <th>Precio</th>
                            <th>Existencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $articulo['codigo']; ?></td>
                            <td><?php echo $articulo['nombre']; ?></td>
                            <td><?php echo '$'.sprintf('%.2f', $articulo['precio']); ?></td>
                            <td><input class="col s8" type="number" id="cantidadCambiar" name="cantidadCambiar" value="<?php echo $almacen['cantidad']; ?>"><div class="col s2"><br><?php echo $articulo['unidad']; ?></div></td>
                        </tr>
                    </tbody>
                </table>
            </div><br>
            <div class="input-field col s12 m6 l6">
                <!--CAMPO PARA AGREGAR LA DESCRIPCIÓN DEL CAMBIO-->
                <select class="browser-default" id="descripcion_cambio" name="descripcion_cambio">
                    <option value="" disabled selected>Seleccione el motivo</option>
                    <option value="Traspaso Interno">Traspaso Interno</option>
                    <option value="Perdida por inventario">Perdida por inventario</option>
                    <option value="Aumento por Inventario">Aumento por Inventario</option>
                </select>
                <!-- PARA DIRIGIR HACIA CONTROL ALMACEN CON EL VALOR 5 -->
                <input id="id_articulo" name="id_articulo" type="hidden" value="<?php echo $id ?>">
                <input id="almacen" name="almacen" type="hidden" value="<?php echo $id_almacen ?>">
                <input id="accion" name="accion" type="hidden" value="5">
            </div><br><br>
            <button class="btn waves-effect waves-light pink right" type="submit" name="action">Guardar<i class="material-icons right">save</i></button>
            <a href="#" class="modal-action modal-close waves-effect waves-green btn red right">Cancelar<i class="material-icons right">close</i></a>
        </form>
    </div>
</div>
                        