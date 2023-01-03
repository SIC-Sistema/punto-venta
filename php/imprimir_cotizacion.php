<?php
//ARCHIVO QUE DETECTA QUE PODAMOS USAR ESTE ARCHIVO SOLO SI HAY ALGUNA SESSION ACTIVA O INICIADA
include("is_logged.php");
// INCLUIMOS EL ARCHIVO CON LA CONEXXIONA LA BD PARA HACER CONSULTAS
include('../php/conexion.php');
//SE INCLUYE EL ARCHIVO QUE CONTIENEN LAS LIBRERIAS FPDF PARA CREAR ARCHIVOS PDF
include("../fpdf/fpdf.php");
/// SACAMOS LA INFORMACION DEL LA COTIZACION
$id = $_GET['id'];//RECIBIMOS POR EL METODO GET EL ID DE LA COTIZACIÓN
$Cotizacion = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM `punto_venta_cotizaciones` WHERE id=$id"));

class PDF extends FPDF{
   //Cabecera de página
   function Header(){ 
	   
   }

   //Pie de pagina 
   function footer(){
	    $this->SetFont('Helvetica','', 10);
	    $this->SetFillColor(28, 98, 163);
	    $this->SetDrawColor(28, 98, 163);
	    $this->SetTextColor(255, 255, 255);
	    $this->SetY(-35);
		 $this->SetX(0);
	    $this->SetFont('Helvetica', 'B', 13);
		 $this->MultiCell(216,10,utf8_decode('    Siguenos en:                                                                                              Estamos ubicados en:'),0,'C',1);
		 $this->SetX(0);
	    $this->MultiCell(15,15,utf8_decode(' '."\n".' '),1,'C',1);
	    $this->SetY(-25);
		 $this->SetX(15);
	    $this->SetFont('Helvetica', '', 10);
		 $this->Image('../img/icon-facebook.png', 5, 253, 9, 9, 'png'); /// LOGO FACEBOOK
		 $this->Image('../img/icon-tiktok.png', 5, 261, 9, 9, 'png'); /// LOGO TIKTOK
		 $this->Image('../img/icon-pagina.png', 5, 269, 9, 9, 'png'); /// LOGO PAGINA
	    $this->MultiCell(145,8,utf8_decode('Servicios Integrales De Computacion Sic'."\n".'sic.serviciosintegrales'."\n".'www.sicsom.com/ventas'."\n".' '),1,'L',1);
	    $this->SetY(-25);
	    $this->SetX(160);
	    $this->MultiCell(56,6,utf8_decode('Av. Hidalgo No. 508 C. P. 99100, Sombrerete, Zac.'."\n".' '),1,'L',1);
	    $this->SetY(-10);
	    $this->SetX(160);
	    $this->AliasNbPages('tpagina');
	    $this->Cell(56,10,utf8_decode($this->PageNo().'/tpagina'),1,0,'R',1);
   }
}

//Creación del objeto de la clase heredada
$pdf=new PDF('P','mm','letter', true);
$pdf->SetAutoPageBreak(true, 35);
$pdf->AliasNbPages();
$pdf->SetMargins(15, 35, 10);
$pdf->setTitle(utf8_decode('SIC | COTIZACION'));// TITULO BARRA NAVEGACION
$pdf->AddPage('portrait', 'letter');

$pdf->SetFont('Helvetica','B', 12);
$pdf->Image('../img/logo.jpg', 30, 10, 25, 25, 'jpg'); /// LOGO SIC
/////   RECUADRO DERECHO  FECHA  //////
$pdf->SetFillColor(28, 98, 163);
$pdf->SetDrawColor(28, 98, 163);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetY($pdf->GetY()-24);
$pdf->SetX(135);
$pdf->Cell(70,8,utf8_decode('Cotización'),1,0,'C',1);
$pdf->SetY($pdf->GetY()+8);
$pdf->SetX(135);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->MultiCell(35,8,utf8_decode('Folio:'."\n".'Fecha:'),1,'R');
$pdf->SetY($pdf->GetY()-16);
$pdf->SetX(170);
$folio = substr(str_repeat(0, 5).$id, - 6);
$pdf->SetFont('Helvetica', '', 10);
$pdf->MultiCell(35,8,utf8_decode($folio."\n".$Cotizacion['fecha']),1,'C');

