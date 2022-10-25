<?php
#INCLUIMOS EL ARCHIVO CON LOS DATOS Y CONEXXION A LA BASE DE DATOS
include('../php/conexion.php');
//ARCHIVO QUE CONDICIONA QUE TENGAMOS ACCESO A ESTE ARCHIVO SOLO SI HAY SESSION INICIADA Y NOS PREMITE TOMAR LA INFORMACION DE ESTA
include('../php/is_logged.php');
//DEFINIMOS LA ZONA  HORARIA
date_default_timezone_set('America/Mexico_City');
$id_user = $_SESSION['user_id'];// ID DEL USUARIO LOGEADO
$Fecha_hoy = date('Y-m-d');// FECHA ACTUAL
$num = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM punto_venta_ventas WHERE usuario = $id_user AND estatus = 0"));
if ($num>1
) {
    echo '<script >M.toast({html:"Solo se pueden tener 2 Ventas en proceso.", classes: "rounded"})</script>';
    echo '<script >M.toast({html:"Pausa o termina una venta para poder abrir una nueva.", classes: "rounded"})</script>';
}else{
    $sql = "INSERT INTO `punto_venta_ventas` (fecha, usuario) VALUES ('$Fecha_hoy', $id_user)";
    //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
    if(mysqli_query($conn, $sql)){
        echo '<script >M.toast({html:"Nueva Venta.", classes: "rounded"})</script>';
        #SELECCIONAMOS EL ULTIMO CORTE CREADO
        $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id) AS id FROM punto_venta_ventas WHERE usuario = $id_user AND fecha = '$Fecha_hoy'"));        
        $Venta = $ultimo['id'];//TOMAMOS EL ID DEL ULTIMO CORTE

        //redireccionar
        if ($num == 1) {
            ?>
            <script>
              var a = document.createElement("a");
                a.target = "_blank";
                a.href = "../views/add_venta.php?id="+<?php echo $Venta; ?>;
                a.click();
            </script>
            <?php   
        }else{
            ?>
            <script>
              var a = document.createElement("a");
                a.href = "../views/add_venta.php?id="+<?php echo $Venta; ?>;
                a.click();
            </script>
            <?php   
        }
    }
}
