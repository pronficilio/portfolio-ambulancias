<?php 	
class Acceso{
    private $_bd;

    function __construct($permisos){
        require_once("permisos.php");
        $this->_bd=new Permisos($permisos);
    }
    
    /**
     * Regresa el idUsuarios dado un token
     * @param  string  $token Token
     * @return integer idUsuarios, 0 en caso de no existir el token
     */
    public function tokenValido($token){
        $token = $this->_bd->valida->validaCampoTexto($token);
        if(!empty($token)){
            $this->_bd->sql->consulta(sprintf("select idUsuarios from usuarios where token='%s' and activo=1",
                                              $token),  "tokenValido($token) Acceso.class.php");
            $dato = $this->_bd->sql->extraerRegistro();
            return $dato['idUsuarios'];
        }
        return 0;
    }
    

    public function limpiaToken($idUsuario){
        $idUsuario = $this->_bd->valida->validaCampoNumerico($idUsuario);
        if(!empty($idUsuario)){
            $this->_bd->sql->consulta(sprintf("update usuarios set token=null where idUsuarios=%d",
                                             $idUsuario), "limpiaToken($idUsuario) Acceso.class.php");
            return true;
        }
        return false;
    }
    
    public function verificaUsuario($nombre, $pass){
        $lim_nombre = $this->_bd->valida->validaCampoTexto($nombre);
        $lim_pass = md5("Ponla_dificil".$pass);
        if(!empty($lim_nombre)){
            $consulta = sprintf("select idUsuarios, permisos from usuarios where nombre='%s' and pass='%s' and activo=1",
                                $lim_nombre, $lim_pass);
            $this->_bd->sql->consulta($consulta, "verificaUsuario($nombre, $pass) Acceso.class.php");
            if($this->_bd->sql->filasAfectadas() == 1){
                $dato = $this->_bd->sql->extraerRegistro();
                return $dato;
            }
        }
        return false;
    }
    
    public function emailRestablecerPass($idUsuario){
        $idUsuario = $this->_bd->valida->validaCampoNumerico($idUsuario);
        if(!empty($idUsuario)){
            $this->_bd->sql->consulta(sprintf("select idUsuarios, email, name from usuarios where idUsuarios=%d and activo=1",
                                              $idUsuario), "emailRestablecerPass($idUsuario) Acceso.class.php");
            if($this->_bd->sql->filasAfectadas()){
                $datos = $this->_bd->sql->extraerRegistro();
                $token = md5($idUsuarios."_".$datos['email'].time());
                $this->_bd->sql->consulta(sprintf("update usuarios set token='%s' where idUsuarios=%d",
                                           $token, $idUsuario), "emailRestablecerPass($idUsuario) Acceso.class.php");
                require_once 'PHPMailer/PHPMailerAutoload.php';
                $_SESSION['email']['link'] = "recupera-contrasena.php?tk=".$token;
                $correo = $this->_bd->requireToVar("../email/emailRecuperaContra.php");
                $mail = new PHPMailer;
                $mail->SMTPAuth = true;
                $mail->SMTPDebug = 2;
                $mail->Mailer = "smtp";
                $mail->Host = "dtcwin106.ferozo.com";
                $mail->Port = 465;
                
                $mail->setFrom($mail->Username, $GLOBALS['g_principal_title']);
                $mail->addAddress($datos['email'], $datos['name']);
                $mail->Subject = "Restablecer contraseña";
                $mail->msgHTML($correo);
                $mail->send();
            }
        }
    }
    
    public function emailNuevoUsuario($user, $nombre, $pass, $email){
        require_once 'PHPMailer/PHPMailerAutoload.php';
        $_SESSION['email']['user'] = $user;
        $_SESSION['email']['pass'] = $pass;
        $correo = $this->_bd->requireToVar("../email/emailNuevaCuenta.php");
        $mail = new PHPMailer;
        $mail->SMTPAuth = true;
        $mail->SMTPDebug = 2;
        $mail->Mailer = "smtp";
        $mail->Host = "dtcwin106.ferozo.com";
        $mail->Port = 465;
        
        $mail->setFrom('noreply@'.$GLOBALS['g_sitio'], $GLOBALS['g_principal_title']);
        $mail->addAddress($email, $nombre);
        $mail->Subject = "Credenciales de acceso - ".$GLOBALS['g_principal_title'];
        $mail->msgHTML($correo);
        $mail->send();
    }
    
