<?php
session_start();
include("../../admin/classes/Achat.class.php");
$chat = new Achat("achat/ajax/iniciaChat.php");

if(empty($_SESSION['chatActivo'])){
    $_SESSION['chatActivo'] = $chat->creaChat($_POST['nombre'], $_POST['email'], $_POST['pregunta']);
    if(empty($_SESSION['chatActivo'])){
        echo "Error: el email que ingresaste no es válido";
    }else{
        $_SESSION['inicioChat'] = date("Y-m-d H:i");
    }
}
