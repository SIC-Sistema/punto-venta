<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
    include('fredyNav.php');
    ?>
    <title>SIC | Nueva Cotización Punto Venta</title>
    <script>
      //FUNCION QUE HACE LA BUSQUEDA DE ARTICULOS (SE ACTIVA AL INICIAR EL ARCHIVO O AL ECRIBIR ALGO EN EL BUSCADOR)
      function buscar_articulos(){
        //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO EL TEXTO REQUERIDO Y LO ASIGNAMOS A UNA VARIABLE
        var texto = $("input#busqueda").val();
        //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_cotizacion.php"
        $.post("../php/control_cotizacion.php", {
          //Cada valor se separa por una ,
            texto: texto,
            accion: 1,
          }, function(mensaje){
              //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_articulo.php"
              $("#articulosALL").html(mensaje);
        });//FIN post
      }//FIN function

      //FUNCION QUE MANDA IMPRIMIR EL CATALOGO SEGUN EL ID DE CATEGORIA
      function imprimir_catalogo(){
        //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO EL TEXTO REQUERIDO Y LO ASIGNAMOS A UNA VARIABLE
        var id = $("select#categoria").val();
        if (id == '') {
          M.toast({html: 'Seleccione una categoria.', classes: 'rounded'});
        }else{
          var a = document.createElement("a");
          a.href = "../php/imprimir_catalogo.php?id="+id;
          a.target = "blank";
          a.click();
        } 
      }

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


      function agregar_articulo(id, insert,id_art=0){
        if (insert) {
            //PEDIMOS VARIABLES Y CONDICIONES PARA INSERTAR ARTICULO A TMP
            M.toast({html: 'Insertar articulo N° '+id_art, classes: 'rounded'});
            //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
            //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_cotizacion.php"
            $.post("../php/control_cotizacion.php", {
            //Cada valor se separa por una ,
                accion: 2,
                insert: insert,
                id: id,
                id_art: id_art,
            }, function(mensaje) {
                //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_articulo.php"
                $("#listaArticulos").html(mensaje);
            });
        }else{
            //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
            //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_cotizacion.php"
            $.post("../php/control_cotizacion.php", {
                //Cada valor se separa por una ,
                accion: 2,
                insert: insert,
                id: id,
            }, function(mensaje) {
                //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
                $("#listaArticulos").html(mensaje);
            });
        }//FIN ELSE insert
      }// FIN function
    </script>
</head>
<main>
    <body onload="buscar_articulos();">
        <div class="container"><br><br>
            <!--    //////    BOTON QUE REDIRECCIONA AL FORMULARIO DE AGREGAR COTIZACIÓN    ///////   -->
            <a href="cotizacion_nueva_punto_venta.php" class="waves-effect waves-light btn pink left right">Agregar Articulo<i class="material-icons prefix left">add</i></a>
            <!-- CREAMOS UN DIV EL CUAL TENGA id = "borrarArticulo"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
            <div id="borrarArticulo"></div>
            <!-- CREAMOS UN DIV EL CUAL TENGA id = "modal"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
            <div id="modal"></div>
            <div class="row">
                <!--    //////    TITULO    ///////   -->
                <h3 class="hide-on-med-and-down col s12 m6 l6">Nueva Cotización</h3>
                <h5 class="hide-on-large-only col s12 m6 l6">Nueva Cotización</h5>
                <!--    //////    INPUT DE EL BUSCADOR    ///////   -->
                <form class="col s12 m6 l6">
                    <div class="row">
                        <div class="input-field col s12">
                            <i class="material-icons prefix">search</i>
                            <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar_articulos();">
                            <label for="busqueda">Buscar(Código, Nombre, Descrpición, Código Fiscal)</label>
                        </div>
                        <!--    //////    BOTÓN PARA IMPRIMIR LA INFORMACIÓN DE LA TABLA    ///////   -->
                        <a onclick="imprimir_catalogo()" class="waves-effect waves-light btn pink right"><i class="material-icons right">print</i>IMPRIMIR CATÁLOGO</a>
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
                        <th>Marca</th>
                        <th>Precio</th>
                        <th>Unidad</th>
                        <th>C. Unidad</th>
                        <th>C. Fiscal</th>
                        <th>Añadir</th>
                        </tr>
                    </thead>
                    <!-- DENTRO DEL tbody COLOCAMOS id = "articulosALL"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION buscar_articulos) -->
                    <tbody id="articulosALL">
                    </tbody>
                </table>
            </div><br><br>
        </div>
        <!-- NUEVO CONTENEDOR EN TEORIA -->
        <div class="container"><br><br>
            <!--    //////    BOTON QUE REDIRECCIONA AL FORMULARIO DE AGREGAR COTIZACIÓN    ///////   -->
            <!-- CREAMOS UN DIV EL CUAL TENGA id = "listaArticulos"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
            <div id="listaArticulos">
            </div>
            <div class="row">
                <!--    //////    TITULO    ///////   -->
                <h3 class="hide-on-med-and-down col s12 m6 l6">Lista de Artículos</h3>
                <h5 class="hide-on-large-only col s12 m6 l6">Lista de Artículos</h5>
            </div>
        </div>
    </body>
</main>
</html>