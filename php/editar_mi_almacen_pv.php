<?php
//VERIFICAMOS QUE SI NOS ENVIE POR POST EL ID DEL ARTICULO
if (isset($_POST['id']) == false) {
  ?>
  <script>    
    M.toast({html: "Regresando a Almacenes.", classes: "rounded"});
    setTimeout("location.href='almacenes_punto_venta.php'", 800);
  </script>
  <?php
}else{
?>
<html>
  <head>
  	<title>SIC | Editar Almacen</title>
    <?php 
    //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
    include('fredyNav.php');
    $id = $_POST['id'];// POR EL METODO POST RECIBIMOS EL ID DEL ARTICULO
    //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL ARTICULO Y ASIGNAMOS EL ARRAY A UNA VARIABLE $datos
    $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_almacenes` WHERE id=$id"));
    //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL ARTICULO EN LA BD `punto_venta_articulos` Y ASIGNAMOS EL ARRAY A UNA VARIABLE $cantidad
    $cantidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_almacen_general` WHERE id_articulo=$id"));
    ?>
    <script>
      //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO LA INFORMCION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
      var textoPrecio = $("input#precio").val();//ej:LA VARIABLE "textoPrecio" GUARDAREMOS LA INFORMACION QUE ESTE EN EL INPUT QUE TENGA EL id = "precio"
      var textoCantidad = $("input#cantidad").val();
      var textoDescripcion_Articulo = $("input#descripcion_articulo").val();
      var textoDescripcion_Cambio = $("input#descripcion_cambio").val();

      // CREAMOS CONDICIONES QUE SI SE CUMPLEN MANDARA MENSAJES DE ALERTA EN FORMA DE TOAST
      //SI SE CUMPLEN LOS IF QUIERE DECIR QUE NO PASA LOS REQUISITOS MINIMOS DE LLENADO...
      if (textoPrecio <= 0) {
        M.toast({html: 'El campo Precio se encuentra vacío.', classes: 'rounded'});
      }else if (textoCantidad <= 0) {
        M.toast({html: 'El campo Cantidad se encuentra vacío.', classes: 'rounded'});
      }else if (textoModelo == "") {
        M.toast({html: 'El campo Marca se encuentra vacío.', classes: 'rounded'});
      }else if(textoDescripcion.length == ""){
        M.toast({html: 'El campo Descripción se encuentra vacío.', classes: 'rounded'});
      }else if(textoPrecio <= 0){
      }else{
          //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_almacen.php"
          $.post("../php/control_almacen.php", {
            //Cada valor se separa por una ,
            accion: 5,
            id: id,
            valorPrecio: textoPrecio,
            valorCantidad: textoCantidad,
            valorDescripcion_Articulo: textoDescripcion_Articulo,
            valorDescripcion_Cambio: textoDescripcion_Cambio,
          }, function(mensaje) {
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_almacen.php"
            $("#resultado_update").html(mensaje);
          }); 
        }//FIN else CONDICIONES
    </script>
  </head>
</html>