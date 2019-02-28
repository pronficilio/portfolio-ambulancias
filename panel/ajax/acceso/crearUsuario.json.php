<?php
session_start();
include("../../admin/classes/Acceso.class.php");
$usu = new Acceso("ajax/crearUsuario.php");
header('Content-Type: application/json; charset=utf-8');
$res['error'] = true;
if($usu->existeUsuario($_POST['name']) == 0){
    $pass = substr(md5($_POST['name']), 3, 9);
    if($usu->agregaUsuarios($_POST['name'], $_POST['nombre'], $_POST['email'], $pass, $_POST['permisos'])){
        $usu->emailNuevoUsuario($_POST['name'], $_POST['nombre'], $pass, $_POST['email']);
        $res['error'] = false;
    }else{
        $res['msg'] = "Error: No se ha podido agregar el usuario, inténtalo más tarde";
    }
}else{
    $res['msg'] = "Error: Usuario en uso";
}
echo json_encode($res);