<?php
include 'fredyNav.php';
use Shuchkin\SimpleXLSX;

ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

require_once __DIR__.'/../ReadXLSX/src/SimpleXLSX.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIC | SUBIR EXCEL</title>
</head>
<body>
    <div class="container">
        <?php
        echo '<ul class="collection">
                  <li class="collection-item indigo"><h4><b class="white-text">Importar de XLSX a MYSQL Tabla Articulos</b></h4></li>
              </ul>';

        if (isset($_FILES['file'])) {
            if ($xlsx = SimpleXLSX::parse($_FILES['file']['tmp_name'])) {
                echo '<h3>Resultados de la Inserción: </h3>';
                echo '<table >';

                $dim = $xlsx->dimension();
                $cols = $dim[0];
                foreach ($xlsx->readRows() as $k => $r) {
                    //      if ($k == 0) continue; // skip first row
                    $codigo = ( isset($r[ 0 ]) ? $r[ 0 ] : '&nbsp;' );
                    $nombre = ( isset($r[ 2 ]) ? $r[ 2 ] : '&nbsp;' );
                    $precio = ( isset($r[ 3 ]) ? $r[ 3 ] : '&nbsp;' );
                    $unidad = ( isset($r[ 5 ]) ? $r[ 5 ] : '&nbsp;' );
                    $CFiscal = ( isset($r[ 7 ]) ? $r[ 7 ] : '&nbsp;' );
                    $CUnidad = ( isset($r[ 8 ]) ? $r[ 8 ] : '&nbsp;' );

                    echo '<tr>';
                    
                        echo '<td>' . $codigo . '</td>';
                        echo '<td>' . $nombre . '</td>';
                        echo '<td>' . $precio . '</td>';
                        echo '<td>' . $unidad . '</td>';
                        echo '<td>' . $CFiscal . '</td>';
                        echo '<td>' . $CUnidad . '</td>';
                        if ($k == 0) {
                            echo '<td>ESTATUS</td>';
                        }else{
                            //VERIFICAMOS QUE NO HALLA UN ARTICULO CON LOS MISMOS DATOS
                            if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `punto_venta_articulos` WHERE codigo='$codigo'"))>0){
                                echo '<td><b class="red-text">¡Repetido! (NO)</b></td>';                             
                            }else{
                                // SI NO HAY NUNGUNO IGUAL CREAMOS LA SENTECIA SQL  CON LA INFORMACION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
                                $Fecha_hoy = date('Y-m-d');// FECHA ACTUAL
                                //REALIZAMOS LA INSERCION A LA BD
                                $sql = "INSERT INTO `punto_venta_articulos` (codigo, nombre, descripcion, precio, unidad, codigo_fiscal, codigo_unidad, modelo, categoria, usuario, fecha)  VALUES('$codigo', '$nombre', '$nombre', '$precio', '$unidad', '$CFiscal', '$CUnidad', 'Por Definir', 10, 49,'$Fecha_hoy')";
                                //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
                                if(mysqli_query($conn, $sql)){
                                    echo '<td><b class="green-text">¡Exito! (SI)</b></td>';                                
                                }else{
                                    echo '<td><b class="red-text">¡Error! (NO)</b></td>';                                
                                }//FIN else DE ERROR   
                            }//FIN ELSE                                                 
                        }
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo SimpleXLSX::parseError();
            }// FIN ELSE
        }// FIN IF FILE
        ?>
        <h3>Formato en el que se debe subir el archivo.xlsx</h3>        
        <h5>Debe asegurarse que el archivo tenga la información en las columnas como en la imagen <br> (Columna A = codigo, Columna C = Descripcion, Columna D = Precio, etc.)</h5>  
        <img src="../img/formato.jpg" />  <br><br>

        <h3>BUSCAR UN ARCHIVO:</h3>       
        <form method="post" enctype="multipart/form-data">
        *.XLSX <input type="file" name="file"  />&nbsp;&nbsp;<input type="submit" value="Subir" />
        </form><br>    
    </div>
</body>
</html>

