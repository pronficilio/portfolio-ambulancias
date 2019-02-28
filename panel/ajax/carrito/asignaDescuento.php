<?php
include("../../admin/classes/Carrito.class.php");
$carr = new Carrito("ajax/carrito/asignaDescuento.php");
if($carr->carritoAbierto($_POST['id'])){
	$carr->descuentazo($_POST['id'], $_POST['idD']);
}else{
	echo "El carrito no está abierto";
}
