<?php
include('../php/conexion.php');

//sí estas variables no estan vacias hcer la inserción
if (isset($_POST['register'])){
    if (strlen($_POST['nombre']) >= 1 &&
    strlen($_POST['direccion']) >= 1 &&
    strlen($_POST['colonia']) >= 1 &&
    strlen($_POST['direccion']) >= 1 &&
    strlen($_POST['cp']) >= 1 &&
    strlen($_POST['rfc']) >= 1 &&
    strlen($_POST['email']) >= 1 &&
    strlen($_POST['telefono']) >= 1){

        $nombre= trim($_POST['nombre']);
        $direccion= trim($_POST['direccion']);
        $colonia= trim($_POST['colonia']);
        $cp= trim($_POST['cp']);
        $rfc= trim($_POST['rfc']);
        $email= trim($_POST['email']);
        $telefono= trim($_POST['telefono']);

        $Consulta = "INSERT INTO punto-venta_proveedores(nombre, direccion, colonia, cp, rfc, email, telefono, dias_c, usuario, fecha) VALUES ($nombre,$direccion=,$colonia,$cp,$rfc,$email,$telefono,NULL,NULL,NULL)";

        $resultado = mysqli_query($conn, $Consulta)
        if($resultado){
            <?php 
            <h2 class="ok">Insercion correcta</h2>
            ?>
        }else{
            <?php 
            <h2 class="bad">nao nao</h2>
            ?>
        }
    }else{
        <?php 
        <h2 class="bad">Complete los campos</h2>
        ?>
    
}
?>