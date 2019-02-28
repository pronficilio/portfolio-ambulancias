<?php 
class Proveedores{
    public $_bd;

    function __construct($permisos){
        require_once("permisos.php");
        $this->_bd=new Permisos($permisos);
    }
    
    
    /**
     * Envia un email con un acceso embebido a un proveedor
     * @param integer $idProveedor Identificador del proveedor
     */
    public function enviarAccesoEmbebido($idProveedor){
        $idProveedor = $this->_bd->valida->validaCampoNumerico($idProveedor);
        if(!empty($idProveedor)){
            $token = md5(time().$idProveedor);
            $this->_bd->sql->consulta(sprintf("select email, nombre, token from proveedor where idProveedor=%d", $idProveedor),
                                      "enviarAccesoEmbebido($idProveedor) Proveedores.class.php");
            if($this->_bd->sql->filasAfectadas()){
                $dato = $this->_bd->sql->extraerRegistro();
                $this->_bd->sql->consulta(sprintf("update proveedor set token='%s' where idProveedor=%d",
                                                  $token, $idProveedor),
                                          "enviarAccesoEmbebido($idProveedor) Proveedores.class.php");
                require_once 'PHPMailer/PHPMailerAutoload.php';
                $_SESSION['email']['token'] = $token;
                if(empty($dato['token'])){ /// primera vez
                    $correo = $this->_bd->requireToVar("../email/emailAccesoProveedorPrimeraVez.php");
                }else{
                    $correo = $this->_bd->requireToVar("../email/emailAccesoProveedor.php");
                }
                $mail = new PHPMailer;
                $mail->setFrom('noreply@'.$GLOBALS['g_sitio'], $GLOBALS['g_principal_title']);
                $mail->addAddress($dato['email'], $dato['nombre']);
                $mail->Subject = "Acceso de proveedor - ".$GLOBALS['g_principal_title'];
                $mail->msgHTML($correo);
                return $mail->send();
            }
        }
        return false;
    }
    
    /**
     * Agrega un proveedor
     * @param  string  $nombre Nombre completo
     * @param  string  $email  Email del proveedor
     * @param  string  $tel    Teléfono 
     * @return boolean Verdadero si se realizo la consulta
     */
    public function agregarProveedor($nombre, $email, $tel){
        $nombre = $this->_bd->valida->validaCampoTexto($nombre);
        $email = $this->_bd->valida->validaCampoTexto($email);
        $tel = $this->_bd->valida->validaCampoTexto($tel);
        if(!empty($nombre) && !empty($email)){
            $this->_bd->sql->consulta(sprintf("insert into proveedor (nombre, email, telefono) values ".
                                              "('%s', '%s', '%s')", $nombre, $email, $tel),
                                      "agregarProveedor($nombre) Proveedores.class.php");
            return true;
        }
        return false;
    }
    
    /**
     * Regresa el idProveedor de un email en caso de existir
     * @param  string  $email Email del proveedor
     * @return integer idProveedor, 0 en caso de no existir
     */
    public function existeEmail($email){
        $email = $this->_bd->valida->validaCampoTexto($email);
        if(!empty($email)){
            $this->_bd->sql->consulta(sprintf("select idProveedor from proveedor where email='%s' and active=1",
                                              $email), "existeEmail($email) Proveedores.class.php");
            $dato = $this->_bd->sql->extraerRegistro();
            return $dato['idProveedor'];
        }
        return 0;
    }
    
    /**
     * Asigna un proveedor a una categoria
     * @param  integer $idCategoria Identificador de la categoria
     * @param  integer $idProveedor Identificador del proveedor
     * @return boolean Verdadero si se realizo la consulta
     */
    public function asignaProveedor($idCategoria, $idProveedor){
        $idCategoria = $this->_bd->valida->validaCampoNumerico($idCategoria);
        $idProveedor = $this->_bd->valida->validaCampoNumerico($idProveedor);
        if(!empty($idCategoria) && !empty($idProveedor)){
            $this->_bd->sql->consulta(sprintf("update categoria set idProveedor=%d where idCategoria=%d",
                                              $idProveedor, $idCategoria),
                                      "asignaProveedor($idCategoria, $idProveedor) Proveedores.class.php");
            return true;
        }
        return false;
    }
    
