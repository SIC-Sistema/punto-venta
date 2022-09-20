<?php
//ARCHIVO QUE DETECTA QUE PODAMOS USAR ESTE ARCHIVO SOLO SI HAY ALGUNA SESSION ACTIVA O INICIADA
include("is_logged.php");
// INCLUIMOS EL ARCHIVO CON LA CONEXXIONA LA BD PARA HACER CONSULTAS
include('../php/conexion.php');
//SE INCLUYE EL ARCHIVO QUE CONTIENEN LAS LIBRERIAS FPDF PARA CREAR ARCHIVOS PDF
include("../fpdf/fpdf.php");
/// SACAMOS LA INFORMACION DEL CATALOGO
$articulos_catalogo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM punto_venta_articulos"));
$catalogo = mysqli_query($conn, "SELECT * FROM punto_venta_articulos");

class PDF extends FPDF{
   //Cabecera de página
   function Header(){ 
	   $this->SetFont('Arial','B', 12);
	   $this->Image('../img/logo_ticket.jpg', 185, 8, 20, 20, 'jpg');
		$this->SetY($this->GetY()-20);
	   $this->Cell(0,5,utf8_decode('SERVICIOS INTEGRALES DE COMPUTACIÓN'),0,0,'C');
	   $this->Ln(8);
	   $this->SetTextColor(40, 40, 135);
	   $this->Cell(0,5,utf8_decode('"Tecnología y comunicación a tu alcance"'),0,0,'C');
   }

   //Pie de pagina 
   function footer(){
	   $this->SetFont('Arial','', 10);
	   $this->SetY(-33);
	   $this->Write(5, 'facebook.com/SIC.SOMBRERETE');
	   $this->Ln();
	   $this->Write(5, 'www.sicsom.com');
	   $this->SetY(-33);
	   $this->SetX(-60);
	   $this->Write(5, 'Avenida Hidalgo No. 508');
	   $this->SetY(-28);
	   $this->SetX(-69);
	   $this->Write(5, 'C.P. 99100    Sombrerete, Zac.');
	   $this->SetY(-23);
	   $this->SetX(-79);
	   $this->Write(5, 'Tels. 433 9 35 62 86 y 433 935 62 88');
	   $this->SetY(-12);
	   $this->SetX(-30);
	   $this->AliasNbPages('tpagina');
	   $this->Write(5, $this->PageNo().'/tpagina');
   }
}

//Creación del objeto de la clase heredada
$pdf=new PDF('P','mm','letter', true);
$pdf->SetMargins(15, 35, 10);
$pdf->SetAutoPageBreak(true, 35);
$pdf->AliasNbPages();
$pdf->AddPage('portrait', 'letter');

$pdf->setTitle(utf8_decode('SIC | CATALOGO: '));

$pdf->SetY($pdf->GetY()+15);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(185,10,utf8_decode('CATALOGO'),0,0,'C');
$pdf->SetDrawColor(30, 40, 125);
$pdf->SetLineWidth(2);
$pdf->Line(60,$pdf->GetY()+9, 150, $pdf->GetY()+9);

/////   RECUADRO DERECHO    //////
$pdf->SetLineWidth(0);
$pdf->SetTextColor(0,0,0);
$pdf->SetY($pdf->GetY()+16);
$pdf->SetX(25);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(90,21,utf8_decode('Fecha:'),0,0,'C');
$pdf->SetDrawColor(30, 40, 125);
$pdf->SetLineWidth(1);
$pdf->Line(105,$pdf->GetY()+15, 198, $pdf->GetY()+15);
$pdf->SetY($pdf->GetY()+10);
$pdf->SetX(104);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20,17,utf8_decode('Fecha Creación: ').$articulos_catalogo['fecha'],0,0,'');
$pdf->SetY($pdf->GetY()+6);
$pdf->SetX(104);
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(30, 40, 125);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0);
$pdf->Ln(10);

////   TITULO ANTES DE TABLA  ///////
$pdf->SetY($pdf->GetY()+6);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(180,10,utf8_decode('ARTICULOS: '),0,0,'C');
$pdf->SetDrawColor(30, 40, 125);
$pdf->SetLineWidth(1);
$pdf->Line(83,$pdf->GetY()+8, 123, $pdf->GetY()+8);
$pdf->Ln(12);

/////   TABLA A MOSTRAR    //////
$pdf->Cell(15,10,utf8_decode('No'),0,0,'C');
$pdf->Cell(40,10,utf8_decode('Codigo'),0,0,'C');
$pdf->Cell(57,10,utf8_decode('Descripcion'),0,0,'C');
$pdf->Cell(33,10,utf8_decode('Precio'),0,0,'C');
$pdf->Cell(39,10,utf8_decode('Unidad'),0,0,'C');
$pdf->Line(15,$pdf->GetY()+9, 200, $pdf->GetY()+9);
////   CONTENIDO DE LA TABLA    /////
$pdf->SetFont('Arial', '', 10);
$pdf->SetFillColor(240, 240, 240);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetLineWidth(0);
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln();
$aux = 1;

while($articulos_catalogo = mysqli_fetch_array($catalogo)){ 
	//$articulos_catalogo['descripcion'] =(strlen ($articulos_catalogo['descripcion'])>22)?'Link':$articulos_catalogo['descripcion'];
	if (strlen ($articulos_catalogo['codigo'])>100 OR strlen ($articulos_catalogo['descripcion'])>60 OR strlen ($articulos_catalogo['precio'])>100 OR strlen ($articulos_catalogo['unidad'])>100) {
		// Doble columna
		$Y = 12;	$extra = ''."\n".' ';
	}else{
		// SENCILLA
		$Y =6; $extra = '';
	}
	$pdf->SetX(15);
    $pdf->MultiCell(15,6,utf8_decode($aux.$extra),1,'C',1);
    $pdf->SetY($pdf->GetY()-$Y);
	$pdf->SetX(30);
	$pdf->MultiCell(40,6,utf8_decode((strlen ($articulos_catalogo['codigo'])>10)?$articulos_catalogo['codigo']:$articulos_catalogo['codigo'].$extra),1,'C',1);
	$pdf->SetY($pdf->GetY()-$Y);
	$pdf->SetX(70);
	$pdf->MultiCell(60,6,utf8_decode((strlen ($articulos_catalogo['descripcion'])>30)?$articulos_catalogo['descripcion']:$articulos_catalogo['descripcion'].$extra),1,'C',1);
	$pdf->SetY($pdf->GetY()-$Y);
	$pdf->SetX(125);
	$pdf->MultiCell(40,6,utf8_decode((strlen ($articulos_catalogo['precio'])>17)?$articulos_catalogo['precio']:$articulos_catalogo['precio'].$extra),1,'C',1);
    $pdf->SetY($pdf->GetY()-$Y);
	$pdf->SetX(160);
    $pdf->MultiCell(40,6,utf8_decode((strlen ($articulos_catalogo['unidad'])>17)?$articulos_catalogo['unidad']:$articulos_catalogo['unidad'].$extra),1,'C',1);
	$aux ++;
}

//Aquí escribimos lo que deseamos mostrar... (PRINT)
$pdf->Output();
?>