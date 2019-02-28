<?php 
class Carrito{
    public $_bd;

    function __construct($permisos){
        require_once("permisos.php");
        $this->_bd=new Permisos($permisos);
    }
    
    // actualizado
    private function damePrecioReal($idProducto){
        $consulta = sprintf("select precio, precioOutlet, iva from producto where idProducto=%d", $idProducto);
        $this->_bd->sql->consulta($consulta, "damePrecioReal($idProducto) Carrito.class.php");
        $dato = $this->_bd->sql->extraerRegistro();
        if(!empty($dato['precioOutlet']))
            return $dato['precioOutlet']*($dato['iva']!="0"?(intval($this->_bd->opcion("iva"))/100)+1:1);
        return $dato['precio']*($dato['iva']!="0"?(intval($this->_bd->opcion("iva"))/100)+1:1);
    }
    
    // actualizado
    public function getCarrosJson($opciones, $f=null, $fi, $ff, $c){
        $pag = $this->_bd->valida->validaCampoNumerico($opciones['start']);
        $cant = $this->_bd->valida->validaCampoNumerico($opciones['length']);
        $draw = $this->_bd->valida->validaCampoNumerico($opciones['draw']);
        $c = $this->_bd->valida->validaCampoNumerico($c);
        $consulta = "select count(idCarrito) from carrito where cerrado=".$opciones['c'];      
        $ce = "";
        if($opciones['c'] == "0"){
            if(!empty($f)){
                $ce = " and fechaCreacion>='".$fi."' and fechaCreacion<='".$ff." 23:59:59'";
            }
        }else{
            if(!empty($f)){
                $ce = " and fechaEntrega>='".$fi."' and fechaEntrega<='".$ff." 23:59:59'";
            }
        }
        if(!empty($c) || $c === "0"){
            $ce .= " and carrito.idCliente=".$c;
        }
        $this->_bd->sql->consulta($consulta.$ce, "getCarrosJson($tabla) Carrito.class.php", true);
        $cuantos = $this->_bd->sql->extraerRegistro();
        $total = $cuantos["count(idCarrito)"];
        $filtrado = $total;
        $consult = "select carrito.idCarrito, cliente.nombre, fechaCreacion, sum(cantidad) as productos, ".
                   "entregado, fechaEntrega, gastoEnvio, idUsuario, factura, carritoRS, carritoE, efectivo, cheque, tarjeta, cuenta, cliente.idCliente from carrito left join cliente on cliente.idCliente=carrito.idCliente ".
                   "left join carritoContenido on carritoContenido.idCarrito=carrito.idCarrito where cerrado=".$opciones['c'].$ce." ".
                   "group by carrito.idCarrito";
        $consult .= " order by fechaCreacion desc";
        if($cant!=-1)
            $consult .= sprintf(" limit %d, %d", $pag, $cant);
        $this->_bd->sql->consulta($consult, "getCarrosJson($tabla) Carrito.class.php", true);
        $res['draw'] = $draw;
        $res['recordsTotal'] = $total;
        $res['recordsFiltered'] = $filtrado;
        $datos = array();
        while($fila = $this->_bd->sql->extraerRegistro()){
            if(!empty($fila['idCarrito']))
                $datos[] = $fila;
        }
        $res['data'] = $datos;
        return $res;
    }
    public function agregaSaldo($idCliente, $saldo){
        $idCliente = $this->_bd->valida->validaCampoNumerico($idCliente);
        $saldo = $this->_bd->valida->validaCampoNumerico($saldo);
        if(!empty($idCliente)){
            $this->_bd->sql->consulta(sprintf("update cliente set saldo=saldo+%f where idCliente=%d", $saldo, $idCliente));
        }
    }
    public function quitaSaldo($idCliente, $saldo){
        $idCliente = $this->_bd->valida->validaCampoNumerico($idCliente);
        $saldo = $this->_bd->valida->validaCampoNumerico($saldo);
        if(!empty($idCliente)){
            $this->_bd->sql->consulta(sprintf("update cliente set saldo=saldo-%f where idCliente=%d", $saldo, $idCliente));
        }
    }
    public function dameValorDescuento($idDescuento){
        $idDescuento = $this->_bd->valida->validaCampoNumerico($idDescuento);
        if(!empty($idDescuento)){
            $this->_bd->sql->consulta(sprintf("select valor from descuento where idDescuento=%d", $idDescuento));
            return $this->_bd->sql->extraerRegistro()['valor'];
        }
        return 0;
    }
    public function asignaClienteCarrito($idCliente, $idCarrito){
        $idCarrito = $this->_bd->valida->validaCampoNumerico($idCarrito);
        $idCliente = $this->_bd->valida->validaCampoNumerico($idCliente);
        if(!empty($idCarrito) && !empty($idCliente)){
            $this->_bd->sql->consulta(sprintf("update carrito set idCliente=%d where idCarrito=%d", $idCliente, $idCarrito));
        }
    }
    // actualizado
    public function dameCarritoRapido($idCarrito){
        $idCarrito = $this->_bd->valida->validaCampoNumerico($idCarrito);
        if(!empty($idCarrito)){
            $this->_bd->sql->consulta(sprintf("select idCliente, idDescuento, factura from carrito where idCarrito=%d", $idCarrito));
            return $this->_bd->sql->extraerRegistro();
        }
        return null;
    }
    public function getCarritoById($idCarrito){
        $idCarrito = $this->_bd->valida->validaCampoNumerico($idCarrito);
        if(!empty($idCarrito)){
            $consulta = sprintf("select c.idCliente, nombre, fechaCreacion, cerrado, entregado, fechaEntrega, email, gastoEnvio, factura, c.idDescuento, efectivo, cheque, tarjeta, cuenta, saldo, carritoRS, carritoE, razonSocial from carrito c ".
                                "inner join cliente on cliente.idCliente=c.idCliente where idCarrito=%d", $idCarrito);
            $this->_bd->sql->consulta($consulta, "getCarritoById($idCarrito) Carrito.class.php");
            $datos = $this->_bd->sql->extraerRegistro();
            $datos['descuento'] = $this->dameValorDescuento($datos['idDescuento']);
            $consulta = sprintf("select idCarritoContenido, cc.idProducto, cantidad, precioUnitario, ".
                                "p.nombre as prod, p.precio from carritoContenido cc ".
                                "inner join producto p on cc.idProducto=p.idProducto ".
                                "where idCarrito=%d", $idCarrito);
            $datos['contenido'] = array();
            $this->_bd->sql->consulta($consulta, "getCarritoById($idCarrito) Carrito.class.php");
            while($fila = $this->_bd->sql->extraerRegistro()){
                $datos['contenido'][] = $fila;
            }
            return $datos;
        }
        return false;
    }
    public function descuentazo($idCarrito, $idDescuento){
        $idCarrito = $this->_bd->valida->validaCampoNumerico($idCarrito);
        $idDescuento = $this->_bd->valida->validaCampoNumerico($idDescuento);
        if(!empty($idCarrito))
            $this->_bd->sql->consulta(sprintf("update carrito set idDescuento=%d where idCarrito=%d", $idDescuento, $idCarrito));
    }
    // actualizado
    public function total($idCarrito){
        $idCarrito = $this->_bd->valida->validaCampoNumerico($idCarrito);
        if(!empty($idCarrito)){
            $consulta = sprintf("select sum(cantidad * precioUnitario) as total from carritoContenido where idCarrito=%d", $idCarrito);
            $this->_bd->sql->consulta($consulta, "total($idCarrito) Carrito.class.php");
            $dato = $this->_bd->sql->extraerRegistro();
            $d = 1;
            $this->_bd->sql->consulta(sprintf("select idDescuento, factura from carrito where idCarrito=%d", $idCarrito));
            $datoC = $this->_bd->sql->extraerRegistro();
            if(!empty($datoC['idDescuento'])){
                $this->_bd->sql->consulta("select valor from descuento where idDescuento=".$datoC['idDescuento']);
                $d = 1-($this->_bd->sql->extraerRegistro()['valor']/100);
            }
            if(!empty($datoC['factura'])){
                $d *= 1.16;
            }
            return $dato['total']*$d;
        }
        return 0;
    }
    
