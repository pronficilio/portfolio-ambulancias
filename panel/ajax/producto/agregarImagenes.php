<?php
require("../../admin/classes/FileUpload.class.php");
require("../../admin/classes/Archivo.class.php");
$arch = new Archivo("producto/agregarProducto.php");
$upload = new FileUpload($_FILES);

if(!empty($_FILES['new_producto_imagen']['name'][0])){
    $upload->setValidTypes(array('jpg','jpeg','png','gif'));
    if($upload->saveTo("../../prod_img", "new_producto_imagen") != 1){
        echo "Ocurrió un error al intentar guardar los siguientes archivos:";
        $datos = $upload->getErrorFilesInfo();
        echo "<ul>";
        foreach($datos as $v)
            echo "<li>".$v."</li>";
        echo "</ul>";
    }
    $datos = $upload->getUploadedFilesInfo();
    if(count($datos)>0){
        foreach($datos as $v){
            if($arch->agregaImagen($v['name'], $_POST['editImagenIdProducto']))
                $upload->compress("../../prod_img/".$v['name'], "../../prod_img/less/".$v['name']);
        }
    }
}