    public function existeUsuario($nombre){
        $lim_nombre = $this->_bd->valida->validaCampoTexto($nombre);
        if(!empty($lim_nombre)){
            $consulta = sprintf("select idUsuarios, permisos from usuarios where nombre='%s' and activo=1",
                                $lim_nombre);
            $this->_bd->sql->consulta($consulta, "existeUsuario($nombre) Acceso.class.php");
            if($this->_bd->sql->filasAfectadas() == 1){
                $dato = $this->_bd->sql->extraerRegistro();
                return $dato['idUsuarios'];
            }
        }
        return 0;
    }
    
    public function agregaUsuarios($nombre, $name, $email, $pass, $permisos){
        $lim_nombre = $this->_bd->valida->validaCampoTexto($nombre);
        $name = $this->_bd->valida->validaCampoTexto($name);
        $email = $this->_bd->valida->validaCampoTexto($email);
        if(empty($name))
            $name = "NULL";
        else
            $name = "'".$name."'";
        $lim_pass = md5("Ponla_dificil".$pass);
        if(!empty($lim_nombre)){
            $consulta = sprintf("insert into usuarios (nombre, pass, email, name, permisos) values ".
                                "('%s', '%s', '%s', %s, %d)", $lim_nombre, $lim_pass, $email,
                                $name, $permisos);
            $this->_bd->sql->consulta($consulta, "agregaUsuarios($nombre, $pass) Acceso.class.php");
            if($this->_bd->sql->filasAfectadas() == 1)
                return true;
        }
        return false;
    }
    
    /**
     * Modifica la informacion principal de un usuario
     * @param  integer $idUsuarios Identificador del usuario
     * @param  string  $nombre     Nombre de usuario del usuario
     * @param  string  $name       Nombre real del usuario
     * @param  string  $email      Email
     * @param  integer [$activo=1] Activo o no activo
     * @return boolean Verdadero si se ejecutó la consulta
     */
    public function modificaUsuario($idUsuarios, $nombre, $name, $email, $activo=1){
        $lim_idUsuarios = $this->_bd->valida->validaCampoNumerico($idUsuarios);
        $lim_nombre = $this->_bd->valida->validaCampoTexto($nombre);
        $name = $this->_bd->valida->validaCampoTexto($name);
        $email = $this->_bd->valida->validaCampoTexto($email);
        $lim_activo = $this->_bd->valida->validaCampoNumerico($activo);
        if(!empty($lim_idUsuarios) && !empty($lim_nombre)){
            $consulta = sprintf("update usuarios set nombre='%s', email='%s', name='%s', activo=%d where idUsuarios=%d",
                                $lim_nombre, $email, $name, $lim_activo, $lim_idUsuarios);
            $this->_bd->sql->consulta($consulta, "modificaUsuario($idUsuarios, $nombre, $activo) Acceso.class.php");
            if($this->_bd->sql->lastError() == "")
                return true;
        }
        return false;
    }
    
    public function modificaContra($idUsuarios, $pass){
        $lim_idUsuarios = $this->_bd->valida->validaCampoNumerico($idUsuarios);
        $lim_pass = md5("Ponla_dificil".$pass);
        if(!empty($lim_idUsuarios)){
            $consulta = sprintf("update usuarios set pass='%s' where idUsuarios=%d", $lim_pass, $lim_idUsuarios);
            $this->_bd->sql->consulta($consulta, "modificaContra($idUsuarios, $pass) Acceso.class.php");
            if($this->_bd->sql->lastError() == "")
                return true;
        }
        return false;
    }
    
    public function eliminaUsuario($idUsuario){
        $idUsuario = $this->_bd->valida->validaCampoNumerico($idUsuario);
        if(!empty($idUsuario)){
            $this->_bd->sql->consulta(sprintf("update usuarios set activo=0 where idUsuarios=%d",
                                      $idUsuario, "eliminaUsuario($idUsuario) Acceso.class.php"));
            if($this->_bd->sql->filasAfectadas())
                return true;
        }
        return false;
    }
    
    /**
    * Regresa un array con los usuarios existentes en activo
    *
    * <code>public function dameUsuarios</code>
    *
    * @param string $noaun Sin parametros
    * @return {array} Array con idUsuarios, nombre o false si no hay datos
    */
    public function dameUsuarios(){
        $consulta = "select idUsuarios, nombre from usuarios where activo=1";
        $this->_bd->sql->consulta($consulta, "dameUsuarios() Acceso.class.php");
        if($this->_bd->sql->filasAfectadas() > 0){
            $datos = array();
            while($fila = $this->_bd->sql->extraerRegistro()){
                $datos[] = $fila;   
            }
            return $datos;
        }
        return false;
    }
    
