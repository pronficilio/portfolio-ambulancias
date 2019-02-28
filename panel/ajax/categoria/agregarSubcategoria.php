<?php
// parametros (post): $nombre, $enlace
require("../../admin/classes/Categoria.class.php");
$categoria1 = new Categoria("ajax/categoria/agregarSubcategoria.php");

$respuesta = $categoria1->agregaCategoria($_POST['nombre'], $_POST['enlace']);

if($respuesta === false){
    echo "Error: no se pudo agregar la subcategoria. Asegurate de no repetir el nombre de una categoría o subcategoría.";
}
?>