    public function eliminaProveedor($idProveedor){
        $idProveedor = $this->_bd->valida->validaCampoNumerico($idProveedor);
        if(!empty($idProveedor)){
            $this->_bd->sql->consulta(sprintf("update proveedor set active=0 where idProveedor=%d", $idProveedor),
                                      "eliminaProveedor($idProveedor) Proveedores.class.php");
            $this->_bd->sql->consulta(sprintf("update categoria set idProveedor=NULL where idProveedor=%d", $idProveedor),
                                      "eliminaProveedor($idProveedor) Proveedores.class.php");
            return true;
        }
        return false;
    }
    
    public function getProveedoresJson($opciones){
        $busqueda = $this->_bd->valida->validaCampoTexto($opciones['search']['value']);
        $pag = $this->_bd->valida->validaCampoNumerico($opciones['start']);
        $cant = $this->_bd->valida->validaCampoNumerico($opciones['length']);
        $draw = $this->_bd->valida->validaCampoNumerico($opciones['draw']);
        
        $consulta = "select count(*) from proveedor where active=1";        
        $this->_bd->sql->consulta($consulta, "getProveedoresJson() Proveedores.class.php");
        $cuantos = $this->_bd->sql->extraerRegistro();
        $total = $cuantos['count(*)'];
        $filtrado = $total;
        if(!empty($busqueda)){
            $busqueda = sprintf(" and (nombre like '%%%s%%' or email like '%s%%')", $busqueda, $busqueda);
            $this->_bd->sql->consulta($consulta.$busqueda, "getProveedoresJson() Proveedores.class.php");
            $cuantos = $this->_bd->sql->extraerRegistro();
            $filtrado = $cuantos['count(*)'];
        }
        
        $res['draw'] = $draw;
        $res['recordsTotal'] = $total;
        $res['recordsFiltered'] = $filtrado;
        
        $consult = "select nombre, telefono, email, token, idProveedor, tiempo from proveedor where active=1".$busqueda;
        if(!empty($opciones['order'][0]['column']))
            $consult .= " order by ".$opciones['order'][0]['column']." ".$opciones['order'][0]['dir'];
        $consult .= sprintf(" limit %d, %d", $pag, $cant);
        $this->_bd->sql->consulta($consult, "getProveedoresJson() Proveedores.class.php");
        $res['data'] = array();
        while($fila = $this->_bd->sql->extraerRegistro()){
            $res['data'][] = $fila;
        }
        return $res;
    }
    
    public function dameProveedor($idProveedor){
        $idProveedor = $this->_bd->valida->validaCampoNumerico($idProveedor);
        if(!empty($idProveedor)){
            $this->_bd->sql->consulta(sprintf("select nombre, telefono, email, idProveedor from proveedor where active=1 and idProveedor=%d",
                                              $idProveedor), "dameProveedor($idProveedor) Proveedores.class.php");
            $dato = $this->_bd->sql->extraerRegistro();
            return $dato;
        }
        return null;
    }
    
    public function editaProveedor($idProveedor, $nombre, $email, $tel){
        $idProveedor = $this->_bd->valida->validaCampoNumerico($idProveedor);
        $nombre = $this->_bd->valida->validaCampoTexto($nombre);
        $email = $this->_bd->valida->validaCampoTexto($email);
        $tel = $this->_bd->valida->validaCampoTexto($tel);
        if(!empty($nombre) && !empty($email) && !empty($idProveedor)){
            $this->_bd->sql->consulta(sprintf("update proveedor set nombre='%s', email='%s', telefono='%s' where idProveedor=%d",
                                              $nombre, $email, $tel, $idProveedor), "editaProveedor($idProveedor) Proveedores.class.php");
            return true;
        }
        return false;
    }
    
    public function dameCategorias($idProveedor){
        $idProveedor = $this->_bd->valida->validaCampoNumerico($idProveedor);
        $datos = array();
        if(!empty($idProveedor)){
            $this->_bd->sql->consulta(sprintf("select nombre, idCategoria from categoria where activo=1 and idProveedor=%d",
                                              $idProveedor), "dameCategorias($idProveedor) Proveedores.class.php");
            while($fila=$this->_bd->sql->extraerRegistro())
                $datos[] = $fila;
        }
        return $datos;
    }
    
    public function eliminaCategoria($idCategoria, $idProveedor){
        $idCategoria = $this->_bd->valida->validaCampoNumerico($idCategoria);
        $idProveedor = $this->_bd->valida->validaCampoNumerico($idProveedor);
        if(!empty($idCategoria) && !empty($idProveedor)){
            $this->_bd->sql->consulta(sprintf("update categoria set idProveedor=null where idProveedor=%d and idCategoria=%d",
                                              $idProveedor, $idCategoria),
                                      "eliminaProveedor($idCategoria, $idProveedor) Proveedores.class.php");
            return true;
        }
        return false;
    }
    
