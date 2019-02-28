<?php
session_start();
include("../../admin/classes/Acceso.class.php");
$acc = new Acceso("eliminaUsuario.json..php");
header('Content-Type: application/json; charset=utf-8'); 
$idUsuario = $_POST['id'];
if($acc->eliminaUsuario($idUsuario)){
    $res['error'] = false;
}else{
    $res['error'] = true;
    $res['msg'] = "Error: No se ha eliminado nada. Parámetros incorrectos (".$idUsuario.")";
}
echo json_encode($res);