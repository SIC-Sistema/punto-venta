<html>
  <head>
  	<title>SIC | Registrar Compra</title>
    <?php 
    //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
    include('fredyNav.php');
    //ARCHIVO QUE RESTRINGE A QUE SOLO ALGUNOS USUARIOS PUEDAN ACCEDER
    include('../php/cobrador.php');
    //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL USUARIO Y ASIGNAMOS EL ARRAY A UNA VARIABLE $datos_user
    $user_id = $_SESSION['user_id'];
    $datos_user = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM users WHERE user_id=$user_id"));
    $id = $datos_user['almacen'];
    $datos_almacen = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM punto_venta_almacenes WHERE id=$id"));
    if ($datos_user['compras'] == 0) {
      ?>
      <script>    
        M.toast({html: "Permiso denegado.", classes: "rounded"});
        M.toast({html: "Contacta a un Administrador.", classes: "rounded"});
        setTimeout("location.href='compras_punto_venta.php'", 1000);
      </script>
      <?php
    }
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

      //FUNCION QUE HACE LA INSERCION DE LA VENTA (SE ACTIVA AL PRECIONAR UN BOTON)
      function insert_compra() {
        almacen = <?php echo $datos_user['almacen']; ?>;
        //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO LA INFORMCION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
        var textoFactura = $("input#factura").val();//ej:LA VARIABLE "textoFactura" GUARDAREMOS LA INFORMACION QUE ESTE EN EL SELECT QUE TENGA EL id = "factura"
        var textoProveedor = $("select#proveedor").val();

        if(document.getElementById('cambio').checked==true){
          textoTipoCambio = "Credito";  
        }else{    
          textoTipoCambio = "Contado";
        }

        // CREAMOS CONDICIONES QUE SI SE CUMPLEN MANDARA MENSAJES DE ALERTA EN FORMA DE TOAST
        //SI SE CUMPLEN LOS IF QUIERE DECIR QUE NO PASA LOS REQUISITOS MINIMOS DE LLENADO...
        if (textoProveedor == 0) {
          M.toast({html: 'Seleccione un Proveedor.', classes: 'rounded'});
        }else if (textoFactura == "") {
          M.toast({html: 'El campo Factura se encuentra vacío.', classes: 'rounded'});
        }else{
          //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
          $.post("../php/control_compra.php", {
            //Cada valor se separa por una ,
              accion: 0,
              almacen: almacen,
              valorProveedor: textoProveedor,
              valorFactura: textoFactura,
              valorTipoCambio: textoTipoCambio,
            }, function(mensaje) {
                //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
                $("#resultado_insert").html(mensaje);
            }); 
        }//FIN else CONDICIONES
      };//FIN function 

      //SI EL SISTEMA DETECTA DIFERENCIA DE PRECIOS CON ESTA FUNCION SE CAMBIA SI EL USUARIO LO DESEA
      function cambiar_precio(id_articulo) {
        var precio = $("input#precioCambio"+id_articulo).val();
        // CREAMOS CONDICIONES QUE SI SE CUMPLEN MANDARA MENSAJES DE ALERTA EN FORMA DE TOAST
        //SI SE CUMPLEN LOS IF QUIERE DECIR QUE NO PASA LOS REQUISITOS MINIMOS DE LLENADO...
        if (precio == "" || precio < 0) {
          M.toast({html: 'Coloque un Precio Fijo valido.', classes: 'rounded'});
        }else{
          //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
          //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_compra.php"
          $.post("../php/control_compra.php", {
              //Cada valor se separa por una ,
              accion: 9,
              id_articulo: id_articulo,
              precio: precio,
            }, function(mensaje){
                //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_compra.php"
                $("#cambio").html(mensaje);
          });//FIN post
        }//FIN else
      }
    </script>
  </head>
  <main>
  <body onload="tmp_articulos(<?php echo $user_id;?>,0)">
    <!-- DENTRO DE ESTE DIV VA TODO EL CONTENIDO Y HACE QUE SE VEA AL CENTRO DE LA PANTALLA.-->
    <div class="container" >
      <!--    //////    TITULO    ///////   -->
      <div class="row" >
        <h3 class="hide-on-med-and-down">Registrar Compra</h3>
        <h5 class="hide-on-large-only">Registrar Compra</h5>
      </div>
      <div class="row" >
        <!-- CREAMOS UN DIV EL CUAL TENGA id = "resultado_insert"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
        <div class="row" id="resultado_insert">
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
                      echo '<script>M.toast({html:"No se encontraron proveedores.", classes: "rounded"})</script>';
                    } else {
                      //RECORREMOS UNO A UNO LOS ARTICULOS CON EL WHILE
                      while($proveedor_pv = mysqli_fetch_array($consulta)) {
                      //Output
                      ?>                      
                      <option value="<?php echo $proveedor_pv['id'];?>"><?php echo $proveedor_pv['nombre'];?></option>-->
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