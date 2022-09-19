<!DOCTYPE html>
<html lang="en">
<head>
  <?php
  //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
  include('fredyNav.php');
  ?>
  <title>SIC | Artículos Punto Venta</title>
  <script>
     //FUNCION QUE HACE LA BUSQUEDA DE ARTICULOS (SE ACTIVA AL INICIAR EL ARCHIVO O AL ECRIBIR ALGO EN EL BUSCADOR)
    function buscar_articulos(){
      //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO EL TEXTO REQUERIDO Y LO ASIGNAMOS A UNA VARIABLE
      var texto = $("input#busqueda").val();
      //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_articulo.php"
      $.post("../php/control_articulo.php", {
        //Cada valor se separa por una ,
          texto: texto,
          accion: 1,
        }, function(mensaje){
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_articulo.php"
            $("#articulosALL").html(mensaje);
      });//FIN post
    }//FIN function
    //FUNCION QUE BORRA LOS ARTICULOS (SE ACTIVA AL INICIAR EL BOTON BORRAR)
    function borrar_articulo_pv(id){
      var answer = confirm("Deseas eliminar el artículo N°"+id+"?");
      if (answer) {
        //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_articulo.php"
        $.post("../php/control_articulo.php", {
          //Cada valor se separa por una ,
          id: id,
          accion: 3,
        }, function(mensaje) {
          //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_articulo.php"
          $("#borrarArticulo").html(mensaje);
        }); //FIN post
      }//FIN IF
    };//FIN function
  </script>
</head>
<main>
<body onload="buscar_articulos();">
  <div class="container"><br><br>
    <!--    //////    BOTON QUE REDIRECCIONA AL FORMULARIO DE AGREGAR ARTICULO    ///////   -->
    <a href="add_articulo.php" class="waves-effect waves-light btn pink left right">Agregar Articulo<i class="material-icons prefix left">add</i></a>
    <!-- CREAMOS UN DIV EL CUAL TENGA id = "borrarProveedor"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
    <div id="borrarArticulo"></div>
    <div class="row">
      <!--    //////    TITULO    ///////   -->
      <h3 class="hide-on-med-and-down col s12 m6 l6">Articulos</h3>
      <h5 class="hide-on-large-only col s12 m6 l6">Articulos</h5>
      <!--    //////    INPUT DE EL BUSCADOR    ///////   -->
      <form class="col s12 m6 l6">
        <div class="row">
          <div class="input-field col s12">
            <i class="material-icons prefix">search</i>
            <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar_articulos();">
            <label for="busqueda">Buscar(N° Codigo, Descrpición, Precio, Unidad)</label>
          </div>
          <!--    //////    BOTÓN PARA IMPRIMIR LA INFORMACIÓN DE LA TABLA    ///////   -->
          <a href="../php/imprimir_catalogo.php" target="blank" class="waves-effect waves-light btn pink right"><i class="material-icons right">print</i>IMPRIMIR CATÁLOGO</a>
        </div>
      </form>
    </div>
    <!--    //////    TABLA QUE MUESTRA LA INFORMACION DE LOS ARTICULO    ///////   -->
    <div class="row">
      <table class="bordered highlight responsive-table">
        <thead>
          <tr>
            <th>N°</th>
            <th>Codigo</th>
            <th>Descripción</th>
            <th>Precio $</th>
            <th>Unidad</th>
            <th>Registro</th>
            <th>Fecha</th>
            <th>Editar</th>
            <th>Borrar</th>
          </tr>
        </thead>
        <!-- DENTRO DEL tbody COLOCAMOS id = "articulosALL"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION buscar_articulos) -->
        <tbody id="articulosALL">
        </tbody>
      </table>
    </div><br><br>
  </div>
</body>
</main>
</html>