<?php 	
class Barcode{
    private $_bd;

    function __construct($permisos){
        require_once("permisos.php");
        $this->_bd=new Permisos($permisos);
    }

    /**
     * Verifica si un codigo de barras esta asignado a un producto o no
     * @param  string  $barcode Código de barras
     * @return integer Falso en caso de no existir, idProducto en caso contrario
     */
    public function existeBarcode($barcode){
        $barcode = $this->_bd->valida->SanitizarQuery($barcode);
        if(!empty($barcode)){
            $consulta = sprintf("select idProducto from producto where barcode=%s", $barcode);
            $this->_bd->sql->consulta($consulta, "existeBarcode($barcode) Barcode.class.php");
            if($this->_bd->sql->filasAfectadas()){
                $dato = $this->_bd->sql->extraerRegistro();
                return $dato['idProducto'];
            }
        }
        return false;
    }
    
    /**
     * Asigna un código de barras a un producto
     * @param  integer $idProducto Identificador del producto
     * @param  string  $barcode    Código de barras
     * @return integer 1 en caso de consulta realizada con exito, 0 en caso de no generar resultados y -1 en caso de error ($barcode en uso)
     */
    public function asignaBarcode($idProducto, $barcode){
        $idProducto = $this->_bd->valida->validaCampoNumerico($idProducto);
        $barcode = $this->_bd->valida->SanitizarQuery($barcode);
        if(!empty($idProducto) && !empty($barcode)){
            $consulta = sprintf("update producto set barcode=%s where idProducto=%d", $barcode, $idProducto);
            $this->_bd->sql->consulta($consulta, "asignaBarcode($idProducto, $barcode) Barcode.class.php");
            if($this->_bd->sql->filasAfectadas()){
                return 1;
            }else{
                if($this->_bd->sql->lastError() != "")
                    return -1;
                return 2;
            }
        }
        return 0;
    }
    
    public function eliminaBarcode($idProducto){
        $idProducto = $this->_bd->valida->validaCampoNumerico($idProducto);
        if(!empty($idProducto)){
            $consulta = sprintf("update producto set barcode=null where idProducto=%d", $idProducto);
            $this->_bd->sql->consulta($consulta, "eliminaBarcode($idProducto) Barcode.class.php");
            if($this->_bd->sql->filasAfectadas()){
                return 1;
            }else{
                if($this->_bd->sql->lastError() == "")
                    return 0;
            }
        }
        return -1;
    }
}

