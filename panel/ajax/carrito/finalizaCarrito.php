<?php
session_start();
include("../../admin/classes/Carrito.class.php");
$carr = new Carrito("ajax/carrito/finalizaCarrito.php");
if(!$carr->finalizaCarrito($_POST['idCarrito'], $_SESSION['_idUser'])){
    echo "Error: ocurrió un error al intentar finalizar el carrito. Inténtelo de nuevo más tarde";
}