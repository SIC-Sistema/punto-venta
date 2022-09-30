<?php
//VERIFICAMOS QUE SI NOS ENVIE POR POST EL ID DEL USUARIO
if (isset($_POST['id']) == false) {
  ?>
  <script>    
    M.toast({html: "Regresando a usuarios.", classes: "rounded"})
    setTimeout("location.href='usuarios.php'", 800);
  </script>
  <?php
}else{
?>
  <!DOCTYPE html>
  <html>
    <head>
    	<title>SIC | Permisos Usuarios</title>
      <?php
      //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
      include('fredyNav.php');
      $id = $_POST['id'];// POR EL METODO POST RECIBIMOS EL ID DEL USUARIO
      //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL USUARIO Y ASIGNAMOS EL ARRAY A UNA VARIABLE $datos
      $datos = mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM users WHERE user_id=$id"));
      $id_almacen_variable = $datos['almacen'];
      $almacen_variable = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_almacenes` WHERE id=$id_almacen_variable"));
      ?>
      <script>
        //FUNCION QUE ENVIA LA INFORMACION PARA CAMBIR LOS PERMISOIS DEL CLIENTE SELECCIONADO
        function cambiar_permisos(id){  
          //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO LA INFORMCION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
          var textoAlmacen = $("select#almacen").val();//ej:LA VARIABLE "textoAlmacen" GUARDAREMOS LA INFORMACION QUE ESTE EN EL SELECT QUE TENGA EL id = "almacen"  
          //SE VERIFICA SI EL SELECT DEL PERMISO ESTA SELECCIONADO O NO Y SE DA UN VALOR
          if(document.getElementById('banco').checked==true){
            Banco = 1;
          }else { Banco = 0; }
          if(document.getElementById('credito').checked==true){
            Credito = 1;
          }else { Credito = 0; }
          if(document.getElementById('b_pagos').checked==true){
            BorrarPagos = 1;
          }else { BorrarPagos = 0; }
          if(document.getElementById('b_clientes').checked==true){
            BorrarClientes = 1;
          }else { BorrarClientes = 0; }
          if(document.getElementById('b_ventas').checked==true){
            BorrarVentas = 1;
          }else { BorrarVentas = 0; }
          if(document.getElementById('b_almacenes').checked==true){
            BorrarAlmacenes = 1;
          }else { BorrarAlmacenes = 0; }
          if(document.getElementById('ventas').checked==true){
            Ventas = 1;
          }else { Ventas = 0; }
          if(document.getElementById('compras').checked==true){
            Compras = 1;
          }else { Compras = 0; }
          if(document.getElementById('articulos').checked==true){
            Articulos = 1;
          }else { Articulos = 0; }
          if (textoAlmacen == 0) {
            M.toast({html: 'El campo Almacen se encuentra vacío.', classes: 'rounded'});
          }else{

            //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO NE LA DIRECCION "../php/control_users.php"
            $.post("../php/control_users.php", { 
              //Cada valor se separa por ,
                accion: 4,
                id: id,
                Banco: Banco,
                Credito: Credito,
                BorrarPagos: BorrarPagos,
                BorrarClientes: BorrarClientes,
                BorrarVentas: BorrarVentas,
                BorrarAlmacenes: BorrarAlmacenes,
                Ventas: Ventas,
                Compras: Compras,                
                Articulos: Articulos,
                valorAlmacen: textoAlmacen,                
            }, function(mensaje) {
              //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_users.php"
                $("#cambio_permisos").html(mensaje);   
            });//FIN post
          }
        }//FIN function
      </script>
    </head>
    <body>
      <!-- DENTRO DE ESTE DIV VA TODO EL CONTENIDO Y HACE QUE SE VEA AL CENTRO DE LA PANTALLA.-->
    	<div class="container">
        <!--    //////    TITULO    ///////   -->
    		<div class="row">
    			<h2 class="hide-on-med-and-down">Permisos del Usuario:</h2>
     			<h4 class="hide-on-large-only">Permisos del Usuario:</h4>
    		</div>
        <!--   //// INFORMACION DEL USUARIO  //// --->
    		<div class="row">
    			<ul class="collection">
                <li class="collection-item avatar">
                  <img src="../img/cliente.png" alt="" class="circle">
                  <span class="title"><b>N°: </b><?php echo $id; ?></span>
                  <p><b>Nombre(s): </b><?php echo $datos['firstname'].' '.$datos['lastname']; ?><br>
                     <b>Usuario: </b><?php echo $datos['user_name']; ?><br>
                     <b>Email: </b><?php echo $datos['user_email']; ?><br>
                     <b>Area: </b><?php echo $datos['area']; ?><br>
                  </p>
                </li>
            </ul>		
    		</div>
        <div class="row"><br>
          <h3 class="hide-on-med-and-down">Permisos:</h3>
          <h5 class="hide-on-large-only">Permisos:</h5>
        </div>
        <!-- ///// FORMULARIO QUE MUESTRA LOS CHECK DE PERMISOS ////-->
        <div class="row">
            <div class="col s6 m3 l3">
              <p>
                <br>
                <label>
                  <input type="checkbox" <?php echo ($datos['banco'] == 1)?"checked":"";?> id="banco"/>
                  <span for="banco">Banco</span>
                </label>
              </p>
            </div>
            <div class="col s6 m3 l3">
              <p>
                <br>
                <label>
                  <input type="checkbox" <?php echo ($datos['credito']  == 1)?"checked":"";?> id="credito"/>
                  <span for="credito">Credito</span>
                </label>
              </p>
            </div>
            <div class="col s6 m3 l3">
              <p>
                <br>
                <label>
                  <input type="checkbox" <?php echo ($datos['b_pagos']  == 1)?"checked":"";?> id="b_pagos"/>
                  <span for="b_pagos">Borrar Pagos</span>
                </label>
              </p>
            </div>
            <div class="col s6 m3 l3">
              <p>
                <br>
                <label>
                  <input type="checkbox" <?php echo ($datos['b_clientes']  == 1)?"checked":"";?> id="b_clientes"/>
                  <span for="b_clientes">Borrar Clientes</span>
                </label>
              </p>
            </div>
            <div class="col s6 m3 l3">
              <p>
                <br>
                <label>
                  <input  type="checkbox" <?php echo ($datos['b_ventas']  == 1)?"checked":"";?> id="b_ventas"/>
                  <span for="b_ventas">Borrar Ventas</span>
                </label>
              </p>
            </div>
            <div class="col s6 m3 l3">
              <p>
                <br>
                <label>
                  <input  type="checkbox" <?php echo ($datos['b_almacenes']  == 1)?"checked":"";?> id="b_almacenes"/>
                  <span for="b_almacenes">Borrar Almacenes</span>
                </label>
              </p>
            </div>
            <div class="col s6 m3 l3">
              <p>
                <br>
                <label>
                  <input type="checkbox" <?php echo ($datos['ventas']  == 1)?"checked":"";?> id="ventas"/>
                  <span for="ventas">Ventas</span>
                </label>
              </p>
            </div>
            <div class="col s6 m3 l3">
              <p>
                <br>
                <label>
                  <input type="checkbox" <?php echo ($datos['compras']  == 1)?"checked":"";?> id="compras"/>
                  <span for="compras">Compras</span>
                </label>
              </p>
            </div>
             <div class="col s6 m3 l3">
              <p>
                <br>
                <label>
                  <input type="checkbox" <?php echo ($datos['b_articulos']  == 1)?"checked":"";?> id="articulos"/>
                  <span for="articulos">Borrar Articulos</span>
                </label>
              </p>
            </div>    
        </div>
          <!--Sub encabezado-->
          <div class="row"><br>
            <h3 class="hide-on-med-and-down">Asignar Almacen a Usuario:</h3>
            <h5 class="hide-on-large-only">Asignar Almacen a Usuario:</h5>
          </div>
          <!-- CAJA DE SELECCION DE ALMACENES -->
          <div class="input-field">
            <i class="material-icons prefix">view_list</i>
            <select id="almacen" name="almacen" class="validate">
              <!-- CONDICIÓN IF PARA QUE EL SELECT APAREZCA CON LA INFORMACIÓN CARGADA PREVIEMAENTE EN CASO DE CONTENER-->
              <?php if($id_almacen_variable == 0):?>
              <!--OPTION PARA QUE LA SELECCION QUEDE POR DEFECTO VACIA-->
              <option value="0" select>Seleccione un almacen</option>
              <?php else: ?>
              <!--OPTION PARA NO SELECCIONAR NADA-->
              <option value="<?php echo $datos['almacen']; ?>" select><?php echo $almacen_variable['nombre'];?></option>
              <option value="0">--Ninguno--</option>
              <?php endif ?><!-- END IF PHP -->
              <?php 
                // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
                $consulta = mysqli_query($conn,"SELECT * FROM punto_venta_almacenes");
                //VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
                if (mysqli_num_rows($consulta) == 0) {
                  echo '<script>M.toast({html:"No se encontraron almacenes.", classes: "rounded"})</script>';
                } else {
                  //RECORREMOS UNO A UNO LOS ARTICULOS CON EL WHILE
                  while($almacen_pv = mysqli_fetch_array($consulta)) {
                    //Output
                    ?>                      
                    <option value="<?php echo $almacen_pv['id'];?>"><?php echo $almacen_pv['nombre'];// MOSTRAMOS LA INFORMACION HTML?></option>
                    <?php
                  }//FIN while
                }//FIN else
              ?>
            </select>
          </div>
           <!--BOTON DE GURARDAR PERMISOS-->
          <div class="row s12">
            <a onclick="cambiar_permisos(<?php echo $id; ?>);" class="waves-effect waves-light btn pink right"><i class="material-icons right">save</i>Guardar Todo</a>
          </div><br><br>
        <!-- CREAMOS UN DIV EL CUAL TENGA id="cambio_permisos" PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
        <div id="cambio_permisos"></div>
    	</div><!--DIV DEL CONTAINER-->
    </body>
  </html>
<?php 
}
?>