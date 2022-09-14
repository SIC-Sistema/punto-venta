<!DOCTYPE html>
<html lang="en">
<head>
  <?php
  //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
  include('fredyNav.php');
  ?>
  <title>SIC | Proveedores Punto Venta</title>
  <script>
     //FUNCION QUE HACE LA BUSQUEDA DE CLIENTES (SE ACTIVA AL INICIAR EL ARCHIVO O AL ECRIBIR ALGO EN EL BUSCADOR)
    function buscar_proveedores(){
      //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO EL TEXTO REQUERIDO Y LO ASIGNAMOS A UNA VARIABLE
      var texto = $("input#busqueda").val();
      //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_proveedor.php"
      $.post("../php/control_proveedor.php", {
        //Cada valor se separa por una ,
          texto: texto,
          accion: 1,
        }, function(mensaje){
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_proveedor.php"
            $("#proveedoresALL").html(mensaje);
      });//FIN post
    }//FIN function
    //FUNCION QUE BORRA LOS PROVEEDORES (SE ACTIVA AL INICIAR EL BOTON BORRAR)
    function borrar_proveedor_pv(id){
      var answer = confirm("Deseas eliminar el cliente N°"+id+"?");
      if (answer) {
        $.post("../php/control_proveedor.php", {
          //Cada valor se separa por una ,
          id: id,
          accion: 3,
        }, function(mensaje) {
          //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_proveedor.php"
          $("#borrarProveedor").html(mensaje);
        }); //FIN post
      }//FIN IF
    };//FIN function
  </script>
</head>
<main>
<body onload="buscar_proveedores();">
  <div class="container"><br><br>
    <!--    //////    BOTON QUE REDIRECCIONA AL FORMULARIO DE AGREGAR CLIENTE    ///////   -->
    <a href="add_proveedor.php" class="waves-effect waves-light btn pink left right">Agregar Proveedor<i class="material-icons prefix left">add</i></a>
    <!-- CREAMOS UN DIV EL CUAL TENGA id = "borrarProveedor"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
    <div id="borrarProveedor"></div>
    <div class="row">
      <!--    //////    TITULO    ///////   -->
      <h3 class="hide-on-med-and-down col s12 m6 l6">Proveedores</h3>
      <h5 class="hide-on-large-only col s12 m6 l6">Proveedores</h5>
      <!--    //////    INPUT DE EL BUSCADOR    ///////   -->
      <form class="col s12 m6 l6">
        <div class="row">
          <div class="input-field col s12">
            <i class="material-icons prefix">search</i>
            <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar_proveedores();">
            <label for="busqueda">Buscar(N° Proveedor, Nombre, ,Dirección, Colonia, C.P., RFC)</label>
          </div>
        </div>
      </form>
    </div>
    <!--    //////    TABLA QUE MUESTRA LA INFORMACION DE LOS CLIENTES    ///////   -->
    <div class="row">
      <table class="bordered highlight responsive-table">
        <thead>
          <tr>
            <th>N°</th>
            <th>Nombre</th>
            <th>Dirección</th>
            <th>Colonia</th>
            <th>C.P.</th>
            <th>RFC</th>
            <th>E-mail</th>
            <th>Teléfono</th>
            <th>Días de crédito</th>
            <th>Registro</th>
            <th>Fecha</th>
            <th>Editar</th>
            <th>Borrar</th>
          </tr>
        </thead>
        <!-- DENTRO DEL tbody COLOCAMOS id = "proveedoresALL"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION buscar_clientes() -->
        <tbody id="proveedoresALL">
        </tbody>
      </table>
    </div><br><br>
  </div>
</body>
</main>
</html>