    // actualizado
    public function actualizaCantidad($idCarritoContenido, $cantidad){
        $idCarritoContenido = $this->_bd->valida->validaCampoNumerico($idCarritoContenido);
        if(!empty($idCarritoContenido)){
            $consulta = sprintf("select idCarrito, cantidad, idProducto from carritoContenido where idCarritoContenido=%d", $idCarritoContenido);
            $this->_bd->sql->consulta($consulta, "actualizaCantidad($idCarritoContenido, $cantidad) Carrito.class.php");
            $dato = $this->_bd->sql->extraerRegistro();
            if($this->carritoAbierto($dato['idCarrito'])){
                $this->_bd->sql->consulta("select enInventario from producto where idProducto=".$dato['idProducto']);
                $enI = $this->_bd->sql->extraerRegistro()['enInventario'];
                if($cantidad-$dato['cantidad'] <= $enI){
                    $consulta = sprintf("update carritoContenido set cantidad=%d where idCarritoContenido=%d", $cantidad, $idCarritoContenido);
                    $this->_bd->sql->consulta($consulta, "actualizaCantidad($idCarritoContenido, $cantidad) Carrito.class.php");
                    if($this->_bd->sql->lastError() == ""){
                        $consulta = sprintf("update producto set enInventario=enInventario-(%d) where idProducto=%d",
                            $cantidad-$dato['cantidad'], $dato['idProducto']);
                        $this->_bd->sql->consulta($consulta, "finalizaCarrito($idCarrito) Carrito.class.php");
                        return 1;
                    }
                }else{
                    return $enI-($cantidad-$dato['cantidad']);
                }
            }
        }
        return false;
    }
    
