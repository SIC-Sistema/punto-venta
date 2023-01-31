<html>
<head>
	<title>SIC | Ventas</title>
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
      //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_ventas.php"
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
      //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_ventas.php"
      $.post("../php/control_ventas.php", {
        //Cada valor se separa por una ,
          texto: texto,
          accion: 10,
        }, function(mensaje){
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_ventas.php"
            $("#VentasP").html(mensaje);
      });//FIN post
    }//FIN function
    //FUNCION QUE HACE LA BUSQUEDA DE ALMACENES (SE ACTIVA AL INICIAR EL ARCHIVO O AL ECRIBIR ALGO EN EL BUSCADOR)
    function buscarVentasSIN(){
      //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO EL TEXTO REQUERIDO Y LO ASIGNAMOS A UNA VARIABLE
      var texto = $("input#busqueda3").val();
      //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_ventas.php"
      $.post("../php/control_ventas.php", {
        //Cada valor se separa por una ,
          texto: texto,
          accion: 12,
        }, function(mensaje){
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_ventas.php"
            $("#VentasSIN").html(mensaje);
      });//FIN post
    }//FIN function
    //FUNCION QUE MOSTRARA EL MODAL PARA ESCOGER FACRTURA O NUEVA
    function facturar(venta){
      //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "modal_factura.php" PARA MOSTRAR EL MODAL
        $.post("modal_factura.php", {
          //Cada valor se separa por una ,
            venta:venta,
          }, function(mensaje){
              //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "modal_factura.php"
              $("#modal").html(mensaje);
      });//FIN post
    }
    function facturar_update(id_venta){
      //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
      var factura = $("select#factura").val();

      //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_facturas.php"
      $.post("../php/control_facturas.php", {
        //Cada valor se separa por una ,
          accion: 0,
          venta: id_venta,
          nueva: factura,
        }, function(mensaje) {
          //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_facturas.php"
          $("#modal").html(mensaje);
      });
    }
    function devolucion_venta_pv(id) {
      //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "modal_devolucion.php" PARA MOSTRAR EL MODAL
      $.post("modal_devolucion.php", {
        //Cada valor se separa por una ,
          id_venta: id,
        }, function(mensaje){
          //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "modal_devolucion.php"
          $("#modal").html(mensaje);
      });//FIN post
    }
    function realizar_devolucion(bandera, id_venta) {
      var answer = confirm("Reviso correctamente la devolucion?");
      if (answer) {
        array = '';/// ARRA DONDE SE ALMACENARA LOS PRODUCTOS A DEVOLVER
        mayor = false;//CONDICION DE SI SOBREPASA LA CANTIDAD
        menor = false;//CONDICION DONDE  SI HAY ALGUNO MENOS O IGUAL A 0
        //RECORREMOS CON UN CICLO LA LISTA DE PRODUCTOS
        for(var i=1;i<=bandera;i++){
            //VERIFICAMOS QUE PORDUCTOS FUERON SELECCIONADOS
            if(document.getElementById('select'+i).checked==true){
                var cantidadVenta = $("input#cantidadA"+i).val();
                var cantidadDevolver = $("input#cantidadD"+i).val();
                var id = $("input#id"+i).val();
                if (array != '') {    array += ', ';     }// SEPARAMOS POR  , CADA PRODUCTO
                if (cantidadVenta < cantidadDevolver) {    mayor = true;      }//CONDICION DE SI SOBREPASA LA CANTIDAD
                if (cantidadDevolver <= 0) {    menor = true;     }//CONDICION DONDE  SI HAY ALGUNO MENOS O IGUAL A 0
                array += id+'-'+cantidadDevolver;// AGREGAMOS EL PRODUCTO AL ARRAY
            }
        }  
        //M.toast({html: ''+array, classes: 'rounded'});
        if (mayor) { 
          M.toast({html: 'La cantidad a devolver no puede superar a la cantidad que se vendio', classes: 'rounded'});
        }else  if (menor) { 
          M.toast({html: 'La cantidad a devolver debe ser mayor a 0', classes: 'rounded'});
        }else if(array == ''){
          M.toast({html: 'Seleccione al menos un articulos de la lista', classes: 'rounded'});
        }else{
          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_ventas.php"
          $.post("../php/control_ventas.php", {
            //Cada valor se separa por una ,
              id_venta: id_venta,
              array: array,
              accion: 11,
            }, function(mensaje){
                //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_ventas.php"
                $("#modal").html(mensaje);
          });//FIN post
        }// FIN ELSE
      }// FIN PREGUNTA reviso
    }//FIN function
  </script>
</head>
<main>
<body onload="buscarVentasR(); buscarVentasP(); buscarVentasSIN();">
  <div class="container">
    <!-- CREAMOS UN DIV EL CUAL TENGA id = "cancelar"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
    <div id="cancelar"></div>
    <!-- CREAMOS UN DIV EL CUAL TENGA id = "modal"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
    <div id="modal"></div>
    <div class="row" ><br>
      <h3 class="hide-on-med-and-down">Ventas</h3>
      <h5 class="hide-on-large-only">Ventas</h5>
    </div>
    <div class="row">
    <!-- ----------------------------  TABs o MENU  ---------------------------------------->
      <div class="col s12">
        <ul id="tabs-swipe-demo" class="tabs">
          <li class="tab col s4"><a class="active black-text" href="#test-swipe-2">VENTAS PAUSADAS O EN PROCESO</a></li>
          <li class="tab col s4"><a class="black-text" href="#test-swipe-3">VENTAS PAGO PENDIENTE</a></li>
          <li class="tab col s4"><a class="black-text" href="#test-swipe-1">VENTAS REALIZADAS</a></li>
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
                  <th>Realizo</th>
                  <th>Detalles</th>
                  <th>Facturar</th>
                  <th>Devolución</th>
                  <th>Borrar</th>
                  <th>Imprimir</th>
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
                  <th>Realiza</th>
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
      <!-- ----------------------------  FORMULARIO 3 Tabs  ---------------------------------------->
      <div  id="test-swipe-3" class="col s12"><br><br>
        <!--    //////    INPUT DE LA BUSQUEDA    ///////   -->   
        <div class="input-field col s12 m6 l6 right">
          <i class="material-icons prefix">search</i>
          <input id="busqueda3" name="busqueda3" type="text" class="validate" onkeyup="buscarVentasSIN();">
          <label for="busqueda3">Buscar: (N° Venta, N° Cliente, Fecha (ej: 2022-10-26))</label>
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
                  <th>Realiza</th>
                  <th>Estatus</th>
                  <th>Ver</th>
                  <th>Cancelar</th>
                </tr>
              </thead>
              <tbody id="VentasSIN">
              </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
</body>
</main>
</html>