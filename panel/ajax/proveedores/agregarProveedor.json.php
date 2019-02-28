<?php
include("../../admin/classes/Proveedores.class.php");
$prov = new Proveedores("ajax/proveedores/agregarProveedor.json.php");
header('Content-Type: application/json; charset=utf-8');
$res['error'] = true;
if($prov->existeEmail($_POST['email']) != 0){
    $res['msg'] = "Error: El email ya pertenece a un proveedor";
}else{
    if($prov->agregarProveedor($_POST['nombre'], $_POST['email'], $_POST['tel'])){
        $res['error'] = false;
    }else{
        $res['msg'] = "Error: No se pudieron validar los datos";
    }
}
echo json_encode($res);