<html>
<head>
	<title>SIC | Factura</title>
  <?php 
    include('fredyNav.php');
    include('../php/cobrador.php');
    date_default_timezone_set('America/Mexico_City');
    $Fecha_hoy = date('Y-m-d');
    $config = mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM punto_venta_config"));
    $folio = $_GET['id'];
    $factura = mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM tmp_pv_factura WHERE folio = $folio "));
  ?>
  <script>
    //FUNCION QUE BUCARA EN LA BASE DE DATOS CLIENTES CON EL MISMO NOMBRE MOSTRARA Y DARA A ELEGIR
    function buscarClientes() {
      var rfc = $("input#rfc").val();
      var razon = $("input#razon_social").val();
      //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_facturas.php"
      $.post("../php/control_facturas.php", {
        //Cada valor se separa por una ,
          rfc: rfc,
          razon: razon,
          accion: 7,
        }, function(mensaje){
        //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_facturas.php"
        $("#buscarClientes").html(mensaje);
      });//FIN post
    }//FIN function 
    //FUNCION QUE BORRA LOS COMPRAS (SE ACTIVA AL INICIAR EL BOTON BORRAR)
    function borrar_venta(id){
      var answer = confirm("Deseas eliminar la venta N°"+id+" de la lista?");
      if (answer) {
        //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_facturas.php"
        $.post("../php/control_facturas.php", {
          //Cada valor se separa por una ,
            id: id,
            folio: <?php echo $folio; ?>,
            accion: 2,
          }, function(mensaje) {
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_facturas.php"
            $("#cancelar").html(mensaje);
        }); //FIN post
      }//FIN IF
    };//FIN function

    //FUNCION QUE HACE LA BUSQUEDA DE ARTICULOS DE LAS VENTAS (SE ACTIVA AL INICIAR EL ARCHIVO)
    function buscarVentas(folio){
      //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_facturas.php"
      $.post("../php/control_facturas.php", {
        //Cada valor se separa por una ,
          folio: folio,
          accion: 1,
        }, function(mensaje){
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_facturas.php"
            $("#VentasP").html(mensaje);
      });//FIN post
    }//FIN function
    //FUNCION QUE GUARDA LOS CAMBIOS REALIZADOS
    function update_factura(folio){
      //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO EL TEXTO REQUERIDO Y LO ASIGNAMOS A UNA VARIABLE
      var regimen = $("select#regimen").val();
      var cdfi = $("select#cdfi").val();
      var metodo_pago = $("select#metodo_pago").val();
      var forma_pago = $("select#forma_pago").val();
      var id_cliente = $("input#id_cliente").val();
      var total = $("input#total").val();

      //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_facturas.php"
      $.post("../php/control_facturas.php", {
        //Cada valor se separa por una ,
          folio: folio,
          regimen: regimen,
          cdfi: cdfi,
          metodo_pago: metodo_pago,
          total: total,
          id_cliente: id_cliente,
          forma_pago: forma_pago,
          accion: 3,
        }, function(mensaje){
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_facturas.php"
            $("#cancelar").html(mensaje);
      });//FIN post
    }//FIN function
    //FUNICION QUE MUESTRA LA INFORMACION DEL CLIENTE SI SELECCIONAMOS ALGUNO O VACIO PARA NUEVO
    function mostrarCliente(id_cliente) {
        //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO NE LA DIRECCION "../php/control_facturas.php"
        $.post("../php/control_facturas.php", {
          //Cada valor se separa por una ,
            accion: 8,
            id_cliente: id_cliente,
        }, function(mensaje) {
          //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_facturas.php"
            $("#infoCliente").html(mensaje);
        }); 
    }//FIN function 
  </script>
