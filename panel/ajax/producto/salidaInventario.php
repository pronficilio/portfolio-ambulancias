<?php
session_start();
require("../../admin/classes/Producto.class.php");
$prod = new Producto("ajax/producto/getProductos.json.php");
$aux = $prod->salidaInventario($_POST['id'], $_POST['cantidad'], $_POST['nota'], $_SESSION['_idUser']);