<?php
include("../../admin/classes/Carrito.class.php");
$carr = new Carrito("ajax/carrito/borraCarrito.php");

$carr->eliminaCarrito($_POST['id']);