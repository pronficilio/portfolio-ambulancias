<?php
session_start();
require("../../admin/classes/Proveedores.class.php");
header('Content-Type: application/json; charset=utf-8');
$prov = new Proveedores("ajax/proveedores/dameProveedores.json.php");
$proveedor = $prov->verificaToken($_POST['tk']);
$res = array();
$res['post'] = $_POST;
if(!empty($proveedor)){
    if($_POST['feIni'] <= $_POST['feFin']){
        $res['error'] = false;
        $res['data'] = $prov->dameProductosVendidos($proveedor['idProveedor'], $_POST['feIni'], $_POST['feFin']);
        $res['post'] = $_POST;
    }else{
        $res['error'] = true;
        $res['msg'] = "La fecha de inicio debe ser menor o igual a la fecha de fin";
    }
}else{
    $res['error'] = true;
    $res['msg'] = "Token inválido";
}
echo json_encode($res);