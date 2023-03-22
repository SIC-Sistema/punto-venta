<?php
    //ARCHIVO QUE CONTIENE LA VARIABLE CON LA CONEXION A LA BASE DE DATOS
    include('../php/conexion.php');
    //ARCHIVO QUE CONDICIONA QUE TENGAMOS ACCESO A ESTE ARCHIVO SOLO SI HAY SESSION INICIADA Y NOS PREMITE TIMAR LA INFORMACION DE ESTA
    include('../php/is_logged.php');
    //DEFINIMOS LA ZONA  HORARIA
    date_default_timezone_set('America/Mexico_City');
    $id_user = $_SESSION['user_id'];// ID DEL USUARIO LOGEADO
    $Fecha_hoy = date('Y-m-d');// FECHA ACTUAL

    if(!empty($_POST["unidad_id"])){ 
        // Fetch state data based on the specific country 
        $query = "SELECT * FROM unidades_medida_sat WHERE id = ".$_POST['unidad_id'].""; 
        $result = $conn->query($query); 
        // Generate HTML of state options list 
        if($result->num_rows > 0){ 
            
            while($row = $result->fetch_assoc()){  
                echo ''.$row['clave'].''; 
            } 
        }else{ 
            echo 'Error al obtener datos'; 
        } 
    }
?>