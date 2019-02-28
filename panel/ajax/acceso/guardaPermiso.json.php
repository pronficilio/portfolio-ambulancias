<?php
session_start();
include("../../admin/classes/Acceso.class.php");
$acc = new Acceso("guardaPermiso.json..php");
header('Content-Type: application/json; charset=utf-8'); 
$res['error'] = false;
$idPermiso = $_POST['id'];
if($acc->eliminaTabs($idPermiso)){
    $acc->agregaTab("home", $idPermiso);
    $visit = array();
    foreach($_POST['permisos'] as $val){
        $val = str_replace("ni_", "", $val);
        if(!isset($visit[$val])){
            $visit[$val] = 1;
            $acc->agregaTab($val, $idPermiso);
        }
    }
}else{
    $res['error'] = true;
    $res['msg'] = "Error: No se han podido guardar los cambios";
}
echo json_encode($res);