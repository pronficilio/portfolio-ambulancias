<?php
require("../../admin/classes/FileUpload.class.php");
require("../../admin/classes/Producto.class.php");
require("../../admin/classes/Archivo.class.php");
require("../../admin/classes/Categoria.class.php");
$producto = new Producto("producto/agregarProducto.php");
$arch = new Archivo("producto/agregarProducto.php");
$cate = new Categoria("producto/agregarProducto.php");

$cat = -1;
foreach($_POST['new_producto_subcategoria'] as $val){
    if(!empty($val))
       $cat = $val;
    else{
       break;
    }
}

$res = $producto->agregarProducto($_POST['new_producto_nombre'], $cat, $_POST['new_producto_descripcion'],
                                  $_POST['new_producto_precio'], $_POST['new_producto_inv'], $_POST['new_producto_inventario_max'],
                                  $_POST['new_producto_inventario_min'], $_POST['new_producto_disponible']);
if($res === false){
    echo "Error: No se pudo guardar el producto";
}else{
    $producto->editaIva($res, $_POST['new_producto_iva']);
    $upload = new FileUpload($_FILES);
    $error = false;
    if(!empty($_FILES['new_producto_pdf']['name'][0])){
        $upload->setValidTypes(array('pdf','doc','docx','xls','xlsx'));
        if($upload->saveTo("../../admin/archivos", "new_producto_pdf") != 1){
            echo "Se ha agregado el producto, pero ocurrió un error al intentar guardar los siguientes archivos:";
            $datos = $upload->getErrorFilesInfo();
            echo "<ul>";
            foreach($datos as $v)
                echo "<li>".$v."</li>";
            echo "</ul>";
        }else{
            $datos = $upload->getUploadedFilesInfo();
            $errors = array();
            foreach($datos as $v){
                $archivo = $arch->agregaArchivo($v['path'], $v['name']);
                if(!$arch->enlazaArchivo($res, $archivo)){
                    $errors[] = $v['name'];
                }
            }
            if(!empty($errors)){
                echo "Se ha agregado el producto, pero ocurrió un error al intentar enlazar los siguientes archivos:";
                echo "<ul>";
                foreach($errors as $v)
                    echo "<li>".$v."</li>";
                echo "</ul>";
            }
        }
    }
    $upload->cleanUploadedFilesInfo();
    if(!empty($_FILES['Rnew_producto_imagen']['name'][0])){
        $upload->setValidTypes(array('jpg','jpeg','png','gif'));
        if($upload->saveTo("../../prod_img", "Rnew_producto_imagen") != 1){
            echo "Se ha agregado el producto, pero ocurrió un error al intentar guardar las siguientes imagenes:";
            $datos = $upload->getErrorFilesInfo();
            echo "<ul>";
            foreach($datos as $v)
                echo "<li>".$v."</li>";
            echo "</ul>";
        }
        $datos = $upload->getUploadedFilesInfo();
        if(count($datos)>0){
            foreach($datos as $v){
                if($arch->agregaImagen($v['name'], $res))
                    $upload->compress("../../prod_img/".$v['name'], "../../prod_img/less/".$v['name']);
            }
        }
    }
}