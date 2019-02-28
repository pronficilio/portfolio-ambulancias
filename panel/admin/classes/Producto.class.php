<?php 	
class Producto{
    private $_bd;

    function __construct($permisos){
        require_once("permisos.php");
        $this->_bd=new Permisos($permisos);
    }
    
    public function visto($idProducto){
        $idProducto = $this->_bd->valida->validaCampoNumerico($idProducto);
        if(!empty($idProducto)){
            $this->_bd->sql->consulta("update producto set visto=visto+1 where idProducto=".$idProducto,
                                      "visto($idProducto) Producto.class.php");
        }
    }
    
    /**
        *Permite agregar un nuevo producto
        *a la base de datos
        *
        *<code>public function agregarProducto($nombre, $idCategoria, $descripcion, $precio, $enInventario, $max, $min, $disponible)</code>
        *
        *@param string $nombre El nombre del producto
        *@param int $precio El precio del producto
        *
        *
        *@return {int} En caso de haber insertado con éxito regresa el id del producto. 
        *En caso contrario regresa false
    */
    public function agregarProducto($nombre, $idCategoria, $descripcion, $precio, $enInventario, $max, $min, $disponible){
        $nombre = $this->_bd->valida->validaCampoTexto($nombre);
        $idCategoria = $this->_bd->valida->validaCampoNumerico($idCategoria);
        $descripcion = $this->_bd->valida->validaCampoTexto($descripcion);
        $precio = $this->_bd->valida->validaCampoNumerico($precio);
        $enInventario = $this->_bd->valida->validaCampoNumerico($enInventario);
        $max = $this->_bd->valida->validaCampoNumerico($max);
        $min = $this->_bd->valida->validaCampoNumerico($min);
        $disponible = $this->_bd->valida->validaCampoNumerico($disponible);
        if(empty($max))
            $max = "NULL";
        if(empty($min))
            $min = "NULL";
        if(empty($enInventario))
            $enInventario = "NULL";

        if(!empty($nombre) && !empty($descripcion)){
            $query = sprintf("insert into producto (nombre, idCategoria, descripcion, precio, enInventario, maximo, minimo, disponible) ".
                             "values (%s, %d, %s, %d, %s, %s, %s, %d)", $this->_bd->valida->SanitizarQuery($nombre), $idCategoria,
                             $this->_bd->valida->SanitizarQuery($descripcion), $precio, $enInventario, $max, $min, $disponible);
            $this->_bd->sql->consulta($query, "agregarProducto($nombre, $idCategoria, $descripcion, $precio, $enInventario, $max, $min, $disponible) producto.class.php");

            if($this->_bd->sql->filasAfectadas() == 1){
                $idProducto = $this->_bd->sql->ultimoId();
                $perma = $this->_bd->creaPermalink($nombre)."-".$idProducto;
                $consulta = sprintf("update producto set permalink='%s' where idProducto=%d", $perma, $idProducto);
                $this->_bd->sql->consulta($consulta, "agregarProducto($nombre, $idCategoria, $precio, etc) Producto.class.php");
                return $idProducto;
            }
        }
        return false;
    }
    
    public function editaIva($idProducto, $iva){
        $idProducto = $this->_bd->valida->validaCampoNumerico($idProducto);
        $iva = $this->_bd->valida->validaCampoNumerico($iva);
        if(!empty($idProducto)){
            $this->_bd->sql->consulta(sprintf("update producto set iva=%d where idProducto=%d",
                                      $iva, $idProducto), "editaIva($idProducto, $iva) Producto.class.php");
        }
    }
    
