<script>
  function recargar_clientes() {
    setTimeout("location.href='clientes_punto_venta.php'", 800);
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
  function home() {
    setTimeout("location.href='home.php'", 1000);
  }
</script>
<!--Termina Script Buscar clientes-->

<!-- Modal Buscar clientes redes-->
  <div id="buscar_clientes_redes" class="modal modal-fixed-footer">
    <div class="modal-content">
      <nav>
        <div class="nav-wrapper">
          <form>
            <div class="input-field pink lighten-4">
              <input id="buscar_cliente_redes" type="search" placeholder="Buscar Cliente" maxlength="30" value="" autocomplete="off" onKeyUp="PulsarTeclaRedes();" autofocus="true" required>
              <label class="label-icon" for="search"><i class="material-icons">search</i></label>
              <i class="material-icons">close</i>
            </div>
          </form>
        </div>
      </nav>
      <p><div id="resultado_clientes_redes"></div></p>
    </div>
    <div class="modal-footer container">
      <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat">Cerrar<i class="material-icons right">close</i></a>
    </div>
  </div>
<!--.....Termina Modal Buscar clientes redes-->

<!-- Modal Buscar clientes -->
  <div id="buscar_clientes" class="modal modal-fixed-footer">
    <div class="modal-content">
      <nav>
        <div class="nav-wrapper">
          <form>
            <div class="input-field pink lighten-4">
              <input id="buscar_cliente" type="search" placeholder="Buscar Cliente" maxlength="30" value="" autocomplete="off" onKeyUp="PulsarTecla();" autofocus="true" required>
              <label class="label-icon" for="search"><i class="material-icons">search</i></label>
              <i class="material-icons">close</i>
            </div>
          </form>
        </div>
      </nav>
      <p><div id="resultado_clientes"></div></p>
    </div>
    <div class="modal-footer container">
      <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat">Cerrar<i class="material-icons right">close</i></a>
    </div>
  </div>
<!--.....Termina Modal Buscar clientes-->

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
