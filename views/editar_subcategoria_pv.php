<?php
//VERIFICAMOS QUE SI NOS ENVIE POR POST EL ID DEL ARTICULO
if (isset($_POST['id']) == false) {
  ?>
  <script>    
    M.toast({html: "Regresando a Categorias.", classes: "rounded"});
    setTimeout("location.href='categorias_punto_venta.php'", 800);
  </script>
  <?php
}else{
?>
  <html>
    <head>
    	<title>SIC | Editar subcategoria</title>
      <?php 
      //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
      include('fredyNav.php');
      $id = $_POST['id'];// POR EL METODO POST RECIBIMOS EL ID DEL ARTICULO
      //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL ARTICULO Y ASIGNAMOS EL ARRAY A UNA VARIABLE $datos
      $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_categorias` WHERE id=$id"));
      ?>
      <script>
        //FUNCION QUE HACE LA ACTUALIZACION DE LA CATEGORIA (SE ACTIVA AL PRECIONAR UN BOTON)
        function update_categoria(id) {
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
                  accion: 6,
                  id: id,
                  valorNombre: textoNombre,
              }, function(mensaje) {
                  //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_categoria.php"
                  $("#resultado_update").html(mensaje);
              }); 
          }//FIN else CONDICIONES
        };//FIN function 
      </script>
    </head>
    <body>
      <!-- DENTRO DE ESTE DIV VA TODO EL CONTENIDO Y HACE QUE SE VEA AL CENTRO DE LA PANTALLA.-->
      <div class="container"><br><br>
        <!--    //////    TITULO    ///////   -->
        <div class="row" >
          <h3 class="hide-on-med-and-down">Editar subcategoría <?php echo $datos['nombre_sub']; ?></h3>
          <h5 class="hide-on-large-only">Editar subcategoría<?php echo $datos['nombre_sub']; ?></h5>
        </div>
        <div class="row" >
         <!-- CREAMOS UN DIV EL CUAL TENGA id = "resultado_update"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
         <div id="resultado_update"></div>
         <div class="row">
          <!-- FORMULARIO EL CUAL SE MUETRA EN PANTALLA .-->
          <form class="row col s12">
            <div class="col s12 m6 l6">
              <br>    
              <div class="input-field">
                <i class="material-icons prefix">edit</i>
                <input id="nombre" type="text" class="validate" data-length="30" required value="<?php echo $datos['nombre_sub']; ?>">
                <label for="nombre">Nombre:</label>
              </div>      
            </div>
          </form>
          <!-- BOTON QUE MANDA LLAMAR EL SCRIPT PARA QUE EL SCRIPT HAGA LO QUE LA FUNCION CONTENGA -->
          <a onclick="update_categoria(<?php echo $id; ?>);" class="waves-effect waves-light btn pink right"><i class="material-icons right">save</i>Guardar</a>
        </div> 
      </div><br>
    </body>
  </html>
<?php
}// FIN else POST
?>