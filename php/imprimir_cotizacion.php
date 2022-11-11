<?php
//ARCHIVO QUE DETECTA QUE PODAMOS USAR ESTE ARCHIVO SOLO SI HAY ALGUNA SESSION ACTIVA O INICIADA
include("is_logged.php");
// INCLUIMOS EL ARCHIVO CON LA CONEXXIONA LA BD PARA HACER CONSULTAS
include('../php/conexion.php');
//SE INCLUYE EL ARCHIVO QUE CONTIENEN LAS LIBRERIAS FPDF PARA CREAR ARCHIVOS PDF
include("../fpdf/fpdf.php");
/// SACAMOS LA INFORMACION DEL LA COTIZACION
$id = $_GET['id'];//RECIBIMOS POR EL METODO GET EL ID DE LA COTIZACIÓN
//echo $id;

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
$pdf->Image('../img/logo.jpg', 30, 10, 30, 30, 'jpg'); /// LOGO SIC
/////   RECUADRO DERECHO  FECHA  //////
$pdf->SetFillColor(28, 98, 163);
$pdf->SetDrawColor(28, 98, 163);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetY($pdf->GetY()-15);
$pdf->SetX(120);
$pdf->Cell(70,8,utf8_decode('Catálogo'),1,0,'C',1);
$pdf->SetY($pdf->GetY()+8);
$pdf->SetX(120);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(35,10,utf8_decode('Fecha Impresión:'),1,0,'C');
$pdf->SetY($pdf->GetY());
$pdf->SetX(155);
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(35,10,date('Y-m-d'),1,0,'C');
$pdf->Ln();
/////   RECAUADRO AZUL DEL CENTRO   ////////
$pdf->SetY($pdf->GetY()+10);
$pdf->SetFillColor(28, 98, 163);
$pdf->SetDrawColor(28, 98, 163);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Helvetica', 'B', 14);
$pdf->MultiCell(0,9,utf8_decode('SERVICIOS INTEGRALES DE COMPUTACIÓN'."\n".'¡Tecnología y Comunicación a tu alcance!'."\n"),0,'C',1);
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetY($pdf->GetY());
$pdf->MultiCell(0,5,utf8_decode('Internet, Telefonía, Asesoría, Implementación de Sistemas de Equipo de Cómputo, Consumibles, Accesorios, Redes, Cámaras de Vigilancia'."\n".'Tels: 433-93-562-86 y 433-93-562-88'),0,'C',1);
$pdf->SetY($pdf->GetY());
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->MultiCell(0,8,utf8_decode('GABRIEL VALLES REYES                                                                         RFC: VARG7511217E5'),1,'C',1);



////   TITULO ANTES DE TABLA  ///////
$pdf->SetTextColor(28, 98, 163);
$pdf->SetY($pdf->GetY()+10);


////   TABLA A MOSTRAR    //////
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Helvetica', 'B', 13);
$pdf->SetY($pdf->GetY());
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(8,8,utf8_decode('N°'),1,0,'C');
$pdf->Cell(35,8,utf8_decode('Imagen'),1,0,'C');
$pdf->Cell(42,8,utf8_decode('Nombre'),1,0,'C');
$pdf->Cell(36,8,utf8_decode('Modelo'),1,0,'C');
$pdf->Cell(16,8,utf8_decode('Cantidad'),1,0,'C');
$pdf->Cell(12,8,utf8_decode('Unidad'),1,0,'C');
$pdf->Cell(22,8,utf8_decode('Precio U'),1,0,'C');
$pdf->Cell(20,8,utf8_decode('Importe'),1,0,'C');


////   CONTENIDO DE LA TABLA    /////
$pdf->SetFillColor(240, 240, 240);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0);
$pdf->Ln();
$aux = 1;


//$detalle_cotizacion = mysqli_query($conn, "SELECT pvc.id as idcotiza, pvc.cotizacion, pvc.id_cliente, pvc.tipo_cambio, pvc.total, pvc.usuario, pvc.fecha, pvdc.id_articulo, pvdc.cantidad, pva.codigo, pva.nombre, pva.descripcion from punto_venta_cotizaciones as pvc INNER JOIN punto_venta_detalle_cotizacion as pvdc on pvc.id=pvdc.id_venta INNER JOIN punto_venta_articulos as pva on pvdc.id_articulo=pva.id WHERE pvc.id=$id");
$detalle_cotizacion = mysqli_query($conn, "SELECT pvc.id as idcotiza, pva.imagen, pva.nombre, pva.descripcion, pva.modelo, pvdc.cantidad, pva.unidad, pvdc.precio_venta_u, pvdc.importe, pvc.total from punto_venta_cotizaciones as pvc INNER JOIN punto_venta_detalle_cotizacion as pvdc on pvc.id=pvdc.id_venta INNER JOIN punto_venta_articulos as pva on pvdc.id_articulo=pva.id WHERE pvc.id=$id");

