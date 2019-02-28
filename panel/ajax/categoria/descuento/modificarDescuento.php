<?php
// parametros (post): $id, $nombre
require("../../../admin/classes/Categoria.class.php");
$categoria1 = new Categoria("ajax/categoria/descuento/modificarColor.php");

$respuesta = $categoria1->modificarDescuento($_POST['id'], $_POST['nombre'], $_POST['valor']);

if($respuesta === false){
    echo "Error: el descuento debe ser un número mayor que cero y menor o igual que 100";
}
?>