/////   RECAUADRO AZUL DEL CENTRO   ////////
$pdf->SetY($pdf->GetY()+4);
$pdf->SetFillColor(28, 98, 163);
$pdf->SetDrawColor(28, 98, 163);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Helvetica', 'B', 14);
$pdf->MultiCell(0,8,utf8_decode('SERVICIOS INTEGRALES DE COMPUTACIÓN'."\n".'¡Tecnología y Comunicación a tu alcance!'."\n"),0,'C',1);
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetY($pdf->GetY());
$pdf->MultiCell(0,5,utf8_decode('Internet, Telefonía, Asesoría, Implementación de Sistemas de Equipo de Cómputo, Consumibles, Accesorios, Redes, Cámaras de Vigilancia'."\n".'Tels: 433-93-562-86 y 433-93-562-88'),0,'C',1);
$pdf->SetY($pdf->GetY());
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->MultiCell(0,7,utf8_decode('GABRIEL VALLES REYES                                                                        RFC: VARG7511217E5'),1,'C',1);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetY($pdf->GetY()+5);
$pdf->SetFont('Helvetica', 'B', 11);
$id_cliente = $Cotizacion['id_cliente'];
$cliente = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM `punto-venta_clientes` WHERE id=$id_cliente"));
$pdf->MultiCell(0,6,utf8_decode('Cliente:  '.$cliente['nombre']."\n".'RFC:  '.$cliente['rfc']."\n".'Domicilio:  '.$cliente['direccion'].', '.$cliente['colonia']."\n".'Tel:  '.$cliente['telefono']),0,'L',0);

$pdf->SetY($pdf->GetY()+3);
$pdf->SetFont('Helvetica', '', 10);
$pdf->MultiCell(0,5,utf8_decode('Le saludo con gusto y aprovecho la ocasión para poner a su disposición la siguiente cotización solicitada:'),0,'L',0);

$pdf->SetY($pdf->GetY()+1);
////   TABLA A MOSTRAR    //////
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell(40,8,utf8_decode('Producto o servicio'),1,0,'C');
$pdf->Cell(34,8,utf8_decode('Imagen'),1,0,'C');
$pdf->Cell(56,8,utf8_decode('Descripción'),1,0,'C');
$pdf->Cell(17,8,utf8_decode('Cantidad'),1,0,'C');
$pdf->Cell(21,8,utf8_decode('Precio U.'),1,0,'C');
$pdf->Cell(23,8,utf8_decode('Importe'),1,0,'C');

////   CONTENIDO DE LA TABLA    /////
$pdf->SetFillColor(240, 240, 240);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0);
$pdf->Ln();

