<?php
//VERIFICAMOS QUE SI NOS ENVIE POR POST EL ID DEL ARTICULO
if (isset($_GET['id']) == false) {
  ?>
  <script>    
    M.toast({html: "Regresando a home.", classes: "rounded"});
    setTimeout("location.href='home.php'", 800);
  </script>
  <?php
}else{
	$Venta = $_GET['id'];
?>
  <!DOCTYPE html>
	<html lang="en">
    <head>
    	<title>SIC | Venta N° <?php echo $Venta ?></title>
    	<?php 
	    //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
	    include('fredyNav.php');
	    //ARCHIVO QUE RESTRINGE A QUE SOLO ALGUNOS USUARIOS PUEDAN ACCEDER
	    include('../php/cobrador.php');
	    //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL USUARIO Y ASIGNAMOS EL ARRAY A UNA VARIABLE $datos_user
	    $Venta = $_GET['id'];
	    //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL USUARIO Y ASIGNAMOS EL ARRAY A UNA VARIABLE $datos_user
	    $user_id = $_SESSION['user_id'];
	    $datos_user = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM users WHERE user_id=$user_id"));
	    if ($datos_user['ventas'] == 0) {
	      ?>
	      <script>    
	        M.toast({html: "Permiso denegado.", classes: "rounded"});
	        M.toast({html: "Contacta a un Administrador.", classes: "rounded"});
	        setTimeout("location.href='ventas_punto_venta.php'", 1000);
	      </script>
	      <?php
	    }
	    ?>
		<script>
	    //FUNCION QUE HACE LA ACTUALIZACION DE LA CATEGORIA (SE ACTIVA AL PRECIONAR UN BOTON)
	    //FUNCION QUE HACE LA BUSQUEDA DE ARTICULOS (SE ACTIVA AL INICIAR EL ARCHIVO O AL ECRIBIR ALGO EN EL BUSCADOR)
      		function buscar_articulos(){
				//PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO EL TEXTO REQUERIDO Y LO ASIGNAMOS A UNA VARIABLE
				var texto = $("input#busquedaArticulo").val();
				//MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
				$.post("../php/control_ventas.php", {
					//Cada valor se separa por una ,
					accion: 13,
					texto: texto,
				}, function(mensaje){
				//SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
				$("#tablaArticulo").html(mensaje);
				});//FIN post
     		 }//FIN function

			function insertBusquedaArticulo(id,codigoArticulo, precio, descripcion){
				idArticulo = id;
				codigoArticulo= codigoArticulo;
				descripcionArticulo = descripcion;
				precioArticulo = precio;
				document.getElementById('id_articulo').value=idArticulo;
				document.getElementById('codigoP').value=codigoArticulo;
				document.getElementById('nombreP').value=descripcion;
				document.getElementById('precio_venta').value=precio;
    			M.updateTextFields();
				$('#modal_addArticulo').modal('close');
			}


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
			function showVentaGeneral() {
				//SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
				//MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
				$.post("../php/control_clientes.php", {
					//Cada valor se separa por una ,
					accion: 6,
					general: 1,
					}, function(mensaje) {
					//SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
					$("#resultado_info").html(mensaje);
					
				});  
			}; 



			 function tmp_articulos(insert){
	        if (insert) {
				
			      var id_art = $("input#id_articulo").val();
			      var cantidad = $("input#cantidadP").val();
			      var precio_venta = $("input#precio_venta").val();
			      if (id_art == '' || cantidad == '' || precio_venta == '') {
	          		M.toast({html: 'Seleccione un articulo', classes: 'rounded'});
			      }else{
		          //PEDIMOS VARIABLES Y CONDICIONES PARA INSERTAR ARTICULO A TMP
		          M.toast({html: 'Insertar articulo N° '+id_art, classes: 'rounded'});
		          //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
		          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_ventas.php"
		          $.post("../php/control_ventas.php", {
		            //Cada valor se separa por una ,
		              accion: 4,
		              insert: insert,
		              id_art: id_art,
		              cantidad: cantidad,
		              precio_venta: precio_venta,
		              id_venta: <?php echo $Venta; ?>,
		            }, function(mensaje) {
		                //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_ventas.php"
		                $("#tablaArticuloVenta").html(mensaje);
		            }); 
		        }//FIN else
	        }else{
	          //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
	          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_ventas.php"
	          $.post("../php/control_ventas.php", {
	            //Cada valor se separa por una ,
	              accion: 4,
	              insert: insert,
		            id_venta: <?php echo $Venta; ?>,
	            }, function(mensaje) {
	                //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_ventas.php"
	                $("#tablaArticuloVenta").html(mensaje);
	            }); 
	        }//FIN ELSE insert
	      }// FIN function

	      //FUNCION QUE BORRA LOS ARTICULOS TMP (SE ACTIVA AL INICIAR EL BOTON BORRAR)
	      function borrar_lista_articulo(id){
	        var answer = confirm("Deseas eliminar el artículo N°"+id+" de la lista ?");
	        if (answer) {
	          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_ventas.php"
	          $.post("../php/control_ventas.php", {
	            //Cada valor se separa por una ,
	            accion: 7,
	            id: id,
		          id_venta: <?php echo $Venta; ?>,
	          }, function(mensaje) {
	            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_ventas.php"
	            $("#tablaArticuloVenta").html(mensaje);
	          }); //FIN post
	        }//FIN IF
	      };//FIN function

	      //FUNCION QUE BORRA TODOS LOS ARTICULOS DE TMP (SE ACTIVA AL INICIAR EL BOTON BORRAR)
	      function borrar_lista_all(){
	        var answer = confirm("Deseas cancelar la venta <?php echo $Venta; ?>?");
	        if (answer) {
	          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_ventas.php"
	          $.post("../php/control_ventas.php", {
	            //Cada valor se separa por una ,
	            accion: 8,
		          id_venta: <?php echo $Venta; ?>,
	          }, function(mensaje) {
	            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_ventas.php"
	            $("#tablaArticuloVenta").html(mensaje);
	          }); //FIN post
	        }//FIN IF
	      };//FIN function

	      function modal_venta() {
	      	var exist = $("input#mayor_exist").val();
	      	if (exist) {
	          M.toast({html: '! NO SE PUEDE REALIZAR LA VENTA ¡', classes: 'rounded'});
	          M.toast({html: 'Alguno de los articulos superan su existencia', classes: 'rounded'});
	      	}else{
	      		//MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "modal_venta.php" PARA MOSTRAR EL MODAL
		        $.post("modal_venta.php", {
		          //Cada valor se separa por una ,
		            id_venta: <?php echo $Venta; ?>,
		          }, function(mensaje){
		              //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "modal_venta.php"
		              $("#modal").html(mensaje);
		        });//FIN post
		      }//FIN else
	      }

	      //FUNCION Calcula el Cambio
	      function cambio(){
	        //RECIBIMOS LOS VALORES DE LOS INPUTS AFECTADOS
	        var total = $("input#total").val();
	        var efectivo = $("input#efectivoV").val();
	        var credito = $("input#creditoV").val();
	        var banco = $("input#bancoV").val(); 
	        var Efectivo = parseFloat(efectivo);
	        var Credito = parseFloat(credito);
	        var Banco = parseFloat(banco);
	        var Total = parseFloat(total);
	        document.getElementById("cambio").value ='$'+((-1)*(Total-Efectivo-Banco-Credito)).toFixed(2);
	      }// FIN function

	      function insert_venta(){
	      	var efectivo = $("input#efectivoV").val();
	        var credito = $("input#creditoV").val();
	        var banco = $("input#bancoV").val(); 
	        var cliente = $("input#cliente").val(); 

			if (typeof cliente ==='undefined'){
				M.toast({html: 'Seleccione un Cliente o venta general.', classes: 'rounded'});
			}else{

				if (efectivo > 0) {
					tipo_cambio = 'Efectivo';
					cantidadPago = efectivo;
				}else if (credito > 0) {
					tipo_cambio = 'Credito';
					cantidadPago = credito;
				}else if (banco > 0) {
					tipo_cambio = 'Banco';
					cantidadPago = banco;
				}

				if(document.getElementById('pago').checked==true){
					var pago = 1;
				}else{
					var pago = 0;
					tipo_cambio = 'Pendiente';
					cantidadPago = 0;
				}

				if (efectivo == 0 && credito== 0 && banco== 0 && pago) {
				M.toast({html: 'Ingrese una forma de pago.', classes: 'rounded'});
				}else{
					//MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_ventas.php"
				$.post("../php/control_ventas.php", {
					//Cada valor se separa por una ,
					accion: 0,
					cliente: cliente,
					pago: pago,
					tipo_cambio: tipo_cambio,
					cantidadPago: cantidadPago,
						id_venta: <?php echo $Venta; ?>,
				}, function(mensaje) {
					//SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_ventas.php"
					$("#tablaArticuloVenta").html(mensaje);
				}); //FIN post
				}
			}	
	      }// FIN function

      	//FUINCION QUE PAUSARA LA VENTA
		  	function pausar_venta() {
		      //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_ventas.php"
		      $.post("../php/control_ventas.php", {
		        //Cada valor se separa por una ,
		          accion: 5,
		          id_venta: <?php echo $Venta; ?>,
		      }, function(mensaje) {
		          //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION  "control_ventas.php"
		          $("#modal").html(mensaje);
		      });  
		    };
	    </script>
    </head>
    <body  onload="tmp_articulos(0)">
    	<!-- DENTRO DE ESTE DIV VA TODO EL CONTENIDO Y HACE QUE SE VEA AL CENTRO DE LA PANTALLA.-->
	    <div class="container"><br><br>
	    	<!-- CREAMOS UN DIV EL CUAL TENGA id = "modal"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
      	<div id="modal"></div>
	      <!--    //////    TITULO    ///////   -->
	      <div class="row" >
	      	<ul class="collection">
			      <li class="collection-item indigo"><b class="white-text">VENTA - Folio N° <?php echo substr(str_repeat(0, 5).$Venta, - 6); ?></b></li>
			    </ul>
	      </div>
	      <div class="row">
          <div class="input-field col s12 m3 l3">
		 	 <a href="#modal_addClientes" class="waves-effect waves-light btn-small modal-trigger  cyan darken-4 right">Buscar cliente<i class="material-icons left">search</i></a>
          </div> 
		  <div class="input-field col s12 m6 l6">
		 	 <a onclick="showVentaGeneral();" class="waves-effect waves-light btn-small modal-trigger  cyan darken-4 right">Venta general<i class="material-icons left">shopping_cart</i></a>
          </div> 
          <div class="col s12 m12 l12 center">
            <!-- CREAMOS UN DIV EL CUAL TENGA id = "resultado_info"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
            <div id="resultado_info"></div>
          </div>
        </div>
        <hr>
        <div id="info_articulo">
		      <div class="row">
		      	<div class="input-field col s6 m3">
	              <i class="material-icons prefix">local_offer</i>
				  <input id="id_articulo" type="hidden" class="validate" data-length="30" required>
				  <input id="codigoP" type="text" class="validate" data-length="30" required>
	              <label for="codigoP">Código Producto:</label>
	          </div>
	          <div class="input-field col s6 m3">
	              <i class="material-icons prefix">edit</i>
	              <input id="nombreP" type="text" class="validate" data-length="30" required>
	              <label for="nombreP">Descripción:</label>
	          </div>
	          <div class="input-field col s4 m2">
	              <i class="material-icons prefix">filter_2</i>
	              <input id="cantidadP" type="number" class="validate" data-length="30" required value="1">
	              <label for="cantidadP">Cantidad:</label>
	          </div>
	          <div class="input-field col s4 m2">
	              <i class="material-icons prefix">monetization_on</i>
	              <input id="precio_venta" type="number" class="validate" data-length="30" required>
	              <label for="precio_venta">Precio Venta:</label>
	          </div>
	          <input type="hidden" id="id_articulo" value="">
	          <div class="col s4 m2"><br>
	          	<!-- BOTON QUE MANDA LLAMAR EL SCRIPT PARA QUE EL SCRIPT HAGA LO QUE LA FUNCION CONTENGA -->
				  <a href="#modal_addArticulo" class="waves-effect waves-light btn-small modal-trigger indigo right">Buscar<i class="material-icons left">add</i></a>
	          </div>	        
		      </div>
		    </div> 
		      <hr>
		      <!-- BOTON QUE MANDA LLAMAR EL SCRIPT PARA QUE EL SCRIPT HAGA LO QUE LA FUNCION CONTENGA -->
	        <a onclick="tmp_articulos(1);" class="waves-effect waves-light btn pink right"><i class="material-icons right">add</i>Agregar Producto</a>
	        <a onclick="pausar_venta(<?php echo $id; ?>);" class="waves-effect waves-light btn indigo lighten-5 indigo-text"><b><i class="material-icons right">pause</i>Pausar Venta</b></a>
        <div id="tablaArticuloVenta"></div>
      </div>	       	
    </body>
    </html>
<?php
}