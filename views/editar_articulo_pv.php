<?php
//VERIFICAMOS QUE SI NOS ENVIE POR POST EL ID DEL ARTICULO
if (isset($_POST['id']) == false) {
  ?>
  <script>    
    M.toast({html: "Regresando a Articulos.", classes: "rounded"});
    setTimeout("location.href='articulos_punto_venta.php'", 800);
  </script>
  <?php
}else{
?>
  <html>
  <head>
  	<title>SIC | Editar Articulos</title>
    <?php 
    //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
    include('fredyNav.php');
    $id = $_POST['id'];// POR EL METODO POST RECIBIMOS EL ID DEL ARTICULO
    //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL ARTICULO Y ASIGNAMOS EL ARRAY A UNA VARIABLE $datos
    $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_articulos` WHERE id=$id"));
    ?>
    <script>
      //FUNCION QUE AL USAR VALIDA LA VARIABLE QUE LLEVE UN FORMATO DE CORREO 
      function validar_email( email )   {
        var regex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email) ? true : false;
      };

      //FUNCION QUE HACE LA ACTUALIZACION DEL CLIENTE (SE ACTIVA AL PRECIONAR UN BOTON)
      function update_articulo(id) {

        //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO LA INFORMCION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
        var textoCodigo = $("input#codigo").val();//ej:LA VARIABLE "textoCodigo" GUARDAREMOS LA INFORMACION QUE ESTE EN EL INPUT QUE TENGA EL id = "codigo"
        var textoDescripcion = $("input#descripcion").val();// ej: TRAE LE INFORMACION DEL INPUT FILA  (id="descripcion")
        var textoPrecio = $("input#precio").val();
        var textoUnidad = $("input#unidad").val();

        // CREAMOS CONDICIONES QUE SI SE CUMPLEN MANDARA MENSAJES DE ALERTA EN FORMA DE TOAST
        //SI SE CUMPLEN LOS IF QUIERE DECIR QUE NO PASA LOS REQUISITOS MINIMOS DE LLENADO...
        if (textoCodigo == "") {
        M.toast({html: 'El campo Código se encuentra vacío.', classes: 'rounded'});
        }else if(textoDescripcion.length == ""){
        M.toast({html: 'El campo Descripción se encuentra vacío.', classes: 'rounded'});
        }else if(textoPrecio == ""){
        M.toast({html: 'El campo Precio se encuentra vacío.', classes: 'rounded'});
        }else if(textoUnidad == ""){
        M.toast({html: 'El campo Unidad se encuentra vacío.', classes: 'rounded'});
        }else{
            //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
            //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_articulo.php"
            $.post("../php/control_articulo.php", {
            //Cada valor se separa por una ,
                accion: 2,
                id: id,
                valorCodigo: textoCodigo,
                valorDescripcion: textoDescripcion,
                valorPrecio: textoPrecio,
                valorUnidad: textoUnidad,
            }, function(mensaje) {
                //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_articulo.php"
                $("#resultado_update").html(mensaje);
            }); 
        }//FIN else CONDICIONES
      };//FIN function 
    </script>
  </head>
  <main>
  <body>
    <!-- DENTRO DE ESTE DIV VA TODO EL CONTENIDO Y HACE QUE SE VEA AL CENTRO DE LA PANTALLA.-->
    <div class="container"><br><br>
      <!--    //////    TITULO    ///////   -->
      <div class="row" >
        <h3 class="hide-on-med-and-down">Editar Articulo N°<?php echo $id; ?></h3>
        <h5 class="hide-on-large-only">Editar Articulo N°<?php echo $id; ?></h5>
      </div>
      <div class="row" >
       <!-- CREAMOS UN DIV EL CUAL TENGA id = "resultado_update"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
       <div id="resultado_update"></div>
       <div class="row">
        <!-- FORMULARIO EL CUAL SE MUETRA EN PANTALLA .-->
        <form class="row col s12">
          <!-- DIV QUE SEPARA A DOBLE COLUMNA PARTE IZQ.-->
          <div class="col s12 m6 l6">
            <br>
            <div class="input-field">
            <i class="material-icons prefix">equalizer</i>
            <input id="codigo" type="text" class="validate" data-length="50" required value="<?php echo $datos['codigo']; ?>">
            <label for="codigo">Codigo:</label>
          </div>      
          <div class="input-field">
            <i class="material-icons prefix">description</i>
            <input id="descripcion" type="text" class="validate" data-length="80" required value="<?php echo $datos['descripcion']; ?>">
            <label for="descripcion">Descripción:</label>
          </div>         
        </div>
          <!-- DIV DOBLE COLUMNA EN ESCRITORIO PARTE DERECHA -->
          <div class="col s12 m6 l6">
            <br>
            <div class="input-field">
            <i class="material-icons prefix">attach_money</i>
            <input id="precio" type="text" class="validate" data-length="35" required value="<?php echo $datos['precio']; ?>">
            <label for="precio">Precio:</label>
          </div>
          <div class="input-field">
            <i class="material-icons prefix">shopping_basket</i>
            <input id="unidad" type="text" class="validate" data-length="15" required value="<?php echo $datos['unidad']; ?>">
            <label for="unidad">Unidad:</label>
          </div>
        </div>
        </form>
        <!-- BOTON QUE MANDA LLAMAR EL SCRIPT PARA QUE EL SCRIPR HAGA LO QUE LA FUNCION CONTENGA -->
        <a onclick="update_articulo(<?php echo $id; ?>);" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>Guardar</a>
      </div> 
    </div><br>
  </body>
  </main>
  </html>
<?php
}// FIN else POST
?>