    public function inicioSesion($idUsuario){
        $idUsuario = $this->_bd->valida->validaCampoNumerico($idUsuario);
        if(!empty($idUsuario)){
            $consulta = sprintf("insert into sesion (idUsuarios, fin) values (%d, '%s')", $idUsuario, date("Y-m-d H:i:s"));
            $this->_bd->sql->consulta($consulta, "inicioSesion($idUsuarios) Acceso.class.php");
            return $this->_bd->sql->ultimoId();
        }
        return false;
    }
    
    public function aquiSigo($idSesion){
        $consulta = sprintf("update sesion set fin='%s' where idSesion=%d",
            date("Y-m-d H:i:s"), $idSesion);
        $this->_bd->sql->consulta($consulta, "aquiSigo($idSesion) Acceso.class.php");
    }
    
    public function dameNombre($idUsuario){
        $idUsuario = $this->_bd->valida->validaCampoNumerico($idUsuario);
        if(!empty($idUsuario)){
            $consulta = sprintf("select nombre from usuarios where idUsuarios=%d", $idUsuario);
            $this->_bd->sql->consulta($consulta, "dameNombre($idUsuario) Acceso.class.php");
            $dato = $this->_bd->sql->extraerRegistro();
            return $dato['nombre'];
        }
        return "";
    }
    
    public function dameDatosPersonales($idUsuario){
        $idUsuario = $this->_bd->valida->validaCampoNumerico($idUsuario);
        if(!empty($idUsuario)){
            $consulta = sprintf("select nombre, email, name from usuarios where idUsuarios=%d", $idUsuario);
            $this->_bd->sql->consulta($consulta, "dameDatosPersonales($idUsuario) Acceso.class.php");
            $dato = $this->_bd->sql->extraerRegistro();
            return $dato;
        }
        return null;
    }
    
