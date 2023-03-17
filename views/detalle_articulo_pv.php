<?php
//VERIFICAMOS QUE SI NOS ENVIE POR POST EL ID DEL USUARIO
if (isset($_POST['articulo']) == false) {
  ?>
  <script>    
    M.toast({html: "Regresando a Cotizaciones.", classes: "rounded"})
    setTimeout("location.href='articulos_punto_venta.php'", 800);
  </script>
  <?php
}else{
?>
  <!DOCTYPE html>
  <html>
    <head>
    	<title>SIC | Detalles Artículo</title>
      <?php
      //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
      include('fredyNav.php');
      $id_articulo = $_POST['articulo'];// POR EL METODO POST RECIBIMOS EL ID DE LA COTIZACIÓN DEL ARCHIVO control_articulo.php EN EL CASO 1
      $id = $_SESSION['user_id'];//  RECIBIMOS EL ID DEL USUARIO LOGEADO
      //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL USUARIO Y ASIGNAMOS EL ARRAY A UNA VARIABLE $datos_user
      $datos_user = mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM users WHERE user_id=$id"));
      $Articulo = mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM punto_venta_articulos WHERE id=$id_articulo"));

      //Nombre de usuario que registro
      $id_usuario_registro = $Articulo['usuario'];
      $Info_usuario =  mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM users WHERE user_id=$id_usuario_registro"));
      $Nombre_usuario = $Info_usuario['firstname'];

      //SE SELECCIONA LAS EXISTENCIAS DEL ARTICULO DEL ALMACEN GENERAL
      $AlmacenGeneral =  mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM `punto_venta_almacen_general` WHERE id_articulo=$id_articulo"));
      if(isset($AlmacenGeneral['cantidad']) && $AlmacenGeneral['cantidad'] != NULL && $AlmacenGeneral['cantidad'] != NAN && $AlmacenGeneral['cantidad'] != 0 && $AlmacenGeneral['cantidad'] != ''){
        $Existencias = $AlmacenGeneral['cantidad'];
      }else{
        $Existencias = "Sin Existencias en el Almacen General";
      }

      //SE ASIGNA EL NOBRE DE LA CATEGORIA
      if(isset($Articulo['categoria']) && $Articulo['categoria'] != NULL && $Articulo['categoria'] != NAN && $Articulo['categoria'] != 0 && $Articulo['categoria'] != ''){
        $id_categoria = $Articulo['categoria'];
        $Categoria =  mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM `punto_venta_categorias` WHERE id=$id_categoria"));
        $NombreCategoria = $Categoria['nombre'];
      }else{
        $NombreCategoria = "Sin Categoria Definida";
      }

      //SE ASIGNA NOMBRE DE LA SUBCATEGORIA
      if(isset($Articulo['categoria']) && $Articulo['categoria'] != NULL && $Articulo['categoria'] != NAN && $Articulo['categoria'] != 0 && $Articulo['categoria'] != ''){
        $id_categoria = $Articulo['categoria'];
        $Categoria =  mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM `punto_venta_categorias` WHERE id=$id_categoria"));
        $NombreCategoria = $Categoria['nombre'];
      }else{
        $NombreCategoria = "Sin Categoria Definida";
      }
      ?>
    </head>
    
    <body>
      <div class="row">
        <!-- Div para la imagen -->
        <?php $img = ($Articulo['imagen'] != '')? '<td style="text-align: center;"><img class="materialboxed"  style="margin-left: 100px;" width="600" src="../Imagenes/Catalogo/'.$Articulo['imagen'].'"></td>': '<td></td>'; ?>
        <div class="col s12 m6 center-align">
          <div class="card-panel">
            <?php echo $img ?>
            <!-- <img src="ruta/a/la/imagen.jpg" class="responsive-img"> -->
          </div>
          <div class="center-align">
            <a class="waves-effect waves-light btn">Regresar</a>
          </div>
        </div>
        <!-- Div para la tabla -->
        <div class="col s12 m6">
          <div class="card-panel  blue lighten-4">
            <table class="striped">
              <thead>
                <h3>Detalles del Artículo <?php echo $Articulo['id'] ?></h3>
              </thead>
              <tbody>
                <tr>
                  <td style="font-weight: bold;">Código: <?php echo $Articulo['codigo']; ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Nombre: <?php echo $Articulo['nombre']; ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Descripción: <?php echo $Articulo['descripcion']; ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Precio: $<?php echo $Articulo['precio']; ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Unidad: <?php echo $Articulo['unidad']; ?></td>
                </tr>
                 <tr>
                  <td style="font-weight: bold;">Existencias: <?php echo $Existencias; ?></td>
                </tr>
                  <td style="font-weight: bold;">Código Fiscal: <?php echo $Articulo['codigo_fiscal']; ?></td>
                <tr>
                  <td style="font-weight: bold;">Código Unidad: <?php echo $Articulo['codigo_unidad']; ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Marca: <?php echo $Articulo['modelo']; ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Categoria: <?php echo $NombreCategoria; ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Subcategoria: <?php echo $Articulo['subcategoria']; ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Registro: <?php echo $Nombre_usuario ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Fecha de Registro: <?php echo $Articulo['fecha']; ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </body><!-- FIN BODY -->
  </html>
<?php 
}
?>