<?php
class Archivo{
    private $_bd;

    function __construct($permisos){
        require_once("permisos.php");
        $this->_bd=new Permisos($permisos);
    }
    
    public function agregaArchivo($dir, $nombre){
        $nombre = $this->_bd->valida->validaCampoTexto($nombre);
        $dir = $this->_bd->valida->validaCampoTexto($dir);
        if(!empty($nombre) && !empty($dir)){
            $consulta = sprintf("insert into archivo (url, nombre) values (%s, %s)",
                                $this->_bd->valida->SanitizarQuery($dir),
                                $this->_bd->valida->SanitizarQuery($nombre));
            $this->_bd->sql->consulta($consulta, "agregaArchivo($dir, $nombre) archivo.class.php");
            if($this->_bd->sql->filasAfectadas() == 1)
                return $this->_bd->sql->ultimoId();
        }
        return false;
    }
    
    public function agregaImagen($dir, $idProducto){
        $idProducto = $this->_bd->valida->validaCampoNumerico($idProducto);
        $dir = $this->_bd->valida->validaCampoTexto($dir);
        if(!empty($idProducto) && !empty($dir)){
            $consulta = sprintf("insert into imagen (url, idProducto) values (%s, %d)",
                                $this->_bd->valida->SanitizarQuery($dir), $idProducto);
            $this->_bd->sql->consulta($consulta, "agregaImagen($dir, $idProducto) archivo.class.php");
            if($this->_bd->sql->filasAfectadas() == 1)
                return true;
        }
        return false;
    }
    
    public function enlazaArchivo($idProducto, $idArchivo){
        $idProducto = $this->_bd->valida->validaCampoNumerico($idProducto);
        $idArchivo = $this->_bd->valida->validaCampoNumerico($idArchivo);
        if(!empty($idProducto) && !empty($idArchivo)){
            $consulta = sprintf("insert into enlaceProductoArchivo (idProducto, idArchivo) values (%d, %d)", $idProducto, $idArchivo);
            $this->_bd->sql->consulta($consulta, "enlazaArchivo($idProducto, $idArchivo) archivo.class.php");
            if($this->_bd->sql->filasAfectadas() == 1)
                return true;
        }
        return false;
    }
    
    public function getArchivosByIdProducto($idProducto){
        $idProducto = $this->_bd->valida->validaCampoNumerico($idProducto);
        $respuesta = array();
        if(!empty($idProducto)){
            $consulta = sprintf("select idArchivo from enlaceProductoArchivo where idProducto=%d", $idProducto);
            $this->_bd->sql->consulta($consulta, "getArchivosByIdProducto($idProducto) archivo.class.php");
            $dato = array();
            while($fila = $this->_bd->sql->extraerRegistro())
                $dato[] = $fila['idArchivo'];
            foreach($dato as $val){
                $consulta = sprintf("select url, nombre, idArchivo from archivo where idArchivo=%d and activo=1", $val);
                $this->_bd->sql->consulta($consulta, "getArchivosByIdProducto($idProducto) archivo.class.php");
                if($this->_bd->sql->filasAfectadas() == 1)
                    $respuesta[] = $this->_bd->sql->extraerRegistro();
            }
        }
        return $respuesta;
    }
    
    public function getImagenesByIdProducto($idProducto, $activo=null){
        $idProducto = $this->_bd->valida->validaCampoNumerico($idProducto);
        $respuesta = array();
        if(!empty($idProducto)){
            $subcon = "";
            if($activo != null){
                $subcon = sprintf(" and activo=%d", $activo);
            }
            $consulta = sprintf("select idImagen, url, activo from imagen where idProducto=%d %s order by activo desc", $idProducto, $subcon);
            $this->_bd->sql->consulta($consulta, "getImagenesByIdProducto($idProducto) archivo.class.php");
            while($fila = $this->_bd->sql->extraerRegistro())
                $respuesta[] = $fila;
        }
        return $respuesta;
    }
    
    public function getCountImagenes($idProducto){
        $idProducto = $this->_bd->valida->validaCampoNumerico($idProducto);
        if(!empty($idProducto)){
            $consulta = sprintf("select count(*) from imagen where idProducto=%d and activo=1", $idProducto);
            $this->_bd->sql->consulta($consulta, "getCountImagenes($idProducto) archivo.class.php");
            $dato = $this->_bd->sql->extraerRegistro();
            return $dato['count(*)'];
        }
        return 0;
    }
    
    public function getCountArchivos($idProducto){
        $idProducto = $this->_bd->valida->validaCampoNumerico($idProducto);
        if(!empty($idProducto)){
            $consulta = sprintf("select count(*) from enlaceProductoArchivo where idProducto=%d", $idProducto);
            $this->_bd->sql->consulta($consulta, "getCountArchivos($idProducto) archivo.class.php");
            $dato = $this->_bd->sql->extraerRegistro();
            return $dato['count(*)'];
        }
        return 0;
    }
    
    public function activadorImagen($idImagen, $estado){
        $idImagen = $this->_bd->valida->validaCampoNumerico($idImagen);
        $estado = $this->_bd->valida->validaCampoNumerico($estado);
        if(!empty($idImagen)){
            $consulta = sprintf("update imagen set activo=%d where idImagen=%d", $estado, $idImagen);
            $this->_bd->sql->consulta($consulta, "activadorImagen($idImagen, $estado) archivo.class.php");
            return true;
        }
        return false;
    }
    
    public function busca($nomb){
        $nomb = $this->_bd->valida->validaCampoTexto($nomb);
        $datos = array();
        if(!empty($nomb)){
            $nomb = "%".$nomb."%";
            $consulta = sprintf("select idArchivo, nombre, url from archivo where nombre like '%s'", $nomb);
            $this->_bd->sql->consulta($consulta, "busca($nomb) archivo.class.php");
            while($fila = $this->_bd->sql->extraerRegistro()){
                $datos[] = $fila;
            }
        }
        return $datos;
    }
    
    public function quitaEnlace($idProducto, $idArchivo){
        $idProducto = $this->_bd->valida->validaCampoNumerico($idProducto);
        $idArchivo = $this->_bd->valida->validaCampoNumerico($idArchivo);
        if(!empty($idProducto) && !empty($idArchivo)){
            $consulta = sprintf("delete from enlaceProductoArchivo where idProducto=%d and idArchivo=%d", $idProducto, $idArchivo);
            $this->_bd->sql->consulta($consulta, "quitaEnlace($idProducto, $idArchivo) archivo.class.php");
            if($this->_bd->sql->filasAfectadas() >= 1)
                return true;
        }
        return false;
    }
}