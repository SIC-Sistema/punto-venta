<?php
//ARCHIVO QUE DETECTA QUE PODAMOS USAR ESTE ARCHIVO SOLO SI HAY ALGUNA SESSION ACTIVA O INICIADA
include("is_logged.php");
// INCLUIMOS EL ARCHIVO CON LA CONEXXIONA LA BD PARA HACER CONSULTAS
include('../php/conexion.php');
//SE INCLUYE EL ARCHIVO QUE CONTIENEN LAS LIBRERIAS FPDF PARA CREAR ARCHIVOS PDF
include("../fpdf/fpdf.php");

// OBTENEMOS LA INFORMACION DEL USUARIO PARA OBTENER EL DATO DEL ALMACEN
$user_id = $_SESSION['user_id'];
$datacenter = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM users WHERE user_id=$user_id"));
$id_almacen = $datacenter['almacen'];// ID DEL ALMACEN ASIGNADO AL USUARIO LOGEADO
//SACAMOS LA INFORMACION DEL ALMACEN
$Almacen = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM `punto_venta_almacenes` WHERE nombre=$id_almacen"));

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
$pdf->setTitle(utf8_decode('SIC | CATALOGO '));// TITULO BARRA NAVEGACION
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
$pdf->Cell(10,8,utf8_decode('N°'),1,0,'C');
$pdf->Cell(50,8,utf8_decode('Código'),1,0,'C');
$pdf->Cell(65,8,utf8_decode('Articulo'),1,0,'C');
$pdf->Cell(66,8,utf8_decode('Descripción'),1,0,'C');
// $pdf->Cell(65,8,utf8_decode('Descripción'),1,0,'C');
// $pdf->Cell(22,8,utf8_decode('Precio'),1,0,'C');

////   CONTENIDO DE LA TABLA    /////
$pdf->SetFillColor(240, 240, 240);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0);
$pdf->Ln();
$aux = 1;

//AQUÍ SE MUESTRA EL CONTENIDO DE LA TABLA
//VARIABLE $articulos PARA TRAER TODO EL CONTENIDO DE LA TABLA DEL ALMACEN DEL USUARIO
$articulos = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM `punto_venta_almacen_general` WHERE id_almacen=$id_almacen"));

foreach($articulos as $articulo)
{
    $pdf->Cell(10,8,utf8_decode('N°'),1,0,'C');
    $pdf->Cell(50,8,utf8_decode('Código'),1,0,'C');
    $pdf->Cell(65,8,utf8_decode('Articulo'),1,0,'C');
    $pdf->Cell(66,8,utf8_decode('Descripción'),1,0,'C');
    $pdf->Ln();
}
//Aquí escribimos lo que deseamos mostrar... (PRINT)
$pdf->Output();
?>