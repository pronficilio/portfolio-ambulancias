<?php
include("../../admin/classes/Producto.class.php");
$prod = new Producto("ajax/producto/eliminaProducto.php");
$prod->eliminaProducto($_POST['idProducto']);
?>