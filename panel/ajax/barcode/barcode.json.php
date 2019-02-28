<?php
include("../../admin/classes/Categoria.class.php");
include("../../admin/classes/Producto.class.php");
include("../../admin/classes/Barcode.class.php");
$prod = new Producto("ajax/barcode/barcode.json.php");
$bc = new Barcode("ajax/barcode/barcode.json.php");
$cat = new Categoria("ajax/barcode/barcode.json.php");
$todo = $cat->getCategoriasFull();
function familia($t, $id){
    $texto = "";
    $nav = $id;
    $fam = array();
    while(!empty($nav)){
        $fam[] = $t[$nav]['nombre'];
        $nav = $t[$nav]['enlace'];
    }
    $cuenta = 0;
    $fam = array_reverse($fam);
    foreach($fam as $val){
        if($texto != "")
            $texto .= "<br>";
        for($i=0; $i<$cuenta; $i++)
            $texto .= "&nbsp;";
        $cuenta+=3;
        $texto .= $val;
    }
    return $texto;
}
header('Content-Type: application/json; charset=utf-8');
//$_POST['barcode'] = 12345679;
//$res['post'] = $_POST;
$idProducto = $bc->existeBarcode($_POST['barcode']);
$res['existe'] = false;
if(!empty($idProducto)){
    $res['existe'] = true;
    $res['id'] = $idProducto;
    $res['producto'] = $prod->getProducto($idProducto);
    $res['producto']['linaje'] = familia($todo, $res['producto']['idCategoria']);
    $res['precioReal'] = $prod->damePrecioReal($idProducto);
}

echo json_encode($res);