    public function dameCarritoContenido($idCarritoContenido){
        $idCarritoContenido = $this->_bd->valida->validaCampoNumerico($idCarritoContenido);
        if(!empty($idCarritoContenido)){
            $consulta = sprintf("select idProducto, cantidad, precioUnitario, idCarrito from carritoContenido where idCarritoContenido=%d", $idCarritoContenido);
            $this->_bd->sql->consulta($consulta, "dameCarritoContenido($idCarritoContenido) Carrito.class.php");
            return $this->_bd->sql->extraerRegistro();
        }
        return null;
    }
    // actualizado
    public function eliminaCarritoContenido($idCarritoContenido){
        $idCarritoContenido = $this->_bd->valida->validaCampoNumerico($idCarritoContenido);
        if(!empty($idCarritoContenido)){
            $consulta = sprintf("select idCarrito, cantidad, idProducto from carritoContenido where idCarritoContenido=%d", $idCarritoContenido);
            $this->_bd->sql->consulta($consulta, "eliminaCarritoContenido($idCarritoContenido) Carrito.class.php");
            $dato = $this->_bd->sql->extraerRegistro();
            //if($this->carritoAbierto($dato['idCarrito'])){
                $consulta = sprintf("delete from carritoContenido where idCarritoContenido=%d", $idCarritoContenido);
                $this->_bd->sql->consulta($consulta, "eliminaCarritoContenido($idCarritoContenido) Carrito.class.php");
                $consulta = sprintf("update producto set enInventario=enInventario+%d where idProducto=%d",
                    $dato['cantidad'], $dato['idProducto']);
                $this->_bd->sql->consulta($consulta, "finalizaCarrito($idCarrito) Carrito.class.php");
                return true;
            //}
        }
        return false;
    }
    
    // actualizado
    public function carritoAbierto($idCarrito){
        $idCarrito = $this->_bd->valida->validaCampoNumerico($idCarrito);
        if(!empty($idCarrito)){
            $consulta = sprintf("select cerrado from carrito where idCarrito=%d", $idCarrito);
            $this->_bd->sql->consulta($consulta, "carritoAbierto($idCarrito) Carrito.class.php");
            $dat = $this->_bd->sql->extraerRegistro();
            if($dat['cerrado'] === "0")
                return true;
            else
                return false;
        }
        return false;
    }
    
    //actualizado
    public function agregaCarrito($idCarrito, $idProducto, $cantidad){
        $idCarrito = $this->_bd->valida->validaCampoNumerico($idCarrito);
        $idProducto = $this->_bd->valida->validaCampoNumerico($idProducto);
        $cantidad = $this->_bd->valida->validaCampoNumerico($cantidad);
        if($this->carritoAbierto($idCarrito)){
            if(!empty($idProducto) && !empty($cantidad)){
                $consulta = sprintf("insert into carritoContenido (idCarrito, idProducto, cantidad, precioUnitario) values (%d, %d, %d, %f)",
                                    $idCarrito, $idProducto, $cantidad, $this->damePrecioReal($idProducto));
                $this->_bd->sql->consulta($consulta, "agregaCarrito($idCarrito, $idProducto, $cantidad) Carrito.class.php");
                if($this->_bd->sql->filasAfectadas() == 1){
                    $id = $this->_bd->sql->ultimoId();
                    $consulta = sprintf("update producto set enInventario=enInventario-%d where idProducto=%d", $cantidad, $idProducto);
                    $this->_bd->sql->consulta($consulta, "finalizaCarrito($idCarrito) Carrito.class.php");
                    return $id;
                }
            }
        }
        return false;
    }
    
