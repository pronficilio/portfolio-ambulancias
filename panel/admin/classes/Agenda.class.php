<?php
class Agenda{
    private $_bd;

    function __construct($permisos){
        require_once("permisos.php");
        $this->_bd = new Permisos($permisos);
    }


    /**
        *Permite agregarle una nueva tarea a un cliente en la base de datos. 
        *Recibe de parámetro obligatorio el id del cliente e inicializa los
        *valores predeterminados.
        *
        *<code>public function agregarTarea($idCliente, $fecha,$hora [,$notasAdicionales,$tipo])</code>
        *
        *@param Int $idCliente El identificador del cliente al que se le desea agregar una nueva tarea.
        *@param String fecha La fecha para la que está programada la tarea en formato 'aaaa-mm-dd hh-mm-ss'
        *
        *
        *<hr>Opcionales:
        *@param String $notasAdicionales Una descripción de la tarea a crear
        *@param String $tipo  El tipo de agendado: "agenda"/"tarea"
        *
        *
        *@return {int} En caso de haber insertado con éxito regresa el id del cliente. En caso de error regresa false
        * Y si el query no afectó nada regresa -1
        *
        *
    */
    public function agregarTarea($idCliente, $fecha, $notasAdicionales=null, $tipo=null, $who){
        $idCliente=$this->_bd->valida->validaCampoNumerico($idCliente);
        $who = $this->_bd->valida->validaCampoNumerico($who);
        if(empty($idCliente)){
            $idCliente="NULL";
        }else{
            $idCliente="'".$idCliente."'";
        }
        $fecha=$this->_bd->valida->validaCampoTexto($fecha);
        if(empty($fecha)){
            $fecha="NULL";
        }else{
            $fecha="'".$fecha."'";
        }
        $notasAdicionales=$this->_bd->valida->SanitizarQuery($notasAdicionales);
        $tipo=$this->_bd->valida->validaCampoTexto($tipo);
        if(empty($tipo)){
            $tipo="NULL";
        }else{
            $tipo="'".$tipo."'";
        }
        if(!empty($fecha)){
            $consulta = sprintf("insert into agenda(idCliente, idUsuarios, fecha, notas, tipo) values (%s, %d, %s, %s, %s);",
                                $idCliente, $who, $fecha, $notasAdicionales,$tipo);
            $this->_bd->sql->consulta($consulta, "agregarTarea($idCliente, $fecha, $notasAdicionales, $tipo) Agenda.class.php");
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
        *Esta función asigna el estado que se le envíe a la tarea $idTarea
        *
        *
        *
        *<code>public function setEstado($idTarea,$estado) </code>
        *
        *@param int $idTarea La tarea que se desea modificar
        *@param boolean $estado El estado que se desea asignar a la tarea (0=no hecha 1=hecha)
        *
        *@return {int} regresa el $idTarea en caso de haber cambiado con éxito, false en caso de error
        *Y -1 si ocurrió un error en el query
    */
    public function setEstado($idTarea, $estado, $who){
        $idTarea=$this->_bd->valida->validaCampoNumerico($idTarea);
        $estado=$this->_bd->valida->validaCampoNumerico($estado);
        $who = $this->_bd->valida->validaCampoNumerico($who);
        if(!empty($idTarea)&&!empty($estado)){
            $query = sprintf("update agenda set hecho=%d, hechoPor=%d WHERE idAgenda=%d;", $estado, $who, $idTarea);
            $this->_bd->sql->consulta($query, "setEstado($idTarea, $estado) Agenda.class.php");
            if($this->_bd->sql->lastError()==""){
                if($this->_bd->sql->filasAfectadas()>0){
                    return $this->_bd->sql->ultimoId();
                }
            }else{
                return false;
            }
        }
        return -1;
    } 

    /**
    *Esta función regresa todas las tareas de una fecha($fecha) dada en formato aaaa-mm-dd
    *
    *
    *
    *<code>public function getTareasDia($fecha) </code>
    *
    *@param string $fecha La fecha de la que se quiere las tareas
    *
    *
    *@return {array} regresa un arreglo con dos elementos:
    *
    *Posición 0: Contiene un arreglo con todas las tareas del dia solicitado 
    *Devuelve para cada fila: ['idAgenda'], ['idCliente'], ['fecha'],['fechaRegistro'],['notasAdicionales'], ['hecho'],['tipo']
    *
    *Posición 1: Contiene un arreglo con todas las reuniones del dia solicitado
    *Retorna en cada fila: ['idReunion'], ['idCliente'], ['fecha'], ['fechaCreacion'], ['notas'], ['hecho']
    *
    *
    *En caso de no encontrar ninguna tarea o ninguna reunión en dicha fecha se regresa null
    * en la respectiva posición del arreglo de dos elementos
    *
    *
    */
    public function getTareasDia($fecha, $idUsuario){
        $fecha=$this->_bd->valida->validaCampoTexto($fecha);
        $idUsuarios = $this->_bd->valida->validaCampoNumerico($idUsuario);
        if(empty($fecha)){
            $fecha="NULL";
        }
        if(!empty($fecha)){    
            $query = sprintf("select idAgenda, agenda.idCliente, nombre, fecha, fechaRegistro, notas, hecho, tipo from agenda ".
                             "left join cliente on agenda.idCliente=cliente.idCliente ".
                             "where date(fecha)<='%s' and hecho=0 and idUsuarios=%d order by fecha;",$fecha, $idUsuario);
            $this->_bd->sql->consulta($query, "getTareasDia($fecha) Agenda.class.php");
            if($this->_bd->sql->lastError()==""){
                $arreglo=array();
                while($fila=$this->_bd->sql->extraerRegistro()){                  
                    $arreglo[]=$fila;
                }
                if(count($arreglo)==0) {return null;}
                return $arreglo; 
            }
        }
        return false;
    } 

    public function getAgendaById($idAgenda){
        $idAgenda = $this->_bd->valida->validaCampoNumerico($idAgenda);
        if(!empty($idAgenda)){
            $consulta = sprintf("select idAgenda, c.nombre, a.idUsuarios, u.nombre as user, tipo, fecha, a.idCliente, hechoPor, fechaRegistro, ".
                                "notas, hecho from agenda a left join cliente c on c.idCliente=a.idCliente inner join usuarios u on a.idUsuarios=u.idUsuarios ".
                                "where idAgenda=%d", $idAgenda);
            $this->_bd->sql->consulta($consulta, "getAgendaById($idAgenda) Agenda.class.php");
            if($this->_bd->sql->filasAfectadas() == 1){
                $dato = $this->_bd->sql->extraerRegistro();
                return $dato;
            }
        }
        return null;
    }
    
    public function getAgendaJson($opciones){
        $busqueda = $this->_bd->valida->validaCampoTexto($opciones['search']['value']);
        $pag = $this->_bd->valida->validaCampoNumerico($opciones['start']);
        $cant = $this->_bd->valida->validaCampoNumerico($opciones['length']);
        $draw = $this->_bd->valida->validaCampoNumerico($opciones['draw']);
        
        $hecho = "hecho=".$opciones['hecho'];
        $consulta = "select count(idAgenda) from agenda where hecho=".$opciones['hecho'];        
        $this->_bd->sql->consulta($consulta, "getAgendaJson() Agenda.class.php");
        $cuantos = $this->_bd->sql->extraerRegistro();
        $total = $cuantos['count(idAgenda)'];
        $filtrado = $total;
        
        if(!empty($busqueda)){
            $extra = " a left join cliente c on c.idCliente=a.idCliente ";
            $busqueda = sprintf("where nombre like '%%%s%%' and %s", $busqueda, $hecho);
            $this->_bd->sql->consulta("select count(idAgenda) from agenda".$extra.$busqueda, "getAgendaJson() Agenda.class.php");
            $cuantos = $this->_bd->sql->extraerRegistro();
            $filtrado = $cuantos['count(idAgenda)'];
        }else{
            $busqueda = "where ".$hecho;
        }
        $consult = "select idAgenda, c.nombre, idUsuarios, tipo, fecha, a.idCliente, hechoPor, fechaRegistro, notas, hecho ".
                   "from agenda a left join cliente c on c.idCliente=a.idCliente ".$busqueda;
        //if(!empty($opciones['order'][0]['column']))
            $consult .= " order by ".($opciones['order'][0]['column']+1)." ".$opciones['order'][0]['dir'];
        $consult .= sprintf(" limit %d, %d", $pag, $cant);
        $this->_bd->sql->consulta($consult, "getAgendaJson() Agenda.class.php");
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
    
    public function eliminaAgenda($idAgenda){
        $idAgenda = $this->_bd->valida->validaCampoNumerico($idAgenda);
        if(!empty($idAgenda)){
            $consulta = sprintf("delete from agenda where idAgenda=%d", $idAgenda);
            $this->_bd->sql->consulta($consulta, "eliminaAgenda($idAgenda) Agenda.class.php");
        }
    }
    
    public function actualizaNota($idAgenda, $nota, $fecha){
        $idAgenda = $this->_bd->valida->validaCampoNumerico($idAgenda);
        $fecha = $this->_bd->valida->validaCampoTexto($fecha);
        if(!empty($idAgenda)){
            $consulta = sprintf("update agenda set notas=%s where idAgenda=%d",
                                $this->_bd->valida->SanitizarQuery($nota), $idAgenda);
            $this->_bd->sql->consulta($consulta, "actualizaNota($idAgenda) Agenda.class.php");
            if(!empty($fecha)){
                $consulta = sprintf("update agenda set fecha=%s where idAgenda=%d",
                                    $this->_bd->valida->SanitizarQuery($fecha), $idAgenda);
                $this->_bd->sql->consulta($consulta, "actualizaNota($idAgenda) Agenda.class.php");
            }
        }
    }
    
    public function dameActividades($ini, $fin){
        $ini = $this->_bd->valida->validaCampoTexto($ini);
        $fin = $this->_bd->valida->validaCampoTexto($fin);
        $datos = array();
        if(!empty($ini) && !empty($fin)){
            $consulta = sprintf("select idAgenda, tipo, hecho, nombre, fecha from agenda left join cliente on agenda.idCliente=cliente.idCliente ".
                                "where fecha >= '%s' and fecha <= '%s'", $ini, $fin);
            $this->_bd->sql->consulta($consulta, "dameActividades($ini, $fin) Agenda.class.php");
            while($fila = $this->_bd->sql->extraerRegistro()){
                $datos[] = $fila;
            }
        }
        return $datos;
    }
    
    public function dameCarritosCerrados($ini, $fin){
        $ini = $this->_bd->valida->validaCampoTexto($ini);
        $fin = $this->_bd->valida->validaCampoTexto($fin);
        $datos = array();
        if(!empty($ini) && !empty($fin)){
            $consulta = sprintf("select idCarrito, nombre, fechaEntrega from carrito left join cliente on carrito.idCliente=cliente.idCliente ".
                                "where fechaEntrega >= '%s' and fechaEntrega <= '%s'", $ini, $fin);
            $this->_bd->sql->consulta($consulta, "dameCarritosCerrados($ini, $fin) Agenda.class.php");
            while($fila = $this->_bd->sql->extraerRegistro()){
                $datos[] = $fila;
            }
        }
        return $datos;
    }
}
?>