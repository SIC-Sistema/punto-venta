<?php
#INCLUIMOS EL ARCHIVO CON LOS DATOS Y CONEXXION A LA BASE DE DATOS
include('../php/conexion.php');
//ARCHIVO QUE CONDICIONA QUE TENGAMOS ACCESO A ESTE ARCHIVO SOLO SI HAY SESSION INICIADA Y NOS PREMITE TOMAR LA INFORMACION DE ESTA
include('../php/is_logged.php');
//DEFINIMOS LA ZONA  HORARIA
$id_user = $_SESSION['user_id'];// ID DEL USUARIO LOGEADO

$sql = "INSERT INTO `tmp_pv_factura` (usuario) VALUES ($id_user)";
    //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
    if(mysqli_query($conn, $sql)){
        echo '<script >M.toast({html:"Nueva Factura.", classes: "rounded"})</script>';
        #SELECCIONAMOS EL ULTIMO CORTE CREADO
        $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(folio) AS id FROM tmp_pv_factura WHERE usuario = $id_user"));       
        $factura = $ultimo['id'];//TOMAMOS EL ID DEL ULTIMO CORTE

        //redireccionar
        ?>
        <script>
            var a = document.createElement("a");
                a.href = "../views/add_factura.php?id="+<?php echo $factura; ?>;
                a.click();
        </script>
        <?php   
        
    }
