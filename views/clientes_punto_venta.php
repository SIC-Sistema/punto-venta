<!DOCTYPE html>
<html lang="en">
<head>
  <?php
  include('fredyNav.php');
  ?>
  <title>SIC | Clientes Punto Venta</title>
  <script>
    function buscar_clientes(){
      var texto = $("input#busqueda").val();
      $.post("../php/buscar_clienteses.php", {
          texto: texto,
        }, function(mensaje){
            $("#clientesALL").html(mensaje);
      });
    }
  </script>
</head>
<main>
<body onload="buscar_clientes();">
  <div class="container"><br><br>
    <a href="add_cliente.php" class="waves-effect waves-light btn pink left right">Agregar Cliente<i class="material-icons prefix left">add</i></a>
    <div class="row">
      <h3 class="hide-on-med-and-down col s12 m6 l6">Clientes</h3>
      <h5 class="hide-on-large-only col s12 m6 l6">Clientes</h5>
      <form class="col s12 m6 l6">
        <div class="row">
          <div class="input-field col s12">
            <i class="material-icons prefix">search</i>
            <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar_clientes();">
            <label for="busqueda">Buscar(N° Cliente, Nombre)</label>
          </div>
        </div>
      </form>
    </div>
    <div class="row">
      <table class="bordered highlight responsive-table">
        <thead>
          <tr>
            <th>N°</th>
            <th>Nombre</th>
            <th>Municipio</th>
            <th>Servidor</th>
            <th>Costo de Prepago</th>
            <th>Costo de Contrato</th>
             <th>Editar</th>
          </tr>
        </thead>
        <tbody id="clientesALL">
        </tbody>
      </table>
    </div><br><br>
  </div>
</body>
</main>
</html>