<?php
require("../../admin/classes/FileUpload.class.php");
require("../../admin/classes/Archivo.class.php");
$arch = new Archivo("producto/agregarArchivos.php");
$upload = new FileUpload($_FILES);

if(!empty($_FILES['new_producto_archivo']['name'][0])){
    $upload->setValidTypes(array('pdf','doc','docx','xls','xlsx'));
    if($upload->saveTo("../../admin/archivos", "new_producto_archivo") != 1){
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
            $archivo = $arch->agregaArchivo($v['path'], $v['name']);
            !$arch->enlazaArchivo($_POST['editArchivoIdProducto'], $archivo);
        }
    }
}