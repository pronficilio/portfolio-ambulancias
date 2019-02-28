<?php
require("../../admin/classes/Carrito.class.php");
require("../../admin/classes/Producto.class.php");
require("../../admin/classes/Categoria.class.php");
$prod = new Producto("ajax/carrito/getCarritosById.json.php");
$carr = new Carrito("ajax/carrito/getCarritosById.json.php");
$cat = new Categoria("ajax/carrito/getCarritosById.json.php");
$aux = $carr->getCarritoById($_GET['idCarrito']);

$todo = $cat->getCategoriasFull();

function familia($t, $id){
    $texto = "";
    $nav = $id;
    $fam = array();
    while(!empty($nav)){
        $fam[] = $t[$nav]['nombre'];
        $nav = $t[$nav]['enlace'];
    }
    $cuenta = 0;
    $fam = array_reverse($fam);
    foreach($fam as $val){
        if($texto != "")
            $texto .= "<br>";
        for($i=0; $i<$cuenta; $i++)
            $texto .= "&nbsp;";
        $cuenta+=3;
        $texto .= $val;
    }
    return $texto;
}

if($aux != false){  
    if(count($aux['contenido'])){
        foreach($aux['contenido'] as $ind=>$val){
            $aux['contenido'][$ind]['detalles'] = $prod->getProducto($val['idProducto']);
        }
    }
}
header('Content-Type: application/json; charset=utf-8');
?>
{
    "draw" : "<?=$_GET['draw'];?>",
    "fechaCreacion" : "<?=date("d-m-Y H:i", strtotime($aux['fechaCreacion']));?>",
    "cerrado" : "<?=$aux['cerrado'];?>",
    "nombre" : "<?=$aux['nombre'];?>",
    "razonSocial" : "<?=$aux['razonSocial'];?>",
    "carritoRS" : "<?=$aux['carritoRS'];?>",
    "carritoE" : "<?=$aux['carritoE'];?>",
    "saldo" : "<?=$aux['saldo'];?>",
    "efectivo" : "<?=$aux['efectivo'];?>",
    "tarjeta" : "<?=$aux['tarjeta'];?>",
    "cheque" : "<?=$aux['cheque'];?>",
    "credito" : "<?=$aux['credito'];?>",
    "entregado" : "<?=$aux['entregado'];?>",
    "factura" : "<?=$aux['factura'];?>",
    "fechaEntrega" : "<?=date("d-m-Y H:i", strtotime($aux['fechaEntrega']));?>",
    "idDescuento" : "<?=$aux['idDescuento'];?>",
    "descuento" : "<?=$carr->dameValorDescuento($aux['idDescuento']);?>",
    "data" : [
        <?php
        $contador = 1;
        $total = array();
        $totalito = 0;
        foreach($aux['contenido'] as $fila){
            $totalito += $fila['precioUnitario']*$fila['cantidad'];
            $epa = '[
            "'.$contador++.'",
            ';
            $epa .= "\"".$fila['detalles']['nombre'];
            $epa .= "\",
            ";
            $epa .= "\"<p class='text-muted'>";
            $epa .= familia($todo, $fila['detalles']['idCategoria']);
            $epa .= "</p>";
            $epa .= "\",
            ";
            $epa .= "\"<p class='text-center'>$".number_format($fila['precioUnitario'], 2)."</p>";
            $epa .= "\",
            ";
            $epa .= "\"<div class='' data-original='".$fila['cantidad']."'>";
            $epa .= "<input type='number' min='1' ".($fila['detalles']['enInventario']>=1&&$fila['detalles']['enInventario']>=$fila['cantidad']?"max='".$fila['detalles']['enInventario']."'":"")." class='form-control input-cantidad' value='".$fila['cantidad']."' disabled>";
            $epa .= "</div>\",
            ";
            $epa .= "\"<p class='text-center'>$".number_format($fila['precioUnitario']*$fila['cantidad'], 2)."</p>";
            $epa .= "\",
            ";
            $epa .= "\"<div class='text-center'><div class='btn-group' role='group' data-idCarritoContenido='".$fila['idCarritoContenido']."' cuanto='".$fila['cantidad']."' quien='".$fila['detalles']['nombre']."'>";
            if($aux['entregado'] == 0){
                $epa .= "<button type='button' class='btn btn-default editaListaCarrito' title='Clic para ver opciones de edición'>";
                $epa .= "<span class='glyphicon glyphicon-pencil'></span>";
                $epa .= "</button>";
                $epa .= "<button type='button' class='btn btn-default fade actualizaListaCarrito invisible _tool' data-toggle='tooltip' title='Clic para aceptar cambios'>";
                $epa .= "<span class='glyphicon glyphicon-ok-sign'></span>";
                $epa .= "</button>";
                $epa .= "<button type='button' class='btn btn-default fade cancelaListaCarrito invisible _tool' data-toggle='tooltip' title='Clic para descartar cambios'>";
                $epa .= "<span class='glyphicon glyphicon-remove-sign'></span>";
                $epa .= "</button>";
                $epa .= "<button type='button' class='btn btn-default fade borraListaCarrito invisible _tool' data-toggle='tooltip' title='Clic para eliminar el producto de la lista'>";
                $epa .= "<span class='glyphicon glyphicon-trash'></span>";
                $epa .= "</button>";
            }else{
                if($_GET['d'] == "1"){
                    $epa .= "<button type='button' class='btn btn-default devolverProducto _tool' data-toggle='tooltip' title='Clic para devolver producto'>";
                    $epa .= "<span class='glyphicon glyphicon-erase'></span>";
                    $epa .= "</button>";
                }
            }
            $epa .= "</div></div>\"
        ]";
            $total[] = $epa;
        }
        if($totalito < intval($carr->_bd->opcion("compraMinima") && $fila['gastoEnvio'])){
            $totalito += intval($carr->_bd->opcion("costoEnvio"));
            $epa = '[
            "'.$contador++.'",
            ';
            $epa .= "\"Gastos de envío\",
            ";
            $epa .= "\"<p class='text-muted'>N/A</p>\",
            ";
            $epa .= "\"<p class='text-center'>$".number_format($carr->_bd->opcion("costoEnvio"), 2)."</p>\",
            ";
            $epa .= "\"1\",
            ";
            $epa .= "\"<p class='text-center'>$".number_format($carr->_bd->opcion("costoEnvio"), 2)."</p>";
            $epa .= "\",
            ";
            $epa .= "\"\"
        ]";
            $total[] = $epa;
        }
        $subtotal = $totalito;
        $totalito = $carr->total($_GET['idCarrito']);
        echo implode(", ", $total);
        ?>
    ],
    "total" : "$<?=number_format($totalito, 2);?>",
    "totalNum" : "<?=number_format($totalito, 2, ".", "");?>",
    "subtotal" : "$<?=number_format($subtotal, 2);?>"
}