    public function cierraCarrito($idCarrito, $cierra=1, $idUsuario=null){
        $idCarrito = $this->_bd->valida->validaCampoNumerico($idCarrito);
        $cierra = $this->_bd->valida->validaCampoNumerico($cierra);
        $idUsuario = $this->_bd->valida->validaCampoNumerico($idUsuario);
        if(empty($idUsuario))
            $idUsuario = "null";
        if(!empty($idCarrito)){
            $consulta = sprintf("update carrito set cerrado=%d, idUsuario=%s where idCarrito=%d", $cierra, $idUsuario, $idCarrito);
            $this->_bd->sql->consulta($consulta, "cierraCarrito($idCarrito, $cierra) Carrito.class.php");
            if($cierra == 0){
                $this->actualizaPrecios($idCarrito);
            }
        }
    }
    
    public function sinGastosEnvio($idCarrito){
        $idCarrito = $this->_bd->valida->validaCampoNumerico($idCarrito);
        if(!empty($idCarrito)){
            $consulta = sprintf("update carrito set gastoEnvio=0 where idCarrito=%d", $idCarrito);
            $this->_bd->sql->consulta($consulta, "sinGastosEnvio($idCarrito) Carrito.class.php");
        }
    }
    
    public function creaCarrito($idCliente, $posibleCero=false){
        $idCliente = $this->_bd->valida->validaCampoNumerico($idCliente);
        if(!empty($idCliente) || $posibleCero){
            $consulta = sprintf("insert into carrito (idCliente) values (%d)", $idCliente);
            $this->_bd->sql->consulta($consulta, "creaCarrito($idCliente) Carrito.class.php");
            if($this->_bd->sql->filasAfectadas()){
                return $this->_bd->sql->ultimoId();
            }
        }
        return false;
    }

    public function agregaAInventario($idProducto, $cant){
        $idProducto = $this->_bd->valida->validaCampoNumerico($idProducto);
        $cant = $this->_bd->valida->validaCampoNumerico($cant);
        if(!empty($idProducto)){
            $this->_bd->sql->consulta(sprintf("update producto set enInventario=enInventario+%d where idProducto=%d", $cant, $idProducto));
        }
    }
    
    public function eliminaCarrito($idCarrito){
        $idCarrito = $this->_bd->valida->validaCampoNumerico($idCarrito);
        if(!empty($idCarrito)){
            $this->_bd->sql->consulta(sprintf("select entregado from carrito where idCarrito=%d", $idCarrito));
            //if($this->_bd->sql->extraerRegistro()['entregado'] == 1){
                $consulta = sprintf("select idProducto, cantidad from carritoContenido where idCarrito=%d", $idCarrito);
                $this->_bd->sql->consulta($consulta, "eliminaCarrito($idCarrito) Carrito.class.php");
                $datos = array();
                while($fila = $this->_bd->sql->extraerRegistro()){
                    $datos[] = $fila;
                }
                foreach($datos as $val){
                    $consulta = sprintf("update producto set enInventario=enInventario+%d where idProducto=%d", $val['cantidad'], $val['idProducto']);
                    $this->_bd->sql->consulta($consulta, "eliminaCarrito($idCarrito) Carrito.class.php");
                }
            //}

            $consulta = sprintf("delete from carrito where idCarrito=%d", $idCarrito);
            $this->_bd->sql->consulta($consulta, "eliminaCarrito($idCarrito) Carrito.class.php");

            $consulta = sprintf("delete from carritoContenido where idCarrito=%d", $idCarrito);
            $this->_bd->sql->consulta($consulta, "eliminaCarrito($idCarrito) Carrito.class.php");
            return true;
        }
        return false;
    }
    
