<?php
session_start();
include("../../admin/classes/Acceso.class.php");
$acc = new Acceso("restablecerPass.json.php");
header('Content-Type: application/json; charset=utf-8');
$posibleId = $_POST['id'];
$idUsuario = $acc->tokenValido($_POST['tk']);
$res['error'] = true;
if($posibleId==$idUsuario){
    if($acc->modificaContra($idUsuario, $_POST['pass'])){
        $acc->limpiaToken($idUsuario);
        $res['error'] = false;
    }else{
        $res['msg'] = "Error: No se ha podido modificar la contraseña del usuario";
    }
}else{
    $res['msg'] = "Error: Token incorrecto";
}
echo json_encode($res);