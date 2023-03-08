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
  <script>
    function cargarSubcategorias(){
      var categoriaId = $("input#id_cat").val();
      $.post("../php/control_categoria.php", {
      //Cada valor se separa por una ,
      accion: 5,
      categoriaId: categoriaId
      }, function(mensaje) {
        //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_categoria.php"
        $("#tablaSubcategorias").html(mensaje);
      }); 
    }

    //FUNCION QUE BORRA LAS SUBCATEGORIAS (SE ACTIVA AL INICIAR EL BOTON BORRAR)
    function borrar_subcategoria_pv(id){
      var answer = confirm("Deseas eliminar la subcategoria N°"+id+"?");
      if (answer) {
        //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_categoria.php"
        $.post("../php/control_categoria.php", {
        //Cada valor se separa por una ,
        id: id,
        accion: 7,
        }, function(mensaje) {
          //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_categoria.php"
          $("#borrarCategoria").html(mensaje);
        }); //FIN post
      }//FIN IF
    };//FIN function    
  </script>
    <title>SIC | Editar Subcategoria</title>
    <?php 
      //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
      include('fredyNav.php');
      $id = $_POST['id'];// POR EL METODO POST RECIBIMOS EL ID DEL ARTICULO
      //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL ARTICULO Y ASIGNAMOS EL ARRAY A UNA VARIABLE $datos
      $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_categorias` WHERE parent_id = $id"));
      ?>
      <input id="id_cat"  name="id_cat" value = "<?php echo $id?>" type="hidden" >
    </head>
    <body onload="cargarSubcategorias()">
      <!-- DENTRO DE ESTE DIV VA TODO EL CONTENIDO Y HACE QUE SE VEA AL CENTRO DE LA PANTALLA.-->
      <div class="container"><br><br>
        <!--    //////    TITULO    ///////   -->
        <div class="row" >
          <h3 class="hide-on-med-and-down">Listado de subcategorías </h3>
          <h5 class="hide-on-large-only">Listado de subcategorias </h5>
        </div>
        <div class="row" >
         <!-- CREAMOS UN DIV EL CUAL TENGA id = "resultado_update"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
         <div id="borrarCategoria"></div>
         <div class="row">
          <!-- FORMULARIO EL CUAL SE MUETRA EN PANTALLA .-->
          <form class="row col s12">
          <div class="col s12 m12 l12">
            <br>    
          <!--    //////    TABLA QUE MUESTRA LA INFORMACION DE LOS ARTICULO    ///////   -->
            <div class="row">
              <table class="bordered highlight responsive-table">
                <thead>
                  <tr>
                    <th>Subcategoría</th>
                    <th>Categoría</th>
                    <th>Editar</th>
                    <th>Borrar</th>
                  </tr>
                </thead>
                <!-- DENTRO DEL tbody COLOCAMOS id = "categoriasALL"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION buscar_categorias() -->
                <tbody id="tablaSubcategorias">
                </tbody>
              </table>
            </div>      
          </div>
        </div>
      </form>  
      </div> 
    </div><br>
    </body>
  </html>
<?php
}// FIN else POST
?>