<?php
 //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
include('fredyNav.php');
//DEFINIMOS LA ZONA  HORARIA
date_default_timezone_set('America/Mexico_City');
$id_user = $_SESSION['user_id'];// ID DEL USUARIO LOGEADO
$Fecha_hoy = date('Y-m-d');// FECHA ACTUAL
$sql = "INSERT INTO `punto_venta_ventas` (fecha, usuario) VALUES ('$Fecha_hoy', $id_user)";
//VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
if(mysqli_query($conn, $sql)){
	echo '<script >M.toast({html:"Nueva Venta.", classes: "rounded"})</script>';
    #SELECCIONAMOS EL ULTIMO CORTE CREADO
    $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id) AS id FROM punto_venta_ventas WHERE usuario = $id_user AND fecha = '$Fecha_hoy'"));        
    $Venta = $ultimo['id'];//TOMAMOS EL ID DEL ULTIMO CORTE
    ?>
    <!DOCTYPE html>
	<html lang="en">
    <head>
    	<title>SIC | Venta N° <?php echo $Venta ?></title>
	    <script>
	        //FUNCION QUE HACE LA ACTUALIZACION DE LA CATEGORIA (SE ACTIVA AL PRECIONAR UN BOTON)
	        function update_categoria(id) {
	          //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO LA INFORMCION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
	          var textoNombre = $("input#nombre").val();//ej:LA VARIABLE "textoNombre" GUARDAREMOS LA INFORMACION QUE ESTE EN EL INPUT QUE TENGA EL id = "nombre"
	          
	          // CREAMOS CONDICIONES QUE SI SE CUMPLEN MANDARA MENSAJES DE ALERTA EN FORMA DE TOAST
	          //SI SE CUMPLEN LOS IF QUIERE DECIR QUE NO PASA LOS REQUISITOS MINIMOS DE LLENADO...
	          if (textoNombre == "") {
	            M.toast({html: 'El campo Nombre se encuentra vacío.', classes: 'rounded'});
	          }else{
	              //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
	              //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_categoria.php"
	              $.post("../php/control_categoria.php", {
	              //Cada valor se separa por una ,
	                  accion: 2,
	                  id: id,
	                  valorNombre: textoNombre,
	              }, function(mensaje) {
	                  //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_categoria.php"
	                  $("#resultado_update").html(mensaje);
	              }); 
	          }//FIN else CONDICIONES
	        };//FIN function 
	    </script>
    </head>
    <body>
    	<!-- DENTRO DE ESTE DIV VA TODO EL CONTENIDO Y HACE QUE SE VEA AL CENTRO DE LA PANTALLA.-->
	    <div class="container"><br><br>
	      <!--    //////    TITULO    ///////   -->
	      <div class="row" >
	        <h3 class="hide-on-med-and-down">Venta Folio: <?php echo $Venta; ?></h3>
	        <h5 class="hide-on-large-only">Venta Folio: <?php echo $Venta; ?></h5>
	      </div>
	      <div class="row" >
	        
	      </div><br>
	    </div>    	
    </body>
    </html>
    <?php
}else{
  ?>
  <script>    
    M.toast({html: "Regresando a home.", classes: "rounded"});
    setTimeout("location.href='home.php'", 800);
  </script>
  <?php
}