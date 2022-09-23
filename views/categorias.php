<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include ('../php/cobrador.php');
?>
<title>SIC | Categorias</title>
<script>
function buscar_categoria(){
  var texto = $("input#busqueda").val();
  $.post("../php/buscar_categorias.php", {
      texto: texto,
    }, function(mensaje){
        $("#CategoriasALL").html(mensaje);
  });
}

function insert_categoria() {
    var textoNombre = $("input#nombre").val();

  
    if (textoNombre == "") {
      M.toast({html :"Por favor ingrese el nombre la comunidad.", classes: "rounded"});
    }else{
      $.post("../php/insert_categoria.php", {
          valorNombre: textoNombre
        }, function(mensaje) {
            $("#resultado_categoria").html(mensaje);
        }); 
    }
};
</script>
</head>
<main>
<body onload="buscar_categoria();">
  <div class="container">
  <div class="row" >
    <h3 class="hide-on-med-and-down">Registrar Categoria</h3>
    <h5 class="hide-on-large-only">Registrar Categoria</h5>
  </div>
    <div class="row">
      <div class="input-field col s7 m4 l4">
         <i class="material-icons prefix">business</i>
        <input type="text" id="nombre">
        <label for="nombre">Categoria:</label>
      </div>
      <div class="input-field">
        <a onclick="insert_categoria();" class="waves-effect waves-light btn pink left right"><i class="material-icons center">send</i></a>
      </div>
    </div>
    <div id="resultado_categoria"></div>
    <div>
    <div class="row">
      <br><br>
      <h3 class="hide-on-med-and-down col s12 m6 l6">Categorias</h3>
          <h5 class="hide-on-large-only col s12 m6 l6">Categorias</h5>

          <form class="col s12 m6 l6">
          <div class="row">
            <div class="input-field col s12">
              <i class="material-icons prefix">search</i>
              <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar_categoria();">
              <label for="busqueda">Buscar(#Numero, Nombre)</label>
            </div>
          </div>
        </form>
    </div>
            <table class="bordered highlight responsive-table">
                <thead>
                    <tr>
                        <th>Numero de categoria</th>
                        <th>Nombre</th>
                        <th>Editar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody id="CategoriasALL">
                </tbody>
            </table>
            <br><br>
        </div>
  </div>
</body>
</main>
</html>