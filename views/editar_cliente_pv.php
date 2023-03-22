<?php
//VERIFICAMOS QUE SI NOS ENVIE POR POST EL ID DEL CLIENTE
if (isset($_POST['id']) == false) {
  ?>
  <script>    
    M.toast({html: "Regresando a clientes.", classes: "rounded"});
    setTimeout("location.href='clientes_punto_venta.php'", 800);
  </script>
  <?php
}else{
?>
  <html>
  <head>
  	<title>SIC | Editar Clientes</title>
    <?php 
    //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
    include('fredyNav.php');
    $id = $_POST['id'];// POR EL METODO POST RECIBIMOS EL ID DEL CLIENTE
    //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL CLIENTE Y ASIGNAMOS EL ARRAY A UNA VARIABLE $datos
    $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto-venta_clientes` WHERE id=$id"));
    ?>
    <script>
      //FUNCION QUE AL USAR VALIDA LA VARIABLE QUE LLEVE UN FORMATO DE CORREO 
      function validar_email( email )   {
        var regex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email) ? true : false;
      };

      //FUNCION QUE HACE LA ACTUALIZACION DEL CLIENTE (SE ACTIVA AL PRECIONAR UN BOTON)
        function update_cliente(id) {
        var textoNombre = $("input#nombre").val();//ej:LA VARIABLE "textoNombre" GUARDAREMOS LA INFORMACION QUE ESTE EN EL INPUT QUE TENGA EL id = "nombre"
        var textoTelefono = $("input#telefono").val();// ej: TRAE LE INFORMACION DEL INPUT FILA 95 (id="telefono")
        var textoEmail = $("input#email").val();
        var textoRFC = $("input#rfc").val();
        var textoLocalidad = $("input#localidad").val();
        var valorEstado = $("select#estado").val();
        var textoCalle = $("input#calle").val();
        var textoNumeroInterior = $("input#numero_interior").val();
        var textoNumeroExterior = $("input#numero_exterior").val();
        var textoColonia = $("input#colonia").val();
        var textoMunicipio = $("input#municipio").val();
        var textoCP = $("input#cp").val();

        // CREAMOS CONDICIONES QUE SI SE CUMPLEN MANDARA MENSAJES DE ALERTA EN FORMA DE TOAST
        //SI SE CUMPLEN LOS IF QUIERE DECIR QUE NO PASA LOS REQUISITOS MINIMOS DE LLENADO...
      if (textoNombre == "") {
        M.toast({html: 'El campo Nombre Completo se encuentra vacío.', classes: 'rounded'});
      }else if(textoTelefono.length < 10){
        M.toast({html: 'El Telefono tiene que tener al menos 10 digitos.', classes: 'rounded'});
      }else if(textoEmail == ""){
        M.toast({html:"Por favor ingrese un Email.", classes: "rounded"});
      }else if (!validar_email(textoEmail)) {
        M.toast({html:"Por favor ingrese un Email correcto.", classes: "rounded"});
      }else if(textoRFC.length < 12){
        M.toast({html: 'El RFC tiene que tener al menos 12 digitos.', classes: 'rounded'});
      }else if(textoRFC.length > 13){
        M.toast({html: 'RFC excede digitos.', classes: 'rounded'});
      }else if(valorEstado == 0){
        M.toast({html: 'Seleccione un estado de la república.', classes: 'rounded'});
      }else if(textoCalle == ""){
        M.toast({html: 'El campo Calle se encuentra vacío.', classes: 'rounded'});
      }else if(textoNumeroExterior == ""){
        M.toast({html: 'El campo Numero Interior se encuentra vacío.', classes: 'rounded'});
      }else if(textoColonia == ""){
        M.toast({html: 'El campo Colonia se encuentra vacío.', classes: 'rounded'});
      }else if(textoMunicipio == ""){
        M.toast({html: 'El campo Municipio se encuentra vacío.', classes: 'rounded'});
      }else if(textoLocalidad == ""){
        M.toast({html: 'El campo Localidad se encuentra vacío.', classes: 'rounded'});
      }else if(textoCP == ""){
        M.toast({html: 'El campo Codigo Postal se encuentra vacío.', classes: 'rounded'});
      }else if(textoCP.length < 5){
        M.toast({html: 'El campo Codigo Postal está incompleto.', classes: 'rounded'});
      }else{
          //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO NE LA DIRECCION "../php/control_clientes.php"
          $.post("../php/control_clientes.php", {
            //Cada valor se separa por una ,
              accion: 2,
              id: id,
              valorNombre: textoNombre,
              valorTelefono: textoTelefono,
              valorEmail: textoEmail,
              valorRFC: textoRFC,
              valorEstadoMx: valorEstado,
              valorCalle: textoCalle,
              valorNumeroInterior: textoNumeroInterior,
              valorNumeroExterior: textoNumeroExterior,
              valorColonia: textoColonia,
              valorMunicipio: textoMunicipio,
              valorLocalidad: textoLocalidad,
              valorCP: textoCP,
            }, function(mensaje) {
                //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_clientes.php"
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
        <h3 class="hide-on-med-and-down">Editar Cliente N°<?php echo $id; ?></h3>
        <h5 class="hide-on-large-only">Editar Cliente N°<?php echo $id; ?></h5>
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
              <i class="material-icons prefix">people</i>
              <input id="nombre" type="text" class="validate" data-length="50" required value="<?php echo $datos['nombre']; ?>">
              <label for="nombre">Nombre Completo:</label>
            </div>      
            <div class="input-field">
              <i class="material-icons prefix">phone</i>
              <input id="telefono" type="number" class="validate" data-length="13" required value="<?php echo $datos['telefono']; ?>">
              <label for="telefono">Teléfono:</label>
            </div> 
            <div class="input-field">
              <i class="material-icons prefix">mail</i>
              <input id="email" type="text" class="validate" data-length="30" required value="<?php echo $datos['email']; ?>">
              <label for="email">E-mail:</label>
            </div>
            <div class="input-field">
              <i class="material-icons prefix">person</i>
              <input id="rfc" type="text" class="validate" data-length="15" required value="<?php echo $datos['rfc']; ?>">
              <label for="rfc">RFC:</label>
            </div>    
            <div class="input-field">
              <i class="material-icons prefix">location_city</i>
              <input id="localidad" type="text" class="validate" data-length="30" required value="<?php echo $datos['localidad']; ?>">
              <label for="localidad">Localidad:</label>
            </div> 
            <div class="input-field">
            <select id="estado" name="estado" class="browser-default">
                <!--OPTION PARA QUE LA SELECCION QUEDE POR DEFECTO VACIA-->
                <?php
                  $idEstado = $datos['estado'];
                  $consultaEstado = mysqli_query($conn, "SELECT * FROM estados_mex WHERE id=$idEstado");
                  if (mysqli_num_rows($consultaEstado) == 0){
                    echo '<script>M.toast({html:"Hubo un error al obtener la información del estado.", classes: "rounded"})</script>';
                  }else{
                    while($seleccionEstado =mysqli_fetch_array($consultaEstado)){
                     ?>
                      <option value="<?php echo $seleccionEstado['id'];?>"><?php echo $seleccionEstado['nombre'];?></option>
                    <?php
                    }
                  }
                ?>              
                <?php 
                  // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
                  $consulta = mysqli_query($conn,"SELECT * FROM estados_mex");
                  //VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
                  if (mysqli_num_rows($consulta) == 0) {
                    echo '<script>M.toast({html:"No se encontraron estados.", classes: "rounded"})</script>';
                  } else {
                    //RECORREMOS UNO A UNO LOS ARTICULOS CON EL WHILE
                    while($estados = mysqli_fetch_array($consulta)) {
                    //Output
                    ?>                      
                    <option value="<?php echo $estados['id'];?>"><?php echo $estados['nombre'];?></option>
                    <?php
                  }//FIN while
                }//FIN else
                ?>
              </select>
          </div>       
          </div>
          <!-- DIV DOBLE COLUMNA EN ESCRITORIO PARTE DERECHA -->
          <div class="col s12 m6 l6">
            <br>
            <div class="input-field">
              <i class="material-icons prefix">location_on</i>
              <input id="calle" type="text"  class="validate" data-length="100" required value="<?php echo $datos['calle']; ?>">
              <label for="calle">Calle:</label>
            </div>
            <div class="input-field">
              <i class="material-icons prefix">location_city</i>
              <input id="numero_exterior" type="number" class="validate" data-length="30" required value="<?php echo $datos['numero_exterior']; ?>">
              <label for="numero_exterior">Número exterior:</label>
            </div> 
            <div class="input-field">
              <i class="material-icons prefix">location_city</i>
              <input id="numero_interior" type="number" class="validate" data-length="30" required value="<?php echo $datos['numero_interior']; ?>">
              <label for="numero_interior">Número interior (opcional):</label>
            </div> 
            <div class="input-field">
              <i class="material-icons prefix">location_on</i>
              <input id="colonia" type="text"  class="validate" data-length="100" required value="<?php echo $datos['colonia']; ?>">
              <label for="colonia">Colonia:</label>
            </div>
            <div class="input-field">
              <i class="material-icons prefix">location_on</i>
              <input id="municipio" type="text"  class="validate" data-length="100" required value="<?php echo $datos['municipio']; ?>">
              <label for="municipio">Municipio:</label>
            </div>
            <div class="input-field">
              <i class="material-icons prefix">location_on</i>
              <input id="cp" type="number" class="validate" data-length="6" required value="<?php echo $datos['cp']; ?>">
              <label for="cp">Codigo Postal:</label>
            </div>
          </div>
        </form>
        <!-- BOTON QUE MANDA LLAMAR EL SCRIPT PARA QUE EL SCRIPR HAGA LO QUE LA FUNCION CONTENGA -->
        <a onclick="update_cliente(<?php echo $id; ?>);" class="waves-effect waves-light btn pink right"><i class="material-icons right">save</i>Guardar</a>
      </div> 
    </div><br>
  </body>
  </main>
  </html>
<?php
}// FIN else POST
?>