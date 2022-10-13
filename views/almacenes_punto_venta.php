<!DOCTYPE html>
<html lang="en">
<head>
  <?php
  //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
  include('fredyNav.php');
  ?>
  <title>SIC | Almacenes Punto Venta</title>
  <script>
    //FUNCION QUE HACE LA BUSQUEDA DE ALMACENES (SE ACTIVA AL INICIAR EL ARCHIVO O AL ECRIBIR ALGO EN EL BUSCADOR)
    function buscar_almacenes(){
      //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO EL TEXTO REQUERIDO Y LO ASIGNAMOS A UNA VARIABLE
      var texto = $("input#busqueda").val();
      //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_almacen.php"
      $.post("../php/control_almacen.php", {
        //Cada valor se separa por una ,
          texto: texto,
          accion: 1,
        }, function(mensaje){
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_almacen.php"
            $("#almacenesALL").html(mensaje);
      });//FIN post
    }//FIN function

    //FUNCION QUE BORRA LOS ALMACENES (SE ACTIVA AL INICIAR EL BOTON BORRAR)
    function borrar_almacen(id){
      var answer = confirm("Deseas eliminar el almacen N°"+id+"?");
      if (answer) {
        //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_almacen.php"
        $.post("../php/control_almacen.php", {
          //Cada valor se separa por una ,
          id: id,
          accion: 3,
        }, function(mensaje) {
          //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_almacenes.php"
          $("#borrarAlmacen").html(mensaje);
        }); //FIN post
      }//FIN IF
    };//FIN function

    //FUNCION QUE HACE LA INSERCION DE UN ALMACEN (SE ACTIVA AL PRESIONAR UN BOTON)
    function insert_almacen() {

      //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO LA INFORMCION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
      var textoNombre = $("input#nombre").val();//ej:LA VARIABLE "textoNombre" GUARDAREMOS LA INFORMACION QUE ESTE EN EL INPUT QUE TENGA EL id = "nombre"

      // CREAMOS CONDICIONES QUE SI SE CUMPLEN MANDARA MENSAJES DE ALERTA EN FORMA DE TOAST
      //SI SE CUMPLEN LOS IF QUIERE DECIR QUE NO PASA LOS REQUISITOS MINIMOS DE LLENADO...
      if (textoNombre == "") {
        M.toast({html: 'El campo Nombre se encuentra vacío.', classes: 'rounded'});
      }else{
        //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
        //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_almacen.php"
        $.post("../php/control_almacen.php", {
          //Cada valor se separa por una ,
            accion: 0,
            valorNombre: textoNombre,
          }, function(mensaje) {
              //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_almacen.php"
              $("#resultado_insert").html(mensaje);
          }); 
      }//FIN else CONDICIONES
    };//FIN function 
  </script>
</head>
<body onload="buscar_almacenes();">
    <div class="container"><br><br>
        <!--    //////    BOTON QUE REDIRECCIONA AL FORMULARIO DE AGREGAR ALMACEN    ///////   -->
        <!-- CREAMOS UN DIV EL CUAL TENGA id = "borrarAlmacen"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
        <div id="borrarAlmacen"></div>
        <div class="row">
            <!--    //////    TITULO    ///////   -->
            <h3 class="hide-on-med-and-down col s12 m6 l6">Almacenes</h3>
            <h5 class="hide-on-large-only col s12 m6 l6">Almacenes</h5>
            <!--    //////    INPUT DEl REGISTRO DE LA CATEGORIA    ///////   -->
            <form class="col s12 m6 l6">
                <!-- CREAMOS UN DIV EL CUAL TENGA id = "resultado_insert"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
                <div id="resultado_insert"></div>
                <div class="row">
                <!-- FORMULARIO EL CUAL SE MUETRA EN PANTALLA .-->
                    <!-- DIV QUE SEPARA A DOBLE COLUMNA .-->
                    <div class="col s12 m6 l6"><br>    
                        <div class="input-field">
                            <i class="material-icons prefix">add</i>
                            <input id="nombre"  name="nombre" type="text" class="validate" data-length="30" required>
                            <label for="nombre">Nuevo Almacen</label>   
                        </div>
                    </div>
                    <div class="col s12 m6 l6"><br><br>
                        <!-- BOTON QUE MANDA LLAMAR EL SCRIPT PARA QUE EL SCRIPT HAGA LO QUE LA FUNCION CONTENGA -->
                        <a onclick="insert_almacen();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>Agregar</a>
                    </div>
                    <!--    //////    INPUT DE LA BUSQUEDA    ///////   -->   
                    <div class="input-field col s12">
                        <i class="material-icons prefix">search</i>
                        <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar_almacenes();">
                        <label for="busqueda">Busqueda por nombre:</label>
                    </div>       
                </div>
            </form>
        </div><br>
        <!--    //////    TABLA QUE MUESTRA LA INFORMACION DE LOS ARTICULO    ///////   -->
        <div class="row">
            <table class="bordered highlight responsive-table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th>Editar</th>
                        <th>Borrar</th>
                    </tr>
                </thead>
                <!-- DENTRO DEL tbody COLOCAMOS id = "almacenesALL"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION buscar_categorias() -->
                <tbody id="almacenesALL">
                </tbody>
            </table>
        </div><br><br>
    </div>
</body>
</html>