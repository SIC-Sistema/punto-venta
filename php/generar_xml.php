<?php
  #crear(); //Creamos el archivo
  //ARCHIVO QUE CONTIENE LA VARIABLE CON LA CONEXION A LA BASE DE DATOS
  include('../php/conexion.php');
  //ARCHIVO QUE CONDICIONA QUE TENGAMOS ACCESO A ESTE ARCHIVO SOLO SI HAY SESSION INICIADA Y NOS PREMITE TIMAR LA INFORMACION DE ESTA
  include('is_logged.php');
  //DEFINIMOS LA ZONA  HORARIA
  date_default_timezone_set('America/Mexico_City');
  $id_user = $_SESSION['user_id'];// ID DEL USUARIO LOGEADO
  $Fecha_hoy = date('Y-m-d');// FECHA ACTUAL
  #leer();  //Luego lo leemos
   
  //Para crear el archivo
 # function crear(){      
      $xml = new DomDocument('1.0', 'UTF-8');

     
$contenido = '<?xml version="1.0" encoding="utf-8"?><cfdi:Comprobante xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd" Version="3.3" Serie="RogueOne" Folio="HNFK231" Fecha="2022-11-01T13:25:26" Sello="LlxamMQ1DTtqkO0td7XsEu+fcAAgeNUnMgE8JiNRYoiInGRJ5wyOD+u5xi1jfIbyy7l9ReTzFK/a/TOaKAoYbpckIxK2aNLzBFzp76C3jGXuYMlYzoKfNvrbXAvi885rv+YeuIFm/nqXzo6E7GufhUb/uuf3eQGUsBG2Ya6d/1saFeZ+HXT0Mp3lcKCDPSNWEZRKrMRUV1Khe5p816wWkPk2cgn3mchV02AVsxZdx3DxgoXFhoZqR9GDjiz3NuPYH/iyOyrsrahk4mCxrlEoc+g5jyu3yXOAC81IwU6QZK5NBlMMDmjeD3kmM8dWFNoUb0Ab8LdjQmJ3rE+Jmdevbw==" FormaPago="01" NoCertificado="20001000000300022816" Certificado="MIIF0TCCA7mgAwIBAgIUMjAwMDEwMDAwMDAzMDAwMjI4MTYwDQYJKoZIhvcNAQELBQAwggFmMSAwHgYDVQQDDBdBLkMuIDIgZGUgcHJ1ZWJhcyg0MDk2KTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExODA2BgNVBAsML0FkbWluaXN0cmFjacOzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNpw7NuMSkwJwYJKoZIhvcNAQkBFhphc2lzbmV0QHBydWViYXMuc2F0LmdvYi5teDEmMCQGA1UECQwdQXYuIEhpZGFsZ28gNzcsIENvbC4gR3VlcnJlcm8xDjAMBgNVBBEMBTA2MzAwMQswCQYDVQQGEwJNWDEZMBcGA1UECAwQRGlzdHJpdG8gRmVkZXJhbDESMBAGA1UEBwwJQ295b2Fjw6FuMRUwEwYDVQQtEwxTQVQ5NzA3MDFOTjMxITAfBgkqhkiG9w0BCQIMElJlc3BvbnNhYmxlOiBBQ0RNQTAeFw0xNjEwMjUyMTU0MTlaFw0yMDEwMjUyMTU0MTlaMIG9MR4wHAYDVQQDExVNQiBJREVBUyBESUdJVEFMRVMgU0MxHjAcBgNVBCkTFU1CIElERUFTIERJR0lUQUxFUyBTQzEeMBwGA1UEChMVTUIgSURFQVMgRElHSVRBTEVTIFNDMSUwIwYDVQQtExxMQU44NTA3MjY4SUEgLyBGVUFCNzcwMTE3QlhBMR4wHAYDVQQFExUgLyBGVUFCNzcwMTE3TURGUk5OMDkxFDASBgNVBAsUC1BydWViYV9DRkRJMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAjHr4KeoEx3BdkQP93AuN4fKo0rCZQsd9RJGBzQFvhmPJjGaVP81OUORM+lCRllxZxATZCAIFPOT3jl5wYgtolGYWWrt1HoAiuja1LKDGKrYgph0qWYKYeuew10fTyV+AeSbx1jTKz1PAAak06hx4M0rvmdiGO/Kg00/0wKz5/L3ZIMXEj+Hgr0IGh/yUIy8m5aKf+9jwuNttm/xDoeW3A8pxuidPU1Z1vliaZs75n89hC9LNwshhoaF3AvXIsgLDeuh9WoMGSm0HrilP9umFnm3nGUESiJa15Ep7LbG4CIhZrrknSm4fyrPk9KAigqLYMJhRsRwfp2qncAnAA+FuSQIDAQABox0wGzAMBgNVHRMBAf8EAjAAMAsGA1UdDwQEAwIGwDANBgkqhkiG9w0BAQsFAAOCAgEAd7t48tgawC9aczrGYt+4GFRcjj1LVKV3NElG+VH2s51KPkKPLj2Sw6OiEOGd+49spxHj1VR5MFvJo/pEJLY3EuLTifC9YZZYC8pHNDiA/eSvKqW5JNzp5/rgs3qAG1GrfdNGuSD3FkqhDdB6tJYqzTc12IC7xEAhKXrWZYCqa+zb9ogtzrUVL3vRRLMpnGEHK2yx8dhvG35qjHEfXyuoBsWILrVmnPpDCFO/CCLQB1OuMti1mlir6voBN0L1EbFK30w2bEuVihAeVLX8vVfMq4ZPI7UTLnblGnN11CCqiZkWhhehYrMdCjb5thMkEA+CMlIaFJYp7pNkLxQd4Y5+r8pTrdxxyvpA51DIWdoxvwaOiz1bzZk6ElVY2rfxwyZaJ17cJ1jmS4Yb5P4h8+5zkmZnPmRqfmaVO3nsApLWP6A38ZBrwwss429PJMSpfeXKGysPsqwF0yP3blsM7Cw53393LSHGKNm2GgG0kcrHnbbku6z6fjBdXMQQ5vjPuMNyw/pe3PzQLVoNOrD5AOoZmSG2TI3DtY4edLdiGmNQjo3MmAMMq4s7lr4AELPWAZRbnOlD1nEWGLdRp1mViteDvXwBL9E98EB4K9xK21DvgJ6rzw/D9rX6epeANfoXazWC0iCYcBNXiPikApcW73a/Jl/WjkEwEdkL/jLj0KCep58=" SubTotal="200.00" Moneda="MXN" TipoCambio="1" Total="603.20" TipoDeComprobante="I" MetodoPago="PUE" LugarExpedicion="06300" xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><cfdi:Emisor Rfc="LAN8507268IA" Nombre="MB IDEAS DIGITALES SC" RegimenFiscal="601" /><cfdi:Receptor Rfc="AAA010101AAA" Nombre="SW SMARTERWEB" UsoCFDI="G03" /><cfdi:Conceptos><cfdi:Concepto ClaveProdServ="50211503" NoIdentificacion="UT421511" Cantidad="1" ClaveUnidad="H87" Unidad="Pieza" Descripcion="Cigarros" ValorUnitario="200.00" Importe="200.00"><cfdi:Impuestos><cfdi:Traslados><cfdi:Traslado Base="200.00" Impuesto="002" TipoFactor="Tasa" TasaOCuota="0.160000" Importe="32.00" /><cfdi:Traslado Base="232.00" Impuesto="003" TipoFactor="Tasa" TasaOCuota="1.600000" Importe="371.20" /></cfdi:Traslados></cfdi:Impuestos></cfdi:Concepto></cfdi:Conceptos><cfdi:Impuestos TotalImpuestosTrasladados="403.20"><cfdi:Traslados><cfdi:Traslado Impuesto="002" TipoFactor="Tasa" TasaOCuota="0.160000" Importe="32.00" /><cfdi:Traslado Impuesto="003" TipoFactor="Tasa" TasaOCuota="1.600000" Importe="371.20" /></cfdi:Traslados></cfdi:Impuestos></cfdi:Comprobante>';
      $xml -> loadXML($contenido);
      $xml->formatOutput = true;
      $el_xml = $xml->saveXML();
      $xml->save('clientes.xml');
      
      //Mostramos el XML puro
      echo "<p><b>El XML ha sido creado.... Mostrando en texto plano:</b></p>".
           htmlentities($el_xml)."
<hr>";
  #}
  
  //Para leerlo
  function leer(){
    echo "<p><b>Ahora mostrandolo con estilo</b></p>";
  
    $xml = simplexml_load_file('libros.xml');
    $salida ="";
  
    foreach($xml->libro as $item){
      $salida .=
        "<b>Autor:</b> " . $item->autor . "
".
        "<b>Título:</b> " . $item->titulo . "
".
        "<b>Ano:</b> " . $item->anio . "
".
        "<b>Editorial:</b> " . $item->editorial . "
<hr/>";
    }
  
    echo $salida;
  }
?>