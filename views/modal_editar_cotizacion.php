<?php
  //ARCHIVO QUE CONTIENE LA VARIABLE CON LA CONEXION A LA BASE DE DATOS
  include('../php/conexion.php');
  // POR EL METODO POST ¿RECIBIMOS EL ID DEL ARTICULO DESDE EL ARCHIVO views/detalle_cotizacion_pv.php
  $id = $conn->real_escape_string($_POST['id']);
  //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL ARTICULO EN LA BD `punto_venta_detalle_cotizacion` Y ASIGNAMOS EL ARRAY A UNA VARIABLE $cotizacion
  $cotizacion = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_detalle_cotizacion` WHERE id=$id LIMIT 1"));
  //CON LA VARIABLE $id_articulo DECIMOS NOS TRAEMOS LA INFORMACION DEL ID DEL ARTICULO PARA SER UTILIZADA 
  $id_articulo=$cotizacion['id_articulo'];

  //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL ARTICULO EN LA BD `punto_venta_articulos Y ASIGNAMOS EL ARRAY A UNA VARIABLE $articulo
  $articulo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_articulos` WHERE id= $id_articulob"));
  //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL ARTICULO EN LA BD `punto_venta_detalle_cotizacion` Y ASIGNAMOS EL ARRAY A UNA VARIABLE $detalle
  $detalle = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_detalle_cotizacion` WHERE id=$id"));
  
?>
<script>
	$(document).ready(function(){
	    $('#modalEditarCotizacion').modal();
	    $('#modalEditarCotizacion').modal('open'); 
	 });
</script>

<!-- MODALES DE COTIZACION -->
<!-- Modal EditarCotizacion Structure -->
<div id="modalEditarCotizacion" class="modal">
    <div class="modal-content"> 
        <h5 class="red-text"><b>EDITAR CANTIDAD Y PRECIO DEL ARTICULO</b></h5><br>
        <h6><b>NOTA:  Cada edición conlleva un motivo del porque se esta editando el artículo.</b></h6><br>
        <form class="row" action="../php/control_cotizacion.php" method="post">
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Articulo</th>
                            <th>Precio $</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $articulo['codigo']; ?></td>
                            <td><?php echo $articulo['nombre']; ?></td>
                            <td><input class="col s8" type="number" id="precioCambiar" name="precioCambiar" value="<?php echo $articulo['precio']; ?>"> </td>
                            <td><input class="col s8" type="number" id="cantidadCambiar" name="cantidadCambiar" value="<?php echo $detalle['cantidad']; ?>"><div class="col s2"><br><?php echo $articulo['unidad']; ?></div></td>
                        </tr>
                    </tbody>
                </table>
            </div><br>
            <div class="input-field col s12 m6 l6">
                <!--CAMPO PARA AGREGAR LA DESCRIPCIÓN DEL CAMBIO-->
                <div class="input-field">
                    <i class="material-icons prefix">comment</i>
                    <input id="descripcion_cambio" name="descripcion_cambio" type="text" class="validate" data-length="200" required>
                    <label for="descripcion_cambio">¿Cuál es el motivo del cambio?:</label>
                </div>
                <!-- PARA DIRIGIR HACIA CONTROL ALMACEN CON EL VALOR >>|10|<< -->
                <input id="id_articulo" name="id_articulo" type="hidden" value="<?php echo $id ?>">
                <!-- <input id="almacen" name="almacen" type="hidden" value="<?php /* echo $id_almacen  */?>"> -->
                <input id="accion" name="accion" type="hidden" value="10">
            </div><br><br>
            <button class="btn waves-effect waves-light pink right" type="submit" name="action">Guardar<i class="material-icons right">save</i></button>
            <a href="#" class="modal-action modal-close waves-effect waves-green btn red right">Cancelar<i class="material-icons right">close</i></a>
        </form>
    </div>
</div>