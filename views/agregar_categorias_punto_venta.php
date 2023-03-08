<!DOCTYPE html>
<html lang="en">
    <head>
      <?php
      //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
      include('fredyNav.php');
      //ARCHIVO QUE RESTRINGE A QUE SOLO ALGUNOS USUARIOS PUEDAN ACCEDER
      include('../php/cobrador.php');
      ?>
      <title>SIC | Agregar categorias Punto Venta</title>
      <script>
        //FUNCION QUE HACE LA BUSQUEDA DE CATEGORIAS (SE ACTIVA AL INICIAR EL ARCHIVO O AL ECRIBIR ALGO EN EL BUSCADOR)
        //FUNCION QUE HACE LA INSERCION DE LA CATEGORIA (SE ACTIVA AL PRECIONAR UN BOTON)
        function insert_categoria() {
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
                accion: 0,
                valorNombre: textoNombre
              }, function(mensaje) {
                  //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_categoria.php"
                  $("#resultado_insert").html(mensaje);
              }); 
          }//FIN else CONDICIONES
        };//FIN function 
      </script>
    </head>
    <body>
      <div class="container"><br><br>
        <div class="row">
        <!--    //////    TITULO    ///////   -->
          <h3 class="hide-on-med-and-down col s12 m12 l12">Insertar nueva categoría</h3>
          <h5 class="hide-on-large-only col s12 m12 l12">Insertar nueva categoría</h5>
          <!--    //////    INPUT DEl REGISTRO DE LA CATEGORIA    ///////   -->
          <form class="col s12 m12 l12 row">
          <!-- CREAMOS UN DIV EL CUAL TENGA id = "resultado_insert"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
          <div id="resultado_insert"></div>
            <!-- FORMULARIO EL CUAL SE MUETRA EN PANTALLA .-->
          <div class="col s12 m6 l6"><br>    
            <div class="input-field">
              <i class="material-icons prefix">add</i>
              <input id="nombre"  name="nombre" type="text" class="validate" data-length="30" required>
              <label for="nombre">Nueva categoria</label>   
            </div>
          </div>
          <div class="col s12 m6 l6">
          <br>
          <br>
          <!-- BOTON QUE MANDA LLAMAR EL SCRIPT PARA QUE EL SCRIPT HAGA LO QUE LA FUNCION CONTENGA -->
          <a onclick="insert_categoria();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>Insertar</a>
        </div>
        </form>
      </div><br>
    </body>
</html>