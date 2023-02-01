
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIC | SUBIR EXCEL</title>
    <?php
    include 'fredyNav.php';
    use Shuchkin\SimpleXLSX;

    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', true);

    require_once __DIR__.'/../ReadXLSX/src/SimpleXLSX.php';

    //VERIFICAMOS QUE SI NOS ENVIE POR POST EL ID DEL ARTICULO
    if (isset($_GET ['id']) == false) {
      ?>
      <script>    
        M.toast({html: "Regresando a Almacen.", classes: "rounded"});
        setTimeout("location.href='almacen_punto_venta.php'", 800);
      </script>
      <?php
    }else{
        $Almacen = $_GET['id'];
    }
    ?>
</head>
<body>
    <div class="container">
        <?php
        echo '<ul class="collection">
                  <li class="collection-item indigo"><h4><b class="white-text">Importar de XLSX a MYSQL Existencia de Almacen N° '.$Almacen.'</b></h4></li>
              </ul>';

        if (isset($_FILES['file'])) {
            if ($xlsx = SimpleXLSX::parse($_FILES['file']['tmp_name'])) {
                $Fecha_hoy = date('Y-m-d');// FECHA ACTUAL
                $id_user = $_SESSION['user_id'];// ID DEL USUARIO LOGEADO
                echo '<h3>Resultados de la Inserción: </h3>';
                echo '<table >';

                $dim = $xlsx->dimension();
                $cols = $dim[0];
                foreach ($xlsx->readRows() as $k => $r) {
                    //      if ($k == 0) continue; // skip first row
                    $codigo = ( isset($r[ 0 ]) ? $r[ 0 ] : '&nbsp;' );
                    $nombre = ( isset($r[ 2 ]) ? $r[ 2 ] : '&nbsp;' );
                    $existencia = ( isset($r[ 6 ]) ? $r[ 6 ] : '&nbsp;' );
                    $unidad = ( isset($r[ 5 ]) ? $r[ 5 ] : '&nbsp;' );

                    echo '<tr>';
                    
                        echo '<td>' . $codigo . '</td>';
                        echo '<td>' . $nombre . '</td>';
                        echo '<td>' . $existencia . '</td>';
                        echo '<td>' . $unidad . '</td>';
                        if ($k == 0) {
                            echo '<td>ESTATUS</td>';
                        }else{
                            $articulo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `punto_venta_articulos` WHERE codigo = '$codigo' OR nombre = '$nombre' LIMIT 1"));
                            $id_articulo = $articulo['id'];
                            if ($id_articulo > 0 AND $existencia >0) {                                
                                //VERIFICAMOS QUE NO HALLA UN ARTICULO CON LOS MISMOS DATOS
                                if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `punto_venta_almacen_general` WHERE id_articulo=$id_articulo AND id_almacen = $Almacen"))>0){
                                    if (mysqli_query($conn, "UPDATE `punto_venta_almacen_general` SET cantidad = cantidad+$existencia, modifico = $id_user, fecha_modifico = '$Fecha_hoy' WHERE id_articulo = '$id_articulo' AND id_almacen = '$Almacen'")) {
                                        echo '<td><b class="blue-text">Solo Suma</b></td>';                            
                                    }
                                }else{
                                    // SI NO HAY NUNGUNO IGUAL CREAMOS LA SENTECIA SQL  CON LA INFORMACION REQUERIDA Y LA ASIGNAMOS A UNA VARIABLE
                                    $Fecha_hoy = date('Y-m-d');// FECHA ACTUAL
                                    //REALIZAMOS LA INSERCION A LA BD
                                    $sql = "INSERT INTO `punto_venta_almacen_general` (id_articulo, cantidad, id_almacen, modifico, fecha_modifico)  VALUES('$id_articulo', '$existencia', '$Almacen', $id_user,'$Fecha_hoy')";
                                    //VERIFICAMOS QUE LA SENTECIA FUE EJECUTADA CON EXITO!
                                    if(mysqli_query($conn, $sql)){
                                        echo '<td><b class="green-text">¡Registro! (SI)</b></td>';                                
                                    }else{
                                        echo '<td><b class="red-text">¡Error! (NO)</b></td>';                                
                                    }//FIN else DE ERROR   
                                }//FIN ELSE       
                            }// FIN IF REGISTRA
                            else{
                                echo '<td><b class="red-text">¡Cero! (NO)</b></td>';                               
                            }                                          
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
        <h5>Debe asegurarse que el archivo tenga la información en las columnas como en la imagen <br> (Columna A = codigo, Columna G = Existencia,etc.)</h5>  
        <img src="../img/formato.jpg" />  <br><br>

        <h3>BUSCAR UN ARCHIVO:</h3>       
        <form method="post" enctype="multipart/form-data">
        *.XLSX <input type="file" name="file"  />&nbsp;&nbsp;<input type="submit" value="Subir" />
        </form><br>    
    </div>
</body>
</html>

