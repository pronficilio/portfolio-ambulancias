<?php
session_start();
require("../../admin/classes/Carrito.class.php");
$carr = new Carrito("ajax/carrito/cierraCarrito.php");
$carr->cierraCarrito($_POST['idCarr'], $_POST['decision'], $_SESSION['_idUser']);