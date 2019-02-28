<?php
include("../../admin/classes/Barcode.class.php");
$bc = new Barcode("ajax/barcode/barcode.json.php");
header('Content-Type: application/json; charset=utf-8');
$res['error'] = true;
$res['msg'] = "";
$respuesta = $bc->eliminaBarcode($_POST['id']);
if($respuesta == 1){
    $res['error'] = false;
}
if($respuesta == 0){
    $res['msg'] = "Producto sin código de barras";
    $res['error'] = false;
}
if($respuesta == -1){
    $res['msg'] = "Error: no se encontró el producto (".$_POST['id'].")";
}
echo json_encode($res);