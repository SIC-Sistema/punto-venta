<?php
//VERIFICAMOS QUE SI NOS ENVIE POR POST EL ID DEL CLIENTE
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
      ?>
      <script>
        //FUNCION QUE ENVIA LA INFORMACION PARA CAMBIR LOS PERMISOIS DEL CLIENTE SELECCIONADO
        function cambiar_permisos(id){    
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
            }, function(mensaje) {
              //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_users.php"
                $("#cambio_permisos").html(mensaje);   
            });//FIN post
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
        <div class="row s12">
          <a onclick="cambiar_permisos(<?php echo $id; ?>);" class="waves-effect waves-light btn pink right"><i class="material-icons right">save</i>Guardar</a>
        </div><br><br>
        <!-- CREAMOS UN DIV EL CUAL TENGA id="cambio_permisos" PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
        <div id="cambio_permisos"></div>
    	</div>
    </body>
  </html>
<?php 
}
?>