<script>
  function recargar_clientes() {
    setTimeout("location.href='clientes_punto_venta.php'", 800);
  }
  function recargar_compra() {
    setTimeout("location.href='compras_punto_venta.php'", 800);
  }
  function recargar_venta() {
    setTimeout("location.href='ventas_punto_venta.php'", 800);
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
  function recargar_cotizaciones(){
    setTimeout("location.href='cotizaciones_punto_venta.php'", 800);
  }
  function recargar_credito(){
    setTimeout("location.href='credito.php'", 800);
  }
  function cerrar_sesion() {
    setTimeout("location.href='../php/cerrar_sesion.php'", 1200);
  }
  function recargar_subcategorias() {
    setTimeout("location.href='../views/agregar_subcat_punto_venta.php'", 800);
  }
</script>
<!--Termina Script Buscar clientes-->

<!-- Modal AGREGAR CLIENTES IMPOTANTE! -->
<div id="modal_addCliente" class="modal"><br>
  <div class="modal-content">
    <div class="row">
      <h6 class="col s12 m5 l5"></h6>
      <!--    //////    INPUT DE EL BUSCADOR    ///////   -->
      <form class="col s12 m7 l7">
        <div class="row">
          <div class="input-field col s12">
            <i class="material-icons prefix">search</i>
            <input id="busquedaCliente" name="busquedaCliente" type="text" class="validate" autocomplete="off" onKeyUp="buscar_clientes();" autofocus="true" required>
            <label for="busquedaCliente">Buscar Cliente(Nombre, Id, RFC, Colonia, Localidad)</label>
          </div>
        </div>
      </form>
      <div id="tablaCliente"></div>
    </div>
  </div>
  <div class="modal-footer">
    <a href="#" class="modal-action modal-close waves-effect waves-green red btn-small">Cerrar<i class="material-icons left">close</i></a>
  </div><br>
</div>
<!--Cierre modal AGREGAR ARTICULOS COMPRA IMPORTANTE! -->

<!-- Modal AGREGAR ARTICULOS IMPOTANTE! -->
<div id="modal_addArticulo" class="modal"><br>
  <div class="modal-content">
    <div class="row">
      <h6 class="col s12 m5 l5"></h6>
      <!--    //////    INPUT DE EL BUSCADOR    ///////   -->
      <form class="col s12 m7 l7">
        <div class="row">
          <div class="input-field col s12">
            <i class="material-icons prefix">search</i>
            <input id="busquedaArticulo" name="busquedaArticulo" type="text" class="validate" autocomplete="off" onKeyUp="buscar_articulos();" autofocus="true" required>
            <label for="busquedaArticulo">Buscar Artículo(Código, Nombre, Descripcion)</label>
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

<!-- Modal AGREGAR PROVEEDORES IMPOTANTE! -->
<div id="modal_addProveedores" class="modal"><br>
  <div class="modal-content">
    <div class="row">
      <h6 class="col s12 m5 l5"></h6>
      <!--    //////    INPUT DE EL BUSCADOR    ///////   -->
      <form class="col s12 m7 l7">
        <div class="row">
          <div class="input-field col s12">
            <i class="material-icons prefix">search</i>
            <input id="busquedaProveedor" name="busquedaProveedor" type="text" class="validate" autocomplete="off" onKeyUp="buscar_proveedores();" autofocus="true" required>
            <label for="busquedaProveedor">Buscar Proveedor(Nombre, RFC, Email, Télefono)</label>
          </div>
        </div>
      </form>
      <div id="tablaProveedores"></div>
    </div>
  </div>
  <div class="modal-footer">
    <a href="#" class="modal-action modal-close waves-effect waves-green red btn-small">Cerrar<i class="material-icons left">close</i></a>
  </div><br>
</div>
<!--Cierre modal AGREGAR PROVEEDORES COMPRA IMPORTANTE! -->

<!-- Modal SELECCIONAR CLIENTES IMPOTANTE! -->
<div id="modal_addClientes" class="modal"><br>
  <div class="modal-content">
    <div class="row">
      <h6 class="col s12 m5 l5"></h6>
      <!--    //////    INPUT DE EL BUSCADOR    ///////   -->
      <form class="col s12 m7 l7">
        <div class="row">
          <div class="input-field col s12">
            <i class="material-icons prefix">search</i>
            <input id="busquedaClientes" name="busquedaClientes" type="text" class="validate" autocomplete="off" onKeyUp="buscar_clientes();" autofocus="true" required>
            <label for="busquedaClientes">Buscar Cliente(Número, Nombre, RFC)</label>
          </div>
        </div>
      </form>
      <div id="tablaClientes"></div>
    </div>
  </div>
  <div class="modal-footer">
    <a href="#" class="modal-action modal-close waves-effect waves-green red btn-small">Cerrar<i class="material-icons left">close</i></a>
  </div><br>
</div>
<!--Cierre modal SELECCIONAR CLIENTES COMPRA IMPORTANTE! -->

<!-- Modal AGREGAR ARTICULOS A LA COTIZACIÓN IMPOTANTE! -->
<div id="modal_addArticuloCotizacion" class="modal"><br>
  <div class="modal-content">
    <div class="row">
      <h6 class="col s12 m5 l5"></h6>
      <!--    //////    INPUT DE EL BUSCADOR    ///////   -->
      <form class="col s12 m7 l7">
        <div class="row">
          <div class="input-field col s12">
            <i class="material-icons prefix">search</i>
            <input id="busquedaArticulo" name="busquedaArticulo" type="text" class="validate" autocomplete="off" onKeyUp="buscar_articulos();" autofocus="true" required>
            <label for="busquedaArticulo">Buscar Artículo(Código, Nombre, Descripcion)</label>
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
    <h4 class="red-text center">! Advertencia !</h4>
    <h6 ><b>Una vez generado el corte se comenzara una nueva lista de pagos para el siguinete corte. </b></h6><br>
    <h5 class="red-text darken-2">¿DESEA CONTINUAR?</h5>
    <div class="row">
      <div class="input-field col s12 m5 l5">
        <i class="material-icons prefix">lock</i>
        <input type="password" name="clave" id="clave">
        <label for="clave">Ingresar Clave</label>
      </div>
      <div class="input-field row col s12 m5 l5">
          <i class="col s1"> <br></i>
          <select id="recibio" class="browser-default col s10" >
            <option value="0" selected >Recibio</option>
            <option value="Jonatan">Jonatan</option>
            <option value="Gabriel">Gabriel</option>
          </select>
      </div>
    </div>
    <h5>¿Desea agregar algun deducible?</h5>
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
      <a onclick="recargar_corte()" class="modal-action modal-close waves-effect waves-green btn green">Aceptar<i class="material-icons right">done</i></a>
      <a href="#" class="modal-action modal-close waves-effect waves-red btn red darken-2">Cerrar<i class="material-icons right">close</i></a>
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