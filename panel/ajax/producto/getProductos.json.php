<?php
// parametros: ninguno
session_start();
require("../../admin/classes/Producto.class.php");
require("../../admin/classes/Archivo.class.php");

include("../../admin/classes/Acceso.class.php");
$acc = new Acceso("ajax/producto/getProductos.json.php");
$arrPerm = $acc->dameMisPermisos($_SESSION['_idUser']);

$prod = new Producto("ajax/producto/getProductos.json.php");
$arch = new Archivo("ajax/producto/getProductos.json.php");
$aux = $prod->getProductoJson($_GET);

header('Content-Type: application/json; charset=utf-8'); 
?>
{
    "draw" : "<?=$aux['draw'];?>",
    "recordsTotal" : "<?=$aux['recordsTotal'];?>",
    "recordsFiltered" : "<?=$aux['recordsFiltered'];?>",
    "data" : [
        <?php
        $contador = 1;
        $total = array();
        foreach($aux['data'] as $fila){
            $epa = '[
            "'.$fila['idProducto'].'",
            ';
            $epa .= "\"".$fila['nombre'];
            $epa .= "<p class='text-muted'>";
            $epa .= "<span class='_tool' data-toggle='tooltip' title='Archivos enlazados al producto'><span class='glyphicon glyphicon-file'></span> <span class='sr-only'><br>Archivos:</span> <span>".$arch->getCountArchivos($fila['idProducto'])."</span></span>  ";
            $epa .= "<span class='sr-only'><br>Imágenes:</span> <span class='_tool' data-toggle='tooltip' title='Imagenes visibles del producto'><span class='glyphicon glyphicon-camera'></span> <span>".$arch->getCountImagenes($fila['idProducto'])."</span></span>";
            $epa .= "</p>";
            $epa .= "\",
            ";
            $epa .= "\"".$fila['cat']."\",
            ";
            $epa .= "\"$".number_format($fila['precio'], 2)."\",
            ";
            $precio = $fila['precioOutlet'];
            if(empty($precio))
                $precio = "N/A";
            else
                $precio = "$".number_format($precio, 2);
            $epa .= "\"".$precio."\",
            ";
            $epa .= "\"".$fila['enInventario']."\",
            ";
            $epa .= "\"<div class='text-center'><div class='btn-group' role='group' data-idProducto='".$fila['idProducto']."' data-precio='".$fila['precio']."'>";
            if(in_array(".editaProducto", $arrPerm)){
                $epa .= "<button type='button' class='btn btn-default editaProducto _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"500\\\"}' title='Haz clic para editar el producto'>";
                $epa .= "<span class='glyphicon glyphicon-pencil'></span>";
                $epa .= "</button>";
            }
            if(in_array(".editaInventario", $arrPerm)){
                $epa .= "<button type='button' class='btn btn-default editaInventario _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"500\\\"}' title='Editar los datos del inventario'>";
                $epa .= "<span class='glyphicon glyphicon-list-alt'></span>";
                $epa .= "</button>";
            }
            if(in_array(".editaOutlet", $arrPerm)){
                $epa .= "<button type='button' class='btn btn-default editaOutlet _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"500\\\"}' title='Clic para outlet'>";
                $epa .= "<span class='glyphicon glyphicon-tag'></span>";
                $epa .= "</button>";
            }
            if(in_array(".agregaImagenProducto", $arrPerm)){
                $epa .= "<button type='button' class='btn btn-default agregaImagenProducto _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"500\\\"}' title='Haz clic para editar imagenes del producto'>";
                $epa .= "<span class='glyphicon glyphicon-camera'></span>";
                $epa .= "</button>";
            }
            if(in_array(".agregaArchivoProducto", $arrPerm)){
                $epa .= "<button type='button' class='btn btn-default agregaArchivoProducto _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"500\\\"}' title='Haz clic para editar los archivos del producto'>";
                $epa .= "<span class='glyphicon glyphicon-file'></span>";
                $epa .= "</button>";
            }
            if(in_array(".eliminaProducto", $arrPerm)){
                $epa .= "<button type='button' class='btn btn-default eliminaProducto _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"500\\\"}' title='Haz clic para eliminar el producto'>";
                $epa .= "<span class='glyphicon glyphicon-trash'></span>";
                $epa .= "</button>";
            }
            $epa .= "</div></div>\"
        ]";
            $total[] = $epa;
        }
        echo implode(", ", $total);
        ?>
        
    ]
}