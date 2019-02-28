<?php
session_start();
include("../../admin/classes/Acceso.class.php");
$acc = new Acceso("cambiarPermiso.json.php");
header('Content-Type: application/json; charset=utf-8');
if($acc->cambiarPermiso($_POST['perm'], $_POST['id'])){
    $res['error'] = false;
}else{
    $res['error'] = true;
    $res['msg'] = "Error: No se ha podido realizar los cambios de permiso [".$_POST['id']."]";
}
echo json_encode($res);