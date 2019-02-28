<?php
include("../../admin/classes/Proveedores.class.php");
$prov = new Proveedores("ajax/proveedores/editarProveedor.json.php");
header('Content-Type: application/json; charset=utf-8');
$res['error'] = true;
$posibleId = $prov->existeEmail($_POST['email']);
if($posibleId != 0 && $posibleId != $_POST['id']){
    $res['msg'] = "Error: El email ya pertenece a un proveedor";
}else{
    if($prov->editaProveedor($_POST['id'], $_POST['nombre'], $_POST['email'], $_POST['tel'])){
        $res['error'] = false;
    }else{
        $res['msg'] = "Error: No se pudieron validar los datos";
    }
}
echo json_encode($res);