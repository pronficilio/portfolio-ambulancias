<?php
require("../../admin/classes/Producto.class.php");
require("../../admin/classes/Categoria.class.php");
$producto = new Producto("producto/modificarProducto.php");
$cate = new Categoria("producto/modificarProducto.php");

$cat = -1;
foreach($_POST['editProdCategoria'] as $val){
    if(!empty($val))
       $cat = $val;
    else{
       break;
    }
}

$res = $producto->editarProducto($_POST['editIdProducto'], $_POST['editProdNombre'], $cat, $_POST['editProdDescripcion'],
                                 $_POST['editProdPrecio'], $_POST['editProdInventario'], $_POST['editProdInventario_max'],
                                 $_POST['editProdInventario_min'], $_POST['editProdDisponible']);
if($res === false)
    echo "Error: No se pudo guardar el producto";
else
    $producto->editaIva($_POST['editIdProducto'], $_POST['editProdIVA']);