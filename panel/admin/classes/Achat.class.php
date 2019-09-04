<?php
class Achat{
    
    private $_bd;
    
    function __construct($permisos){
        include_once("permisos.php");
        $this->_bd = new Permisos($permisos);
    }
    
    private function sigoAqui($idChat){
        $consulta = sprintf("update chat set tiempo='%s' where idChat=%d",
            date("Y-m-d H:i:s"), $idChat);
        $this->_bd->sql->consulta($consulta, "sigoAqui($idChat) achat.class.php");
    }
    
    public function creaChat($nombre, $email, $pregunta, $tel=""){
        $nombre = $this->_bd->valida->validaCampoTexto($nombre);
        $email = $this->_bd->valida->validaEmail($email);
        $tel = $this->_bd->valida->validaCampoTexto($tel);
        if(!empty($nombre) && !empty($email)){
            $consulta = sprintf("insert into chat (nombre, email, preguntaInicial, tel) values ('%s', '%s', %s, '%s')",
                                $nombre, $email, $this->_bd->valida->SanitizarQuery($pregunta), $tel);
            $this->_bd->sql->consulta($consulta, "creaChat($nombre, $email) achat.class.php");
            $idChat = $this->_bd->sql->ultimoId();
            if(!empty($idChat)){
                $this->creaMensaje($idChat, $pregunta, 0);
                return $idChat;
            }
        }
        return false;
    }
    
    public function creaMensaje($idChat, $mensaje, $quien){
        $idChat = $this->_bd->valida->validaCampoNumerico($idChat);
        $quien = $this->_bd->valida->validaCampoNumerico($quien);
        if(!empty($idChat)){
            $consulta = sprintf("insert into conversaciones (idChat, texto, quien) values (%d, %s, %d)",
                                $idChat, $this->_bd->valida->SanitizarQuery($mensaje), $quien);
            $this->_bd->sql->consulta($consulta, "creaMensaje($idChat, $quien) achat.class.php");
            $this->sigoAqui($idChat);
        }
    }
    
    public function dameMensajes($idChat, $actualiza=true){
        $idChat = $this->_bd->valida->validaCampoNumerico($idChat);
        $datos = array();
        if(!empty($idChat)){
            $consulta = sprintf("select texto, quien from conversaciones where idChat=%d", $idChat);
            $this->_bd->sql->consulta($consulta, "dameMensajes($idChat) achat.class.php");
            while($fila = $this->_bd->sql->extraerRegistro()){
                $datos[] = $fila;
            }
            if($actualiza)
                $this->sigoAqui($idChat);
        }
        return $datos;
    }
    
    public function cierraChats(){
        $consulta = sprintf("update chat set activo=0 where tiempo < '%s'",
                            date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." -1 hour")));
        $this->_bd->sql->consulta($consulta, "cierraChats() achat.class.php");
    }
    
    public function dameChats(){
        $consulta = "select idChat, nombre, email, preguntaInicial, tiempo, tel from chat where activo=1";
        $this->_bd->sql->consulta($consulta, "dameChats() achat.class.php");
        $datos = array();
        while($fila = $this->_bd->sql->extraerRegistro()){
            $fila['tiempo'] = strtotime($fila['tiempo']);
            $datos[] = $fila;
        }
        return $datos;
    }
    
    public function cierraChat($idChat){
        $idChat = $this->_bd->valida->validaCampoNumerico($idChat);
        if(!empty($idChat)){
            $consulta = sprintf("update chat set activo=0 where idChat=%d", $idChat);
            $this->_bd->sql->consulta($consulta, "cierraChat($idChat) achat.class.php");
        }
    }
    
    public function estadoChat($idChat){
        $idChat = $this->_bd->valida->validaCampoNumerico($idChat);
        if(!empty($idChat)){
            $consulta = sprintf("select activo from chat where idChat=%d", $idChat);
            $this->_bd->sql->consulta($consulta, "estadoChat($idChat) achat.class.php");
            $dato = $this->_bd->sql->extraerRegistro();
            return $dato['activo'];
        }
        return null;
    }
    
    public function dameChatCerrados(){
        $consulta = "select idChat, nombre, email, preguntaInicial, tiempo, tel from chat where activo=0 order by tiempo desc";
        $this->_bd->sql->consulta($consulta, "dameChatCerrados() achat.class.php");
        $datos = array();
        while($fila = $this->_bd->sql->extraerRegistro()){
            $datos[] = $fila;
        }
        return $datos;
    }
    
    public function chatDisponible(){
        $disponible = 0;
        $consulta = "select opcion, valor from opciones where opcion like 'achat-%'";
        $this->_bd->sql->consulta($consulta, "chatDisponible() achat.class.php");
        if($this->_bd->sql->filasAfectadas() >= 3){
            while($fila = $this->_bd->sql->extraerRegistro()){
                $dato[$fila['opcion']] = $fila['valor'];
            }
            if($dato['achat-ini'] <= date("H:i") && $dato['achat-fin'] >= date("H:i")){
                if(!empty($dato['achat-dias'])){
                    $dias = explode(",", $dato['achat-dias']);
                    if(in_array(intval(date("w")), $dias))
                        $disponible = 1;
                }else
                    $disponible = 2;
            }
        }
        if(!$disponible){
            $consulta = "select fin from sesion order by fin desc limit 1";
            $this->_bd->sql->consulta($consulta, "chatDisponible() achat.class.php");
            $dato = $this->_bd->sql->extraerRegistro();
            if(!empty($dato['fin']))
                if(date("Y-m-d H:i") < date("Y-m-d H:i", strtotime($dato['fin'])+300))
                    $disponible = 3;
        }
        return $disponible;
    }
    
    public function cabecera(){
        $consulta = "select valor from opciones where opcion='achat-cabecera'";
        $this->_bd->sql->consulta($consulta, "cabecera() achat.class.php");
        $dato = $this->_bd->sql->extraerRegistro();
        return $dato['valor'];
    }
}
?>