<?php
session_start();
require("../../admin/classes/Carrito.class.php");
$carr = new Carrito("ajax/carrito/creaCarrito.php");

$carrito = $carr->creaCarrito($_POST['cliente']);

if(!empty($carrito)){
    foreach($_POST['prod'] as $ind=>$val){
        $carr->agregaCarrito($carrito, $val, $_POST['cant'][$ind]);
    }
    if($_POST['cerrado'] == "1"){
        $carr->cierraCarrito($carrito, 1, $_SESSION['_idUser']);
    }
}