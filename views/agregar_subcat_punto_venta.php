<!DOCTYPE html>
<html lang="en">
  <head>
  <?php
    //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
    include('fredyNav.php');
    //ARCHIVO QUE RESTRINGE A QUE SOLO ALGUNOS USUARIOS PUEDAN ACCEDER
    include('../php/cobrador.php');
  ?>
  <title>SIC | Agregar subcategorias Punto Venta</title>
  <script>
    //FUNCION QUE HACE LA BUSQUEDA DE CATEGORIAS (SE ACTIVA AL INICIAR EL ARCHIVO O AL ECRIBIR ALGO EN EL BUSCADOR)
    //FUNCION QUE HACE LA INSERCION DE LA CATEGORIA (SE ACTIVA AL PRECIONAR UN BOTON)
    function insert_subcategoria() {
    //PRIMERO VAMOS Y BUSCAMOS EN ESTE MISMO ARCHIVO LA INFORMCION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
    var categoryId = $("select#categories").val();
    var textoNombre = $("input#nombre").val();//ej:LA VARIABLE "textoNombre" GUARDAREMOS LA INFORMACION QUE ESTE EN EL INPUT QUE TENGA EL id = "nombre"
    // CREAMOS CONDICIONES QUE SI SE CUMPLEN MANDARA MENSAJES DE ALERTA EN FORMA DE TOAST
    //SI SE CUMPLEN LOS IF QUIERE DECIR QUE NO PASA LOS REQUISITOS MINIMOS DE LLENADO...
    if (textoNombre == "") {
      M.toast({html: 'El campo Nombre se encuentra vacío.', classes: 'rounded'});
      }else if (categoryId == "0"){
      M.toast({html: 'Seleccione una categoría.', classes: 'rounded'});
      }else {
      //SI LOS IF NO SE CUMPLEN QUIERE DECIR QUE LA INFORMACION CUENTA CON TODO LO REQUERIDO
      //MEDIANTE EL METODO POST ENVIAMOS UN ARRAY CON LA INFORMACION AL ARCHIVO EN LA DIRECCION "../php/control_categoria.php"
      $.post("../php/control_categoria.php", {
      //Cada valor se separa por una ,
        accion: 4,
        valorNombre: textoNombre,
        valorCategoria: categoryId
        }, function(mensaje) {
          //SE CREA UNA VARIABLE LA CUAL TRAERA EN TEXTO HTML LOS RESULTADOS QUE ARROJE EL ARCHIVO AL CUAL SE LE ENVIO LA INFORMACION "control_categoria.php"
          $("#resultado_insert").html(mensaje);
        }); 
      }//FIN else CONDICIONES
    };//FIN function 
  </script>
  </head>
  <body>
  <style rel="stylesheet">
	  .select-dropdown{
    overflow-y: auto !important;
    }
	</style>
    <div class="container"><br><br>
      <div class="row">  
        <!--    //////    TITULO    ///////   -->
        <h3 class="hide-on-med-and-down col s12 m12 l12">Insertar nueva subcategoría</h3>
        <h5 class="hide-on-large-only col s12 m12 l12">Insertar nueva subcategoría</h5>
        <!--    //////    INPUT DEl REGISTRO DE LA CATEGORIA    ///////   -->
        <form class="col s12 m12 l12 row">
          <!-- CREAMOS UN DIV EL CUAL TENGA id = "resultado_insert"  PARA QUE EN ESTA PARTE NOS MUESTRE LOS RESULTADOS EN TEXTO HTML DEL SCRIPT EN FUNCION  -->
          <div id="resultado_insert"></div>
          <!-- FORMULARIO EL CUAL SE MUETRA EN PANTALLA .-->
          <div class="col s12 m6 l6"><br>    
              <div class="input-field">
              <!-- CAJA DE SELECCION DE CATEGORIAS -->
              <i class="material-icons prefix">view_list</i>
              <!--<label for="categoria">Categoria:</label>-->
              <select id="categories" name="categories" class="validate">
              <!--OPTION PARA QUE LA SELECCION QUEDE POR DEFECTO VACIA-->
              <option value="0" select>Seleccione una categoria</option>
              <?php 
                // REALIZAMOS LA CONSULTA A LA BASE DE DATOS MYSQL Y GUARDAMOS EN FORMARTO ARRAY EN UNA VARIABLE $consulta
                $consulta = mysqli_query($conn,"SELECT * FROM punto_venta_categorias WHERE parent_id =0");
                //VERIFICAMOS QUE LA VARIABLE SI CONTENGA INFORMACION
                if (mysqli_num_rows($consulta) == 0) {
                  echo '<script>M.toast({html:"No se encontraron categorias.", classes: "rounded"})</script>';
                  echo '<option value="">Categorías no disponible</option>'; 
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
                <div class="input-field">
                  <i class="material-icons prefix">edit</i>
                  <input id="nombre" type="text" class="validate" data-length="30" required>
                  <label for="nombre">Ingresa el nombre de la nueva subcategoría:</label>
                </div>  
            </div>
        </div>
            <div class="col s12 m6 l6">
              <br>
              <br>
              <!-- BOTON QUE MANDA LLAMAR EL SCRIPT PARA QUE EL SCRIPT HAGA LO QUE LA FUNCION CONTENGA -->
              <a onclick="insert_subcategoria();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>Insertar</a>
            </div>
        </form>
      </div><br>
    </body>
</html>