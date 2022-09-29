<?php
include('../php/conexion.php');
$id = $conn->real_escape_string($_POST['id']);
?>
<script>
	$(document).ready(function(){
	    $('#modalImagen').modal();
	    $('#modalImagen').modal('open'); 
	 });
</script>
<div id="modalImagen" class="modal">
    <div class="modal-content row">
      <div class="row">
        <h4 class="red-text center"><b>¡ADVERTENCIA!</b></h4><br>
        <h5 class="blue-text">CONDICIONES PARA SUBIR IMAGEN:</h5>
        <h6 class="blue-text">
          1. La imagen debe ser de 200x200<br>
          2. En formato JPG<br>
          NOTA:<br>
          1. Puede ser que la imagen sea redimensionada, es por eso que se pide que se cumpla con las condiciones anteriores.<br>
          2. Si existe una imagen esta sera reemplazada por la ahora elegida. <br>
        </h6>
      </div>
      <form class="row" id="respuesta" action="../php/control_articulo.php" method="post" enctype="multipart/form-data">
      <div class="input-field col s12 m6 l6">
          <div class="file-field input-field">
            <div class="btn">
              <span><i class="material-icons center">file_upload</i></span>
              <input type="file" name="imagen" id = "imagen"  accept="imagen/jpeg" required>
            </div>
            <div class="file-path-wrapper">
              <input class="file-path validate" type="text" placeholder="Subir Imagen JPG">
            </div>
          </div>
          <input id="id" name="id" type="hidden" value="<?php echo $id ?>">
          <input id="accion" name="accion" type="hidden" value="4">
      </div><br><br><br><br><br>
      <a href="#" class="modal-action modal-close waves-effect waves-green btn red accent-2">Cancelar<i class="material-icons right">close</i></a>
      <button class="btn waves-effect waves-light pink right" type="submit" name="action">Subir<i class="material-icons right">file_upload</i></button>
      </form>
    </div>
</div>