</head>
<main>
<body onload="buscarVentas( <?php echo $folio; ?>);">
  <div class="container">
    <!-- CREAMOS UN DIV EL CUAL TENGA id = "cancelar"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
    <div id="cancelar"></div>
    <div class="row" ><br>
      <h3 class="hide-on-med-and-down col s12 m8">Registrar Factura N° <?php echo $folio; ?> (Temporal)</h3>
      <h5 class="hide-on-large-only col s12">Registrar Factura N° <?php echo $folio; ?> (Temporal) </h5>
      <!--    //////    BOTON QUE REDIRECCIONA AL FORMULARIO DE AGREGAR COMPRA    ///////   -->
      <br><a onclick="update_factura(<?php echo $folio; ?>);" class="waves-effect waves-light btn-small tooltipped green right" data-tooltip="Guardar Cambios">Guardar<i class="material-icons prefix left">save</i></a>
      <a onclick="crear_factura();" class="waves-effect waves-light btn-small tooltipped indigo right" data-tooltip="Crear Factura Solo en Sistema"> Crear Factura<i class="material-icons prefix left">picture_as_pdf</i></a>
    </div>
    <div class="row">
    <!-- ----------------------------  TABs o MENU  ---------------------------------------->
      <div class="col s12">
        <ul id="tabs-swipe-demo" class="tabs">
          <li class="tab col s4"><a class="active black-text" href="#test-swipe-1">EMISOR - RESEPTOR</a></li>
          <li class="tab col s4"><a class="black-text" href="#test-swipe-2">ARTICULOS (CONCEPTOS)</a></li>
          <li class="tab col s4"><a class="black-text" href="#test-swipe-3">OTROS DATOS</a></li>
        </ul>
      </div>
      <!-- ----------------------------  FORMULARIO 1 Tabs  ---------------------------------------->
      <div  id="test-swipe-1" class="col s12"><br><br>
      
        <div class="row"><br>
          <ul class="collection">
            <li class="collection-item indigo"><b class="white-text">EMISOR: </b></li>
          </ul>
          <div class="row col s12">
            <div class="col s5 m4">
              <b class="right">*RFC: </b><br>
              <b class="right">*Razón Social: </b><br><br>
              <b class="right">*Regimen Fiscal: </b><br>
              <b class="right">*Lugar de Expedición (Código Postal):</b><br>
              <b class="right">Correo Electronico:</b><br>
            </div>
            <div class="col s7 m8">
              <?php echo $config['rfc']; ?><br>
              <?php echo $config['razon_social']; ?><br>
              <?php
              if ($factura['regimen_fiscal'] == NULL) {
                $value = ''; $show = 'Seleccone un regimen';
              }else{
                $array = array("612" => "Personas Físicas con Actividades Empresariales y Profesionales");
                $value = $factura['regimen_fiscal']; $show = $factura['regimen_fiscal'].'-'.$array[$factura['regimen_fiscal']];
              }
              ?>
              <select class="browser-default col s12 m9" id="regimen">
                <option value="<?php echo $value; ?>" selected><?php echo $show; ?></option>
                <option value="612">612- Personas Físicas con Actividades Empresariales y Profesionales</option>
              </select><br><br>
              <?php echo $config['cp']; ?><br>
              <input class="col s12 m8" type="" value="<?php echo $config['correo']; ?>"><br>
            </div>
          </div><br><br><br><br><br><br><br>

          <ul class="collection">
            <li class="collection-item indigo"><b class="white-text">RECEPTOR: </b></li>
          </ul>
           <div class="row col s12" id="infoCliente">
            <div class="col s5 m4">
              <b class="right">*RFC: </b><br>
              <b class="right">Razón Social: </b><br><br>
              <b class="right">*Uso de CFDI: </b><br>
              <b class="right">Correo Electronico:</b><br>
            </div>
            <div class="col s7 m8">
              <?php
              if ($factura['cliente'] == 0) {
                $rfc = ''; $razon_social = ''; $correo = '';
              }else{
                $id = $factura['cliente'];
                $cliente = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM `punto-venta_clientes` WHERE id = $id"));
                $rfc = $cliente['rfc']; $razon_social = $cliente['nombre']; $correo = $cliente['email'];
              }
              if ($factura['uso_cdfi'] == NULL) {
                $value = ''; $show = 'Seleccone un uso CDFI';
              }else{
                $array = array("G01" => "Adquisición de mercancia" , "G02" => "Devoluciones, descuentos o bonificaciones", "G03" => "Gastos en general", "I01" => "Construcciones", "I02" => "Moviliario y equipo de oficina por inverciones", "I03" => "Equipo de transporte", "I04" => "Equipo de computo y accesorios", "I05" => "Dados, troqueles, moldes, matrices y herramientas", "I06" => "Comunicaciones telefonicas", "I07" => "Comunicaciones satelitales", "I08" => "Otra maquinaria y equipo", "D01" => "Honorario médicos, dentales y gastos hospitalarios", "D02" => "Gastos médicos por incapacidad o discapacidad", "D03" => "Gastos funerales", "D04" => "Donativos", "D05" => "Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)", "D06" => "Aportaciones voluntarias al SAR", "D07" => "Primas por seguros de gastos medicos", "D08" => "Gastos de transportación escolar obligatoria", "D09" => "Depositos de cuentas para el ahorro, primas que tengan como base plabes de pensiones", "D10" => "Pagos por servicios educativos (colegiaturas)", "P01" => "Por definir");
                $value = $factura['uso_cdfi']; $show = $factura['uso_cdfi'].'-'.$array[$factura['uso_cdfi']];
              }
              ?>
              <input class="col s12 m5" type="" id = "rfc" value="<?php echo $rfc; ?>" onkeyup="buscarClientes()"/><div class="col s12 m7" id="buscarClientes" align="right"><br></div>
              <input type="hidden" id="id_cliente" value="<?php echo $factura['cliente']; ?>" />
              <input class="col s12 m7" type="" id = "razon_social" value="<?php echo $razon_social; ?>"  onkeyup="buscarClientes()"/><br>
              <select class="browser-default col s12 m9" id="cdfi">
                <option value="<?php echo $value; ?>" selected><?php echo $show; ?></option>
                <option value="G01">G01-Adquisición de mercancia</option>
                <option value="G02">G02-Devoluciones, descuentos o bonificaciones</option>
                <option value="G03">G03-Gastos en general</option>
                <option value="I01">I01-Construcciones</option>
                <option value="I02">I02-Moviliario y equipo de oficina por inverciones</option>
                <option value="I03">I03-Equipo de transporte</option>
                <option value="I04">I04-Equipo de computo y accesorios</option>
                <option value="I05">I05-Dados, troqueles, moldes, matrices y herramientas</option>
                <option value="I06">I06-Comunicaciones telefonicas</option>
                <option value="I07">I07-Comunicaciones satelitales</option>
                <option value="I08">I08-Otra maquinaria y equipo</option>
                <option value="D01">D01-Honorario médicos, dentales y gastos hospitalarios</option>
                <option value="D02">D02-Gastos médicos por incapacidad o discapacidad</option>
                <option value="D03">D03-Gastos funerales</option>
                <option value="D04">D04-Donativos</option>
                <option value="D05">D05-Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)</option>
                <option value="D06">D06-Aportaciones voluntarias al SAR</option>
                <option value="D07">D07-Primas por seguros de gastos medicos</option>
                <option value="D08">D08-Gastos de transportación escolar obligatoria</option>
                <option value="D09">D09-Depositos de cuentas para el ahorro, primas que tengan como base plabes de pensiones</option>
                <option value="D10">D10-Pagos por servicios educativos (colegiaturas)</option>
                <option value="P01">P01-Por definir</option>
              </select>
              <input class="col s12 m8" type="" id="correo" value="<?php echo $correo; ?>"><br>
            </div>
           </div><br><br><br><br><br><br><br>
        </div>
      </div>
      <!-- ----------------------------  FORMULARIO 2 Tabs  ---------------------------------------->
      <div  id="test-swipe-2" class="col s12"><br><br>
        <div class="row"><br>
            <!--    //////    BOTON QUE REDIRECCIONA AL FORMULARIO DE AGREGAR COMPRA    ///////   -->
            <a href="ventas_punto_venta.php" class="waves-effect waves-light btn pink left right">Agregar Venta<i class="material-icons prefix left">add</i></a>
            <div id="VentasP"></div>
        </div>
      </div>
      <!-- ----------------------------  FORMULARIO 3 Tabs  ---------------------------------------->
      <div  id="test-swipe-3" class="col s12"><br>        
        <div class="row"><br>
          <div class="col s12 m6">
            <label><h4>Método de Pago</h4></label>
            <?php
              if ($factura['metodo_pago'] == NULL) {
                $value = ''; $show = 'Seleccone:';
              }else{
                $array = array("PUE" => "Pago en una sola exhibición", "PPD" => "Pago en parcialidades o diferido");
                $value = $factura['metodo_pago']; $show = $factura['metodo_pago'].'-'.$array[$factura['metodo_pago']];
              }
            ?>
            <select class="browser-default" id="metodo_pago">
                <option value="<?php echo $value; ?>" selected><?php echo $show; ?></option>
                <option value="PUE">PUE-Pago en una sola exhibición</option>
                <option value="PPD">G01-Pago en parcialidades o diferido</option>
            </select>
          </div>
          <div class="col s12 m6">
            <label><h4>Forma de Pago</h4></label>  
            <?php
              if ($factura['forma_pago'] == NULL) {
                $value = ''; $show = 'Seleccone:';
              }else{
                $array = array("01" => "Efectivo" , "02" => "Cheque nominativo", "03" => "Transferencia electrónica de fondos", "04" => "Tarjeta de crédito", "05" => "Monedero electrónico", "06" => "Dinero electrónico", "08" => "Vales de despensa", "12" => "Donación en pago", "15" => "Condonación", "17" => "Compensación", "13" => "Pago por subrogación", "23" => "Novación", "24" => "Confusión", "25" => "Remisión de deuda", "26" => "Prescripción o caducidad", "27" => "A satisfacción del acreedor", "28" => "Tarjeta de Débito", "29" => "Tarjeta de Servicio", "30" => "Aplicación de anticipos", "31" => "Intermediario pagos", "99" => "Por definir");

                $value = $factura['forma_pago']; $show = $factura['forma_pago'].'-'.$array[$factura['forma_pago']];
              }
            ?>
            <select class="browser-default" id="forma_pago">
                <option value="<?php echo $value; ?>" selected><?php echo $show; ?></option>
                <option value="01">01-Efectivo</option>
                <option value="02">02-Cheque nominativo</option>
                <option value="03">03-Transferencia electrónica de fondos</option>
                <option value="04">04-Tarjeta de crédito</option>
                <option value="05">05-Monedero electrónico</option>
                <option value="06">06-Dinero electrónico</option>
                <option value="08">08-Vales de despensa</option>
                <option value="12">12-Donación en pago</option>
                <option value="13">13-Pago por subrogación</option>
                <option value="14">14-Pago por consignación</option>
                <option value="15">15-Condonación</option>
                <option value="17">17-Compensación</option>
                <option value="23">23-Novación</option>
                <option value="24">24-Confusión</option>
                <option value="25">25-Remisión de deuda</option>
                <option value="26">26-Prescripción o caducidad</option>
                <option value="27">27-A satisfacción del acreedor</option>
                <option value="28">28-Tarjeta de Débito</option>
                <option value="29">29-Tarjeta de Servicio</option>
                <option value="30">30-Aplicación de anticipos</option>
                <option value="31">31-Intermediario pagos</option>
                <option value="99">99-Por definir</option>
            </select>  
          </div>            
        </div>
      </div>
    </div>
    <br><a onclick="update_factura(<?php echo $folio; ?>);" class="waves-effect waves-light btn-small tooltipped green right" data-position="top" data-tooltip="Guardar Cambios">Guardar<i class="material-icons prefix left">save</i></a>
    <a onclick="crear_factura();" class="waves-effect waves-light btn-small tooltipped indigo right" data-position="top" data-tooltip="Crear Factura Solo en Sistema"> Crear Factura<i class="material-icons prefix left">picture_as_pdf</i></a>
  </div>
</body>
</main>
</html>