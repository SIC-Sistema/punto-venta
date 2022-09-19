<html>
<head>
	<title>SIC | Agregar Proveedores</title>
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

    //FUNCION QUE HACE LA INSERCION DEL PROVEEDOR (SE ACTIVA AL PRECIONAR UN BOTON)
    function insert_proveedor() {

      //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO LA INFORMCION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
      var textoNombre = $("input#nombre").val();//ej:LA VARIABLE "textoNombre" GUARDAREMOS LA INFORMACION QUE ESTE EN EL INPUT QUE TENGA EL id = "nombre"
      var textoDireccion = $("input#direccion").val();// ej: TRAE LE INFORMACION DEL INPUT FILA  (id="direccion")
      var textoColonia = $("input#colonia").val();
      var textoCP = $("input#cp").val();
      var textoRFC = $("input#rfc").val();
      var textoEmail = $("input#email").val();
      var textoTelefono = $("input#telefono").val();
      var textoDias_Credito = $("input#dias_credito").val();

      // CREAMOS CONDICIONES QUE SI SE CUMPLEN MANDARA MENSAJES DE ALERTA EN FORMA DE TOAST
      //SI SE CUMPLEN LOS IF QUIERE DECIR QUE NO PASA LOS REQUISITOS MINIMOS DE LLENADO...
      if (textoNombre == "") {
        M.toast({html: 'El campo Nombre Completo se encuentra vacío.', classes: 'rounded'});
      }else if(textoDireccion.length == ""){
        M.toast({html: 'El campo Dirección se encuentra vacío.', classes: 'rounded'});
      }else if(textoColonia == ""){
        M.toast({html: 'El campo Colonia se encuentra vacío.', classes: 'rounded'});
      }else if(textoCP == ""){
        M.toast({html: 'El campo Codigo Postal se encuentra vacío.', classes: 'rounded'});
      }else if(textoRFC.length < 12){
        M.toast({html: 'El RFC tiene que tener al menos 12 dijitos.', classes: 'rounded'});
      }else if(textoEmail == ""){
        M.toast({html:"Por favor ingrese un Email.", classes: "rounded"});
      }else if (!validar_email(textoEmail)) {
        M.toast({html:"Por favor ingrese un Email correcto.", classes: "rounded"});
      }else if(textoTelefono.length < 10){
        M.toast({html: 'El Telefono tiene que tener al menos 10 dijitos.', classes: 'rounded'});
      }else if(textoDias_Credito.length == ""){
        M.toast({html: 'El campo Días de crédito se encuentra vacío.', classes: 'rounded'});
      }else{
        //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
        //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_proveedor.php"
        $.post("../php/control_proveedor.php", {
          //Cada valor se separa por una ,
            accion: 0,
            valorNombre: textoNombre,
            valorDireccion: textoDireccion,
            valorColonia: textoColonia,
            valorCP: textoCP,
            valorRFC: textoRFC,
            valorEmail: textoEmail,
            valorTelefono: textoTelefono,
            valorDias_Credito: textoDias_Credito,
          }, function(mensaje) {
              //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_proveedor.php"
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
      <h3 class="hide-on-med-and-down">Registrar Proveedor</h3>
      <h5 class="hide-on-large-only">Registrar Proveedor</h5>
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
            <i class="material-icons prefix">people</i>
            <input id="nombre" type="text" class="validate" data-length="50" required>
            <label for="nombre">Nombre Completo:</label>
          </div>      
          <div class="input-field">
            <i class="material-icons prefix">location_city</i>
            <input id="direccion" type="text" class="validate" data-length="80" required>
            <label for="direccion">Dirección:</label>
          </div> 
          <div class="input-field">
            <i class="material-icons prefix">location_city</i>
            <input id="colonia" type="text" class="validate" data-length="35" required>
            <label for="colonia">Colonia:</label>
          </div>
          <div class="input-field">
            <i class="material-icons prefix">location_on</i>
            <input id="cp" type="text" class="validate" data-length="15" required>
            <label for="cp">Código Postal:</label>
          </div>         
        </div>
        <!-- DIV DOBLE COLUMNA EN ESCRITORIO PARTE DERECHA -->
        <div class="col s12 m6 l6">
          <br>
          <div class="input-field">
            <i class="material-icons prefix">contact_mail</i>
            <input id="rfc" type="text"  class="validate" data-length="100" required>
            <label for="rfc">RFC:</label>
          </div>
          <div class="input-field">
            <i class="material-icons prefix">email</i>
            <input id="email" type="text" class="validate" data-length="45" required>
            <label for="email">E-mail:</label>
          </div> 
          <div class="input-field">
            <i class="material-icons prefix">phone</i>
            <input id="telefono" type="text" class="validate" data-length="30" required>
            <label for="telefono">Teléfono:</label>
          </div> 
          <div class="input-field">
            <i class="material-icons prefix">date_range</i>
            <input id="dias_credito" type="number" class="validate" data-length="11" required>
            <label for="dias_credito">Días Crédito:</label>
          </div>
        </div>
      </form>
      <!-- BOTON QUE MANDA LLAMAR EL SCRIPT PARA QUE EL SCRIPT HAGA LO QUE LA FUNCION CONTENGA -->
      <a onclick="insert_proveedor();" class="waves-effect waves-light btn pink right"><i class="material-icons right">add</i>Agregar</a>
    </div> 
  </div><br>
</body>
</main>
</html>
