<?php 
	
class Categoria{
    public $_bd;

    function __construct($permisos){
        require_once("permisos.php");
        $this->_bd=new Permisos($permisos);
    }
    
    /**
        * Agrega una categoría. Si se le manda un idCategoría, lo hace su hijo
        *
        *
        *<code>public function agregaCategoria($nombre [, $idCategoria])</code>
        *
        *@param string $nombre Nombre de la categoría/subcategoría
        *
        *<hr>Opcional:
        *@param int $idCategoria Campo opcional que se hace padre de la nueva categoria (subcategoria)
        *
        *@return {int} idCategoria de la nueva categoría ingresada, false en caso de error
    */
    public function agregaCategoria($nombre, $idCategoria=null){
        $nombre = $this->_bd->valida->validaCampoTexto($nombre);
        $idCategoria = $this->_bd->valida->validaCampoNumerico($idCategoria);
        if(!empty($nombre)){
            $consulta = "insert into categoria (nombre, permalink";
            $perma = $this->_bd->creaPermalink($nombre);
            if(!empty($idCategoria))
                $consulta .= ", enlace";
            $consulta .= sprintf(") values ('%s', '%s'", $nombre, $perma);
            if(!empty($idCategoria))
                $consulta .= sprintf(", %d", $idCategoria);
            $consulta .= ")";
            $this->_bd->sql->consulta($consulta, "agregaCategoria($nombre, $idCategoria) categoria.class.php");          
            if($this->_bd->sql->filasAfectadas() == 1)
                return true;
        }
        return false;
    }
    
    public function agregaDescuento($nombre, $descuento){
        $nombre = $this->_bd->valida->validaCampoTexto($nombre);
        $descuento = $this->_bd->valida->validaCampoNumerico($descuento);
        if(!empty($nombre) && !empty($descuento) && $descuento > 0 && $descuento <= 100){
            $consulta = sprintf("insert into descuento (nombre, valor) values (%s, %d)",
                                $this->_bd->valida->SanitizarQuery($nombre), $descuento);
            $this->_bd->sql->consulta($consulta, "agregaDescuento($nombre, $descuento) categoria.class.php");
            return $this->_bd->sql->ultimoId();
        }
        return false;
    }
    
    public function modificarCategoria($idCategoria, $nombre){
        $idCategoria = $this->_bd->valida->validaCampoNumerico($idCategoria);
        $nombre = $this->_bd->valida->validaCampoTexto($nombre);
        if(!empty($idCategoria) && !empty($nombre)){
            $perma = $this->_bd->creaPermalink($nombre);
            
            $consulta = sprintf("update categoria set nombre='%s', permalink='%s' where idCategoria=%d",
                                $nombre, $perma, $idCategoria);
            $this->_bd->sql->consulta($consulta, "modificarCategoria($idCategoria, $nombre) categoria.class.php");
            if($this->_bd->sql->lastError()==""){
                return true;
            }
        }
        return false;
    }
    
    public function modificarDescuento($idDescuento, $nombre, $descuento){
        $idDescuento = $this->_bd->valida->validaCampoNumerico($idDescuento);
        $descuento = $this->_bd->valida->validaCampoNumerico($descuento);
        $nombre = $this->_bd->valida->validaCampoTexto($nombre);
        if(!empty($idDescuento) && !empty($nombre) && !empty($descuento) && $descuento > 0 && $descuento <= 100){
            $consulta = sprintf("update descuento set nombre='%s', valor=%d where idDescuento=%d", $nombre, $descuento, $idDescuento);
            $this->_bd->sql->consulta($consulta, "modificarDescuento($idDescuento, $nombre, $descuento) categoria.class.php");
            if($this->_bd->sql->lastError()==""){
                return true;
            }
        }
        return false;
    }
    
    public function getDescuento(){
        $consulta = "select idDescuento, nombre, valor from descuento where activo=1";
        $this->_bd->sql->consulta($consulta, "getDescuento() categoria.class.php");
        $res = array();
        while($fila = $this->_bd->sql->extraerRegistro())
            $res[] = $fila;
        return $res;
    }
    /**
        * Regresa un array con todas las categorías existentes
        *
        *
        *<code>public function getCategorias([$idCategoria])</code>
        *
        *@param int $idCategoria Campo opcional para regresar una categoria específica
        *
        *@return {array} Un array con idCategoria y nombre por cada elemento regresado
    */
    public function getCategorias($idCategoria=null){
        $idCategoria = $this->_bd->valida->validaCampoNumerico($idCategoria);
        $consulta = "select idCategoria, nombre from categoria where ";
        if(empty($idCategoria))
            $consulta .= "enlace is null";
        else
            $consulta .= sprintf("idCategoria=%d", $idCategoria);
        $consulta .= " and activo=1 order by nombre";
        $this->_bd->sql->consulta($consulta, "getCategorias($idCategoria) categoria.class.php");
        $datos = array();
        while($fila = $this->_bd->sql->extraerRegistro()){
            $datos[] = $fila;
        }
        return $datos;
    }
    
