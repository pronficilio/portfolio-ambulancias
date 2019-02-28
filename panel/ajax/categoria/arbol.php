<?php
// parametros: ninguno
session_start();

require("../../admin/classes/Categoria.class.php");
$cat = new Categoria("ajax/categoria/getCategorias.php");
$cate = $cat->getCategorias();

function imprimeFamilia(&$catt, &$categorias){
    echo "<ul>";
    foreach($categorias as $val){
        echo "<li>";
        echo "<a href='#'>".$val['nombre']."</a>";
        $hijos = $catt->getHijos($val['idCategoria']);
        if(count($hijos)){
            imprimeFamilia($catt, $hijos);
        }   
        echo "</li>";
    }
    echo "</ul>";
}
//echo "<ul><li><a href='#'>Categorias:</a>";
foreach($cate as $val){
    echo "<div class='tree clearfix'>";
    echo "<ul>";
    echo "<li>";
    echo "<a href='#'>".$val['nombre']."</a>";
    $hijos = $cat->getHijos($val['idCategoria']);
    if(count($hijos))
        imprimeFamilia($cat, $hijos);
    echo "</li>";
    echo "</ul>";
    echo "</div>";
    echo "<hr>";
}

//echo "</li></ul>";