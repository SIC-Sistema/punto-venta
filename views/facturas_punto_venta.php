<html>
<head>
	<title>SIC | Facturas</title>
  <?php 
  include('fredyNav.php');
  include('../php/cobrador.php');
  date_default_timezone_set('America/Mexico_City');
  $Fecha_hoy = date('Y-m-d');?>
  <script>
    //FUNCION QUE BORRA LOS COMPRAS (SE ACTIVA AL INICIAR EL BOTON BORRAR)
      function borrar_venta_pv(id){
        var answer = confirm("Deseas eliminar la venta N°"+id+"?");
        if (answer) {
          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_facturas.php"
          $.post("../php/control_facturas.php", {
              //Cada valor se separa por una ,
              id: id,
              accion: 3,
            }, function(mensaje) {
              //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_facturas.php"
              $("#cancelar").html(mensaje);
            }); //FIN post
        }//FIN IF
      };//FIN function

    //FUNCION QUE BORRA TODOS LOS ARTICULOS DE TMP (SE ACTIVA AL INICIAR EL BOTON BORRAR)
    function cancelar_factura(folio){
      var answer = confirm("Deseas cancelar la factura "+folio+"?");
      if (answer) {
        //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_facturas.php"
        $.post("../php/control_facturas.php", {
          //Cada valor se separa por una ,
          accion: 6,
          folio: folio,
        }, function(mensaje) {
          //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_facturas.php"
          $("#cancelar").html(mensaje);
        }); //FIN post
      }//FIN IF
    };//FIN function
    
    //FUNCION QUE HACE LA BUSQUEDA DE ALMACENES (SE ACTIVA AL INICIAR EL ARCHIVO O AL ECRIBIR ALGO EN EL BUSCADOR)
    function buscarFacturasP(){
      //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO EL TEXTO REQUERIDO Y LO ASIGNAMOS A UNA VARIABLE
      var texto = $("input#busqueda1").val();
      //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_facturas.php"
      $.post("../php/control_facturas.php", {
        //Cada valor se separa por una ,
          texto: texto,
          accion: 4,
        }, function(mensaje){
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_facturas.php"
            $("#FacturasP").html(mensaje);
      });//FIN post
    }//FIN function}

    //FUNCION QUE HACE LA BUSQUEDA DE ALMACENES (SE ACTIVA AL INICIAR EL ARCHIVO O AL ECRIBIR ALGO EN EL BUSCADOR)
    function buscarFacturasR(){
      //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO EL TEXTO REQUERIDO Y LO ASIGNAMOS A UNA VARIABLE
      var texto = $("input#busqueda2").val();
      //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/controlventas.php"
      $.post("../php/control_facturas.php", {
        //Cada valor se separa por una ,
          texto: texto,
          accion: 5,
        }, function(mensaje){
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_facturas.php"
            $("#FacturasR").html(mensaje);
      });//FIN post
    }//FIN function
  </script>
</head>
<main>
<body onload="buscarFacturasP(); buscarFacturasR();">
  <div class="container">
    <!-- CREAMOS UN DIV EL CUAL TENGA id = "cancelar"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
    <div id="cancelar"></div>
    <!-- CREAMOS UN DIV EL CUAL TENGA id = "modal"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
    <div id="modal"></div>
    <div class="row" ><br>
      <h3 class="hide-on-med-and-down">Facturas</h3>
      <h5 class="hide-on-large-only">Facturas</h5>
    </div>
    <div class="row">
    <!-- ----------------------------  TABs o MENU  ---------------------------------------->
      <div class="col s12">
        <ul id="tabs-swipe-demo" class="tabs">
          <li class="tab col s6"><a class="active black-text" href="#test-swipe-1">FACTURAS EN PROCESO</a></li>
          <li class="tab col s6"><a class="black-text" href="#test-swipe-2">FACTURAS REALIZADAS</a></li>
        </ul>
      </div>
      <!-- ----------------------------  FORMULARIO 1 Tabs  ---------------------------------------->
      <div  id="test-swipe-1" class="col s12"><br><br>
        <!--    //////    INPUT DE LA BUSQUEDA    ///////   -->   
        <div class="input-field col s12 m6 l6 right">
          <i class="material-icons prefix">search</i>
          <input id="busqueda1" name="busqueda1" type="text" class="validate" onkeyup="buscarFacturasP();">
          <label for="busqueda1">Buscar: (N° Factura, N° Cliente)</label>
        </div>
        <div class="row"><br>
            <table class="bordered centered highlight">
              <thead>
                <tr>
                  <th>N°</th>
                  <th>Cliente</th>
                  <th>Uso CDFI</th>
                  <th>Regimen Fiscal</th>            
                  <th>Metodo Pago</th>
                  <th>Forma Pago</th>
                  <th>Total</th>
                  <th>Usuario</th>
                  <th>Detalles</th>
                  <th>Cancelar</th>
                </tr>
              </thead>
              <tbody id="FacturasP">
              </tbody>
            </table>
        </div>
      </div>
      <!-- ----------------------------  FORMULARIO 2 Tabs  ---------------------------------------->
      <div  id="test-swipe-2" class="col s12"><br><br>
        <!--    //////    INPUT DE LA BUSQUEDA    ///////   -->   
        <div class="input-field col s12 m6 l6 right">
          <i class="material-icons prefix">search</i>
          <input id="busqueda2" name="busqueda2" type="text" class="validate" onkeyup="buscarFacturasR();">
          <label for="busqueda2">Buscar: (N° Factura, N° Cliente)</label>
        </div>
        <div class="row"><br>
            <table class="bordered centered highlight">
              <thead>
                <tr>
                  <th>N°</th>
                  <th>Cliente</th>
                  <th>Uso CDFI</th>
                  <th>Regimen Fiscal</th>            
                  <th>Metodo Pago</th>
                  <th>Forma Pago</th>
                  <th>Total</th>
                  <th>Usuario</th>
                  <th>Imprimir</th>
                  <th>Timbrar</th>
                </tr>
              </thead>
              <tbody id="FacturasR">
              </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
</body>
</main>
</html>