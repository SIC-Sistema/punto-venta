<?php 
//ARCHIVO QUE CONTIENE LA VARIABLE CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
//ARCHIVO QUE CONDICIONA QUE TENGAMOS ACCESO A ESTE ARCHIVO SOLO SI HAY SESSION INICIADA Y NOS PREMITE TIMAR LA INFORMACION DE ESTA
include('is_logged.php');
$id = $_SESSION['user_id'];// ID DEL USUARIO LOGEADO
$user_id = $conn->real_escape_string($_POST['valorId']);//VALOR DEL USUARIO A EDITAR POR POST "perfil_user.php"

//REALIZAMOS LA CONSULTA PARA SACAR LA INFORMACION DEL USUARIO Y ASIGNAMOS EL ARRAY A UNA VARIABLE $area
$area = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id"));

if($area['area'] == "Administrador" OR $user_id == $id){
	//CON POST RECIBIMOS TODAS LAS VARIABLES DEL FORMULARIO POR EL SCRIPT "perfil_user.php" QUE NESECITAMOS PARA ACTUALIZAR
	$Nombres = $conn->real_escape_string($_POST['valorNombres']);
	$Apellidos = $conn->real_escape_string($_POST['valorApellidos']);
	$Usuario = $conn->real_escape_string($_POST['valorUsuario']);
	$Email = $conn->real_escape_string($_POST['valorEmail']);
	//CREAMOS LA SENTENCIA SQL PARA HACER LA ACTUALIZACION DE LA INFORMACION DEL USUARIO Y LA GUARDAMOS EN UNA VARIABLE
	$sql = "UPDATE users SET firstname='$Nombres', lastname='$Apellidos', user_name='$Usuario', user_email = '$Email' WHERE user_id='$user_id'";
	//VERIFICAMOS QUE SE EJECUTE LA SENTENCIA EN MYSQL 
	if(mysqli_query($conn, $sql)){
		echo '<script>M.toast({html:"El perfil se actualizó correctamente.", classes: "rounded"})</script>';
		echo '<script>recargar_usuarios()</script>';// REDIRECCIONAMOS (FUNCION ESTA EN ARCHIVO modals.php)
	}else{
		echo '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
	}
}else{
  echo "<script >M.toast({html: 'Sólo un administrador o el mismo usuario puede editar un perfil.', classes: 'rounded'});</script>";
}
mysqli_close($conn);
?>