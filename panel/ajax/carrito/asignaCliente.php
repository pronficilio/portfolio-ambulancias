<?php
include("../../admin/classes/Carrito.class.php");
$carr = new Carrito("ajax/carrito/asignaCliente.php");
$carr->asignaClienteCarrito($_POST['idCliente'], $_POST['id']);
