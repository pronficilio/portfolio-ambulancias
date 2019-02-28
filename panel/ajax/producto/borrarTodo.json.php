<?php
include("../../admin/classes/Producto.class.php");
$prod = new Producto("ajax/producto/borraTodo.json.php");
$prod->eliminaTodo();
