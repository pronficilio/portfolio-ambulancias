<?php
include("../../admin/classes/Producto.class.php");
include("../../admin/classes/Archivo.class.php");
include("../../admin/classes/Categoria.class.php");
$prod = new Producto("ajax/configuracion/destacadoTabla.php");
$arch = new Archivo("ajax/configuracion/destacadoTabla.php");
$cat = new Categoria("ajax/configuracion/destacadoTabla.php");

$datos = $prod->dameDestacados();

foreach($datos as $val){
    echo "<tr>";
    $img = $arch->getImagenesByIdProducto($val['idProducto'], 1);
    if(count($img)){
        $img = "prod_img/less/".$img[0]['url'];
    }else{
        $img = "web/images/no_photo.jpg";
    }
    $lina = $cat->getLinaje($val['idCategoria']);
    $lina[] = $val['idCategoria'];
    echo "<td><img src='$img' class='img' height='150px'></td>";
    echo "<td>".$val['nombre']."</td>";
    echo "<td>".$cat->getNombreCategoria($lina[0])."</td>";
    echo "<td>".$cat->getNombreCategoria($lina[1])."</td>";
    echo "</tr>";
}