    public function getUsuariosJson($opciones){
        $busqueda = $this->_bd->valida->validaCampoTexto($opciones['search']['value']);
        $pag = $this->_bd->valida->validaCampoNumerico($opciones['start']);
        $cant = $this->_bd->valida->validaCampoNumerico($opciones['length']);
        $draw = $this->_bd->valida->validaCampoNumerico($opciones['draw']);
        
        $consulta = "select count(idUsuarios) from usuarios where activo=1";        
        $this->_bd->sql->consulta($consulta, "getUsuariosJson() Acceso.class.php");
        $cuantos = $this->_bd->sql->extraerRegistro();
        $filtrado = $total = $cuantos['count(idUsuarios)'];
        if(!empty($busqueda)){
            $busqueda = sprintf(" and (nombre like '%s%%' or name like '%s%%' or email like '%s%%')", $busqueda,
                                $busqueda, $busqueda);
            $this->_bd->sql->consulta("select count(idUsuarios) from usuarios where activo=1".$busqueda, "getUsuariosJson() Acceso.class.php");
            $cuantos = $this->_bd->sql->extraerRegistro();
            $filtrado = $cuantos['count(idUsuarios)'];
        }
        $consult = "select idUsuarios, name, email, nombre, permisos from usuarios where activo=1".$busqueda;
        $consult .= " order by ".($opciones['order'][0]['column']+1)." ".$opciones['order'][0]['dir'];
        $consult .= sprintf(" limit %d, %d", $pag, $cant);
        $this->_bd->sql->consulta($consult, "getUsuariosJson() Acceso.class.php");
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
    
    
    public function dameTodosPermisos($opciones){
        $busqueda = $this->_bd->valida->validaCampoTexto($opciones['search']['value']);
        $pag = $this->_bd->valida->validaCampoNumerico($opciones['start']);
        $cant = $this->_bd->valida->validaCampoNumerico($opciones['length']);
        $draw = $this->_bd->valida->validaCampoNumerico($opciones['draw']);
        
        $consulta = "select count(idPermiso) from permiso";        
        $this->_bd->sql->consulta($consulta, "dameTodosPermisos() Acceso.class.php");
        $cuantos = $this->_bd->sql->extraerRegistro();
        $filtrado = $total = $cuantos['count(idPermiso)'];
        if(!empty($busqueda)){
            $busqueda = sprintf("where nombrePermiso like '%s%%'", $busqueda);
            $this->_bd->sql->consulta("select count(idPermiso) from permiso ".$busqueda, "dameTodosPermisos() Acceso.class.php");
            $cuantos = $this->_bd->sql->extraerRegistro();
            $filtrado = $cuantos['count(idPermiso)'];
        }
        $consult = "select idPermiso, nombrePermiso from permiso ".$busqueda;
        $consult .= " order by ".($opciones['order'][0]['column']+1)." ".$opciones['order'][0]['dir'];
        $consult .= sprintf(" limit %d, %d", $pag, $cant);
        $this->_bd->sql->consulta($consult, "dameTodosPermisos() Acceso.class.php");
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
     * Agrega un nuevo tipo de permiso
     * @param  string  $nombre Descriptor del permiso
     * @return integer idPermiso en caso de creacion exitosa, 0 en caso contrario
     */
    public function agregaPermiso($nombre){
        $nombre = $this->_bd->valida->validaCampoTexto($nombre);
        if(!empty($nombre)){
            $this->_bd->sql->consulta(sprintf("insert into permiso (nombrePermiso) values ('%s')", $nombre), "agregaPermiso($nombre) Acceso.class.php");
            return $this->_bd->sql->ultimoId();
        }
        return 0;
    }
    /**
     * Agrega una tab permitida para un tipo de permiso
     * @param  string  $tab       Descriptor del tab
     * @param  integer $idPermiso Identificador del permiso
     * @return boolean Verdadero si se realizó la consulta
     */
    public function agregaTab($tab, $idPermiso){
        $idPermiso = $this->_bd->valida->validaCampoNumerico($idPermiso);
        $tab = $this->_bd->valida->validaCampoTexto($tab);
        if(!empty($idPermiso) && !empty($tab)){
            $this->_bd->sql->consulta(sprintf("insert into detper (idPermiso, permitido) values (%d, '%s')", $idPermiso, $tab), "agregaTab($tab, $idPermiso) Acceso.class.php");
            if($this->_bd->sql->filasAfectadas())
                return true;
        }
        return false;
    }
    
    /**
     * Elimina todas las tabs de un idPermiso
     * @param  integer $idPermiso Identificador del permiso
     * @return boolean Verdadero si se realizo la consulta de eliminacion
     */
    public function eliminaTabs($idPermiso){
        $idPermiso = $this->_bd->valida->validaCampoNumerico($idPermiso);
        if(!empty($idPermiso)){
            $this->_bd->sql->consulta(sprintf("delete from detper where idPermiso=%d", $idPermiso), "eliminaTabs($idPermiso) Acceso.class.php");
            return true;
        }
        return false;
    }
    
    public function eliminaPermiso($idPermiso){
        $idPermiso = $this->_bd->valida->validaCampoNumerico($idPermiso);
        if(!empty($idPermiso)){
            $this->_bd->sql->consulta(sprintf("delete from permiso where idPermiso=%d", $idPermiso), "eliminaPermiso($idPermiso) Acceso.class.php");
            return true;
        }
        return false;
    }

    public function dameMisPermisos($idUsuario){
        $idUsuario = $this->_bd->valida->validaCampoNumerico($idUsuario);
        $perm = array();
        if(!empty($idUsuario)){
            $this->_bd->sql->consulta(sprintf("select permitido from detper ".
                                              "inner join permiso on permiso.idPermiso=detper.idPermiso ".
                                              "inner join usuarios on permisos=permiso.idPermiso and idUsuarios=%d",
                                              $idUsuario), "dameMisPermisos($idUsuario) Acceso.class.php");
            while($fila = $this->_bd->sql->extraerRegistro())
                $perm[] = $fila['permitido'];
        }
        return $perm;
    }

    public function damePermisosById($idPermiso){
        $idPermiso = $this->_bd->valida->validaCampoNumerico($idPermiso);
        $perm = array();
        if(!empty($idPermiso)){
            $this->_bd->sql->consulta(sprintf("select permitido from detper ".
                                              "inner join permiso on permiso.idPermiso=detper.idPermiso ".
                                              "and permiso.idPermiso=%d",
                                              $idPermiso), "damePermisosById($idPermiso) Acceso.class.php");
            while($fila = $this->_bd->sql->extraerRegistro())
                $perm[] = $fila['permitido'];
        }
        return $perm;
    }
    
    public function damePermisosTodos(){
        $this->_bd->sql->consulta("select idPermiso, nombrePermiso from permiso", "damePermisosTodos() Acceso.class.php");
        $datos = array();
        while($fila = $this->_bd->sql->extraerRegistro())
            $datos[] = $fila;
        return $datos;
    }
    
    public function cambiarPermiso($idPermiso, $idUsuario){
        $idPermiso = $this->_bd->valida->validaCampoNumerico($idPermiso);
        $idUsuario = $this->_bd->valida->validaCampoNumerico($idUsuario);
        if(!empty($idPermiso) && !empty($idUsuario)){
            $this->_bd->sql->consulta(sprintf("update usuarios set permisos=%d where idUsuarios=%d",
                                      $idPermiso, $idUsuario), "cambiarPermiso($idPermiso, $idUsuario) Acceso.class.php");
            return true;
        }
        return false;
    }
}