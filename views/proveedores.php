<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
//NAVBAR-->
include('fredyNav.php');
?>
<title>SIC | Inicio Proveedores</title>
</head>
<body>
    <!--TITULO-->
    <h1 class="header center orange-text">Nuevo proveedor</h1>
    <!--FORMULARIO-->
    <ul>
        <div class="row">
        <form method="post" class="col s12">
        <!--primera fila-->
        <div class="row">
            <div class="input-field col s6">
            <input placeholder="Ingrese Nombre" id="nombre" type="text" class="validate">
            <label for="nombre">Nombre</label>
            </div>
            <div class="input-field col s6">
            <input placeholder="Ingrese Dirección" id="direccion" type="text" class="validate">
            <label for="direccion">Dirección</label>
            </div>
        </div>
        <!--segunda fila-->
        <div class="row">
            <div class="input-field col s6">
            <input placeholder="Ingrese Colonia" id="colonia" type="text" class="validate">
            <label for="colonia">Colonia</label>
            </div>
            <div class="input-field col s6">
            <input placeholder="Ingrese Dirección" id="direccion" type="text" class="validate">
            <label for="direccion">Dirección</label>
            </div>
        </div>
        <!--tercera fila-->
        <div class="row">
            <div class="input-field col s6">
            <input placeholder="Ingrese C.P" id="cp" type="number" class="validate">
            <label for="cp">C.P.</label>
            </div>
            <div class="input-field col s6">
            <input placeholder="Ingrese RFC" id="rfc" type="text" class="validate">
            <label for="rfc">RFC</label>
            </div>
        </div>
        <!--cuarta fila-->
        <div class="row">
            <div class="input-field col s6">
            <input placeholder="Ingrese Email" id="email" type="text" class="validate">
            <label for="email">Email</label>
            </div>
            <div class="input-field col s6">
            <input placeholder="Ingrese Teléfono" id="telefono" type="text" class="validate">
            <label for="telefono">Teléfono</label>
            </div>
        </div>
        <div class="button">
        <input type="submit" name="registrar">
        </div>
        </form>
        <?php
        include("crud_proveedores.php");
        ?>
    </ul>
</body>
</html>