    public function actualizaCarrito($idCarritoContenido, $cantidad){
        $idCarritoContenido = $this->_bd->valida->validaCampoNumerico($idCarritoContenido);
        $cantidad = $this->_bd->valida->validaCampoNumerico($cantidad);
        if(!empty($idCarritoContenido)){
            $consulta = sprintf("update carritoContenido set cantidad=%d where idCarritoContenido=%d", $cantidad, $idCarritoContenido);
            $this->_bd->sql->consulta($consulta, "actualizaCarrito($idCarritoContenido, $cantidad) Carrito.class.php");
            if($this->_bd->sql->lastError() == "")
                return true;
        }
        return false;
    }
    
    public function damePrecio($idProducto){
        $idProducto = $this->_bd->valida->validaCampoNumerico($idProducto);
        if(!empty($idProducto)){
            $consulta = sprintf("select precio from producto where idProducto=%d", $idProducto);
            $this->_bd->sql->consulta($consulta, "damePrecio($idProducto) Carrito.class.php");
            $dato = $this->_bd->sql->extraerRegistro();
            return $dato['precio'];
        }
    }
    
    public function carroActual($idCliente){
        $idCliente = $this->_bd->valida->validaCampoNumerico($idCliente);
        if(!empty($idCliente)){
            $consulta = sprintf("select idCarrito from carrito where idCliente=%d and cerrado=0", $idCliente);
            $this->_bd->sql->consulta($consulta, "carroActual($idCliente) Carrito.class.php");
            if($this->_bd->sql->filasAfectadas()){
                $dato = $this->_bd->sql->extraerRegistro($idCliente);
                return $dato['idCarrito'];
            }
        }
        return false;
    }
    public function aplicaFactura($idCarrito, $rs, $cf, $w=1){
        $idCarrito = $this->_bd->valida->validaCampoNumerico($idCarrito);
        $rs = $this->_bd->valida->validaCampoTexto($rs);
        $cf = $this->_bd->valida->validaCampoTexto($cf);
        if(!empty($idCarrito)){
            $this->_bd->sql->consulta(sprintf("update carrito set factura=%d, carritoRS='%s', carritoE='%s' where idCarrito=%d",
                $w, $rs, $cf, $idCarrito));
        }
    }
    
    public function finalizaCarrito($idCarrito, $idUsuario=null, $t=0, $c=0, $e=0, $acuenta=0, $cr=0, $rf=null, $rs=null, $cf=null){
        $idCarrito = $this->_bd->valida->validaCampoNumerico($idCarrito);
        $idUsuario = $this->_bd->valida->validaCampoNumerico($idUsuario);
        $t = $this->_bd->valida->validaCampoNumerico($t);
        $c = $this->_bd->valida->validaCampoNumerico($c);
        $e = $this->_bd->valida->validaCampoNumerico($e);
        $acuenta = $this->_bd->valida->validaCampoNumerico($acuenta);
        $cr = $this->_bd->valida->validaCampoNumerico($cr);
        $rf = $this->_bd->valida->validaCampoNumerico($rf);
        $rs = $this->_bd->valida->validaCampoTexto($rs);
        $cf = $this->_bd->valida->validaCampoTexto($cf);
        if(empty($idUsuario))
            $idUsuario = "null";
        if(!empty($idCarrito)){
            $consulta = sprintf("update carrito set entregado=1, fechaEntrega=now(), cerrado=1, idUsuario=%s where idCarrito=%d", $idUsuario, $idCarrito);
            $this->_bd->sql->consulta($consulta, "finalizaCarrito($idCarrito) Carrito.class.php");
            if($this->_bd->sql->lastError() == ""){
                if($t+$c+$e+$acuenta+$cr > 0)
                    $this->_bd->sql->consulta(sprintf("update carrito set efectivo=%f, tarjeta=%f, cheque=%f, cuenta=%f, credito=%f where idCarrito=%d", $e, $t, $c, $acuenta, $cr, $idCarrito));
                if(!empty($rf))
                    $this->_bd->sql->consulta(sprintf("update carrito set factura=1, carritoRS='%s', carritoE='%s' where idCarrito=%d", $rs, $cf, $idCarrito));
                return true;
            }
        }
        return true;
    }
    
