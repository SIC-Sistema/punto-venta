<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATPS
    include('../php/conexion.php');
    #INCLUIMOS EL ARCHIVO CON LAS LIBRERIAS DE FPDF PARA PODER CREAR ARCHIVOS CON FORMATO PDF
    include("../fpdf/fpdf.php");
    #INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
    include('is_logged.php');

    $Venta =$_GET['v'];//TOMAMOS EL ID DE LA VENTA PREVIAMENTE CREADO
    $FormaPago = $_GET['p'];
    $descripcion = 'Venta N°'.$Venta;
    $pago = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pagos WHERE descripcion = '$descripcion' AND tipo = 'Punto Venta'"));

    #DEFINIMOS UNA ZONA HORARIA
    date_default_timezone_set('America/Mexico_City');
    $Fecha_hoy = date('Y-m-d');//CREAMOS UNA FECHA DEL DIA EN CURSO SEGUN LA ZONA HORARIA
    $Hora = date('H:i:s');
   
class PDF extends FPDF{

    }

    $pdf = new PDF('P', 'mm', array(80,297));
    $pdf->setTitle(utf8_decode('SIC | TICKET VENTA'));// TITULO BARRA NAVEGACION
    $pdf->AddPage();

    $pdf->Image('../img/logo.jpg', 30, 2, 20, 21, 'jpg'); /// LOGO SIC

    /// INFORMACION DE LA EMPRESA ////
    $pdf->SetFont('Courier','B', 8);
    $pdf->SetY($pdf->GetY()+15);
    $pdf->SetX(6);
    $pdf->MultiCell(69,3,utf8_decode('SERVICIOS INTEGRALES DE COMPUTACIÓN'."\n".'GABRIEL VALLES REYES'."\n".'RFC: VARG7511217E5'."\n".'AV. HIDALGO COL. CENTRO C.P. 99100 SOMBRERETE, ZACATECAS '."\n".'TEL. 4339356288'),0,'C',0);
    /// INFORMACION DE LA VENTA
    $pdf->SetY($pdf->GetY()+4);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','B', 10);    
    $corte = substr(str_repeat(0, 5).$Venta, - 6);
    $pdf->MultiCell(69,4,utf8_decode(date_format(new \DateTime($Fecha_hoy.' '.$Hora), "d/m/Y H:i" ).'             FOLIO: '.$corte),0,'C',0);
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','B', 11);
    $pdf->MultiCell(69,4,utf8_decode('TICKET VENTA'),0,'C',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);   
    if ($pago['id_cliente'] == 0) {
        $pdf->SetY($pdf->GetY()+3);
        $pdf->SetX(6);
        $pdf->SetFont('Helvetica','B', 10);
        $pdf->MultiCell(69,4,utf8_decode('==== VENTA AL PUBLICO ===='),0,'C',0);
        $pdf->SetY($pdf->GetY()+3);
        $pdf->SetX(6);
    }else{
        $pdf->SetY($pdf->GetY());
        $pdf->SetX(6);
        $id_cliente = $pago['id_cliente']-100000;
        $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto-venta_clientes` WHERE id = $id_cliente"));
        $pdf->SetFont('Courier','B', 9);
        $pdf->MultiCell(69,3,utf8_decode('CLIENTE: '.$cliente['nombre']."\n".'RFC:  '.$cliente['rfc']."\n".'TELEFONO:  '.$cliente['telefono']."\n".'EMAIL: '.$cliente['email']."\n".'DIRECCION: '.$cliente['direccion'].' '.$cliente['colonia'].', '.$cliente['localidad']),0,'L',0);
        $pdf->SetY($pdf->GetY());
        $pdf->SetX(6);
    }// FIN else    
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','B', 8);    
    $pdf->MultiCell(69,4,utf8_decode('  DESCRIPCION          CANT.    PRECIO      TOTAL'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
    $sql_detalle_venta = mysqli_query($conn, "SELECT * FROM `punto_venta_detalle_venta` WHERE id_venta = $Venta");
    if (mysqli_num_rows($sql_detalle_venta) > 0) {
        $pdf->SetFont('Helvetica','', 8);    
        while ($detalle = mysqli_fetch_array($sql_detalle_venta)){
            $id_art = $detalle['id_producto'];
            $articulo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_articulos` WHERE id = $id_art"));
            $pdf->SetY($pdf->GetY()+1);
            $pdf->SetX(6);
            $pdf->MultiCell(30,3,utf8_decode($articulo['nombre']),0,'L',0);
            $pdf->SetY($pdf->GetY()-3);
            $pdf->SetX(36);
            $pdf->MultiCell(8,3,utf8_decode($detalle['cantidad']),0,'R',0);            
            $pdf->SetY($pdf->GetY()-3);
            $pdf->SetX(44);
            $pdf->MultiCell(16,3,utf8_decode('$'.sprintf('%.1f',$detalle['precio_venta'])),0,'R',0);                  
            $pdf->SetY($pdf->GetY()-3);
            $pdf->SetX(60);
            $pdf->MultiCell(16,3,utf8_decode('$'.sprintf('%.1f',$detalle['importe'])),0,'R',0);
        }
    }
    $pdf->SetY($pdf->GetY()+2);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
    $pdf->SetFont('Helvetica','', 9);
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(6);
    $pdf->MultiCell(27,5,utf8_decode('IVA:'."\n".'SUBTOTAL:'."\n".'TOTAL VENTAS:'),0,'R',0);    
    $pdf->SetY($pdf->GetY()-15);
    $pdf->SetX(35);
    $ventaAll = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_ventas` WHERE id = $Venta"));
    $pdf->MultiCell(39,5,utf8_decode('$'.sprintf('%.2f',$ventaAll['total']*0.16)."\n".'$'.sprintf('%.2f',$ventaAll['total']-($ventaAll['total']*0.16))."\n".'$'.sprintf('%.2f',$ventaAll['total'])),0,'R',0);

    $pdf->SetY($pdf->GetY());
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY()+1);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','B', 10);
    $pdf->MultiCell(27,5,utf8_decode('Pago '.$ventaAll['tipo_cambio'].':'."\n".'Cambio:'),0,'R',0);
    $pdf->SetY($pdf->GetY()-10);
    $pdf->SetX(35);
    $pdf->MultiCell(39,5,utf8_decode('$'.sprintf('%.2f',$FormaPago)."\n".'$'.sprintf('%.2f',$FormaPago-$ventaAll['total'])),0,'R',0);

    $pdf->SetY($pdf->GetY()+12);
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','B', 9);      
    $id_user = $ventaAll['usuario'];// ID DEL USUARIO AL QUE SE LE APLICO EL CORTE
    #TOMAMOS LA INFORMACION DEL USUARIO QUE ESTA LOGEADO QUIEN HIZO LOS COBROS
    $usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));  
    $pdf->MultiCell(69,4,utf8_decode('LE ATENDIO: '.$usuario['firstname'].' '.$usuario['lastname']),0,'C',0);
    $pdf->SetY($pdf->GetY());
    $pdf->SetX(6);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);

    $pdf->SetY($pdf->GetY()+3);
    $pdf->SetX(6);    
    $pdf->SetFont('Helvetica','B', 9);      
    $pdf->MultiCell(69,4,utf8_decode('¡GRACIAS POR TU COMPRA!'."\n".'TODO LO QUE QUIERES ESTA EN SIC'),0,'C',0);
    $pdf->SetY($pdf->GetY()+3);
    $pdf->SetFont('Helvetica','', 8);
    $pdf->SetX(6);
    $pdf->MultiCell(69,3,utf8_decode('-----------------------------------------------------------------------'),0,'L',0);


    $pdf->Output('VENTA','I');
?>