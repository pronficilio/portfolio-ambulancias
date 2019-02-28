<?php
session_start();
require("../../admin/classes/Carrito.class.php");
require("../../admin/classes/Producto.class.php");
require("../../admin/classes/Categoria.class.php");
$carr = new Carrito("ajax/carrito/dameTicket.php");
$aux = $carr->getCarritoById($_POST['idCarrito']);
if($aux != false){
    unset($_SESSION['email']);
    $_SESSION['email']['cliente'] = $_SESSION['email']['carritoRS'] = $_SESSION['email']['carritoE'] = "";
    if(!empty($aux['idCliente'])){
        $_SESSION['email']['cliente'] = $aux['nombre'];
    }
    if(!empty($aux['carritoRS'])){
        $_SESSION['email']['carritoRS'] = $aux['carritoRS'];
    }
    if(!empty($aux['carritoE'])){
        $_SESSION['email']['carritoE'] = $aux['carritoE'];
    }
    $_SESSION['email']['productos'] = array();
    $_SESSION['email']['idCarrito'] = $_POST['idCarrito'];
    $total = 0;
    $arti = 0;
    foreach($aux['contenido'] as $val){
        $_SESSION['email']['productos'][] = array(
            'producto' => $val['prod'],
            'precioUnitario' => $val['precioUnitario'],
            'cnt' => $val['cantidad'],
            'sub' => $val['precioUnitario'] * $val['cantidad']
        );
        $arti += $val['cantidad'];
        $total += $val['precioUnitario'] * $val['cantidad'];
    }
    if($aux['gastoEnvio'] != 0 && $carr->_bd->opcion("compraMinima") > $total){
        $ce = $carr->_bd->opcion("costoEnvio");
        $_SESSION['email']['productos'][] = array(
            'producto' => "Gasto de envio",
            'precioUnitario' => $ce,
            'cnt' => 1,
            'sub' => $ce
        );
        $total += $ce;
    }
    if(!empty($aux['descuento'])){
        $_SESSION['email']['descuento'] = number_format($total*$aux['descuento']/100, 2);

        $total *= (1-$aux['descuento']/100);
    }else{
        $_SESSION['email']['descuento'] = "0";
    }
    if($aux['factura']){
        $_SESSION['email']['iva'] = $total * 0.16;
        $total *= 1.16;
    }
    else
        $_SESSION['email']['iva'] = 0;
    $_SESSION['email']['articulos'] = $arti;
    $_SESSION['email']['total'] = $total;
    $_SESSION['email']['cuenta'] = $aux['cuenta'];
    $_SESSION['email']['tiempo'] = $aux['fechaEntrega'];
    $_SESSION['email']['formaPago'] = "";
    if(!empty($aux['efectivo'])){
        $_SESSION['email']['formaPago'] = "Efectivo";
    }
    if(!empty($aux['cheque'])){
        if(!empty($_SESSION['email']['formaPago']))
            $_SESSION['email']['formaPago'] .= ", ";
        $_SESSION['email']['formaPago'] .= "Cheque";
    }
    if(!empty($aux['tarjeta'])){
        if(!empty($_SESSION['email']['formaPago']))
            $_SESSION['email']['formaPago'] .= ", ";
        $_SESSION['email']['formaPago'] .= "Tarjeta";
    }
    if(!empty($aux['credito'])){
        if(!empty($_SESSION['email']['formaPago']))
            $_SESSION['email']['formaPago'] .= ", ";
        $_SESSION['email']['formaPago'] .= "Credito";
    }
    $_SESSION['email']['credito'] = $aux['credito'];
    $_SESSION['email']['efectivo'] = $aux['efectivo'];
    $_SESSION['email']['tarjeta'] = $aux['tarjeta'];
    $_SESSION['email']['cheque'] = $aux['cheque'];
}
if(empty($_POST['cual']))
    include("../../admin/email/ticketCompra.php");
else
    include("../../admin/email/notaCompra.php");