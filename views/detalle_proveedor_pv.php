<?php
//VERIFICAMOS QUE SI NOS ENVIE POR POST EL ID DEL PROVEEDOR DESDE EL ARCHIVO control_proveedor.php
if (isset($_POST['proveedor']) == false) {
  ?>
  <script>    
    M.toast({html: "Regresando a Proveedores.", classes: "rounded"})
    setTimeout("location.href='proveedores_punto_venta.php'", 800);
  </script>
  <?php
}else{
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles proveedor</title>
    <?php
      //INCLUIMOS EL ARCHIVO QUE CONTIENE LA BARRA DE NAVEGACION TAMBIEN TIENE (scripts, conexion, is_logged, modals)
      include('fredyNav.php');
      $id_proveedor = $_POST['proveedor'];// POR EL METODO POST RECIBIMOS EL ID DE LA COTIZACIÓN DEL ARCHIVO control_articulo.php EN EL CASO 1
      $id = $_SESSION['user_id'];//  RECIBIMOS EL ID DEL USUARIO LOGEADO
      //REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL USUARIO Y ASIGNAMOS EL ARRAY A UNA VARIABLE $datos_user
      $datos_user = mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM users WHERE user_id=$id"));
      $Proveedor = mysqli_fetch_array( mysqli_query($conn,"SELECT * FROM `punto_venta_proveedores` WHERE id=$id_proveedor"));
    ?>
</head>
<body>
  <div class="container">
    <h1>Detalles Proveedor</h1>
    <div class="row">
			<ul class="collection">
        <li class="collection-item avatar">
          <img src="../img/cliente.png" alt="" class="circle">
          <span class="title"><b>No. Proveedor: </b><?php echo $id_proveedor; ?></span>
          <p>
            <b>Nombre: </b><?php echo $Proveedor['nombre']; ?><br>
            <b>Telefono: </b><?php echo $Proveedor['telefono']; ?><br>
            <b>RFC: </b><?php echo $Proveedor['rfc']; ?><br>
            <b>Dirección: </b><?php echo $Proveedor['direccion']; ?><br>
          </p>
        </li>
      </ul>		
		</div>
  </div>
</body>
</html>
<?php
}
?>