    public function getNombreCategoria($idCategoria){
        $idCategoria = $this->_bd->valida->validaCampoNumerico($idCategoria);
        if(!empty($idCategoria)){
            $consulta = sprintf("select nombre from categoria where idCategoria=%d and activo=1", $idCategoria);
            $this->_bd->sql->consulta($consulta, "getNombreCategoria($idCategoria) categoria.class.php");
            $dato = $this->_bd->sql->extraerRegistro();
            return $dato['nombre'];
        }
        return false;
    }
    
    /**
        * Regresa un array con las especificaciones que quiere datetable
        *
        *
        *<code>public function getCategorias()</code>
        *
        *@return {array} Un array con idCategoria y nombre por cada elemento regresado
    */
    public function getCategoriasJson($opciones){
        $busqueda = $this->_bd->valida->validaCampoTexto($opciones['search']['value']);
        $pag = $this->_bd->valida->validaCampoNumerico($opciones['start']);
        $cant = $this->_bd->valida->validaCampoNumerico($opciones['length']);
        $draw = $this->_bd->valida->validaCampoNumerico($opciones['draw']);
        
        $consulta = "select count(idCategoria) from categoria where activo=1 and enlace is null";        
        $this->_bd->sql->consulta($consulta, "getCategoriasJson() categoria.class.php");
        $cuantos = $this->_bd->sql->extraerRegistro();
        $total = $cuantos['count(idCategoria)'];
        $filtrado = $total;
        if(!empty($busqueda)){
            $busqueda = sprintf(" and nombre like '%%%s%%'", $busqueda);
            $this->_bd->sql->consulta($consulta.$busqueda, "getCategoriasJson() categoria.class.php");
            $cuantos = $this->_bd->sql->extraerRegistro();
            $filtrado = $cuantos['count(idCategoria)'];
        }
        $consult = "select idCategoria, nombre from categoria where activo=1 and enlace is null".$busqueda;
        $consult .= sprintf(" order by nombre limit %d, %d", $pag, $cant);
        $this->_bd->sql->consulta($consult, "getCategoriasJson() categoria.class.php");
        $res['draw'] = $draw;
        $res['recordsTotal'] = $total;
        $res['recordsFiltered'] = $filtrado;
        $datos = array();
        while($fila = $this->_bd->sql->extraerRegistro()){
            $datos[] = $fila;
        }
        $res['data'] = $datos;
        return $res;
    }
    
    /**
        * Regresa un array con todas las categorias y subcategorías existentes
        *
        *
        *<code>public function getSubcategoriasFullJson()</code>
        *
        *
        *@return {array} Un array con idCategoria y nombre por cada elemento regresado
    */
    public function getSubcategoriasJson($opciones){
        $busqueda = $this->_bd->valida->validaCampoTexto($opciones['search']['value']);
        $pag = $this->_bd->valida->validaCampoNumerico($opciones['start']);
        $cant = $this->_bd->valida->validaCampoNumerico($opciones['length']);
        $draw = $this->_bd->valida->validaCampoNumerico($opciones['draw']);
        
        $consulta = "select count(idCategoria) from categoria where activo=1 and enlace is not null";        
        $this->_bd->sql->consulta($consulta, "getSubcategoriasFullJson() categoria.class.php");
        $cuantos = $this->_bd->sql->extraerRegistro();
        $total = $cuantos['count(idCategoria)'];
        $filtrado = $total;
        if(!empty($busqueda)){
            $busqueda = sprintf(" and nombre like '%%%s%%'", $busqueda);
            $this->_bd->sql->consulta($consulta.$busqueda, "getSubcategoriasFullJson() categoria.class.php");
            $cuantos = $this->_bd->sql->extraerRegistro();
            $filtrado = $cuantos['count(idCategoria)'];
        }
        $res['draw'] = $draw;
        $res['recordsTotal'] = $total;
        $res['recordsFiltered'] = $filtrado;
        
        $consult = "select idCategoria, nombre, enlace from categoria where activo=1 and enlace is not null".$busqueda;
        $consult .= sprintf(" order by nombre limit %d, %d", $pag, $cant);
        $this->_bd->sql->consulta($consult, "getSubcategoriasFullJson() categoria.class.php");
        $datos = array();
        while($fila = $this->_bd->sql->extraerRegistro()){
            $datos[] = $fila;
        }
        $res['data'] = $datos;
        return $res;
    }
    
