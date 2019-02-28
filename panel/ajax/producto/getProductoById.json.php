<?php
// parametros: idProducto
require("../../admin/classes/Producto.class.php");
require("../../admin/classes/Categoria.class.php");
require("../../admin/classes/Archivo.class.php");
$prod = new Producto("ajax/producto/getProductos.json.php");
$cat = new Categoria("ajax/producto/getProductos.json.php");
$arch = new Archivo("ajax/producto/getProductos.json.php");

$aux = $prod->getProducto($_POST['idProducto']);
$aux['linaje'] = $cat->getLinaje($aux['idCategoria']);
$aux['archivo'] = $arch->getArchivosByIdProducto($_POST['idProducto']);
$aux['imagen'] = $arch->getImagenesByIdProducto($_POST['idProducto']);
header('Content-Type: application/json; charset=utf-8'); 
echo json_encode($aux);
?>