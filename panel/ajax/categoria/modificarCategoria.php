<?php
// parametros (post): $id, $nombre
require("../../admin/classes/Categoria.class.php");
$categoria1 = new Categoria("ajax/categoria/modificarCategoria.php");

$respuesta = $categoria1->modificarCategoria($_POST['id'], $_POST['nombre']);

if($respuesta === false){
    echo "Error: no se pudo modificar la categoria";
}
?>