    public function editarProducto($idProducto, $nombre, $idCategoria, $descripcion, $precio, $enInventario, $max, $min, $disponible){
        $idProducto = $this->_bd->valida->validaCampoNumerico($idProducto);
        $nombre = $this->_bd->valida->validaCampoTexto($nombre);
        $idCategoria = $this->_bd->valida->validaCampoNumerico($idCategoria);
        $descripcion = $this->_bd->valida->validaCampoTexto($descripcion);
        $precio = $this->_bd->valida->validaCampoNumerico($precio);
        $max = $this->_bd->valida->validaCampoNumerico($max);
        $min = $this->_bd->valida->validaCampoNumerico($min);
        $enInventario = $this->_bd->valida->validaCampoNumerico($enInventario);
        $disponible = $this->_bd->valida->validaCampoNumerico($disponible);
        
        if(empty($max))
            $max = "NULL";
        if(empty($min))
            $min = "NULL";
        if(empty($enInventario))
            $enInventario = "NULL";

        if(!empty($nombre) && !empty($descripcion)){
            $query = sprintf("update producto set nombre=%s, idCategoria=%d, descripcion=%s, precio=%d, enInventario=%s, maximo=%s, ".
                             "minimo=%s, disponible=%d where idProducto=%d", $this->_bd->valida->SanitizarQuery($nombre),
                             $idCategoria, $this->_bd->valida->SanitizarQuery($descripcion), $precio, $enInventario, $max,
                             $min, $disponible, $idProducto);
            $this->_bd->sql->consulta($query, "editarProducto($idProducto, $nombre, $idCategoria, $descripcion, $precio, ".
                                      "$enInventario, $max, $min, $disponible) producto.class.php");

            if($this->_bd->sql->lastError() == ""){
                $perma = $this->_bd->creaPermalink($nombre)."-".$idProducto;
                $consulta = sprintf("update producto set permalink='%s' where idProducto=%d", $perma, $idProducto);
                return true;
            }
        }
        return false;
    }
    
    public function getProductoJson($opciones){
        $busqueda = $this->_bd->valida->validaCampoTexto($opciones['search']['value']);
        $pag = $this->_bd->valida->validaCampoNumerico($opciones['start']);
        $cant = $this->_bd->valida->validaCampoNumerico($opciones['length']);
        $draw = $this->_bd->valida->validaCampoNumerico($opciones['draw']);
        
        $consulta = "select count(*) from producto where activo=1";        
        $this->_bd->sql->consulta($consulta, "getProductoJson() producto.class.php");
        $cuantos = $this->_bd->sql->extraerRegistro();
        $total = $cuantos['count(*)'];
        $filtrado = $total;
        if(!empty($busqueda)){
            $busqueda = sprintf(" and producto.nombre like '%%%s%%'", $busqueda);
            $this->_bd->sql->consulta($consulta.$busqueda, "getProductoJson() producto.class.php");
            $cuantos = $this->_bd->sql->extraerRegistro();
            $filtrado = $cuantos['count(*)'];
        }
        
        $res['draw'] = $draw;
        $res['recordsTotal'] = $total;
        $res['recordsFiltered'] = $filtrado;
        
        $consult = "select producto.nombre, categoria.nombre as cat, precio, precioOutlet, enInventario, idProducto, descripcion, ".
                   "maximo, minimo, disponible from producto left join categoria on categoria.idCategoria=producto.idCategoria ".
                   "where producto.activo=1".$busqueda;
        if(!empty($opciones['order'][0]['column']))
            $consult .= " order by ".$opciones['order'][0]['column']." ".$opciones['order'][0]['dir'];
        $consult .= sprintf(" limit %d, %d", $pag, $cant);
        $this->_bd->sql->consulta($consult, "getProductoJson() producto.class.php");
        $res['data'] = array();
        while($fila = $this->_bd->sql->extraerRegistro()){
            $res['data'][] = $fila;
        }
        return $res;
    }

    public function getSalidaProductoJson($opciones, $fi, $ff){
        $busqueda = $this->_bd->valida->validaCampoTexto($opciones['search']['value']);
        $pag = $this->_bd->valida->validaCampoNumerico($opciones['start']);
        $cant = $this->_bd->valida->validaCampoNumerico($opciones['length']);
        $draw = $this->_bd->valida->validaCampoNumerico($opciones['draw']);
        $consulta = "select count(*) from salida where 1";        
        if(!empty($fi) && !empty($ff)){
            $consulta .= " and tiempo>='".$fi."' and tiempo<='".$ff." 23:59:59'";
        }
        $this->_bd->sql->consulta($consulta, "getProductoJson() producto.class.php");
        $cuantos = $this->_bd->sql->extraerRegistro();
        $total = $cuantos['count(*)'];
        $filtrado = $total;
        if(!empty($busqueda)){
            $busqueda = sprintf(" and producto.nombre like '%%%s%%'", $busqueda);
            $this->_bd->sql->consulta($consulta.$busqueda, "getProductoJson() producto.class.php");
            $cuantos = $this->_bd->sql->extraerRegistro();
            $filtrado = $cuantos['count(*)'];
        }
        $res['draw'] = $draw;
        $res['recordsTotal'] = $total;
        $res['recordsFiltered'] = $filtrado;
        
        $consult = "select p.nombre, descripcion, cantidad, nota, tiempo from salida s ".
            "inner join producto p on p.idProducto=s.idProducto where 1";
        if(!empty($fi) && !empty($ff)){
            $consult .= " and tiempo>='".$fi."' and tiempo<='".$ff." 23:59:59'";
        }
        if(!empty($opciones['order'][0]['column']))
            $consult .= " order by ".$opciones['order'][0]['column']." ".$opciones['order'][0]['dir'];
        if($cant != -1)
            $consult .= sprintf(" limit %d, %d", $pag, $cant);
        $this->_bd->sql->consulta($consult, "getProductoJson() producto.class.php");
        $res['data'] = array();
        while($fila = $this->_bd->sql->extraerRegistro()){
            $res['data'][] = $fila;
        }
        return $res;
    }
    
