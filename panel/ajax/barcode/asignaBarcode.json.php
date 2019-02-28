<?php
include("../../admin/classes/Barcode.class.php");
$bc = new Barcode("ajax/barcode/barcode.json.php");
header('Content-Type: application/json; charset=utf-8');
$res['error'] = true;
$res['msg'] = "";
$respuesta = $bc->asignaBarcode($_POST['id'], $_POST['barcode']);
if($respuesta == 1){
    $res['error'] = false;
}
if($respuesta == 0){
    $res['msg'] = "No se realizó ningun cambio";
    $res['error'] = false;
}
if($respuesta == 2){
    $res['msg'] = "El código de barras ya estaba asignado al producto";
    $res['error'] = false;
}
if($respuesta == -1){
    $res['msg'] = "Error: el código de barras ya está asignado a otro producto";
}
echo json_encode($res);