    public function actualizaPrecios($idCarrito){
        $consulta = sprintf("select idProducto from carritoContenido where idCarrito=%d", $idCarrito);
        $this->_bd->sql->consulta($consulta, "actualizaPrecios($idCarrito) Carrito.class.php");
        $productos = array();
        while($fila = $this->_bd->sql->extraerRegistro()){
            $productos[] = $fila['idProducto'];
        }
        foreach($productos as $val){
            $precio = $this->damePrecioReal($val);
            $consulta = sprintf("update carritoContenido set precioUnitario=%f where idCarrito=%d and idProducto=%d", $precio, $idCarrito, $val);
            $this->_bd->sql->consulta($consulta, "actualizaPrecios($idProducto) Producto.class.php");
        }
    }
    
    public function avisaMailCarritoCerrado($idCliente, $idCarrito){
        $idCliente = $this->_bd->valida->validaCampoNumerico($idCliente);
        $idCarrito = $this->_bd->valida->validaCampoNumerico($idCarrito);
        if(!empty($idCliente) && !empty($idCarrito)){
            $datos = $this->getCarritoById($idCarrito);
            $remitente = "MIME-Version: 1.0\r\n";
            $remitente .= "Content-Type: text/html; charset=UTF-8\r\n";
            $remitente .= $GLOBALS["g_comprobanteCompra_remitente"]." <no-responder@".$GLOBALS["g_sitio"].">\r\n";
            $remitente .= "Reply-To: no-responder@".$GLOBALS["g_sitio"]."\r\n";
            $asunto = "Comprobante de compra";
            $contenido = "
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='utf-8'>
</head>
<body>
    <div>
        <article style=\"width:500px;border: 1px solid #666;border-radius: 5px;text-align: center;background: white;font-family: 'Gudea', sans-serif;\">
            <p style='margin: 0px; padding-top:10px;'>
                <img src='http://".$GLOBALS["g_sitio"].$GLOBALS["g_nueva"]."/images/logo.jpeg' width='200'>
            </p>
            <h3 style='text-align: left;padding-left: 10px;font-weight: bold;'>Gracias por tu compra</h3>
            <div style='text-align: left;padding-left: 30px;'>
                Resumen de tu compra:
                <table border='1' style='width:450px;'>
                    <tr>
                        <th style='text-align:center'>Producto</th>
                        <th style='text-align:center'>Precio unitario</th>
                        <th style='text-align:center'>Cantidad</th>
                        <th style='text-align:center'>Subtotal</th>
                    </tr>
                    ";
            $total = 0;
            foreach($datos['contenido'] as $val){
                $total += ($val['precioUnitario']*$val['cantidad']);
                $contenido .= "
                    <tr>
                        <td>".$val['prod']."</td>
                        <td style='text-align:center'>$".number_format($val['precioUnitario'], 2)."</td>
                        <td style='text-align:center'>".$val['cantidad']."</td>
                        <td style='text-align:center'>$".number_format($val['precioUnitario']*$val['cantidad'], 2)."</td>
                    </tr>
                ";
            }
            if($total < intval($this->_bd->opcion("compraMinima")) && $datos['gastoEnvio']){
                $total += intval($this->_bd->opcion("costoEnvio"));
                $contenido .= "
                    <tr>
                        <td>Gastos de envío</td>
                        <td>$".number_format($this->_bd->opcion("costoEnvio"), 2)."</td>
                        <td>1</td>
                        <td>$".number_format($this->_bd->opcion("costoEnvio"), 2)."</td>
                    </tr>
                ";
            }
            $contenido .= "
                </table>
                <br>
                <strong><mark>Total a pagar: $".number_format($total, 2)."</mark></strong><br>
                <strong>Número de pedido: ".$idCarrito."</strong><br>
                <p>".$this->_bd->opcion('concretarVenta')."</p>
            </div>
        </article>
        <ul style='font-style:italic;width:460px;'>
            <li>Gracias <a href='http://".$GLOBALS["g_sitio"].$GLOBALS["g_nueva"]."/' target='_blank'>Ir a la Tienda</a></li>
        </ul>
    </div>
</body>
</html>";
            mail($datos['email'], $asunto, $contenido, $remitente);
            mail($GLOBALS['g_comprobanteCompra_emailAviso'], "Compra concluida",
                 "El cliente ".$datos['nombre']." ha concluido su compra. ".
                 "Visita el panel para ver mas informacion<br><a href='http://".$GLOBALS["g_sitio"].$GLOBALS["g_nueva"]."/panel' target='_blank'>http://".$GLOBALS["g_sitio"].$GLOBALS["g_nueva"]."/panel/</a>"."<hr> Número de pedido: ".$idCarrito, $remitente);
        }
    }
}