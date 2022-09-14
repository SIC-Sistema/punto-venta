<?php
include('../php/conexion.php');
$valorId = $conn->real_escape_string($_POST["valorId"]);

$sql_delete = "DELETE FROM users WHERE user_id=$valorId";

if(mysqli_query($conn, $sql_delete)){
    ?>
    <script>
        M.toast({html:"Usuario eliminado.", classes: "rounded"});
        setTimeout("location.href='usuarios.php'", 800);
    </script>
    <?php
}else{
    echo '<script>M.toast({html:"Hubo un error, intentelo mas tarde.", classes: "rounded"})</script>';
}
?>