    public function getDescuentoJson($opciones){
        $busqueda = $this->_bd->valida->validaCampoTexto($opciones['search']['value']);
        $pag = $this->_bd->valida->validaCampoNumerico($opciones['start']);
        $cant = $this->_bd->valida->validaCampoNumerico($opciones['length']);
        $draw = $this->_bd->valida->validaCampoNumerico($opciones['draw']);
        
        $consulta = "select count(idDescuento) from descuento where activo=1";        
        $this->_bd->sql->consulta($consulta, "getDescuentoJson() categoria.class.php");
        $cuantos = $this->_bd->sql->extraerRegistro();
        $total = $cuantos["count(idDescuento)"];
        $filtrado = $total;
        if(!empty($busqueda)){
            $busqueda = sprintf(" and (nombre like '%%%s%%' or valor like '%%%s%%')", $busqueda, $busqueda);
            $this->_bd->sql->consulta($consulta.$busqueda, "getDescuentoJson() categoria.class.php");
            $cuantos = $this->_bd->sql->extraerRegistro();
            $filtrado = $cuantos["count(idDescuento)"];
        }
        $consult = "select idDescuento, nombre, valor from descuento where activo=1".$busqueda;
        $consult .= sprintf(" order by nombre limit %d, %d", $pag, $cant);
        $this->_bd->sql->consulta($consult, "getDescuentoJson() categoria.class.php");
        $res['draw'] = $draw;
        $res['recordsTotal'] = $total;
        $res['recordsFiltered'] = $filtrado;
        $datos = array();
        while($fila = $this->_bd->sql->extraerRegistro()){
            $datos[] = $fila;
        }
        $res['data'] = $datos;
        return $res;
    }
    
    public function getCategoriasFull(){
        $consulta = "select idCategoria, nombre, enlace from categoria where activo=1";
        $this->_bd->sql->consulta($consulta, "getCategoriasFull() categoria.class.php");
        $datos = array();
        while($fila = $this->_bd->sql->extraerRegistro()){
            $datos[$fila['idCategoria']] = $fila;
        }
        return $datos;
    }
    
    public function getHijos($idCategoria){
        $idCategoria = $this->_bd->valida->validaCampoNumerico($idCategoria);
        $datos = array();
        if(!empty($idCategoria)){
            $consulta = sprintf("select idCategoria, nombre, enlace from categoria where activo=1 and enlace=%d", $idCategoria);
            $this->_bd->sql->consulta($consulta, "getHijos($idCategoria) categoria.class.php");
            while($fila = $this->_bd->sql->extraerRegistro()){
                $datos[] = $fila;
            }
        }
        return $datos;
    }
    
    public function getLinaje($idCategoria){
        $linaje = array();
        $categoriaActual = $this->_bd->valida->validaCampoNumerico($idCategoria);
        do{
            $consulta = sprintf("select enlace from categoria where idCategoria=%d", $categoriaActual);
            $this->_bd->sql->consulta($consulta, "getLinaje($idCategoria) categoria.class.php");
            $dat = $this->_bd->sql->extraerRegistro();
            if(!empty($dat['enlace']))
                $linaje[] = $dat['enlace'];
            $categoriaActual = $dat['enlace'];
        }while(!empty($categoriaActual));
        $linaje = array_reverse($linaje);
        return $linaje;
    }
    
    public function eliminaCategoria($idCat){
        $idCat = $this->_bd->valida->validaCampoNumerico($idCat);
        if(!empty($idCat)){
            $hijos = $this->getHijos($idCat);
            if(count($hijos))
                foreach($hijos as $val)
                    $this->eliminaCategoria($val['idCategoria']);
            $consulta = sprintf("update categoria set activo=0, permalink=NULL where idCategoria=%d", $idCat);
            $consult = sprintf("update producto set idCategoria=NULL where idCategoria=%d", $idCat);
            $this->_bd->sql->consulta($consulta, "eliminaCategoria($idCat)");
            $this->_bd->sql->consulta($consult, "eliminaCategoria($idCat)");
        }
    }
    
    public function eliminarDescuento($idDescuento){
        $idDescuento = $this->_bd->valida->validaCampoNumerico($idDescuento);
        if(!empty($idDescuento)){
            $consulta = sprintf("update descuento set activo=0 where idDescuento=%d", $idDescuento);
            $this->_bd->sql->consulta($consulta, "eliminarDescuento($idDescuento)");
        }
    }
    /**
    * Regresa el ID de una categoria por su nombre
    *
    *
    *<code>public function verificaCategoria($nombre)</code>
    *
    *@param string $nombre Categoría a buscar
    *
    *@return {int} El idCategoria si existe, 0 si no.
    */
    public function verificaCategoria($nombre){
        $nombre = $this->_bd->valida->validaCampoTexto($nombre);
        if(!empty($nombre)){
            $consulta = sprintf("select idCategoria from categoria where permalink='%s' and activo=1", $nombre);
            $this->_bd->sql->consulta($consulta, "verificaCategoria($nombre)");
            $dato = $this->_bd->sql->extraerRegistro();
            return $dato['idCategoria'];
        }
        return 0;
    }
}
?>