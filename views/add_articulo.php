<html>
<head>
	<title>SIC | Agregar Artículos</title>
  <?php 
  //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
  include('fredyNav.php');
  ?>
  <script>
    //FUNCION QUE AL USAR VALIDA LA VARIABLE QUE LLEVE UN FORMATO DE CORREO 
    function validar_email( email )   {
      var regex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      return regex.test(email) ? true : false;
    };

    //FUNCION QUE HACE LA INSERCION DEL ARTICULO (SE ACTIVA AL PRECIONAR UN BOTON)
    function insert_articulo() {

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
            accion: 0,
            valorCodigo: textoCodigo,
            valorDescripcion: textoDescripcion,
            valorPrecio: textoPrecio,
            valorUnidad: textoUnidad,
          }, function(mensaje) {
              //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_articulo.php"
              $("#resultado_insert").html(mensaje);
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
      <h3 class="hide-on-med-and-down">Registrar Artículo</h3>
      <h5 class="hide-on-large-only">Registrar Artículo</h5>
    </div>
    <div class="row" >
     <!-- CREAMOS UN DIV EL CUAL TENGA id = "resultado_insert"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
     <div id="resultado_insert"></div>
     <div class="row">
      <!-- FORMULARIO EL CUAL SE MUETRA EN PANTALLA .-->
      <form class="row col s12">
        <!-- DIV QUE SEPARA A DOBLE COLUMNA PARTE IZQ.-->
        <div class="col s12 m6 l6">
          <br>
          <div class="input-field">
            <i class="material-icons prefix">equalizer</i>
            <input id="codigo" type="text" class="validate" data-length="50" required>
            <label for="codigo">Codigo:</label>
          </div>      
          <div class="input-field">
            <i class="material-icons prefix">description</i>
            <input id="descripcion" type="text" class="validate" data-length="80" required>
            <label for="descripcion">Descripción:</label>
          </div>         
        </div>
        <!-- DIV DOBLE COLUMNA EN ESCRITORIO PARTE DERECHA -->
        <div class="col s12 m6 l6">
          <br>
          <div class="input-field">
            <i class="material-icons prefix">attach_money</i>
            <input id="precio" type="text" class="validate" data-length="35" required>
            <label for="precio">Precio:</label>
          </div>
          <div class="input-field">
            <i class="material-icons prefix">shopping_basket</i>
            <input id="unidad" type="text" class="validate" data-length="15" required>
            <label for="unidad">Unidad:</label>
          </div>  
        </div>
      </form>
      <!-- BOTON QUE MANDA LLAMAR EL SCRIPT PARA QUE EL SCRIPT HAGA LO QUE LA FUNCION CONTENGA -->
      <a onclick="insert_articulo();" class="waves-effect waves-light btn pink right"><i class="material-icons right">add</i>Agregar</a>
    </div> 
  </div><br>
</body>
</main>
</html>