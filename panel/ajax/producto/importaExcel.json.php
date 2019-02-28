<?php
require("../../admin/classes/FileUpload.class.php");
require("../../admin/classes/Producto.class.php");
include("../../admin/classes/Barcode.class.php");

header('Content-Type: application/json; charset=utf-8'); 

function siono($txt){
    $txt = strtolower($txt);
    if($txt == "si"){
        return 1;
    }
    return 0;
}

$producto = new Producto("ajax/producto/importaExcel.json.php");
$bc = new Barcode("ajax/producto/importaExcel.json.php");
/*
$res = $producto->agregarProducto($_POST['new_producto_nombre'], $cat, $_POST['new_producto_descripcion'],
                                  $_POST['new_producto_precio'], $_POST['new_producto_inv'], $_POST['new_producto_inventario_max'],
                                  $_POST['new_producto_inventario_min'], $_POST['new_producto_disponible']);
if($res === false){
    echo "Error: No se pudo guardar el producto";
}else{
    $producto->editaIva($res, $_POST['new_producto_iva']);
    $error = false;
}*/
$upload = new FileUpload($_FILES);
$error = false;

$aux = array();
$aux['file'] = $_FILES;
if(!empty($_FILES['importa_prod']['name'][0])){
    $aux['error'] = false;
    $upload->setValidTypes(array('csv'));
    if($upload->saveTo("xls", "importa_prod") != 1){
        $datos = $upload->getErrorFilesInfo();
        $aux['data'] = $datos;
    }else{
        $aux['ok'] = "1";
        $datos = $upload->getUploadedFilesInfo();
        $aux['ok2'] = $datos;
        $errors = array();
        foreach($datos as $v){
            $aux['xd'][] = $v;
            $row = 1;
            if (($handle = fopen($v['pathReal'], "r")) !== FALSE) {
                while (($data = fgetcsv($handle)) !== FALSE) {
                    if($row == 1){
                        $row = 2;
                    }else{
                        $idProducto = $producto->agregarProducto($data[0], $data[2], $data[1], $data[3], $data[5], $data[6], $data[7], siono($data[8]));
                        $aux['agregado'][] = $idProducto;
                        if(!empty($data[9])){
                            $producto->editaIva($idProducto, siono($data[9]));
                        }
                        if(!empty($data[4])){
                            $bc->asignaBarcode($idProducto, $data[4]);    
                        }
                        
                    }
                }
                fclose($handle);
            }
        }
        if(!empty($errors)){
            $aux['errores'] = $errors;
        }
    }
}else{
    $aux['error'] = true;
    $aux['msg'] = "No se recibio ningun archivo";
}

echo json_encode($aux);