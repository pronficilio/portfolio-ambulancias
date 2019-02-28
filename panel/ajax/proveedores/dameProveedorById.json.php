<?php
session_start();
require("../../admin/classes/Proveedores.class.php");

$prov = new Proveedores("ajax/proveedores/dameProveedores.json.php");

$res['data'] = $prov->dameProveedor($_POST['idProv']);

header('Content-Type: application/json; charset=utf-8');

if(empty($res['data'])){
    $res['error'] = true;
    $res['msg'] = "Error: no se ha podido verificar el proveedor (".$_POST['idProv'].")";
}else{
    $res['error'] = false;
}
echo json_encode($res);