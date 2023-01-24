<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATPS
    include('../php/conexion.php');
    #INCLUIMOS EL ARCHIVO CON LAS LIBRERIAS DE FPDF PARA PODER CREAR ARCHIVOS CON FORMATO PDF
    include("../fpdf/fpdf.php");
    #INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
    include('is_logged.php');

    $id_devolucion = $_GET['id'];;//TOMAMOS EL ID DE LA DEVOLUCION
    $devolucion = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM punto_venta_devoluciones_articulos WHERE id = $id_devolucion"));

    #DEFINIMOS UNA ZONA HORARIA
    date_default_timezone_set('America/Mexico_City');
    $Fecha_hoy = date('Y-m-d');//CREAMOS UNA FECHA DEL DIA EN CURSO SEGUN LA ZONA HORARIA
   
class PDF extends FPDF{

    }

   $pdf = new PDF('P', 'mm', array(80,297));
    $pdf->setTitle(utf8_decode('SIC | TICKET DEVOLUCION'));// TITULO BARRA NAVEGACION
    $pdf->AddPage();

    $pdf->Image('../img/logo.jpg', 30, 2, 20, 21, 'jpg'); /// LOGO SIC

    /// INFORMACION DE LA EMPRESA ////
    $pdf->SetFont('Courier','B', 8);
    $pdf->SetY($pdf->GetY()+15);
    $pdf->SetX(6);
    $pdf->MultiCell(69,3,utf8_decode('SERVICIOS INTEGRALES DE COMPUTACIÓN'."\n".'GABRIEL VALLES REYES'."\n".'RFC: VARG7511217E5'."\n".'AV. HIDALGO COL. CENTRO C.P. 99100 SOMBRERETE, ZACATECAS '."\n".'TEL. 4339356288'),0,'C',0);
    /// INFORMACION DE LA DEVOLUCION
    $pdf->SetY($pdf->GetY()+4);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','B', 10);    
    $folio = substr(str_repeat(0, 5).$id_devolucion, - 6);
    $pdf->MultiCell(69,4,utf8_decode(date_format(new \DateTime($devolucion['fecha'].' '.$devolucion['hora']), "d/m/Y H:i" ).'             FOLIO: '.$folio),0,'C',0);
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','B', 11);
    $pdf->MultiCell(69,4,utf8_decode('TICKET DEVOLUCION'),0,'C',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);   
    $id_venta = $devolucion['id_venta'];//TOMAMOS EL ID DE LA VENTA
    $venta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM punto_venta_ventas WHERE id = $id_venta"));
    if ($venta['id_cliente'] == 0) {
        $pdf->SetY($pdf->GetY()+2);
        $pdf->SetX(6);
        $pdf->SetFont('Courier','B', 10);
        $pdf->MultiCell(69,3,utf8_decode('VENTA N°'.$devolucion['id_venta']."\n".'VENTA AL PUBLICO'),0,'L',0);
        $pdf->SetY($pdf->GetY()+3);
        $pdf->SetX(6);
    }else{
        $pdf->SetY($pdf->GetY()+2);
        $pdf->SetX(6);
        $id_cliente = $venta['id_cliente'];
        $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto-venta_clientes` WHERE id = $id_cliente"));
        $pdf->SetFont('Courier','B', 10);
        $pdf->MultiCell(69,3,utf8_decode('VENTA N°'.$devolucion['id_venta']."\n".'CLIENTE: '.$cliente['nombre']."\n".'RFC:  '.$cliente['rfc']."\n".'TELEFONO:  '.$cliente['telefono']."\n".'EMAIL: '.$cliente['email']),0,'L',0);
        $pdf->SetY($pdf->GetY()+5);
        $pdf->SetX(6);
    }// FIN else   

    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0); 
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','B', 9);    
    $pdf->MultiCell(70,4,utf8_decode('CODIGO             CANT.  DESCRIPCION'),0,'L',0);

    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $sql_detalles = mysqli_query($conn, "SELECT * FROM `pv_detalles_devoluciones` WHERE id_devolucion = $id_devolucion");
    if (mysqli_num_rows($sql_detalles)>0) {
        while ($articulo = mysqli_fetch_array($sql_detalles)) {
            $id_articulo = $articulo['id_articulo'];
            $info_articulo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_articulos` WHERE id = $id_articulo"));
            $pdf->SetY($pdf->GetY()+1);
            $pdf->SetX(5);
            $pdf->SetFont('Helvetica','', 9);
            $pdf->MultiCell(25,3,utf8_decode($info_articulo['codigo']),0,'L',0);
            $pdf->SetY($pdf->GetY()-3);
            $pdf->SetX(30);
            $pdf->MultiCell(10,3,utf8_decode($articulo['cantidad']),0,'R',0);    
            $pdf->SetY($pdf->GetY()-3);
            $pdf->SetX(40);
            $pdf->MultiCell(34,3,utf8_decode($info_articulo['nombre']),0,'L',0);
        }
    }

    $pdf->SetY($pdf->GetY()+5);
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','B', 10); 
    $id_user = $devolucion['usuario'];     
    #TOMAMOS LA INFORMACION DEL USUARIO QUE ESTA LOGEADO QUIEN HIZO LOS COBROS
    $usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));  
    $pdf->MultiCell(70,4,utf8_decode('LE ATENDIO: '.$usuario['firstname'].' '.$usuario['lastname']),0,'C',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(5);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(70,3,utf8_decode('------------------------------------------------------------------------'),0,'L',0);

    $pdf->Output('VENTA','I');
?>