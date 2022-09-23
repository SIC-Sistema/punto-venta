<?php 
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Nombre = $conn->real_escape_string($_POST['valorNombre']);

//Variable vacía (para evitar los E_NOTICE)
$mensaje = "";

$sql_categoria = "SELECT * FROM punto_venta_categorias WHERE nombre='$Nombre'";
if(mysqli_num_rows(mysqli_query($conn, $sql_categoria))>0){
    $mensaje = '<script>M.toast({html :"Ya se encuentra una comunidad con el mismo nombre.", classes: "rounded"})</script>';
}else{
    //o $consultaBusqueda sea igual a nombre + (espacio) + apellido
    $sql = "INSERT INTO punto_venta_categorias (nombre) VALUES('$Nombre')";
    if(mysqli_query($conn, $sql)){
        echo '<script>M.toast({html :"La categoria se registró satisfactoriamente.", classes: "rounded"})</script>';
        ?>
        <script>    
            setTimeout("location.href='../views/categorias.php'", 800);
        </script>
        <?php
    }else{
        echo '<script>M.toast({html :"Ha ocurrido un error.", classes: "rounded"})</script>';   
    }
}
mysqli_close($conn);
?>