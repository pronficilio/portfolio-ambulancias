<?php
require("../../admin/classes/Producto.class.php");
$prod = new Producto("ajax/producto/autocomplete.php");
$dat = $prod->busca($_GET['q']);
$res = $dat;
echo json_encode($res);
?>