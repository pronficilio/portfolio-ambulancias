<?php
// parametros (post): $nombre
require("../../admin/classes/Categoria.class.php");
$categoria1 = new Categoria("ajax/categoria/agregarCategoria.php");

$respuesta = $categoria1->agregaCategoria($_POST['nombre']);

if($respuesta === false){
    echo "Error: no se pudo agregar la categoria. Asegurate de no repetir el nombre de una categoría o subcategoría.";
}
?>