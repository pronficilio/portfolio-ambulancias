<?php
require("../../../admin/classes/Categoria.class.php");
$cate = new Categoria("categoria/descuento/agregarDescuento.php");
if($cate->agregaDescuento($_POST['nombre'], $_POST['valor']) === false){
    echo "Error: el descuento debe ser un número mayor que cero y menor o igual que 100";
}