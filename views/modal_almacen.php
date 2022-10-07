<?php
  //ARCHIVO QUE CONTIENE LA VARIABLE CON LA CONEXION A LA BASE DE DATOS
  include('../php/conexion.php');
  // POR EL METODO POST ¿RECIBIMOS EL ID DEL ARTICULO? DESDE EL ARCHIVO views/almacen_punto_venta.php
  $id = $conn->real_escape_string($_POST['id']);
?>
<script>
	$(document).ready(function(){
	    $('#modalAlmacen').modal();
	    $('#modalAlmacen').modal('open'); 
	 });
</script>
<!-- MODALES DE ALMACEN -->
<!-- Modal EditarAlmacen Structure -->
<?php 
    // $id = $conn->real_escape_string($_POST['id']);// POR EL METODO POST RECIBIMOS EL ID DEL ARTICULO DESDE EL ARCHIVO php/control_almacen.php EN EL CASO 4
    // $id = $_POST['id'];
    // $id = mysqli_fetch_array(mysqli_query($conn, "SELECT id FROM `punto_venta_articulos`");
    //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL ARTICULO EN LA BD `punto_venta_articulos` Y ASIGNAMOS EL ARRAY A UNA VARIABLE $datos
    $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_articulos` WHERE id=$id"));
    //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL ARTICULO EN LA BD `punto_venta_articulos` Y ASIGNAMOS EL ARRAY A UNA VARIABLE $cantidad
    $cantidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_almacen_general` WHERE id_articulo=$id"));
    $id_categoria = $datos['categoria'];
    $categoria = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_categorias` WHERE id=$id_categoria"));
?>
<div id="modalAlmacen" class="modal">
    <div class="modal-content">
        <h5>Editar artículo:</h5> 
        <h6 class="red-text"><b>¡¡ATENCION!!____Gracias por su atención.</b></h6>
        <!-- CREAMOS UN DIV EL CUAL TENGA id = "resultado_update"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
        <div id="resultado_update"></div> 
        <form action="../php/control_almacen.php" method="post" enctype="multipart/form-data">
        <!-- <form action="../php/editar_mi_almacen_pv.php" method="post" enctype="multipart/form-data"> -->
            <div class="input-field col s12 m6 l6">
                <!--CAMPO PARA EDITAR EL PRECIO-->
                <div class="input-field">
                    <i class="material-icons prefix">attach_money</i>
                    <input id="precio" type="number" class="validate" data-length="35" required value="<?php echo $datos['precio']; ?>">
                    <label for="precio">Precio:</label>
                </div> 
                <!--CAMPO PARA EDITAR LA CANTIDAD-->
                <div class="input-field">
                    <i class="material-icons prefix">shopping_basket</i>
                    <input id="cantidad" type="number" class="validate" data-length="40" required value="<?php echo $cantidad['cantidad']; ?>">
                    <label for="cantidad">Existencia:</label>
                </div>
                <!--CAMPO PARA EDITAR LA DESCRIPCIÓN DEL ARTICULO-->
                <div class="input-field">
                    <i class="material-icons prefix">description</i>
                    <input id="descripcion_articulo" type="text" class="validate" data-length="40" required value="<?php echo $datos['descripcion']; ?>">
                    <label for="descripcion_articulo">Descripción del artículo:</label>
                    </div>
                <!--CAMPO PARA AGREGAR LA DESCRIPCIÓN DEL CAMBIO-->
                <div class="input-field">
                    <i class="material-icons prefix">comment</i>
                    <input id="descripcion_cambio" type="text" class="validate" data-length="200" required>
                    <label for="descripcion_cambio">¿Cuál es el motivo del cambio?:</label>
                </div>
                <!-- PARA DIRIGIR HACIA CONTROL ALMACEN CON EL VALOR 5 -->
                <input id="id" name="id" type="hidden" value="<?php echo $id ?>">
                <input id="accion" name="accion" type="hidden" value="5">

            </div><br>
            <a href="#" class="modal-action modal-close waves-effect waves-green btn red accent-2">Cancelar<i class="material-icons right">close</i></a>
            <button class="btn waves-effect waves-light pink right" type="submit" name="action">Subir<i class="material-icons right">file_upload</i></button>
        </form>
    </div>
</div>