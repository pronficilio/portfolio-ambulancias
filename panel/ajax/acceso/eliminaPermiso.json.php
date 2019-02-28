<?php
session_start();
include("../../admin/classes/Acceso.class.php");
$acc = new Acceso("guardaPermiso.json..php");
header('Content-Type: application/json; charset=utf-8'); 
$res['error'] = false;
$idPermiso = $_POST['id'];
if($acc->eliminaTabs($idPermiso)){
    $acc->eliminaPermiso($idPermiso);
}else{
    $res['error'] = true;
    $res['msg'] = "Error: No se ha eliminado nada. Parámetros incorrectos (".$idPermiso.")";
}
echo json_encode($res);