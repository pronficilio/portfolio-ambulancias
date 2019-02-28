<?php
session_start();
include_once("../../admin/classes/Acceso.class.php");
$user = new Acceso("ajax/acceso/login.php");

if(!empty($_SESSION['_idUser'])){
    if(time() < $_SESSION['_sessTiempo'] + (60*7)){
        $user->aquiSigo($_SESSION['_sess']);
    }else{
        $_SESSION['_sess'] = $user->inicioSesion($_SESSION['_idUser']);
    }
    $_SESSION['_sessTiempo'] = time();
}else{
    echo ":(";
}