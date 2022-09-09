<?php
#INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
include('../php/is_logged.php');
#INCLUIMOS EL ARCHIVO CON LOS DATOS Y CONEXXION A LA BASE DE DATOS
include('../php/conexion.php');
#GENERAMOS UNA FECHA DEL DIA EN CURSO REFERENTE A LA ZONA HORARIA
#TOMAMOS EL ID DEL USUARIO CON LA SESSION INICIADA
$id = $_SESSION['user_id'];
#TOMAMOS LA INFORMACION DEL USUARIO (PARA SABER A QUE AREA PERTENECE)
$area = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id"));
$Hoy = date('Y-m-d');
$instalaciones = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM clientes WHERE instalacion IS NULL"));
$reportes = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM reportes WHERE ((fecha_visita = '$Hoy'  AND atender_visita = 0) OR (fecha_visita < '$Hoy' AND atender_visita = 0 AND visita = 1) OR atendido != 1 OR atendido IS NULL) AND id_cliente < 10000"));
$reportesEsp = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM reportes WHERE ((fecha_visita = '$Hoy'  AND atender_visita = 0) OR (fecha_visita < '$Hoy' AND atender_visita = 0 AND visita = 1) OR atendido != 1 OR atendido IS NULL) AND id_cliente > 10000 AND descripcion LIKE 'Reporte Especial:%'"));
$Ordenes_Redes = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM orden_servicios  WHERE  estatus IN ('PorConfirmar', 'Revisar', 'Ejecutar', 'Cotizar', 'Cotizado', 'Pedir', 'Autorizado')  AND dpto = 1"));
$Ordenes_Taller = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM orden_servicios  WHERE  estatus IN ('PorConfirmar', 'Revisar', 'Ejecutar', 'Cotizar', 'Cotizado', 'Pedir', 'Autorizado')  AND dpto = 2"));
$Ordenes_Ventas = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM orden_servicios  WHERE  estatus IN ('PorConfirmar', 'Revisar', 'Ejecutar', 'Cotizar', 'Cotizado', 'Pedir', 'Autorizado') AND dpto = 3"));
if ($area['area'] == 'Taller' OR $id == 28) { $Orden = $Ordenes_Taller['count(*)']; }elseif ($id == 49 OR $id == 10 OR $id == 56 OR $id == 101) { $Orden = $Ordenes_Taller['count(*)']+$Ordenes_Ventas['count(*)']+$Ordenes_Redes['count(*)']; }elseif ( $area['area'] == 'Redes' OR $id == 25 OR $id == 28) {  $Orden = $Ordenes_Redes['count(*)'];  }else{ $Orden = $Ordenes_Ventas['count(*)']; }
$Mantenimiento = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM reportes WHERE ((fecha_visita = '$Hoy'  AND atender_visita = 0) OR (fecha_visita < '$Hoy' AND atender_visita = 0 AND visita = 1) OR atendido != 1 OR atendido IS NULL) AND id_cliente > 10000 AND descripcion LIKE 'Mantenimiento:%'"));
$tel = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM pagos WHERE Cotejado =1"));
$pendientes = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*)FROM dispositivos WHERE estatus IN ('Cotizado','En Proceso','Pendiente') AND fecha > '2019-01-01'"));
$listos = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*)FROM dispositivos WHERE estatus IN ('Listo (En Taller)','Listo (No Reparado)', 'Listo') AND fecha > '2019-01-01'"));
$almacen = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*)FROM dispositivos WHERE estatus = 'Almacen'"));
$rutas = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*)FROM rutas WHERE estatus = 0"));
?>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<!--Import material-icons.css-->
      <link href="css/material-icons.css" rel="stylesheet">
      <!--Import materialize.css-->
      <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
      <!--Let browser know website is optimized for mobile-->
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	  <link rel="shortcut icon" href="../img/logo.jpg" type="image/jpg" />
      <style rel="stylesheet">
		.dropdown-content{  overflow: visible;	}
	  </style>
	<div class="navbar-fixed">
	<nav class="indigo darken-4">
		<div class="nav-wrapper container">
			<a  class="brand-logo" href="home.php"><img  class="responsive-img" style="width: 60px; height: 56px;" src="../img/logo.jpg"></a>
			<a href="#" data-target="menu-responsive" class="sidenav-trigger">
				<i class="material-icons">menu</i>
			</a>
			<ul class="right hide-on-med-and-down">
				<li><a class='dropdown-button' data-target='dropdown1'><i class="material-icons left">phonelink_setup</i>Catálogo <i class="material-icons right">arrow_drop_down</i></a></li>
				<ul id='dropdown1' class='dropdown-content'>
				    <li><a href="form_entradas.php" class="black-text"><i class="material-icons">add</i>Item 1 </a></li>
				    <li><a href="dispositivos.php" class="black-text"><i class="material-icons">phonelink</i>Item 2  </a></li>
				    <li><a href="ver_almacen.php" class="black-text"><i class="material-icons">dashboard</i>Item 3 <span class="new badge pink" data-badge-caption=""><?php echo $almacen['count(*)'];?></span> </a></li>  			 
 				 </ul>
				<li><a class='dropdown-button' data-target='dropdown2'><i class="material-icons left">language</i>Compras<span class=" new badge pink" data-badge-caption=""><?php echo $instalaciones['count(*)']+$reportes['count(*)']+$reportesEsp['count(*)']+$Orden+$Mantenimiento['count(*)'];?></span><i class="material-icons right">arrow_drop_down</i></a></li>
				<ul id='dropdown2' class='dropdown-content'>
				    <li><a href="../views/form_instalacion.php" class="black-text"><i class="material-icons">add</i>Item 1</a></li>    
					<li><a href="form_mantenimiento.php" class="black-text"><i class="material-icons">add_circle_outline</i>Item 2</a></li>
					<li><a href="form_orden.php" class="black-text"><i class="material-icons">add_circle</i>Item 3</a></li>
					 <li><a href="clientes.php" class="black-text"><i class="material-icons">people</i>Clientes </a></li>
				    <li><a href="../views/instalaciones.php" class="black-text"><i class="material-icons">list</i>Item 4 <span class=" new badge pink" data-badge-caption=""><?php echo $instalaciones['count(*)']?></span></a></li>
				    <li><a class='dropdown-btn1 black-text' data-target='sub-dropdown4'><i class="material-icons left">assignment_ind</i> Item 11 <i class="material-icons right">chevron_right</i></a></li>
					<ul id='sub-dropdown4' class='dropdown-content'>
				    	<li><a href="facturar_p.php" class="black-text"><i class="material-icons">assignment_late</i>Pendientes </a></li>   
				    	<li><a href="facturar_l.php" class="black-text"><i class="material-icons">assignment_turned_in</i>Listas </a></li>
				    </ul>	
					 
 				</ul>
 				<li><a class='dropdown-button' data-target='dropdown5'><i class="material-icons left">add</i>Ventas <i class="material-icons right">arrow_drop_down</i></a></li>
				<ul id='dropdown5' class='dropdown-content'>
					<li><a href="centrales_pings.php" class="black-text"><i class="material-icons">settings_input_antenna</i>Item 1 </a></li>   
				    <li><a href="paquetes.php" class="black-text"><i class="material-icons">import_export</i>Item 2 </a></li>   
				    <li><a href="comunidades.php" class="black-text"><i class="material-icons">business</i>Item 4 </a></li>
				    <li><a href="servidores.php" class="black-text"><i class="material-icons">router</i>Item 4 </a></li>
				    <li><a href="centrales.php" class="black-text"><i class="material-icons">satellite</i>Item 5 </a></li>
				</ul>
 				<li><a class='dropdown-button' data-target='dropdown4'><?php echo $_SESSION['user_name'];?> <i class="material-icons right">arrow_drop_down</i></a></li>
				<ul id='dropdown4' class='dropdown-content'>
				    <li><a href="../../SIC5.0" class="black-text"><i class="material-icons">laptop_mac</i>Sistema SIC5.0 </a></li>
				    <li><a href="perfil_user.php" class="black-text"><i class="material-icons">account_circle</i>Perfil </a></li>
				    <li><a href="../php/cerrar_sesion.php" class="black-text"><i class="material-icons">exit_to_app</i>Cerrar Sesión</a></li>
 				 </ul>
			</ul>
			<ul class="right hide-on-large-only hide-on-small-only">
				<li><a class='dropdown-button' data-target='dropdown10'><?php echo $_SESSION['user_name'];?> <i class="material-icons right">arrow_drop_down</i></a></li>
				<ul id='dropdown10' class='dropdown-content'>
					<li><a href="../../SIC5.0" class="black-text"><i class="material-icons">laptop_mac</i>Sistema SIC5.0 </a></li>
				    <li><a href="perfil_user.php" class="black-text"><i class="material-icons">account_circle</i>Perfil </a></li>
				    <li><a href="../php/cerrar_sesion.php" class="black-text"><i class="material-icons">exit_to_app</i>Cerrar Sesión</a></li>
 				 </ul>
			</ul>
			<ul class="right hide-on-med-and-up">
		        <li><a class='dropdown-button' data-target='dropdown8'><i class="material-icons left">account_circle</i><b>></b></a></li>
				<ul id='dropdown8' class='dropdown-content'>
					<li><a href="../../SIC5.0" class="black-text"><i class="material-icons">laptop_mac</i>Sistema SIC5.0 </a></li>
				    <li><a href="perfil_user.php" class="black-text"><i class="material-icons">account_circle</i>Perfil </a></li>
				   <li><a href="../php/cerrar_sesion.php" class="black-text"><i class="material-icons">exit_to_app</i>Cerrar Sesión</a></li>
 				</ul>
		    </ul>			
		</div>		
	</nav>
	</div>
	<ul class="sidenav indigo lighten-5" id="menu-responsive" style="width: 270px;">
				<h2>Menú</h2>
    			<li><div class="divider"></div></li><br>
				<li>
	    			<ul class="collapsible collapsible-accordion">
	    				<li>
	    				  <div class="collapsible-header"><i class="material-icons">phonelink_setup</i>Catálogo <i class="material-icons right">arrow_drop_down</i></div>
		      				<div class="collapsible-body indigo lighten-5">
		      				  <span>
		      					<ul>
		      					  <li><a href="form_entrada.php"><i class="material-icons">add</i>Item 1</a></li>
			      				  <li><a href="dispositivos.php"><i class="material-icons">phonelink</i>Item 2</a></li>
				    			  <li><a href="ver_almacen.php"><i class="material-icons">dashboard</i>Item 3<span class="new badge pink" data-badge-caption=""><?php echo $almacen['count(*)'];?></span> </a></li>
			      				  <li><a href="listos.php"><i class="material-icons">assignment_turned_in</i>Item 4 <span class="new badge pink" data-badge-caption=""><?php echo $listos['count(*)'];?></span> </a></li>    			 
					    		</ul>
					          </span>
		      			  </div>    			
	    				</li>	    			
	    			</ul>	     				
	    		</li>
				<li>
	    			<ul class="collapsible collapsible-accordion">
	    				<li>
	    				  <div class="collapsible-header"><i class="material-icons">phonelink_setup</i>Compras <i class="material-icons right">arrow_drop_down</i></div>
		      				<div class="collapsible-body indigo lighten-5">
		      				  <span>
		      					<ul>
								  <li><a href="form_entrada.php"><i class="material-icons">add</i>Item 1</a></li>
			      				  <li><a href="dispositivos.php"><i class="material-icons">phonelink</i>Item 2</a></li>
				    			  <li><a href="ver_almacen.php"><i class="material-icons">dashboard</i>Item 3<span class="new badge pink" data-badge-caption=""><?php echo $almacen['count(*)'];?></span> </a></li> 			 
					    		</ul>
					          </span>
		      			  </div>    			
	    				</li>	    			
	    			</ul>	     				
	    		</li>
				<li>
	    			<ul class="collapsible collapsible-accordion">
	    				<li>
	    				  <div class="collapsible-header"><i class="material-icons">language</i>Ventas <i class="material-icons right">arrow_drop_down</i></div>
		      				<div class="collapsible-body  indigo lighten-5">
		      				  <span>
		      					<ul>
		      					  <li><a href="../views/form_instalacion.php"><i class="material-icons">add</i>Item 1</a></li>
					 			  <li><a href="form_mantenimiento.php"><i class="material-icons">add_circle_outline</i>Item 2</a></li>
								  <li><a href="form_orden.php"><i class="material-icons">add_circle</i>Item 3</a></li>
					 			  <li><a href="clientes.php"><i class="material-icons">people</i>Clientes </a></li>
				    			  <li><a href="stock.php" class="black-text"> <i class="material-icons">assignment_ind</i>Item 4 </a></li>
			      				  <li><a href="../views/instalaciones.php"><i class="material-icons">list</i>Item 5 <span class="new badge pink" data-badge-caption=""><?php echo $instalaciones['count(*)'];?></span></a></li>
						    	  <li><a href="reportes.php"><i class="material-icons">perm_scan_wifi</i>Item 6<span class=" new badge pink" data-badge-caption=""><?php echo $reportes['count(*)'];?></span></a></li>
					    		</ul>
					          </span>
		      			  </div>    			
	    				</li>	    			
	    			</ul>	     				
	    		</li>
	</ul>
	<?php 
	include('../views/modals.php');
	include('../php/scripts.php');
	?>
	<script src="js/jquery-3.1.1.js"></script>
	<!--JavaScript at end of body for optimized loading-->
    <script type="text/javascript" src="js/materialize.min.js"></script>
	<script>
    	$(document).ready(function() {	    
	 	$('.dropdown-button').dropdown({
	      	  inDuration: 500,
	          outDuration: 500, 
	          constrainWidth: false, // Does not change width of dropdown to that of the activator
	          coverTrigger: false, 
	    });
	    $('.dropdown-btn').dropdown({
	      	  inDuration: 500,
	          outDuration: 500,
	          hover: true,
	          constrainWidth: true, // Does not change width of dropdown to that of the activator
	          coverTrigger: false, 
	    });
	    $('.dropdown-btn1').dropdown({
	      	  inDuration: 500,
	          outDuration: 500,
	          alignment: 'left',
	          hover: true,
	          constrainWidth: true, // Does not change width of dropdown to that of the activator
	          coverTrigger: false, 
	    });
	    $('tooltipped').tooltip();
	    });
		document.addEventListener('DOMContentLoaded', function(){
			M.AutoInit();
		});
		document.addEventListener('DOMContentLoaded', function() {
		    var elems = document.querySelectorAll('.fixed-action-btn');
		    var instances = M.FloatingActionButton.init(elems, {
		      direction: 'left'
		    });
		});
	</script>