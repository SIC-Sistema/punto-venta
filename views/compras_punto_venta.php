<!DOCTYPE html>
<html lang="en">
  <head>
    <?php
    //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
    include('fredyNav.php');
    //ARCHIVO QUE RESTRINGE A QUE SOLO ALGUNOS USUARIOS PUEDAN ACCEDER
    include('../php/cobrador.php');
    ?>
    <title>SIC | Compras Punto Venta</title>
    <script>
      //FUNCION QUE HACE LA BUSQUEDA DE Compras (SE ACTIVA AL INICIAR EL ARCHIVO O AL ECRIBIR ALGO EN EL BUSCADOR)
      function buscar_compras(){
        //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO EL TEXTO REQUERIDO Y LO ASIGNAMOS A UNA VARIABLE
        var texto = $("input#busqueda").val();
        //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
        $.post("../php/control_compra.php", {
          //Cada valor se separa por una ,
            texto: texto,
            accion: 1,
          }, function(mensaje){
              //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
              $("#ComprasALL").html(mensaje);
        });//FIN post
      }//FIN function

      //FUNCION QUE BORRA LOS COMPRAS (SE ACTIVA AL INICIAR EL BOTON BORRAR)
      function borrar_compra_pv(id){
        var answer = confirm("Deseas eliminar el compra N°"+id+"?");
        if (answer) {
        //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
        $.post("../php/control_compra.php", {
            //Cada valor se separa por una ,
            id: id,
            accion: 3,
          }, function(mensaje) {
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
            $("#borrarcompra").html(mensaje);
          }); //FIN post
        }//FIN IF
      };//FIN function
    </script>
  </head>
  <main>
  <body onload="buscar_compras();">
    <div class="container"><br><br>
      <!--    //////    TITULO    ///////   -->
      <div class="row" ><br>
        <h3 class="hide-on-med-and-down col s12 m6 l6">Compras</h3>
        <h5 class="hide-on-large-only col s12 m6 l6">Compras</h5>
        <!--    //////    BOTON QUE REDIRECCIONA AL FORMULARIO DE AGREGAR COMPRA    ///////   -->
        <a href="add_compra.php" class="waves-effect waves-light btn pink left right">REGISTRAR COMPRA<i class="material-icons prefix left">add</i></a>
      </div>
      <!-- CREAMOS UN DIV EL CUAL TENGA id = "borrarcompra"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
      <div id="borrarcompra"></div>
      <div class="row">
        <!-- ----------------------------  TABs o MENU  ---------------------------------------->
        <div class="col s12">
          <ul id="tabs-swipe-demo" class="tabs">
            <li class="tab col s4"><a class="active black-text" href="#test-swipe-1">HISTORIAL DE TODAS LAS COMPRAS</a></li>
          </ul>
        </div>
        <!-- ----------------------------  FORMULARIO 1 Tabs  ---------------------------------------->
        <div  id="test-swipe-1" class="col s12"><br><br>
          <!--    //////    INPUT DE EL BUSCADOR    ///////   -->
          <form class="col s12 m6 l6">
            <div class="row">
              <div class="input-field col s12">
                <i class="material-icons prefix">search</i>
                <input id="busqueda" name="busqueda" type="text" onkeyup="buscar_compras();">
                <label for="busqueda">Buscar(N° Compra, N° Proveedor, N° Factura)</label>
              </div>
            </div>
          </form>
          <!--    //////    TABLA QUE MUESTRA LA INFORMACION DE LAS Compras    ///////   -->
          <div class="row">
            <table class="bordered highlight responsive-table">
              <thead>
                <tr>
                  <th>N°</th>
                  <th>N° Factura</th>
                  <th>Proveedor</th>
                  <th>Tipo Cambio</th>
                  <th>Total</th>
                  <th>Registro</th>
                  <th>Fecha</th>
                  <th>Detalles</th>
                  <th>Borrar</th>
                </tr>
              </thead>
              <!-- DENTRO DEL tbody COLOCAMOS id = "ComprasALL"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION buscar_compras() -->
              <tbody id="ComprasALL">
              </tbody>
            </table>
          </div><br><br>
        </div>
      </div>
    </div>
  </body>
  </main>
</html>