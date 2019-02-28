<?php
include("../../admin/classes/Carrito.class.php");
$carr = new Carrito("ajax/carrito/actualizaCarrito.php");

$res = $carr->actualizaCantidad($_POST['id'], $_POST['cantidad']);
if($res <= -1)
	echo "Error: no hay suficiente producto en inventario, faltan ".($res*-1)." productos";
if($res === false)
    echo "Error: no se pudo actualizar la cantidad de productos del carrito. ¿El carrito está abierto?";
