<script>
  function recargar_clientes() {
    setTimeout("location.href='clientes_punto_venta.php'", 800);
  }
  function recargar_compra() {
    setTimeout("location.href='compras_punto_venta.php'", 800);
  }
  function recargar_proveedores() {
    setTimeout("location.href='proveedores_punto_venta.php'", 800);
  }
  function recargar_usuarios() {
    setTimeout("location.href='usuarios.php'", 800);
  }
  function recargar_articulo() {
    setTimeout("location.href='articulos_punto_venta.php'", 800);
  }
  function recargar_categoria() {
    setTimeout("location.href='categorias_punto_venta.php'", 800);
  }
  function recargar_almacen_lista() {
    setTimeout("location.href='almacenes_punto_venta.php'", 800);
  }
  function recargar_mi_almacen() {
    setTimeout("location.href='almacen_punto_venta.php'", 800);
  }
  function home() {
    setTimeout("location.href='home.php'", 1000);
  }
</script>
<!--Termina Script Buscar clientes-->


<!-- Modal AGREGAR ARTICULOS IMPOTANTE! -->
<div id="modal_addArticulo" class="modal"><br>
  <div class="modal-content">
    <div class="row">
        <h6 class="col s12 m5 l5">.</h6>
        <!--    //////    INPUT DE EL BUSCADOR    ///////   -->
        <form class="col s12 m7 l7">
          <div class="row">
            <div class="input-field col s12">
              <i class="material-icons prefix">search</i>
              <input id="busqueda" name="busqueda" type="text" class="validate" autocomplete="off" onKeyUp="buscar_articulos();" autofocus="true" required>
              <label for="busqueda">Buscar Artículo(Código, Nombre, Descripcion)</label>
            </div>
          </div>
        </form>
        <div id="tablaArticulo"></div>
    </div>
  </div>
  <div class="modal-footer">
      <a href="#" class="modal-action modal-close waves-effect waves-green red btn-small">Cerrar<i class="material-icons left">close</i></a>
  </div><br>
</div>
<!--Cierre modal AGREGAR ARTICULOS COMPRA IMPORTANTE! -->

<!--Modal cortes-->
<div id="corte" class="modal">
  <div class="modal-content">
    <h4 class="red-text center">! Advertencia !</h4><br>
    <h6 ><b>Una vez generado el corte se comenzara una nueva lista de pagos para el siguinete corte. </b></h6><br>
    <h5 class="red-text darken-2">¿DESEA CONTINUAR?</h5>
    <div class="row">
    <div class="input-field col s12 m6 l6">
        <i class="material-icons prefix">lock</i>
        <input type="password" name="clave" id="clave">
        <label for="clave">Ingresar Clave</label>
    </div>
    </div>
    <h4>¿Desea agregar algun deducible?</h4>
      <form class="row">
      <div class="input-field col s12 m6 l4">
          <i class="material-icons prefix">attach_money</i>
          <input id="cantidadD" type="number" class="validate" data-length="30" value="0" required>
          <label for="cantidadD">Cantidad:</label>
      </div>
      <div class="input-field col s12 m6 l6">
          <i class="material-icons prefix">edit</i>
          <input id="descripcionD" type="text" class="validate" data-length="30" required>
          <label for="descripcionD">Descripcion:(ej: Viaticos para Marcos y Luis) </label>
      </div>
      </form>
  </div>
  <div class="modal-footer">
      <a onclick="recargar_corte()" class="modal-action modal-close waves-effect waves-green btn-flat">Aceptar</a>
      <a href="#" class="modal-action modal-close waves-effect waves-red btn-flat">Cerrar<i class="material-icons right">close</i></a>
  </div>
</div>
<!--Cierre modal Cortes-->


<!--Modal cortes PARCIALES-->
<div id="corteP" class="modal">
  <div class="modal-content">
    <h4 class="red-text center">! Advertencia !</h4><br>
    <h6 ><b>Una vez generado el corte se comenzara una nueva lista de pagos para el siguinete corte parcial. </b></h6><br>
    <h5 class="red-text darken-2">¿DESEA CONTINUAR?</h5>
    <div class="row">
    <div class="input-field col s12 m6 l6">
        <i class="material-icons prefix">lock</i>
        <input type="password" name="claveP" id="claveP">
        <label for="claveP">Ingresar Clave</label>
    </div>
    </div>
    <h4>Nombre del cobrador</h4>
      <form class="row">
      <div class="input-field col s12 m6 l6">
          <i class="material-icons prefix">people</i>
          <input id="cobradorP" type="text" class="validate" data-length="30" required>
          <label for="cobradorP">Nombre:(ej: Marcos Santillan) </label>
      </div>
      </form>
  </div>
  <div class="modal-footer">
      <a onclick="recargar_corteP()" class="modal-action modal-close waves-effect waves-green btn-flat right">Aceptar</a>
      <a href="#" class="modal-action modal-close waves-effect waves-red btn-flat">Cerrar<i class="material-icons right">close</i></a>
  </div>
</div>
<!--Cierre modal Cortes PARCIALES-->

<!-- MODALES DE ALMACEN -->
<!-- Modal EditarAlmacen Structure -->
<?php 
    $id = $_POST['id'];// POR EL METODO POST RECIBIMOS EL ID DEL ARTICULO DESDE EL ARCHIVO php/control_almacen.php EN EL CASO 4
    //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL ARTICULO EN LA BD `punto_venta_articulos` Y ASIGNAMOS EL ARRAY A UNA VARIABLE $datos
    $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_articulos` WHERE id=$id"));
    //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL ARTICULO EN LA BD `punto_venta_articulos` Y ASIGNAMOS EL ARRAY A UNA VARIABLE $cantidad
    $cantidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_almacen_general` WHERE id_articulo=$id"));
    $id_categoria = $datos['categoria'];
    $categoria = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_categorias` WHERE id=$id_categoria"));
?>
<div id="EditarAlmacen" class="modal">
  <div class="modal-content">
    <h5>Editar artículo:</h5> 
    <h6 class="red-text"><b>¡¡ATENCION!!____Gracias por su atención.</b></h6>
    <!-- CREAMOS UN DIV EL CUAL TENGA id = "resultado_update"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
    <div id="resultado_update"></div> 
    <form action="../php/editar_mi_almacen_pv.php" method="post" enctype="multipart/form-data">
      <!--CAMPO PARA EDITAR EL PRECIO-->
      <div class="input-field col s12 m6 l6">
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
      </div><br>
      <a href="#" class="modal-action modal-close waves-effect waves-green btn red accent-2">Cancelar<i class="material-icons right">close</i></a>
      <button class="btn waves-effect waves-light pink right" type="submit" name="action">Subir<i class="material-icons right">file_upload</i></button>
    </form>
  </div>
</div>
