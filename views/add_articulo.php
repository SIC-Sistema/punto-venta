<html>
  <head>
  	<title>SIC | Agregar Artículos</title>
    <?php 
    //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
    include('fredyNav.php');
    //ARCHIVO QUE RESTRINGE A QUE SOLO ALGUNOS USUARIOS PUEDAN ACCEDER
    include('../php/cobrador.php');
    ?>
    <script>
      //FUNCION QUE HACE LA INSERCION DEL ARTICULO (SE ACTIVA AL PRECIONAR UN BOTON)
      function insert_articulo() {

        //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO LA INFORMCION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
        var textoCodigo = $("input#codigo").val();//ej:LA VARIABLE "textoCodigo" GUARDAREMOS LA INFORMACION QUE ESTE EN EL INPUT QUE TENGA EL id = "codigo"
        var textoDescripcion = $("input#descripcion").val();// ej: TRAE LE INFORMACION DEL INPUT FILA 142 (id="descripcion")
        var textoNombre = $("input#nombre").val();
        var textoModelo = $("input#modelo").val();
        var textoPrecio = $("input#precio").val();
        var textoUnidad = $("input#unidad").val();
        var textoCUnidad = $("input#codigo_unidad").val();
        var textoCFiscal = $("input#c_fiscal").val();
        var textoCategoria = $("select#categoria").val();

        // CREAMOS CONDICIONES QUE SI SE CUMPLEN MANDARA MENSAJES DE ALERTA EN FORMA DE TOAST
        //SI SE CUMPLEN LOS IF QUIERE DECIR QUE NO PASA LOS REQUISITOS MINIMOS DE LLENADO...
        if (textoCodigo == "") {
          M.toast({html: 'El campo Código se encuentra vacío.', classes: 'rounded'});
        }else if (textoNombre == "") {
          M.toast({html: 'El campo Nombre se encuentra vacío.', classes: 'rounded'});
        }else if (textoModelo == "") {
          M.toast({html: 'El campo Marca se encuentra vacío.', classes: 'rounded'});
        }else if(textoDescripcion.length == ""){
          M.toast({html: 'El campo Descripción se encuentra vacío.', classes: 'rounded'});
        }else if(textoPrecio <= 0){
          M.toast({html: 'El campo Precio no puede ser menor o igual a 0.', classes: 'rounded'});
        }else if(textoUnidad == ""){
          M.toast({html: 'El campo Unidad se encuentra vacío.', classes: 'rounded'});
        }else if(textoCUnidad == ""){
          M.toast({html: 'El campo Codigo Unidad se encuentra vacío.', classes: 'rounded'});
        }else if(textoCFiscal == ""){
          M.toast({html: 'El campo Codigo Fiscal se encuentra vacío.', classes: 'rounded'});
        }else if(textoCategoria == 0){
          M.toast({html: 'El campo de Categoria se encuentra vacío.', classes: 'rounded'});
        }else{
          //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_articulo.php"
          $.post("../php/control_articulo.php", {
            //Cada valor se separa por una ,
              accion: 0,
              valorCodigo: textoCodigo,
              valorNombre: textoNombre,
              valorModelo: textoModelo,
              valorDescripcion: textoDescripcion,
              valorPrecio: textoPrecio,
              valorUnidad: textoUnidad,
              valorCUnidad: textoCUnidad,
              valorCFiscal: textoCFiscal,
              valorCategoria: textoCategoria,
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
              <i class="material-icons prefix">fiber_pin</i>
              <input id="codigo" type="number" class="validate" data-length="50" required>
              <label for="codigo">Código de Artículo:</label>
            </div>      
            <div class="input-field">
              <i class="material-icons prefix">edit</i>
              <input id="nombre" type="text" class="validate" data-length="30" required>
              <label for="nombre">Nombre:</label>
            </div>  
            <div class="input-field">
              <i class="material-icons prefix">local_offer</i>
              <input id="modelo" type="text" class="validate" data-length="30" required>
              <label for="modelo">Marca:</label>
            </div>  
            <div class="input-field">
              <i class="material-icons prefix">vpn_key</i>
              <input id="c_fiscal" type="text" class="validate" data-length="80" required>
              <label for="c_fiscal">Código Fiscal:</label>
            </div>
            <!-- CAJA DE SELECCION DE CATEGORIAS -->
            <div class="input-field">
              <i class="material-icons prefix">view_list</i>
              <!--<label for="categoria">Categoria:</label>-->
              <select id="categoria" name="categoria" class="validate">
                <!--OPTION PARA QUE LA SELECCION QUEDE POR DEFECTO VACIA-->
                <option value="0" select>Seleccione una categoria</option>
                <?php 
                  // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
                  $consulta = mysqli_query($conn,"SELECT * FROM punto_venta_categorias");
                  //VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
                  if (mysqli_num_rows($consulta) == 0) {
                    echo '<script>M.toast({html:"No se encontraron categorias.", classes: "rounded"})</script>';
                  } else {
                    //RECORREMOS UNO A UNO LOS ARTICULOS CON EL WHILE
                    while($categoria_pv = mysqli_fetch_array($consulta)) {
                    //Output
                    ?>                      
                    <option value="<?php echo $categoria_pv['id'];?>"><?php echo $categoria_pv['nombre'];// MOSTRAMOS LA INFORMACION HTML?></option>-->
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
              <i class="material-icons prefix">attach_money</i>
              <input id="precio" type="number" class="validate" data-length="35" required>
              <label for="precio">Precio:</label>
            </div>
            <div class="input-field">
              <i class="material-icons prefix">description</i>
              <input id="descripcion" type="text" class="validate" data-length="80" required>
              <label for="descripcion">Descripción:</label>
            </div> 
            <div class="input-field">
              <i class="material-icons prefix">local_offer</i>
              <input id="unidad" type="text" class="validate" data-length="15" required>
              <label for="unidad">Unidad:</label>
            </div>
            <div class="input-field">
              <i class="material-icons prefix">vpn_key</i>
              <input id="codigo_unidad" type="text" class="validate" data-length="15" required>
              <label for="codigo_unidad">Código Unidad:</label>
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