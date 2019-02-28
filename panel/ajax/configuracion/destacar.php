<?php
include("../../admin/classes/Producto.class.php");
$prod = new Producto("ajax/configuracion/destacar.php");
$prod->destaca($_POST['id']);