    public function verificaToken($token){
        $token = $this->_bd->valida->validaCampoTexto($token);
        if(!empty($token)){
            $this->_bd->sql->consulta(sprintf("select idProveedor, nombre from proveedor where token='%s'",
                                             $token), "verificaToken($token) Proveedores.class.php");
            return $this->_bd->sql->extraerRegistro();
        }
        return null;
    }
    
    private function hijosCategoria($idCat){
        $cola[] = $idCat;
        $ini = 0;
        while($ini < count($cola)){
            $saca = $cola[$ini++];
            $consulta = sprintf("select idCategoria from categoria where enlace=%d", $saca);
            $this->_bd->sql->consulta($consulta, "hijosCateogira($idCat) producto.class.php");
            while($fila = $this->_bd->sql->extraerRegistro()){
                $cola[] = $fila['idCategoria'];
            }
        }
        return $cola;
    }
    
    public function dameProductos($idProveedor){
        $idProveedor = $this->_bd->valida->validaCampoNumerico($idProveedor);
        $res = array();
        if(!empty($idProveedor)){
            $cat = $this->dameCategorias($idProveedor);
            if(count($cat)){
                $consulta = "select idProducto, nombre, precio, precioOutlet, idCategoria, descripcion, disponible, iva, enInventario from producto where activo=1 and ";
                $totalidad = array();
                foreach($cat as $val){
                    foreach($this->hijosCategoria($val['idCategoria']) as $idC){
                        $totalidad[] = "idCategoria=".$idC;
                    }
                }
                $quienes = implode(" or ", $totalidad);
                $consulta .= "(".$quienes.")";
                $this->_bd->sql->consulta($consulta, "dameProductos($pag, $idCat, $cant) Producto.class.php");
                while($fila = $this->_bd->sql->extraerRegistro()){
                    $res[] = $fila;
                }
            }
        }
        return $res;
    }
    
    public function dameProductosVendidos($idProveedor, $feIni, $feFin){
        $idProveedor = $this->_bd->valida->validaCampoNumerico($idProveedor);
        $feIni = $this->_bd->valida->validaCampoTexto($feIni);
        $feFin = $this->_bd->valida->validaCampoTexto($feFin);
        $res = array();
        $acumulado = 0;
        if(!empty($idProveedor)){
            $cat = $this->dameCategorias($idProveedor);
            if(count($cat)){
                $totalidad = array();
                foreach($cat as $val){
                    foreach($this->hijosCategoria($val['idCategoria']) as $idC){
                        $totalidad[] = $idC;
                    }
                }
                $this->_bd->sql->consulta(sprintf("select fechaEntrega, idCarrito, gastoEnvio from carrito where fechaEntrega<='%s' and fechaEntrega>='%s'",
                                                  $feFin, $feIni), "dameProductosVendidos($idProveedor, $feIni, $feFin) Proveedores.class.php");
                $datos = array();
                while($fila = $this->_bd->sql->extraerRegistro())
                    $datos[] = $fila;
                foreach($datos as $val){
                    $this->_bd->sql->consulta(sprintf("select cc.idProducto, cc.cantidad, cc.precioUnitario, p.idCategoria, p.nombre from carritoContenido cc ".
                                                      "inner join producto p on p.idProducto=cc.idProducto where cc.idCarrito=%d", $val['idCarrito']),
                                              "dameProductosVendidos($idProveedor, $feIni, $feFin) Proveedores.class.php");
                    while($fila = $this->_bd->sql->extraerRegistro()){
                        if(in_array($fila['idCategoria'], $totalidad)){ /// producto vendido del proveedor
                            $acumulado += $fila['cantidad'] * $fila['precioUnitario'];
                            $res[] = array(
                                "producto" => $fila['nombre'],
                                "fechaVenta" => $val['fechaEntrega'],
                                "fechaTime" => strtotime($val['fechaEntrega']),
                                "gastoEnvio" => $val['gastoEnvio'],
                                "precioUnitario" => number_format($fila['precioUnitario'], 2),
                                "cantidad" => $fila['cantidad'],
                                "subtotal" => number_format($fila['cantidad'] * $fila['precioUnitario'], 2),
                                "acum" => number_format($acumulado, 2)
                            );
                        }
                    }
                }
            }
        }
        return $res;
    }
}