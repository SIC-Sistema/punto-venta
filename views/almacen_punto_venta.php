<?php
//INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
include('fredyNav.php');
//REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL USUARIO Y ASIGNAMOS EL ARRAY A UNA VARIABLE $datos
$user_id = $_SESSION['user_id'];
$datos = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM users WHERE user_id=$user_id"));
//VERIFICAMOS QUE SI NOS ENVIE POR POST EL ID DEL PROVEEDOR
if ($datos['almacen'] == 0) {
  ?>
  <script>    
    M.toast({html: "No tienes un almacen asignado.", classes: "rounded"});
    M.toast({html: "Comunicate con un ADMINISTRADOR.", classes: "rounded"});
    setTimeout("location.href='home.php'", 2500);
  </script>
  <?php
}else{
  $id_almacen = $datos['almacen'];// ID DEL ALMACEN ASIGNADO AL USUARIO LOGEADO
  //SACAMOS LA INFORMACION DEL ALMACEN
  $almacen = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM `punto_venta_almacenes` WHERE id=$id_almacen"));
?>
  <!DOCTYPE html>
  <html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIC | Mi Almacen</title>
  </head>
  <script>    
    //FUNCION QUE HACE LA BUSQUEDA DE ALMACENES (SE ACTIVA AL INICIAR EL ARCHIVO O AL ECRIBIR ALGO EN EL BUSCADOR)
    function buscarAlmacen(id){
      //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO EL TEXTO REQUERIDO Y LO ASIGNAMOS A UNA VARIABLE
      var texto = $("input#busqueda").val();
      //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_almacen.php"
      $.post("../php/control_almacen.php", {
        //Cada valor se separa por una ,
          texto: texto,
          id: id,
          accion: 4,
        }, function(mensaje){
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_almacen.php"
            $("#Almacen").html(mensaje);
      });//FIN post
    }//FIN function
  </script>
  <body onload="buscarAlmacen(<?php echo $id_almacen; ?>)">
    <div class="container">
      <div class="row">
        <h2 class="hide-on-med-and-down">Almacen: <?php echo $id_almacen.'. '.$almacen['nombre']; ?></h2>
        <h4 class="hide-on-large-only">Almacen: <?php echo $id_almacen.'. '.$almacen['nombre']; ?></h4>
      </div>
      <form class="row col s12">
         <!--    //////    INPUT DE LA BUSQUEDA    ///////   -->   
          <div class="input-field col s12 m6 l6 right">
              <i class="material-icons prefix">search</i>
              <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscarAlmacen(<?php echo $id_almacen; ?>);">
              <label for="busqueda">Codigo, Articulo, Descripcion, Marca</label>
          </div>    
      </form>
      <table>
        <thead>
          <tr>
            <th>Codigo</th>
            <th>Articulo</th>
            <th>Descripcion</th>
            <th>Precio</th>
            <th>Existencia</th>
            <th>Editar</th>
          </tr>
        </thead>
        <!-- DENTRO DEL tbody COLOCAMOS id = "Almacen"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION buscar_categorias() -->
        <tbody id="Almacen">
          
        </tbody>
      </table>
    </div>
  </body>
  </html>
<?php
}
?>