<?php
include("../../admin/classes/Proveedores.class.php");
$prov = new Proveedores("ajax/proveedores/asignaProveedor.json.php");
header('Content-Type: application/json; charset=utf-8');
$res['error'] = true;
if(empty($_POST['idProv'])){
    $res['msg'] = "Error: Faltan parámetros";
}else{
    if($prov->enviarAccesoEmbebido($_POST['idProv'])){
        $res['error'] = false;
    }else{
        $res['msg'] = "Error: No se pudo enviar la información";
    }
}
echo json_encode($res);