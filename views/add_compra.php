<html>
  <head>
  	<title>SIC | Registrar Compra</title>
    <?php 
    //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
    include('fredyNav.php');
    //ARCHIVO QUE RESTRINGE A QUE SOLO ALGUNOS USUARIOS PUEDAN ACCEDER
    include('../php/cobrador.php');
    //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL USUARIO Y ASIGNAMOS EL ARRAY A UNA VARIABLE $datos
    $user_id = $_SESSION['user_id'];
    $datos_user = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM users WHERE user_id=$user_id"));
    $id = $datos_user['almacen'];
    $datos_almacen = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM punto_venta_almacenes WHERE id=$id"));
    ?>
    <script>
      //FUINCION QUE AL SELECCIONAR UN PROOVEDOR MUESTRA SU INFORMACION
      function showContent() {
        element = document.getElementById("infoProveedor");
        var textoProveedor = $("select#proveedor").val();
        if (textoProveedor != 0) {
            element.style.display='block';
        } else {
            element.style.display='none';
        }  

        //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
        //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
        $.post("../php/control_compra.php", {
          //Cada valor se separa por una ,
            accion: 2,
            proveedor: textoProveedor,
          }, function(mensaje) {
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
            $("#resultado_info").html(mensaje);
        });  
      };

      function tmp_articulos(id, insert,id_art=0){
        if (insert) {
          //PEDIMOS VARIABLES Y CONDICIONES PARA INSERTAR ARTICULO A TMP
          M.toast({html: 'Insertar articulo N° '+id_art, classes: 'rounded'});
          //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
          $.post("../php/control_compra.php", {
            //Cada valor se separa por una ,
              accion: 4,
              insert: insert,
              id: id,
              id_art: id_art,
            }, function(mensaje) {
                //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
                $("#articulosCompra").html(mensaje);
            }); 
        }else{
          //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
          $.post("../php/control_compra.php", {
            //Cada valor se separa por una ,
              accion: 4,
              insert: insert,
              id: id,
            }, function(mensaje) {
                //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
                $("#articulosCompra").html(mensaje);
            }); 
        }//FIN ELSE insert
      }// FIN function

      //FUNCION QUE BORRA LOS ARTICULOS TMP (SE ACTIVA AL INICIAR EL BOTON BORRAR)
      function borrar_lista_articulo(id){
        var answer = confirm("Deseas eliminar el artículo N°"+id+" de la lista ?");
        if (answer) {
          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
          $.post("../php/control_compra.php", {
            //Cada valor se separa por una ,
            accion: 7,
            id: id,
          }, function(mensaje) {
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
            $("#articulosCompra").html(mensaje);
          }); //FIN post
        }//FIN IF
      };//FIN function

      //FUNCION QUE BORRA TODOS LOS ARTICULOS DE TMP (SE ACTIVA AL INICIAR EL BOTON BORRAR)
      function borrar_lista_all(usuario){
        var answer = confirm("Deseas cancelar la compra?");
        if (answer) {
          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
          $.post("../php/control_compra.php", {
            //Cada valor se separa por una ,
            accion: 8,
            usuario: usuario,
          }, function(mensaje) {
            //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
            $("#articulosCompra").html(mensaje);
          }); //FIN post
        }//FIN IF
      };//FIN function

      //FUNCION QUE HACE LA BUSQUEDA DE ARTICULOS (SE ACTIVA AL INICIAR EL ARCHIVO O AL ECRIBIR ALGO EN EL BUSCADOR)
      function buscar_articulos(){
        
        //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO EL TEXTO REQUERIDO Y LO ASIGNAMOS A UNA VARIABLE
        var texto = $("input#busquedaArticulo").val();
        //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
        $.post("../php/control_compra.php", {
            //Cada valor se separa por una ,
            accion: 6,
            texto: texto,
          }, function(mensaje){
              //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
              $("#tablaArticulo").html(mensaje);
        });//FIN post
      }//FIN function

      //FUNCION QUE MODIFICARA LOS VALORES DE LOS TOTALES
      function totales(id_art, id_usuario){
        //RECIBIMOS LOS VALORES DE LOS INPUTS AFECTADOS
        var CantidadA_Aux = $("input#cantidadA"+id_art).val();
        var PrecioC_Aux = $("input#precio_compra"+id_art).val();
        var Total_Aux = $("input#totalCompra").val();        
        var Importe_Aux = $("input#importe"+id_art).val();
        var CantidadA = parseFloat(CantidadA_Aux);
        var PrecioC = parseFloat(PrecioC_Aux);
        var Importe = parseFloat(Importe_Aux); 
        var Total = parseFloat(Total_Aux);

        //MODIFICAMOS LOS VALPORES DE LOS INPUTS EN CUANTO CAMBIE ALGUN VALOR
        document.getElementById("importe"+id_art).value = (PrecioC*CantidadA).toFixed(2);          
        document.getElementById("subtotal").value =((Total+((PrecioC*CantidadA)-Importe))-((Total+((PrecioC*CantidadA)-Importe))*0.16)).toFixed(2);
        document.getElementById("impuesto").value =((Total+((PrecioC*CantidadA)-Importe))*0.16).toFixed(2);
        document.getElementById("totalCompra").value =(Total+((PrecioC*CantidadA)-Importe)).toFixed(2);

        //REALIZAREMOS LOS CAMBIOS EN LA BASE DE DATOS
        //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
        //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
        $.post("../php/control_compra.php", {
            //Cada valor se separa por una ,
            accion: 5,
            valorIdArt: id_art,
            valorIdUs: id_usuario,
            valorCantidadA: CantidadA,
            valorPrecioU: PrecioC,
          }, function(mensaje){
              //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
              $("#resultado_insert").html(mensaje);
        });//FIN post
      }

      //FUNCION QUE HACE LA INSERCION DEL ARTICULO (SE ACTIVA AL PRECIONAR UN BOTON)
      function insert_compra() {

        //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO LA INFORMCION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
        var textoCodigo = $("input#codigo").val();//ej:LA VARIABLE "textoCodigo" GUARDAREMOS LA INFORMACION QUE ESTE EN EL INPUT QUE TENGA EL id = "codigo"
        var textoDescripcion = $("input#descripcion").val();// ej: TRAE LE INFORMACION DEL INPUT FILA  (id="descripcion")
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
          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
          $.post("../php/control_compra.php", {
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
                //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
                $("#resultado_insert").html(mensaje);
            }); 
        }//FIN else CONDICIONES
      };//FIN function 
    </script>
  </head>
  <main>
  <body onload="tmp_articulos(<?php echo $user_id;?>,0)">
    <!-- DENTRO DE ESTE DIV VA TODO EL CONTENIDO Y HACE QUE SE VEA AL CENTRO DE LA PANTALLA.-->
    <div class="container">
      <!-- CREAMOS UN DIV EL CUAL TENGA id = "resultado_insert"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
      <div id="resultado_insert"></div>
      <!--    //////    TITULO    ///////   -->
      <div class="row" >
        <h3 class="hide-on-med-and-down">Registrar Compra</h3>
        <h5 class="hide-on-large-only">Registrar Compra</h5>
      </div>
      <div class="row" >
       <div class="row">
        <!-- FORMULARIO EL CUAL SE MUETRA EN PANTALLA .-->
        <form class="row col s12" name="formCompras">
            <!-- CAJA DE SELECCION DE PROVEEDORES -->
            <hr>
            <div class="row">
              <div class="input-field col s12 m3 l3">
                <i class="material-icons prefix">people</i>
                <select id="proveedor" name="proveedor" class="validate" onchange="javascript:showContent()">
                  <!--OPTION PARA QUE LA SELECCION QUEDE POR DEFECTO VACIA-->
                  <option value="0" select>Seleccione un proveedor</option>
                  <?php 
                    // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
                    $consulta = mysqli_query($conn,"SELECT * FROM punto_venta_proveedores");
                    //VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
                    if (mysqli_num_rows($consulta) == 0) {
                      echo '<script>M.toast({html:"No se encontraron categorias.", classes: "rounded"})</script>';
                    } else {
                      //RECORREMOS UNO A UNO LOS ARTICULOS CON EL WHILE
                      while($proveedor_pv = mysqli_fetch_array($consulta)) {
                      //Output
                      ?>                      
                      <option value="<?php echo $proveedor_pv['id'];?>"><?php echo $proveedor_pv['nombre'];// MOSTRAMOS LA INFORMACION HTML?></option>-->
                      <?php
                    }//FIN while
                  }//FIN else
                  ?>
                </select>
              </div> 
              <div id="infoProveedor" class="col s12 m9 l9" style="display: none;"><br>
                <!-- CREAMOS UN DIV EL CUAL TENGA id = "resultado_info"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
                <div id="resultado_info"></div>
              </div>
            </div>
            <hr>
            <div  class="row">
              <div class="col s12 m6 l6">
                <h5 class="input-field col s4"><b>N° Factura</b></h5>
                <div class="input-field col s7">
                  <i class="material-icons prefix">tab</i>
                  <input id="factura" type="text" class="validate" data-length="50" required>
                  <label for="factura">(ej: 13436)</label>
                </div> 
              </div>
              <div class="col s12 m6 l6">
                <h4>Almacen N° <?php echo $datos_user['almacen'];?> - <?php echo $datos_almacen['nombre'];?></h4>
              </div>
            </div>
            <hr>            
            <a href="#modal_addArticulo" class="waves-effect waves-light btn-small modal-trigger pink right">Agregar Artículo<i class="material-icons left">add</i></a><br><br>
            <!-- CREAMOS UN DIV EL CUAL TENGA id = "articulosCompra"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
            <div class="row" id="articulosCompra">
            </div>
        </form>
      </div> 
    </div><br>
  </body>
  </main>
</html>