$detalle_cotizacion = mysqli_query($conn, "SELECT pvc.id as idcotiza, pva.imagen, pva.nombre, pva.descripcion, pva.modelo, pvdc.cantidad, pva.unidad, pvdc.precio_venta_u, pvdc.importe, pvc.total from punto_venta_cotizaciones as pvc INNER JOIN punto_venta_detalle_cotizacion as pvdc on pvc.id=pvdc.id_venta INNER JOIN punto_venta_articulos as pva on pvdc.id_articulo=pva.id WHERE pvc.id=$id");
$Total = 0;
while($articulo = mysqli_fetch_array($detalle_cotizacion)){ 

	///VERIFICAMOS CUANTAS COLUMNAS TENDRA EL RENGLON SEGUN EL LARGO DEL NOMBRE O DESCRIPCION	
	$ContNombre = ceil(strlen($articulo['nombre'])/19);
	$ContDescripcion = ceil(strlen($articulo['descripcion'])/25);

	$masN = ((strlen($articulo['nombre'])>17 AND strlen($articulo['nombre'])<=23) OR (strlen($articulo['nombre'])>32 AND strlen($articulo['nombre'])<38) OR (strlen($articulo['nombre'])>47 AND strlen($articulo['nombre'])<54)) ?  '          ':'';
	$masD = ((strlen($articulo['descripcion'])>24 AND strlen($articulo['descripcion'])<=30) OR (strlen($articulo['descripcion'])>49 AND strlen($articulo['descripcion'])<56) OR (strlen($articulo['descripcion'])>70 AND strlen($articulo['descripcion'])<78)) ?  '             ':'';
		
	//LE DECIMOS CUANTAS FILAS AFECTA A LOS DEMAS
	$AgregaG = 3;
	$AgregaN = 4-$ContNombre;
	$AgregaD = 4-$ContDescripcion;

	$pdf->SetX(15);
	$pdf->SetFont('Helvetica', '', 9);
	$pdf->MultiCell(40,6,utf8_decode("\n".$articulo['nombre'].$masN.str_repeat("\n", $AgregaN).' '),1,'C',1);
	$pdf->SetY($pdf->GetY()-30);
	$pdf->SetX(55);
	$pdf->MultiCell(34,6,utf8_decode(str_repeat("\n", 4).' '),1,'C',1);
	if ($articulo['imagen'] != '') {
		$pdf->Image('../Imagenes/Catalogo/'.$articulo['imagen'], 55.5, $pdf->GetY()-29.5, 33, 29, 'jpg'); /// LOGO SIC
	}
	$pdf->SetY($pdf->GetY()-30);
	$pdf->SetX(89);
	$pdf->MultiCell(56,6,utf8_decode("\n".$articulo['descripcion'].$masD.str_repeat("\n", $AgregaD).' '),1,'C',1);
	$pdf->SetY($pdf->GetY()-30);
	$pdf->SetX(145);
	$pdf->MultiCell(17,6,utf8_decode("\n".$articulo['cantidad'].$articulo['unidad'].str_repeat("\n", $AgregaG).' '),1,'C',1);
	$pdf->SetY($pdf->GetY()-30);
	$pdf->SetX(162);
	$pdf->MultiCell(21,6,utf8_decode("\n".'$'.sprintf('%.2f', $articulo['precio_venta_u']).str_repeat("\n", $AgregaG).' '),1,'C',1);
	$pdf->SetY($pdf->GetY()-30);
	$pdf->SetX(183);
	$pdf->SetFont('Helvetica', 'B', 9);
	$pdf->MultiCell(23,6,utf8_decode("\n".'$'.sprintf('%.2f', $articulo['importe']).str_repeat("\n", $AgregaG).' '),1,'R',1);
	$Total += $articulo['importe'];
}//FIN WHILE CATALOGO

//Casilla de total
$pdf->SetX(145);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(38,8,utf8_decode('TOTAL'),1,0,'C');
$pdf->Cell(23,8,utf8_decode('$'.sprintf('%.2f',$Total)),1,0,'R');
$pdf->SetY($pdf->GetY()+11);
$pdf->SetFont('Helvetica', '', 8);
$pdf->MultiCell(0,5,utf8_decode(' Nota: El precio de las instalaciones solo cubre el uso de 10 mts de cable en caso de ser necesario usar mas el precio por metro es de $15.00 pesos, puede aumentar un poco el precio dependiendo del material adicional que se requiera. Tiempo de realización promedio de 5 días hábiles. La presente cotización tiene una vigencia de 10 días a partir de su emisión.'),0,'C',0);
$pdf->SetY($pdf->GetY()+5);
$pdf->SetFont('Helvetica', 'B', 10);
$id_usuario = $Cotizacion['usuario'];
$usuario = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM `users` WHERE user_id=$id_usuario"));
$pdf->MultiCell(0,6,utf8_decode('En espera de vernos favorecidos con su elección, quedo a sus ordenes para cualquier duda o aclaración. '."\n".'ATENTAMENTE:'."\n". 'Ing. '.$usuario['firstname'].' '.$usuario['lastname']),0,'L',0);
//Aquí escribimos lo que deseamos mostrar... (PRINT)
$pdf->Output();
?>