<?php
include("../../admin/classes/Producto.class.php");
$prod = new Producto("ajax/producto/modificaPrecioOutlet.php");
$prod->modificaOutlet($_POST['idProducto'], $_POST['precio']);
?>