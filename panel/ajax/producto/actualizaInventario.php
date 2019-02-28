<?php
include("../../admin/classes/Producto.class.php");
$prod = new Producto("ajax/producto/eliminaProducto.php");
$prod->actualizaInventario($_POST['idP'], $_POST['cnt']);
