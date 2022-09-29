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
      //FUNCION QUE SUBE LA IMAGEN DEL ARTICULO (ACTUALIZA TABLA Y SUBE IMAGEN A CARPETA)
      function subirImagen(id){
        //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "modal_imagen.php" PARA MOSTRAR EL MODAL
        $.post("modal_imagen.php", {
          //Cada valor se separa por una ,
            id:id,
          }, function(mensaje){
              //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "modal_imagen.php"
              $("#modal").html(mensaje);
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
      <!-- CREAMOS UN DIV EL CUAL TENGA id = "borrarArticulo"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
      <div id="borrarArticulo"></div>
      <!-- CREAMOS UN DIV EL CUAL TENGA id = "modal"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
      <div id="modal"></div>
      <div class="row">
        <!--    //////    TITULO    ///////   -->
        <h3 class="hide-on-med-and-down col s12 m6 l6">Artículos</h3>
        <h5 class="hide-on-large-only col s12 m6 l6">Artículos</h5>
        <!--    //////    INPUT DE EL BUSCADOR    ///////   -->
        <form class="col s12 m6 l6">
          <div class="row">
            <div class="input-field col s12">
              <i class="material-icons prefix">search</i>
              <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar_articulos();">
              <label for="busqueda">Buscar(Código, Nombre, Descrpición, Código Fiscal)</label>
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
              <th>Código</th>
              <th>Imagen</th>
              <th>Nombre</th>
              <th>Descripción</th>
              <th>Modelo</th>
              <th>Precio</th>
              <th>Unidad</th>
              <th>C. Unidad</th>
              <th>C. Fiscal</th>
              <th>Categoria</th>
              <th>Registro</th>
              <th>Fecha</th>
              <th>Editar</th>
              <th>Borrar</th>
              <th>Imagen</th>
            </tr>
          </thead>
          <!-- DENTRO DEL tbody COLOCAMOS id = "articulosALL"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION buscar_articulos) -->
          <tbody id="articulosALL">
          </tbody>
        </table>
      </div><br><br>
    </div>
    <!--Modal cortes-->
    <div id="corte" class="modal">
      <div class="modal-content">
        <h4 class="red-text center">! Advertencia !</h4><br>
        <h6 ><b>Una vez generado el corte se comenzara una nueva lista de pagos para el siguinete corte. </b></h6><br>
        <h5 class="red-text darken-2">¿DESEA CONTINUAR?</h5>
        <div class="row">
        <div class="input-field col s12 m6 l6">
            <i class="material-icons prefix">lock</i>
            <input type="password" name="clave" id="clave">
            <label for="clave">Ingresar Clave</label>
        </div>
        </div>
        <h4>¿Desea agregar algun deducible?</h4>
          <form class="row">
          <div class="input-field col s12 m6 l4">
              <i class="material-icons prefix">attach_money</i>
              <input id="cantidadD" type="number" class="validate" data-length="30" value="0" required>
              <label for="cantidadD">Cantidad:</label>
          </div>
          <div class="input-field col s12 m6 l6">
              <i class="material-icons prefix">edit</i>
              <input id="descripcionD" type="text" class="validate" data-length="30" required>
              <label for="descripcionD">Descripcion:(ej: Viaticos para Marcos y Luis) </label>
          </div>
          </form>
      </div>
      <div class="modal-footer">
          <a onclick="recargar_corte()" class="modal-action modal-close waves-effect waves-green btn-flat">Aceptar</a>
          <a href="#" class="modal-action modal-close waves-effect waves-red btn-flat">Cerrar<i class="material-icons right">close</i></a>
      </div>
    </div>
    <!--Cierre modal Cortes-->
  </body>
  </main>
</html>