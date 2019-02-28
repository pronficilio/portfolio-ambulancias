<?php
include("../../admin/classes/Proveedores.class.php");
$prov = new Proveedores("ajax/proveedores/asignaProveedor.json.php");
header('Content-Type: application/json; charset=utf-8');
$res['error'] = true;
if($prov->asignaProveedor($_POST['idCat'], $_POST['idProv'])){
    $res['error'] = false;
}else{
    $res['msg'] = "Error: No se pudieron validar los datos";
}
echo json_encode($res);