while($articulos_catalogo = mysqli_fetch_array($detalle_cotizacion)){ 

	///VERIFICAMOS CUANTAS COLUMNAS TENDRA EL RENGLON SEGUN EL LARGO DEL NOMBRE O DESCRIPCION	
	$ContNombre = ceil(strlen($articulos_catalogo['nombre'])/17);
	$ContDescripcion = ceil(strlen($articulos_catalogo['descripcion'])/36);

	$masN = ((strlen($articulos_catalogo['nombre'])>16 AND strlen($articulos_catalogo['nombre'])<=21) OR (strlen($articulos_catalogo['nombre'])>32 AND strlen($articulos_catalogo['nombre'])<38) OR (strlen($articulos_catalogo['nombre'])>47 AND strlen($articulos_catalogo['nombre'])<54)) ?  '         ':'';
	$masD = ((strlen($articulos_catalogo['descripcion'])>35 AND strlen($articulos_catalogo['descripcion'])<=48) OR (strlen($articulos_catalogo['descripcion'])>70 AND strlen($articulos_catalogo['descripcion'])<85) OR (strlen($articulos_catalogo['descripcion'])>106 AND strlen($articulos_catalogo['descripcion'])<120)) ?  '                ':'';
		
	//LE DECIMOS CUANTAS FILAS AFECTA A LOS DEMAS
	$AgregaG = 4;
	$AgregaN = 5-$ContNombre;
	$AgregaD = 6-$ContDescripcion;

	$pdf->SetX(15);
	$pdf->SetFont('Helvetica', '', 9);
	$pdf->MultiCell(8,6,utf8_decode("\n".$aux.str_repeat("\n", $AgregaG).' '),1,'C',1);
	$pdf->SetY($pdf->GetY()-36);
	$pdf->SetX(23);
	$pdf->MultiCell(35,6,utf8_decode(str_repeat("\n", 5).' '),1,'C',1);
	if ($articulos_catalogo['imagen'] != '') {
		$pdf->Image('../Imagenes/Catalogo/'.$articulos_catalogo['imagen'], 24.5, $pdf->GetY()-34, 32, 32, 'jpg'); /// LOGO SIC
	}
	$pdf->SetY($pdf->GetY()-36);
	$pdf->SetX(58);
	$pdf->MultiCell(42,6,utf8_decode("\n".$articulos_catalogo['nombre'].str_repeat("\n", $AgregaG).' '),1,'C',1);
	$pdf->SetY($pdf->GetY()-36);
	$pdf->SetX(100);
	$pdf->MultiCell(36,6,utf8_decode("\n".$articulos_catalogo['modelo'].$masN.str_repeat("\n", $AgregaN).' '),1,'C',1);
	$pdf->SetY($pdf->GetY()-36);
	$pdf->SetX(136);
	$pdf->MultiCell(16,6,utf8_decode("\n".$articulos_catalogo['cantidad'].$masN.str_repeat("\n", $AgregaN).' '),1,'C',1);

	$pdf->SetY($pdf->GetY()-36);
	$pdf->SetX(152);
	$pdf->MultiCell(12,6,utf8_decode("\n".$articulos_catalogo['unidad'].$masN.str_repeat("\n", $AgregaN).' '),1,'C',1);
	$pdf->SetY($pdf->GetY()-36);
	$pdf->SetX(164);
	$pdf->MultiCell(22,6,utf8_decode("\n".'$'.$articulos_catalogo['precio_venta_u'].$masN.str_repeat("\n", $AgregaN).' '),1,'C',1);
	$pdf->SetY($pdf->GetY()-36);
	$pdf->SetX(186);
	$pdf->SetFont('Helvetica', 'B', 9);
	$pdf->MultiCell(20,6,utf8_decode("\n".'$'.sprintf('%.2f', $articulos_catalogo['importe']).str_repeat("\n", $AgregaG).' '),1,'R',1);
	$aux ++;
}//FIN WHILE CATALOGO


$Total = mysqli_query($conn, "SELECT * FROM `punto_venta_cotizaciones` WHERE id = $id");
//Casilla de total
$pdf->SetX(164);
$pdf->Cell(42,8,utf8_decode('Total'),1,0,'C');
$pdf->SetY($pdf->GetY()+8);
$pdf->SetX(164);
//$pdf->MultiCell(42,1.5,utf8_decode("\n".$Total['total'].$masN.str_repeat("\n", $AgregaN).' '),1,'C',1);

//Aquí escribimos lo que deseamos mostrar... (PRINT)
$pdf->Output();

?>