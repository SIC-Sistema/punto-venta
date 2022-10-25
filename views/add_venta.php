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
	        function buscar() {
	          //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO LA INFORMCION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
	          var textoCodigo = $("input#codigoP").val();//ej:LA VARIABLE "textoCodigo" GUARDAREMOS LA INFORMACION QUE ESTE EN INPUT QUE TENGA EL id = "codigoP"
	          var textoNombre = $("input#nombreP").val();//ej:LA VARIABLE "textoNombre" GUARDAREMOS LA INFORMACION QUE ESTE EN INPUT QUE TENGA EL id = "nombreP"
	          
	          // CREAMOS CONDICIONES QUE SI SE CUMPLEN MANDARA MENSAJES DE ALERTA EN FORMA DE TOAST
	          //SI SE CUMPLEN LOS IF QUIERE DECIR QUE NO PASA LOS REQUISITOS MINIMOS DE LLENADO...
	          if (textoNombre == "" && textoCodigo == "") {
	            M.toast({html: 'El campo Nombre y/o Codigo se encuentra vacío.', classes: 'rounded'});
	          }else{
	              //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
	              //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_ventas.php"
	              $.post("../php/control_ventas.php", {
	              //Cada valor se separa por una ,
	                  accion: 6,
	                  valorNombre: textoNombre,
	                  valorCodigo: textoCodigo,
	              }, function(mensaje) {
	                  //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_ventas.php"
	                  $("#info_articulo").html(mensaje);
	              }); 
	          }//FIN else CONDICIONES
	        };//FIN function 

	        //FUINCION QUE AL SELECCIONAR UN Cliente MUESTRA SU INFORMACION
		      function showContent() {
		        var textoCliente = $("select#cliente").val();

		        //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
		        //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_ventas.php"
		        $.post("../php/control_ventas.php", {
		          //Cada valor se separa por una ,
		            accion: 2,
		            cliente: textoCliente,
		          }, function(mensaje) {
		            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_ventas.php"
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

      function insert_venta() {
      	var exist = $("input#mayor_exist").val();
      	if (exist) {
          	M.toast({html: '! NO SE PUEDE REALIZAR LA VENTA ¡', classes: 'rounded'});
          	M.toast({html: 'Alguno de los articulos superan su existencia', classes: 'rounded'});
      	}else{
      		//MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "modal_almacen.php" PARA MOSTRAR EL MODAL
	        $.post("modal_venta.php", {
	          //Cada valor se separa por una ,
	            id: 1,
	            almacen: 1,
	            id_venta: <?php echo $Venta; ?>,
	          }, function(mensaje){
	              //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "modal_almacen.php"
	              $("#modal").html(mensaje);
	        });//FIN post
	      	}

      }
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
            <i class="material-icons prefix">people</i>
            <select id="cliente" name="cliente" class="validate" onchange="javascript:showContent()">
              <!--OPTION PARA QUE LA SELECCION QUEDE POR DEFECTO VACIA-->
              <option value="0" select>Seleccione un cliente</option>
              <option value="0">N/A</option>
                <?php 
                  // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
                  $consulta = mysqli_query($conn,"SELECT * FROM `punto-venta_clientes`");
                  //VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
                  if (mysqli_num_rows($consulta) == 0) {
                    echo '<script>M.toast({html:"No se encontraron proveedores.", classes: "rounded"})</script>';
                  } else {
                    //RECORREMOS UNO A UNO LOS ARTICULOS CON EL WHILE
                    while($cliente = mysqli_fetch_array($consulta)) {
	                    //Output
	                    ?>                      
	                    <option value="<?php echo $cliente['id'];?>"><?php echo $cliente['nombre'];?></option>-->
	                    <?php
                    }//FIN while
                  }//FIN else
                ?>
            </select>
          </div> 
          <div class="col s12 m9 l9 center">
            <!-- CREAMOS UN DIV EL CUAL TENGA id = "resultado_info"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
            <div id="resultado_info"><h5><b>VENTA AL PUBLICO</b></h5></div>
          </div>
        </div>
        <hr>
        <div id="info_articulo">
		      <div class="row">
		      	<div class="input-field col s6 m3">
	              <i class="material-icons prefix">local_offer</i>
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
	        		<a onclick="buscar();" class="waves-effect waves-light btn indigo lighten-5 indigo-text right"><b><i class="material-icons right">search</i>Buscar</b></a>
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