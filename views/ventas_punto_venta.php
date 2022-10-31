<html>
<head>
	<title>SIC | Ventas</title>
  <?php 
  include('fredyNav.php');
  include('../php/cobrador.php');
  date_default_timezone_set('America/Mexico_City');
  $Fecha_hoy = date('Y-m-d');
  ?>
  <script>
    //FUNCION QUE BORRA TODOS LOS ARTICULOS DE TMP (SE ACTIVA AL INICIAR EL BOTON BORRAR)
    function borrar_lista_all(venta){
      var answer = confirm("Deseas cancelar la venta "+venta+"?");
      if (answer) {
        //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_ventas.php"
        $.post("../php/control_ventas.php", {
          //Cada valor se separa por una ,
          accion: 8,
          id_venta: venta,
        }, function(mensaje) {
          //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_ventas.php"
          $("#cancelar").html(mensaje);
        }); //FIN post
      }//FIN IF
    };//FIN function
    //FUNCION QUE HACE LA BUSQUEDA DE ALMACENES (SE ACTIVA AL INICIAR EL ARCHIVO O AL ECRIBIR ALGO EN EL BUSCADOR)
    function buscarVentasR(){
      //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO EL TEXTO REQUERIDO Y LO ASIGNAMOS A UNA VARIABLE
      var texto = $("input#busqueda1").val();
      //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/controlventas.php"
      $.post("../php/control_ventas.php", {
        //Cada valor se separa por una ,
          texto: texto,
          accion: 9,
        }, function(mensaje){
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_ventas.php"
            $("#VentasR").html(mensaje);
      });//FIN post
    }//FIN function}

    //FUNCION QUE HACE LA BUSQUEDA DE ALMACENES (SE ACTIVA AL INICIAR EL ARCHIVO O AL ECRIBIR ALGO EN EL BUSCADOR)
    function buscarVentasP(){
      //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO EL TEXTO REQUERIDO Y LO ASIGNAMOS A UNA VARIABLE
      var texto = $("input#busqueda2").val();
      //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/controlventas.php"
      $.post("../php/control_ventas.php", {
        //Cada valor se separa por una ,
          texto: texto,
          accion: 10,
        }, function(mensaje){
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_ventas.php"
            $("#VentasP").html(mensaje);
      });//FIN post
    }//FIN function
  </script>
</head>
<main>
<body onload="buscarVentasR(); buscarVentasP();">
  <div class="container">
    <!-- CREAMOS UN DIV EL CUAL TENGA id = "cancelar"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
    <div id="cancelar"></div>
    <div class="row" ><br>
      <h3 class="hide-on-med-and-down">Ventas</h3>
      <h5 class="hide-on-large-only">Ventas</h5>
    </div>
    <div class="row">
    <!-- ----------------------------  TABs o MENU  ---------------------------------------->
      <div class="col s12">
        <ul id="tabs-swipe-demo" class="tabs">
          <li class="tab col s6"><a class="active black-text" href="#test-swipe-1">VENTAS REALIZADAS</a></li>
          <li class="tab col s6"><a class="black-text" href="#test-swipe-2">VENTAS PAUSADAS O EN PROCESO</a></li>
        </ul>
      </div>
      <!-- ----------------------------  FORMULARIO 1 Tabs  ---------------------------------------->
      <div  id="test-swipe-1" class="col s12"><br><br>
        <!--    //////    INPUT DE LA BUSQUEDA    ///////   -->   
        <div class="input-field col s12 m6 l6 right">
          <i class="material-icons prefix">search</i>
          <input id="busqueda1" name="busqueda1" type="text" class="validate" onkeyup="buscarVentasR();">
          <label for="busqueda1">Buscar: (N° Venta, N° Cliente, Fecha (ej: 2022-10-26))</label>
        </div>
        <div class="row"><br>
            <table class="bordered centered highlight">
              <thead>
                <tr>
                  <th>N°</th>
                  <th>Cliente</th>
                  <th>Fecha y Hora</th>
                  <th>Cambio</th>            
                  <th>Total</th>
                  <th>Usuario</th>
                  <th>Estatus</th>
                  <th>Detalles</th>
                  <th>Borrar</th>
                </tr>
              </thead>
              <tbody id="VentasR">
              </tbody>
            </table>
        </div>
      </div>
      <!-- ----------------------------  FORMULARIO 2 Tabs  ---------------------------------------->
      <div  id="test-swipe-2" class="col s12"><br><br>
        <!--    //////    INPUT DE LA BUSQUEDA    ///////   -->   
        <div class="input-field col s12 m6 l6 right">
          <i class="material-icons prefix">search</i>
          <input id="busqueda2" name="busqueda2" type="text" class="validate" onkeyup="buscarVentasP();">
          <label for="busqueda2">Buscar: (N° Venta, N° Cliente, Fecha (ej: 2022-10-26))</label>
        </div>
        <div class="row"><br>
            <table class="bordered centered highlight">
              <thead>
                <tr>
                  <th>N°</th>
                  <th>Cliente</th>
                  <th>Fecha y Hora</th>
                  <th>Cambio</th>            
                  <th>Total</th>
                  <th>Usuario</th>
                  <th>Estatus</th>
                  <th>Ver</th>
                  <th>Cancelar</th>
                </tr>
              </thead>
              <tbody id="VentasP">
              </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
</body>
</main>
</html>