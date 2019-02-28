<?php
include("../../admin/classes/Proveedores.class.php");
$prov = new Proveedores("ajax/proveedores/eliminaCategoria.json.php");
header('Content-Type: application/json; charset=utf-8');
$res['error'] = true;
if($prov->eliminaCategoria($_POST['idCat'], $_POST['idProv'])){
    $res['error'] = false;
}else{
    $res['msg'] = "Error: No se pudo realizar la eliminación";
}
echo json_encode($res);