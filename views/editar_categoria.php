<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
?>
<title>SIC | Editar Categoria</title>
<script>
  function update_categoria() {
      var textoNombre = $("input#nombre").val();

      if (textoNombre == "") {
        M.toast({html:"Por favor ingrese el nombre la comunidad.", classes: "rounded"});
      }else{
        $.post("../php/update_categoria.php", {
            id: id,
            valorNombre: textoNombre
          }, function(mensaje) {
              $("#resultado_update_categoria").html(mensaje);
          }); 
      }
  };
</script>
</head>
<main>
<?php
require('../php/conexion.php');
if (isset($_POST['no_categoria']) == false) {
  ?>
  <script>    
    M.toast({html: "Regresando al listado...", classes: "rounded"})
    setTimeout("location.href='categorias.php'", 1000);
  </script>
  <?php
}else{
$id_categoria = $_POST['no_categoria'];
?>
<body>
<div id="resultado_update_categoria">
</div>
<?php

$categoria = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM punto_venta_categorias WHERE id=$id_categoria"));
?>
  <div class="container">
  <br>
  <h3 class="hide-on-med-and-down">Editando Categoria</h3>
  <h5 class="hide-on-large-only">Editando Categoria</h5>
  <br>
    <div class="row">
     <input type="hidden" id="id_categoria" value="<?php echo $categoria['id_categoria'];?>">
      <div class="input-field col s12 m3 l3">
        <input type="text" id="nombre" value="<?php echo $categoria['nombre'];?>">
        <label for="nombre">Nombre de la Categoria:</label>
      </div>
      <div class="input-field col s12 m12 l12">
        <a onclick="update_categoria();" class="waves-effect waves-light btn pink left right"><i class="material-icons center">send</i></a>
      </div>
    </div>
    <br><br>
  </div>
</body>
<?php
}
?>
</main>
</html>