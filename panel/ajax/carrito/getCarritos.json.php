<?php
session_start();
require("../../admin/classes/Acceso.class.php");
require("../../admin/classes/Carrito.class.php");
$carr = new Carrito("ajax/carrito/getCarritos.json.php");
$ac = new Acceso("ajax/carrito/getCarritos.json.php");
$aux = $carr->getCarrosJson($_GET, $_GET['full'], $_GET['fechaIni'], $_GET['fechaFin'], $_GET['cliente']);
header('Content-Type: application/json; charset=utf-8');
$contador = 1;
$total = array();
$totalTotal = 0;
$totalE = 0;
$totalT = 0;
$totalC = 0;
$totalIVA = 0;
$totalAC = 0;
$totalU = 0;
$productosVendidos = array();
$productoNombre = array();
if(count($aux['data'])){
    foreach($aux['data'] as $fila){
        $productos = $carr->getCarritoById($fila['idCarrito']);
        foreach($productos['contenido'] as $v){
            if(isset($productosVendidos[$v['idProducto']]))
                $productosVendidos[$v['idProducto']] += $v['cantidad'];
            else
                $productosVendidos[$v['idProducto']] = $v['cantidad'];
            $productoNombre[$v['idProducto']] = $v['prod'];
        }
        $epa = '["'.$fila['idCarrito'].'",';
        $epa .= "\"".$fila['nombre'];
        $epa .= "<p class='text-muted'>";
        $epa .= "<span class='_tool' data-toggle='tooltip' title='Cantidad de productos'><span class='glyphicon glyphicon-list-alt'></span> <span>".$fila['productos']."</span></span><br>";
        $totalU += $fila['productos'];
        $epa .= "<span class='_tool' data-toggle='tooltip' title='Fecha de creación'><span class='glyphicon glyphicon-calendar'></span> <span>".date("d-m-Y", strtotime($fila['fechaCreacion']))."</span></span>";
        if($fila['entregado'] == 1){
            $epa .= "<br><span class='_tool' data-toggle='tooltip' title='Fecha de finalización'><span class='glyphicon glyphicon-check'></span> <span>".date("d-m-Y", strtotime($fila['fechaEntrega']))."</span></span>";
        }
        $epa .= "</p>";
        $epa .= "\",";
        $totall = $carr->total($fila['idCarrito']); 
        if(!empty($fila['factura'])){
            $totalIVA += ($totall/1.16*0.16);
        }
        if($totall < intval($carr->_bd->opcion("compraMinima")) && $fila['gastoEnvio'])
            $totall += intval($carr->_bd->opcion("costoEnvio"));
        $totalTotal += $totall;
        $fila['totaltotal'] = $totall;
        $totalE += $fila['efectivo'];
        $totalT += $fila['tarjeta'];
        $totalC += $fila['cheque'];
        $totalAC += $fila['cuenta'];
        $fila['vendedor'] = $ac->dameNombre($fila['idUsuario']);
        $epa .= "\"".$fila['vendedor'];
        $epa .= "\",";
        if(!empty($fila['factura'])){
            $epa .= "\"<small class='_tool' title='RFC'><strong>RFC:</strong> ".$fila['carritoRS']."</small><br>";
            $epa .= "<small class='_tool' title='Teléfono'><span class='glyphicon glyphicon-earphone'></span> ".$fila['carritoE']."</small>";
            $epa .= "\",
            ";
        }else{
            $epa .= "\"\",";
        }
        $epa .= "\"$".number_format($totall, 2);
        $epa .= "\",";
        $epa .= "\"<div class='text-center'><div class='btn-group' role='group' data-idCarrito='".$fila['idCarrito']."'>";
        $epa .= "<button type='button' class='btn btn-default _tool ver_lista_modal_carrito' title='Ver productos en el carrito' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"500\\\"}'>";
        $epa .= "<span class='glyphicon glyphicon-shopping-cart'></span>";
        $epa .= "</button>";
        if($fila['entregado'] == 0){
            if($_GET['c'] == 1){
                $epa .= "<button type='button' class='btn btn-default _tool finalizaCarrito' title='Finalizar el carrito' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"500\\\"}'>";
                $epa .= "<span class='glyphicon glyphicon-ok'></span>";
                $epa .= "</button>";
            }
        }else{
            $epa .= "<button type='button' class='btn btn-default _tool' title='Imprimir ticket' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"500\\\"}' onclick='imprimeTicket(".$fila['idCarrito'].")'>";
            $epa .= "<span class='glyphicon glyphicon-print'></span>";
            $epa .= "</button>";
            if($_GET['full'] == 1){
                $epa .= "<button type='button' class='btn btn-default _tool' title='Imprimir ticket' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"500\\\"}' onclick='verTicket(".$fila['idCarrito'].")'>";
                $epa .= "<span class='glyphicon glyphicon-eye-open'></span>";
                $epa .= "</button>";
            }
        }
        $epa .= "<button type='button' class='btn btn-default _tool borrarCarrito' title='Borrar carrito' data-toggle='tooltip'>";
        $epa .= "<span class='glyphicon glyphicon-trash'></span>";
        $epa .= "</button>";
        $epa .= "</div></div>\"]";
        if($_GET['full'] == 1){
            $total[] = $fila;
        }else{
            $total[] = json_decode($epa);
        }
    }
}
$aux['productosVendidos'] = array();
arsort($productosVendidos);
foreach($productosVendidos as $ind=>$v){
    $aux['productosVendidos'][] = "[".$v."] ".$productoNombre[$ind];
}
$aux['data'] = $total;
if(empty($aux['data']))
    $aux['data'] = [];
$aux['total'] = "$".number_format($totalTotal, 2);
$aux['totalNum'] = $totalTotal;
$aux['totalE'] = "$".number_format($totalE, 2);
$aux['totalT'] = "$".number_format($totalT, 2);
$aux['totalC'] = "$".number_format($totalC, 2);
$aux['totalU'] = number_format($totalU, 0);
$aux['totalAC'] = "$".number_format($totalAC, 2);
$aux['totalIVA'] = "$".number_format($totalIVA, 2);

$_SESSION['red'] = $aux;
echo json_encode($aux);