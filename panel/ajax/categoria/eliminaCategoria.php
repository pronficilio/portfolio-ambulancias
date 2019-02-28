<?php
// parametros: idCategoria
require("../../admin/classes/Categoria.class.php");
$cat = new Categoria("ajax/categoria/getCategorias.php");
$cat->eliminaCategoria($_POST['idCategoria']);