    private function cuentaDiferentesCaracteristicas($who, $idProducto){
        $consulta = "select count(distinct ".$who.") as tot from caracteristica where idProducto=".$idProducto;
        $this->_bd->sql->consulta($consulta, "cuentaDiferentesCaracteristicas($who, $idProducto)");
        $dato = $this->_bd->sql->extraerRegistro();
        return $dato['tot'];
    }
    
    /*
    * Regresa los datos generales del producto: nombre, idCategoria, precio, precioOutlet, idProducto, descripcion, maximo, minimo, disponible
    * y enInventario
    */
    public function getProducto($idProducto){
        $idProducto = $this->_bd->valida->validaCampoNumerico($idProducto);
        if(!empty($idProducto)){
            $consulta = "select nombre, idCategoria, precio, precioOutlet, idProducto, descripcion, enInventario, maximo, minimo, disponible, iva ";
            $consulta .= sprintf("from producto where activo=1 and idProducto=%d", $idProducto);
            $this->_bd->sql->consulta($consulta, "getProducto($idProducto) producto.class.php");
            $dato = $this->_bd->sql->extraerRegistro();
            return $dato;
        }
        return false;
    }
    
    public function eliminaProducto($idProducto){
        $idProducto = $this->_bd->valida->validaCampoNumerico($idProducto);
        if(!empty($idProducto)){
            $consulta = sprintf("update producto set activo=0, permalink=null, barcode=null where idProducto=%d", $idProducto);
            $this->_bd->sql->consulta($consulta, "eliminaProducto($idProducto) producto.class.php");
        }
    }
    public function eliminaTodo(){
        $this->_bd->sql->consulta("select idProducto from producto where activo=1");
        $datos = array();
        while($fila = $this->_bd->sql->extraerRegistro())
            $datos[] = $fila['idProducto'];
        foreach($datos as $v)
            $this->eliminaProducto($v);
    }
    
    public function busca($nomb){
        $nomb = $this->_bd->valida->validaCampoTexto($nomb);
        $datos = array();
        if(!empty($nomb)){
            $nomb = "%".$nomb."%";
            $consulta = sprintf("select idProducto, nombre from producto where nombre like '%s' and activo=1", $nomb);
            $this->_bd->sql->consulta($consulta, "busca($nomb) producto.class.php");
            while($fila = $this->_bd->sql->extraerRegistro()){
                $datos[] = $fila;
            }
            foreach($datos as $ind=>$val){
                $datos[$ind]['precio'] = $this->damePrecioReal($val['idProducto']);
            }
        }
        return $datos;
    }
    
