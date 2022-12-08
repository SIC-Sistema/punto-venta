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
  ?>
  <script>
    //FUNCION QUE BORRA LOS COMPRAS (SE ACTIVA AL INICIAR EL BOTON BORRAR)
    function borrar_venta_pv(id){
      var answer = confirm("Deseas eliminar el venta N°"+id+"?");
      if (answer) {
        //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_ventas.php"
        $.post("../php/control_ventas.php", {
          //Cada valor se separa por una ,
            id: id,
            accion: 3,
          }, function(mensaje) {
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_ventas.php"
            $("#cancelar").html(mensaje);
        }); //FIN post
      }//FIN IF
    };//FIN function

    //FUNCION QUE HACE LA BUSQUEDA DE ARTICULOS DE LAS VENTAS (SE ACTIVA AL INICIAR EL ARCHIVO)
    function buscarVentas(folio){
      //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO EL TEXTO REQUERIDO Y LO ASIGNAMOS A UNA VARIABLE
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
  </script>
</head>
<main>
<body onload="buscarVentas( <?php echo $folio; ?>);">
  <div class="container">
    <!-- CREAMOS UN DIV EL CUAL TENGA id = "cancelar"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
    <div id="cancelar"></div>
    <div class="row" ><br>
      <h3 class="hide-on-med-and-down">Registrar Factura N° <?php echo $folio; ?> (Temporal)</h3>
      <h5 class="hide-on-large-only">Registrar Factura N° <?php echo $folio; ?> (Temporal) </h5>
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
              <select class="browser-default col s12 m9">
                <option value="" disabled selected>Seleccone un regimen</option>
                <option value="612">612- Personas Físicas con Actividades Empresariales y Profesionales</option>
              </select><br><br>
              <?php echo $config['cp']; ?><br>
              <input class="col s12 m8" type="" name="" value="<?php echo $config['correo']; ?>"><br>
            </div>
          </div><br><br><br><br><br><br><br>

          <ul class="collection">
            <li class="collection-item indigo"><b class="white-text">RECEPTOR: </b></li>
          </ul>
           <div class="row col s12">
            <div class="col s5 m4">
              <b class="right">*RFC: </b><br>
              <b class="right">Razón Social: </b><br><br>
              <b class="right">*Uso de CFDI: </b><br>
              <b class="right">Correo Electronico:</b><br>
            </div>
            <div class="col s7 m8">
              <input class="col s12 m6" type="" name="" value="<?php echo ''; ?>"><br>
              <input class="col s12 m7" type="" name="" value="<?php echo ''; ?>"><br>
              <select class="browser-default col s12 m9">
                <option value="" disabled selected>Seleccone un uso CDFI</option>
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
              <input class="col s12 m8" type="" name="" value="<?php echo ''; ?>"><br>
            </div>
          </div><br><br><br><br><br><br><br>
        </div>
      </div>
      <!-- ----------------------------  FORMULARIO 2 Tabs  ---------------------------------------->
      <div  id="test-swipe-2" class="col s12"><br><br>
        <div class="row"><br>
            <!--    //////    BOTON QUE REDIRECCIONA AL FORMULARIO DE AGREGAR COMPRA    ///////   -->
            <a href="ventas_punto_venta.php" class="waves-effect waves-light btn pink left right">Agregar Venta<i class="material-icons prefix left">add</i></a>
            <table class="bordered centered highlight">
              <thead>
                <tr>
                  <th>CLAVE PRODUCTO/SERVICIO</th>
                  <th>CANTIDAD</th>
                  <th>CLAVE UNIDAD</th>
                  <th>UNIDAD</th>            
                  <th>DESCRIPCION</th>
                  <th>VALOR UNITARIO</th>
                  <th>IMPORTE</th>
                  <th>ACCION</th>
                </tr>
              </thead>
              <tbody id="VentasP">
              </tbody>
            </table>
        </div>
      </div>
      <!-- ----------------------------  FORMULARIO 3 Tabs  ---------------------------------------->
      <div  id="test-swipe-3" class="col s12"><br>        
        <div class="row"><br>
          <div class="col s12 m6">
            <label><h4>Método de Pago</h4></label>
            <select class="browser-default">
                <option value="" disabled selected>Seleccone:</option>
                <option value="PUE">PUE-Pago en una sola exhibición</option>
                <option value="PPD">G01-Pago en parcialidades o diferido</option>
            </select>
          </div>
          <div class="col s12 m6">
            <label><h4>Forma de Pago</h4></label>          
            <select class="browser-default">
                <option value="" disabled selected>Seleccone:</option>
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
  </div>
</body>
</main>
</html>