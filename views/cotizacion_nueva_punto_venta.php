<html>
  <head>
  	<title>SIC | Registrar Cotización</title>
    <?php 
    //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
    include('fredyNav.php');
    //ARCHIVO QUE RESTRINGE A QUE SOLO ALGUNOS USUARIOS PUEDAN ACCEDER
    include('../php/cobrador.php');
    //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL USUARIO Y ASIGNAMOS EL ARRAY A UNA VARIABLE $datos_user
    $user_id = $_SESSION['user_id'];
    $datos_user = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM users WHERE user_id=$user_id"));
    $id = $datos_user['ventas'];
    $id_alamcen = $datos_user['almacen'];
    $datos_almacen = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM punto_venta_almacenes WHERE id=$id"));
    if ($id_alamcen == 0) {
      ?>
      <script>    
        M.toast({html: "Permiso denegado, no tiene almacen asignado", classes: "rounded"});
        M.toast({html: "Contacta a un Administrador.", classes: "rounded"});
        setTimeout("location.href='cotizaciones_punto_venta.php'", 1000);
      </script>
      <?php
    }
    ?>
    <script>
       //FUNCION QUE HACE LA BUSQUEDA DE ARTICULOS (SE ACTIVA AL INICIAR EL ARCHIVO O AL ECRIBIR ALGO EN EL BUSCADOR)
			function buscar_clientes(){
        //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO EL TEXTO REQUERIDO Y LO ASIGNAMOS A UNA VARIABLE
        var texto = $("input#busquedaClientes").val();
        //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
        $.post("../php/control_clientes.php", {
          //Cada valor se separa por una ,
					accion: 4,
					texto: texto,
				}, function(mensaje){
					//SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
					$("#tablaClientes").html(mensaje);
				});//FIN post
			}//FIN function
    
    //FUINCION QUE AL SELECCIONAR UN CLIENTE MUESTRA SU INFORMACION
		  function showContent(id_cliente) {
        idCliente = id_cliente;
        //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
			  //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
			  $.post("../php/control_clientes.php", {
				  //Cada valor se separa por una ,
				  accion: 5,
				  cliente: idCliente,
				}, function(mensaje) {
				//SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
				  $("#resultado_info").html(mensaje);
				  $('#modal_addClientes').modal('close');
			  });  
		  };   

     //FUINCION QUE AL SELECCIONAR UN CLIENTE MUESTRA SU INFORMACION
			function showSinCliente() {
				//SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
				//MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
				$.post("../php/control_clientes.php", {
					//Cada valor se separa por una ,
					accion: 6,
					general: 2,
					}, function(mensaje) {
					//SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
					$("#resultado_info").html(mensaje);
				});  
			}; 
 

      function tmp_articulos(id, insert,id_art=0){
        if (insert) {
          //PEDIMOS VARIABLES Y CONDICIONES PARA INSERTAR ARTICULO A TMP
          M.toast({html: 'Insertar articulo N° '+id_art, classes: 'rounded'});
          //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
          $.post("../php/control_cotizacion.php", {
            //Cada valor se separa por una ,
              accion: 4,
              insert: insert,
              id: id,
              id_art: id_art,
            }, function(mensaje) {
                //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
                $("#articulosCompra").html(mensaje);
            }); 
        }else{
          //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
          $.post("../php/control_cotizacion.php", {
            //Cada valor se separa por una ,
              accion: 4,
              insert: insert,
              id: id,
            }, function(mensaje) {
                //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
                $("#articulosCompra").html(mensaje);
            }); 
        }//FIN ELSE insert
      }// FIN function

      //FUNCION QUE BORRA LOS ARTICULOS TMP (SE ACTIVA AL INICIAR EL BOTON BORRAR)
      function borrar_lista_articulo(id){
        var answer = confirm("Deseas eliminar el artículo N°"+id+" de la lista ?");
        if (answer) {
          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
          $.post("../php/control_cotizacion.php", {
            //Cada valor se separa por una ,
            accion: 7,
            id: id,
          }, function(mensaje) {
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
            $("#articulosCompra").html(mensaje);
          }); //FIN post
        }//FIN IF
      };//FIN function

      //FUNCION QUE BORRA TODOS LOS ARTICULOS DE TMP (SE ACTIVA AL INICIAR EL BOTON BORRAR)
      function borrar_lista_all(usuario){
        var answer = confirm("Deseas cancelar la cotizacion?");
        if (answer) {
          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
          $.post("../php/control_cotizacion.php", {
            //Cada valor se separa por una ,
            accion: 8,
            usuario: usuario,
          }, function(mensaje) {
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
            $("#articulosCompra").html(mensaje);
          }); //FIN post
        }//FIN IF
      };//FIN function

      //FUNCION QUE HACE LA BUSQUEDA DE ARTICULOS (SE ACTIVA AL INICIAR EL ARCHIVO O AL ECRIBIR ALGO EN EL BUSCADOR)
      function buscar_articulos(){
        
        //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO EL TEXTO REQUERIDO Y LO ASIGNAMOS A UNA VARIABLE
        var texto = $("input#busquedaArticulo").val();
        //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_cotizacion.php"
        $.post("../php/control_cotizacion.php", {
            //Cada valor se separa por una ,
            accion: 6,
            texto: texto,
          }, function(mensaje){
              //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_cotizacion.php"
              $("#tablaArticulo").html(mensaje);
        });//FIN post
      }//FIN function

      //FUNCION QUE MODIFICARA LOS VALORES DE LOS TOTALES
      function totales(id_art, id_usuario){
        //RECIBIMOS LOS VALORES DE LOS INPUTS AFECTADOS
        var CantidadA_Aux = $("input#cantidadA"+id_art).val();
        var PrecioC_Aux = $("input#precio_compra"+id_art).val();
        var Total_Aux = $("input#totalCompra").val();        
        var Importe_Aux = $("input#importe"+id_art).val();
        var CantidadA = parseFloat(CantidadA_Aux);
        var PrecioC = parseFloat(PrecioC_Aux);
        var Importe = parseFloat(Importe_Aux); 
        var Total = parseFloat(Total_Aux);

        //MODIFICAMOS LOS VALPORES DE LOS INPUTS EN CUANTO CAMBIE ALGUN VALOR
        document.getElementById("importe"+id_art).value = (PrecioC*CantidadA).toFixed(2);          
        document.getElementById("subtotal").value =((Total+((PrecioC*CantidadA)-Importe))-((Total+((PrecioC*CantidadA)-Importe))*0.16)).toFixed(2);
        document.getElementById("impuesto").value =((Total+((PrecioC*CantidadA)-Importe))*0.16).toFixed(2);
        document.getElementById("totalCompra").value =(Total+((PrecioC*CantidadA)-Importe)).toFixed(2);

        //REALIZAREMOS LOS CAMBIOS EN LA BASE DE DATOS
        //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
        //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
        $.post("../php/control_cotizacion.php", {
            //Cada valor se separa por una ,
            accion: 5,
            valorIdArt: id_art,
            valorIdUs: id_usuario,
            valorCantidadA: CantidadA,
            valorPrecioU: PrecioC,
          }, function(mensaje){
              //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
              $("#resultado_insert").html(mensaje);
        });//FIN post
      }

      //FUNCION QUE HACE LA INSERCION DEL ARTICULO (SE ACTIVA AL PRECIONAR UN BOTON)
      function crear_cotizacion() {
        var textoCliente = $("input#cliente").val();
        // CREAMOS CONDICIONES QUE SI SE CUMPLEN MANDARA MENSAJES DE ALERTA EN FORMA DE TOAST
        //SI SE CUMPLEN LOS IF QUIERE DECIR QUE NO PASA LOS REQUISITOS MINIMOS DE LLENADO...
        if (typeof textoCliente === 'undefined') {
          M.toast({html: 'Seleccione un Cliente o pulse SIN CLIENTE.', classes: 'rounded'});
        }else{
          //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_cotizacion.php"
          $.post("../php/control_cotizacion.php", {
            //Cada valor se separa por una ,
              accion: 0,
              valorCliente: textoCliente,
            }, function(mensaje) {
                //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
                $("#resultado_insert").html(mensaje);
            }); 
        }//FIN else CONDICIONES
      };//FIN function 
    </script>
  </head>
  <main>
  <body onload="tmp_articulos(<?php echo $user_id;?>,0)">
    <!-- DENTRO DE ESTE DIV VA TODO EL CONTENIDO Y HACE QUE SE VEA AL CENTRO DE LA PANTALLA.-->
    <div class="container" >
      <!--    //////    TITULO    ///////   -->
      <div class="row" >
        <h3 class="hide-on-med-and-down">Nueva Cotización</h3>
        <h5 class="hide-on-large-only">Nueva Cotización</h5>
      </div>
      <div class="row" >
      <!-- CREAMOS UN DIV EL CUAL TENGA id = "resultado_insert"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
      <div class="row" id="resultado_insert"></div>
      <!-- FORMULARIO EL CUAL SE MUETRA EN PANTALLA .-->
      <form class="row col s12" name="formCotizacion">
        <!-- CAJA DE SELECCION DE CLIENTES -->
        <hr>
        <div class="row">
            <div class="input-field col s12 m3 l3">
            <a href="#modal_addClientes" class="waves-effect waves-light btn-small modal-trigger  cyan darken-4 right">Buscar cliente<i class="material-icons left">search</i></a>
            </div> 
            <div class="input-field col s12 m3 l3">
              <a onclick="showSinCliente();" class="waves-effect waves-light btn-small modal-trigger  cyan darken-4 right">Sin cliente<i class="material-icons left">shopping_cart</i></a>
            </div>  
              <div id="infoCliente" class="col s12 m12 l12"><br>
                <!-- CREAMOS UN DIV EL CUAL TENGA id = "resultado_info"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
                <div id="resultado_info"></div>
              </div>
            </div>
            <hr>
            <div  class="row">
              <div class="col s12">
                <h4>Almacen N° <?php echo $datos_user['almacen'];?> - <?php echo $datos_almacen['nombre'];?></h4>
              </div>
            </div>
            <hr>            
            <a href="#modal_addArticulo" class="waves-effect waves-light btn-small modal-trigger pink right">Agregar Artículo<i class="material-icons left">add</i></a><br><br>
            <!-- CREAMOS UN DIV EL CUAL TENGA id = "articulosCompra"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
            <div class="row" id="articulosCompra">
            </div>
        </form>
      </div> 
    </div><br>
  </body>
  </main>
</html>