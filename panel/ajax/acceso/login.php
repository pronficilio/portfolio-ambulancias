<?php
error_reporting(-1);
session_start();
include_once("../../admin/classes/Acceso.class.php");
$user = new Acceso("ajax/acceso/login.php");

//if($_POST['secretismo'] == md5(md5(date("Y-m-d"))))
   $usuario = $user->verificaUsuario($_POST['usuario'], $_POST['pass']);

if(!empty($usuario)){
    $_SESSION['_idUser'] = $usuario['idUsuarios'];
    $_SESSION['_permisos'] = $usuario['permisos'];
    $_SESSION['_user'] = $_POST['usuario'];
    $_SESSION['_sess'] = $user->inicioSesion($usuario['idUsuarios']);
    $user->aquiSigo($_SESSION['_sess']);
    $_SESSION['_sessTiempo'] = time();
}else{
    echo "Error: credenciales incorrectas";
}
?>