    public function hijosCategoria($idCat){
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
    
    public function dameProductos($pag, $idCat=null, $cant=6, $orden="desc"){
        $pag = $this->_bd->valida->validaCampoNumerico($pag);
        $idCat = $this->_bd->valida->validaCampoNumerico($idCat);
        $cant = $this->_bd->valida->validaCampoNumerico($cant);
        $res = array();
        if(!empty($cant)){
            $consulta = "select idProducto, nombre, precio, precioOutlet, idCategoria, descripcion, disponible, iva from producto where activo=1 ";
            $cantidad = "select count(idProducto) from producto where activo=1 ";
            if(!empty($idCat)){
                $quienes = implode(" or idCategoria=", $this->hijosCategoria($idCat));
                $consulta .= "and (idCategoria=".$quienes.") ";
                $cantidad .= "and (idCategoria=".$quienes.") ";
            }
            $consulta .= "order by precio ".$orden;
            $consulta .= sprintf(" limit %d, %d", $pag*$cant, $cant);
            $this->_bd->sql->consulta($consulta, "dameProductos($pag, $idCat, $cant) Producto.class.php");
            while($fila = $this->_bd->sql->extraerRegistro()){
                $res[] = $fila;
            }
            include("Archivo.class.php");
            $arch = new Archivo("Producto.class.php");
            foreach($res as $ind=>$val){
                $res[$ind]['arch'] = $arch->getArchivosByIdProducto($val['idProducto']);
                $res[$ind]['img'] = $arch->getImagenesByIdProducto($val['idProducto']);
            }
            $this->_bd->sql->consulta($cantidad, "dameProductos($pag, $idCat, $cant) Producto.class.php");
            $cant = $this->_bd->sql->extraerRegistro();
            $cant = $cant['count(idProducto)'];
            if($cant != 0)
                $res[0]['cantidadConsulta'] = $cant;
        }
        return $res;
    }
    
    public function dameProductosRandom($cant = 6){
        $consulta = "SELECT idProducto, nombre, precio, precioOutlet, idCategoria, descripcion, disponible FROM producto AS r1 JOIN ".
                    "(SELECT CEIL(RAND() * (SELECT MAX(idProducto) FROM producto)) AS id) AS r2 ".
                    "WHERE r1.idProducto >= r2.id and activo=1 ORDER BY r1.idProducto ASC LIMIT 1";
        $datos = 0;
        $antiInfinito = $cant * 6;
        $arrRes = array();
        include("Archivo.class.php");
        $arch = new Archivo("Producto.class.php");
        while($datos < $cant && $antiInfinito > 0){
            $this->_bd->sql->consulta($consulta, "dameProductosRandom($cant) Producto.class.php");
            $res = $this->_bd->sql->extraerRegistro();
            $yaEsta = false;
            foreach($arrRes as $val)
                if($val['idProducto'] == $res['idProducto'])
                    $yaEsta = true;
            if(!$yaEsta){
                $res['arch'] = $arch->getArchivosByIdProducto($res['idProducto']);
                $res['img'] = $arch->getImagenesByIdProducto($res['idProducto']);
                $arrRes[] = $res;
                $datos++;
            }
            $antiInfinito--;
        }
        return $arrRes;
    }
    
    public function dameProductosOutlet($pag, $idCat=null, $cant=6){
        $pag = $this->_bd->valida->validaCampoNumerico($pag);
        $idCat = $this->_bd->valida->validaCampoNumerico($idCat);
        $cant = $this->_bd->valida->validaCampoNumerico($cant);
        $res = array();
        if(!empty($cant)){
            $consulta = "select idProducto, nombre, precio, precioOutlet, idCategoria, descripcion, disponible from producto ".
                        "where activo=1 and precioOutlet is not null and precioOutlet != 0 ";
            $cantidad = "select count(idProducto) from producto where activo=1 and precioOutlet is not null and precioOutlet != 0 ";
            if(!empty($idCat)){
                $quienes = implode(" or idCategoria=", $this->hijosCategoria($idCat));
                $consulta .= "and (idCategoria=".$quienes.") ";
                $cantidad .= "and (idCategoria=".$quienes.") ";
            }
            $consulta .= "order by idProducto desc";
            $consulta .= sprintf(" limit %d, %d", $pag*$cant, $cant);
            $this->_bd->sql->consulta($consulta, "dameProductosOutlet($pag, $idCat, $cant) Producto.class.php");
            while($fila = $this->_bd->sql->extraerRegistro()){
                $res[] = $fila;
            }
            include("Archivo.class.php");
            $arch = new Archivo("Producto.class.php");
            foreach($res as $ind=>$val){
                $res[$ind]['arch'] = $arch->getArchivosByIdProducto($val['idProducto']);
                $res[$ind]['img'] = $arch->getImagenesByIdProducto($val['idProducto']);
            }
            $this->_bd->sql->consulta($cantidad, "dameProductosOutlet($pag, $idCat, $cant) Producto.class.php");
            $cant = $this->_bd->sql->extraerRegistro();
            $cant = $cant['count(idProducto)'];
            if($cant != 0)
                $res[0]['cantidadConsulta'] = $cant;
        }
        return $res;
    }
    
    public function destaca($idProducto){
        $idProducto = $this->_bd->valida->validaCampoNumerico($idProducto);
        if(!empty($idProducto)){
            $consulta = sprintf("insert into destacados (idProducto) values (%d)", $idProducto);
            $this->_bd->sql->consulta($consulta, "destaca($idProducto) Producto.class.php");
            if($this->_bd->sql->lastError() == "")
                return true;
        }
        return false;
    }
    
    public function dameDestacados(){
        $consulta = "select destacados.idProducto, idCategoria, nombre from destacados ".
                    "inner join producto on producto.idProducto=destacados.idProducto order by idDestacados desc limit 3";
        $this->_bd->sql->consulta($consulta, "dameDestacados() Producto.class.php");
        $dat = array();
        while($fila = $this->_bd->sql->extraerRegistro()){
            $dat[] = $fila;
        }
        return $dat;
    }
    
    public function modificaOutlet($idProducto, $precio){
        $idProducto = $this->_bd->valida->validaCampoNumerico($idProducto);
        $precio = $this->_bd->valida->validaCampoNumerico($precio);
        if(!empty($idProducto)){
            $consulta = sprintf("update producto set precioOutlet=%d where idProducto=%d", $precio, $idProducto);
            $this->_bd->sql->consulta($consulta, "modificaOutlet($idProducto, $precio) Producto.class.php");
            $this->actualizaPrecios($idProducto);
        }
    }
    
    public function damePrecioReal($idProducto){
        $consulta = sprintf("select precio, precioOutlet, iva from producto where idProducto=%d", $idProducto);
        $this->_bd->sql->consulta($consulta, "damePrecioReal($idProducto) Producto.class.php");
        $dato = $this->_bd->sql->extraerRegistro();
        if(!empty($dato['precioOutlet']))
            return $dato['precioOutlet']*($dato['iva']!="0"?(intval($this->_bd->opcion("iva"))/100)+1:1);
        return $dato['precio']*($dato['iva']!="0"?(intval($this->_bd->opcion("iva"))/100)+1:1);
    }
    
    public function actualizaPrecios($idProducto){
        $precio = $this->damePrecioReal($idProducto);
        $consulta = "select idCarrito from carrito where cerrado=0";
        $this->_bd->sql->consulta($consulta, "actualizaPrecios($idProducto) Producto.class.php");
        $carros = array();
        while($fila = $this->_bd->sql->extraerRegistro()){
            $carros[] = $fila['idCarrito'];
        }
        foreach($carros as $val){
            $consulta = sprintf("update carritoContenido set precioUnitario=%d where idCarrito=%d and idProducto=%d", $precio, $val, $idProducto);
            $this->_bd->sql->consulta($consulta, "actualizaPrecios($idProducto) Producto.class.php");
        }
    }
    public function salidaInventario($idProducto, $cantidad, $nota, $idU){
        $idProducto = $this->_bd->valida->validaCampoNumerico($idProducto);
        $cantidad = $this->_bd->valida->validaCampoNumerico($cantidad);
        $idU = $this->_bd->valida->validaCampoNumerico($idU);
        $nota = $this->_bd->valida->validaCampoTexto($nota);
        if(!empty($idProducto) && !empty($cantidad)){
            $this->_bd->sql->consulta("select enInventario from producto where idProducto=".$idProducto);
            $cantidadAnt = $this->_bd->sql->extraerRegistro()['enInventario'];    
            $this->_bd->sql->consulta(sprintf("insert into salida (idProducto, idUsuario, cantidad, cantidadAnt, nota) value (%d, %d, %d, %d, '%s')", $idProducto, $idU, $cantidad, $cantidadAnt, $nota));
            $this->_bd->sql->consulta("update producto set enInventario=enInventario-".$cantidad." where idProducto=".$idProducto);
            return true;
        }
        return false;
    }
    public function actualizaInventario($idProducto, $enInventario){
        $idProducto = $this->_bd->valida->validaCampoNumerico($idProducto);
        $enInventario = $this->_bd->valida->validaCampoNumerico($enInventario);
        if(!empty($idProducto)){
            if(empty($enInventario))
                $enInventario = "NULL";
            $consulta = sprintf("update producto set enInventario=%s where idProducto=%d", $enInventario, $idProducto);
            $this->_bd->sql->consulta($consulta, "actualizaInventario($idProducto, $enInventario) Producto.class.php");
        }
    }
    
    /**
    * Regresa el ID de un producto por su nombre
    *
    *
    *<code>public function verificaProducto($nombre)</code>
    *
    *@param string $nombre Producto a buscar
    *
    *@return {int} El idProducto si existe, 0 si no.
    */
    public function verificaProducto($nombre){
        $nombre = $this->_bd->valida->validaCampoTexto($nombre);
        if(!empty($nombre)){
            $consulta = sprintf("select idProducto from producto where permalink='%s' and activo=1", $nombre);
            $this->_bd->sql->consulta($consulta, "verificaProducto($nombre)");
            $dato = $this->_bd->sql->extraerRegistro();
            return $dato['idProducto'];
        }
        return 0;
    }
}