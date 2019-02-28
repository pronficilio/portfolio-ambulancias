<?php
class Llamada{
    private $_bd;

    function __construct($permisos){
        require_once("permisos.php");
        $this->_bd = new Permisos($permisos);
    }


    /**
        *Permite registrar una llamada del cliente $idCliente en la base de datos
        *
        *
        *<code>public function agregarLlamada($idCliente, $fecha [,$comentario])</code>
        *
        *@param Int $idCliente El identificador del cliente del cuál se desea registrar los datos de su llamada
        *@param String fecha La fecha para la que está programada la tarea en formato 'aaaa-mm-dd hh-mm-ss'
        *
        *
        *<hr>Opcionales:
        *@param String $comentario Una descripción de la llamada
        *
        *
        *@return {int} En caso de haber insertado con éxito regresa el id del cliente. En caso de error regresa false
        * Y si el query no afectó nada regresa -1
        *
        *
    */
    public function agregarLlamada($idCliente, $idTelefono, $fecha, $comentario=null, $who){
        $who = $this->_bd->valida->validaCampoNumerico($who);
        $idCliente=$this->_bd->valida->validaCampoNumerico($idCliente);
        $idTelefono=$this->_bd->valida->validaCampoNumerico($idTelefono);
        if(empty($idCliente)){
            $idCliente="NULL";
        }else{
            $idCliente="'".$idCliente."'";
        }
        if(empty($idTelefono)){
            $idTelefono="NULL";
        }else{
            $idTelefono="'".$idTelefono."'";
        }
        $fecha=$this->_bd->valida->validaCampoTexto($fecha);
        if(empty($fecha)){
            $fecha="NULL";
        }else{
            $fecha="'".$fecha."'";
        }
        $comentario=$this->_bd->valida->validaCampoTexto($comentario);
        if(empty($comentario)){
            $comentario="NULL";
        }else{
            $comentario="'".$comentario."'";
        }

        if(!empty($idCliente)&&!empty($fecha)){

            $consulta = sprintf("insert into llamadas(idCliente, idUsuarios, idTelefono, fecha, comentario) values (%s,%d,%s,%s,%s);",
                                $idCliente, $who, $idTelefono, $fecha, $comentario);

            $this->_bd->sql->consulta($consulta, "agregarLlamada($idCliente, $idTelefono, $fecha,$comentario) Llamada.class.php");

            if($this->_bd->sql->lastError()==""){
                if($this->_bd->sql->filasAfectadas() == 1){
                    return $this->_bd->sql->ultimoId();
                }
            }else{
                return false;
            }
        }
        return -1;
    }



    /**
        *Esta función regresa un array con todas las llamadas de un cliente 
        *ordenadas además en orden cronológico
        *
        *
        *<code>public function getLlamadas($idCliente)</code>
        *
        *
        *@param int $idCliente El cliente del que se quieren sus llamadas
        *
        *
        *
        *@return {array} Si el query tuvo éxito y se encontraron registros regresa un arreglo
        * con todos los registros de las llamadas hechas por dicho cliente
        *
        *Retorna en cada fila: ['idCliente'], ['fecha'], ['comentario'], ['fechaRegistro']
        *
        *Si el query tuvo un error regresa false
    */
    public function getLlamadas($idCliente){
        $idCliente=$this->_bd->valida->validaCampoNumerico($idCliente);
        if(!empty($idCliente)){
            $query = sprintf("select idTelefono, idCliente,fecha,comentario,fechaRegistro from llamadas where idCliente=%d order by fecha desc, idLlamada desc;", $idCliente);
            $this->_bd->sql->consulta($query, "getLlamadas($idCliente) Llamada.class.php");
            if($this->_bd->sql->lastError()==""){
                $arreglo=array();
                while($fila=$this->_bd->sql->extraerRegistro()){                  
                    $arreglo[]=$fila;
                }
                return $arreglo; 
            }
        }
        return false;
    }


    /**
        *Esta función regresa un array con todas las llamadas realizadas en cierto día
        *en orden cronológico
        *
        *
        *<code>public function getLlamadasFecha($fecha)</code>
        *
        *
        *@param String $fecha El día del que se quieren las fechas
        *
        *
        *
        *@return {array} Si el query tuvo éxito y se encontraron registros regresa un arreglo
        * con todos los registros de las llamadas hechas por dicho cliente
        *
        *Retorna en cada fila: ['idCliente'], ['fecha'], ['comentario'], ['fechaRegistro']
        *
        *Si el query tuvo un error regresa false
    */
    public function getLlamadasFecha($fecha){
        $fecha=$this->_bd->valida->validaCampoTexto($fecha);
        if(empty($fecha)){
            $fecha="NULL";
        }else{
            $fecha="'".$fecha."'";
        }

        $query="select idCliente,fecha,comentario,fechaRegistro from llamadas where left(fecha,10)="
        .$fecha." order by fecha;";

        $this->_bd->sql->consulta($query, "getLlamadasFecha($fecha) Llamada.class.php");
        if($this->_bd->sql->lastError()==""){
            $arreglo=array();
            while($fila=$this->_bd->sql->extraerRegistro()){                  
                $arreglo[]=$fila;
            }
            if(count($arreglo)==0){
                return null;
            }
            return $arreglo; 
        }
        return false;
    }
    
    public function getTelefono($idTelefono){
        $idTelefono = $this->_bd->valida->validaCampoNumerico($idTelefono);
        if(!empty($idTelefono)){
            $consulta = sprintf("select numero, label from telefono where idTelefono=%d", $idTelefono);
            $this->_bd->sql->consulta($consulta, "getTelefono($idTelefono) Llamada.class.php");
            $dato = $this->_bd->sql->extraerRegistro();
            return $dato;
        }
        return null;
    }
}
?>