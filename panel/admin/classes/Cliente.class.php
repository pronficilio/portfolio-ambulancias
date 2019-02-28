<?php
    class Cliente{
        private $_bd;

        function __construct($permisos){
            require_once("permisos.php");
            $this->_bd = new Permisos($permisos);
        }
        
        private function genPass($len = 10) {
            for ($i=0;$i<=$len;$i++) {
                do{
                    $who = mt_rand(48, 90);
                }while($who >= 58 && $who <= 64);
                $passwd = sprintf('%s%c', isset($passwd) ? $passwd : NULL, $who);
            }
            return $passwd;
        }

        /**
            *Permite agregar un nuevo cliente a la base de datos. 
            *Recibe de parámetro obligatorio el nombre del cliente e inicializa los
            *valores predeterminados.
            *
            *<code>public function agregarCliente($nombre [, $correo,$direccion])</code>
            *
            *@param String $nombre El nombre completo del cliente. Este es el único campo obligatorio que ingresa el cliente.
            *
            *<hr>Opcionales:
            *@param String $correo Aquí se guarda el correo electrónico del cliente
            *@param String $direccion La dirección del cliente
            *
            *
            *@return {int} En caso de haber insertado con éxito regresa el id del cliente. En caso de error regresa false
            * Y si el query no afectó nada regresa -1
            *
            *
        */
        public function agregarCliente($nombre, $correo=null, $idDescuento=0, $gen=null, $direccion=null, $notas=null, $cat, $estado,
            $tipoTienda, $noTienda, $expo, $tipoDoc, $rfc, $razSoc, $cb, $formaPago, $correoFac, $envioNombre, $fc, $tareas){
            $fc=$this->_bd->valida->validaCampoTexto($fc);
            $tareas=$this->_bd->valida->validaCampoTexto($tareas);
            $cat=$this->_bd->valida->validaCampoTexto($cat);
            $estado=$this->_bd->valida->validaCampoTexto($estado);
            $tipoTienda=$this->_bd->valida->validaCampoTexto($tipoTienda);
            $noTienda=$this->_bd->valida->validaCampoTexto($noTienda);
            $expo=$this->_bd->valida->validaCampoTexto($expo);
            $tipoDoc=$this->_bd->valida->validaCampoTexto($tipoDoc);
            $razSoc=$this->_bd->valida->validaCampoTexto($razSoc);
            $rfc=$this->_bd->valida->validaCampoTexto($rfc);
            $cb=$this->_bd->valida->validaCampoTexto($cb);
            $formaPago=$this->_bd->valida->validaCampoTexto($formaPago);
            $correoFac=$this->_bd->valida->validaCampoTexto($correoFac);
            $envioNombre=$this->_bd->valida->validaCampoTexto($envioNombre);
            $nombre=$this->_bd->valida->validaCampoTexto($nombre);
            if(!empty($nombre)){
                $nombre="'".$nombre."'";
            }
            $gen=$this->_bd->valida->validaCampoTexto($gen);
            if(empty($gen)){
                $gen="NULL";
            }else{
                $gen="'".$gen."'";
            }
            $correo=$this->_bd->valida->validaEmail($correo);
            if(empty($correo)){
                $correo="NULL";
            }else{
                $correo="'".$correo."'";
            }
            if(empty($direccion)){
                $direccion="NULL";
            }else{
                $direccion=$this->_bd->valida->SanitizarQuery($direccion);
            }
            if(empty($notas)){
                $notas="NULL";
            }else{
                $notas=$this->_bd->valida->SanitizarQuery($notas);
            }
            if(!empty($nombre)){
                $consulta = sprintf("insert into cliente (nombre,email,idDescuento,genero, direccion, notasCliente, categoria, estado, ".
                    "tipoTienda, noTiendas, expo, tipoDocumento, rfc, razonSocial, cuentaBancaria, formaPago, correoFac, envioNombre, fechaContacto, tareas) ".
                    "values (%s,%s,%d,%s,%s,%s, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');",
                    $nombre, $correo, $idDescuento, $gen, $direccion, $notas, $cat, $estado, $tipoTienda, $noTienda, $expo,
                    $tipoDoc, $rfc, $razSoc, $cb, $formaPago, $correoFac, $envioNombre, $fc, $tareas);
                $this->_bd->sql->consulta($consulta, "agregarCliente($nombre,$correo,$gen, $direccion, $notas, $cat, $estado, ".
                    "tipoTienda, noTienda, expo, tipoDocumento, razonSocial, cuentaBancaria, formaPago, correoFac, envioNombre) Cliente.class.php");
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
            *Asigna una contraseña al cliente. Si no se le pasa una contraseña como parámetro, se genera
            *una contraseña random y se le envia por mail()
            *
            *<code>public function asignaContra($idCliente [, $contra])</code>
            *
            *@param int $idCliente Identificador del cliente
            *
            *<hr>Opcionales:
            *@param String $contra Contraseña elegida por el cliente
            *
            *
        */
        public function asignaContra($idCliente, $contra=NULL){
            $idCliente = $this->_bd->valida->validaCampoNumerico($idCliente);
            if(!empty($idCliente)){
                if(empty($contra)){
                    $contra = $this->genPass();
                    // enviar por mail :S
                }
                $contraMD5 = md5("Ponla_dificil".$contra);
                $consulta = sprintf("update cliente set pass='%s' where idCliente=%d", $contraMD5, $idCliente);
                $this->_bd->sql->consulta($consulta, "asignaContra($idCliente, $contra) Cliente.class.php");
            }
        }


        /**
            *Permite agregar un nuevo teléfono a un cliente de la base de datos. 
            *Recibe de parámetro el id del cliente, el numero y opcionalmente el texto descriptivo(label)
            *
            *
            *<code>public function agregarTelefono($idCliente, $numero[, $label])</code>
            *
            *@param String $idCliente El id del cliente en la tabla Cliente al que se desea agregar un nuevo numero
            *@param String $numero El numero telefónico a almacenar
            *
            *<hr>Opcionales:
            *@param String $label Una etiqueta para describir los datos del número telefónico, ejemplo: casa/telefono de oficina, etc
            *
            *
            *@return {int} En caso de haber insertado con éxito regresa el id del cliente. En caso de error regresa false
            * Y si el query no afectó nada regresa -1
        */
        public function agregarTelefono($idCliente, $numero,$label=null){
            
            $idCliente=$this->_bd->valida->validaCampoNumerico($idCliente);
            if(empty($idCliente)){
                $idCliente="NULL";
            }else{
                $idCliente="'".$idCliente."'";
            }
            $numero=$this->_bd->valida->validaCampoTexto($numero);
            if(empty($numero)){
                $numero="NULL";
            }else{
                $numero="'".$numero."'";
            }
            $label=$this->_bd->valida->validaCampoTexto($label);
            if(empty($label)){
                $label="NULL";
            }else{
                $label="'".$label."'";
            }
            
            if(!empty($idCliente)&&!empty($numero)){
                $consulta = sprintf("select idTelefono from telefono where label=%s and idCliente=%s", $label, $idCliente);
                $this->_bd->sql->consulta($consulta, "agregarTelefono($idCliente, $numero, $label) Cliente.class.php");
                if($this->_bd->sql->filasAfectadas()){
                    $idTelefono = $this->_bd->sql->extraerRegistro();
                    $consulta = sprintf("update telefono set numero=%s where idTelefono=%d", $numero, $idTelefono['idTelefono']);
                    $this->_bd->sql->consulta($consulta, "agregarTelefono($idcliente, $numero, $label) Cliente.class.php");
                }else{
                    $consulta = sprintf("insert into telefono (idCliente,numero,label) values (%s,%s,%s);",$idCliente, $numero, $label);
                    $this->_bd->sql->consulta($consulta, "agregartelefono($idCliente,$numero,$label) Cliente.class.php");
                }
                if($this->_bd->sql->lastError()==""){
                    return true;
                }else{
                    return false;
                }
            }
            return -1;
        }




        /**
            *Permite modificar un cliente de la base de datos. Recibe de parámetros el id del cliente 
            * a modificar y todos sus atributos uno a uno posteriormente
            *
            *<code>public function modificarCliente($_idCliente, $nombre, [, $direccion])</code>
            *@param int $_idCliente Con este Id sabremos que cliente modificar.}
            *@param string $nombre El nombre que se desea asignar al cliente en id $_idCliente
            *
            *<hr>Opcionales:
            *@param string $direccion La direccion donde vive el cliente
            *
            *@return {int} En caso de haber modificado con éxito regresa el id del cliente. 
            *En caso de error regresa false y -1 si el query no afectó nada
        */
        public function modificarCliente($_idCliente, $nombre, $direccion=null){
            
            $_idCliente=$this->_bd->valida->validaCampoNumerico($_idCliente);
            if(empty($idCliente)){
                $idCliente="NULL";
            }else{
                $idCliente="'".$idCliente."'";
            }
            $nombre=$this->_bd->valida->validaCampoTexto($nombre);
            if(empty($nombre)){
                $nombre="NULL";
            }else{
                $nombre="'".$nombre."'";
            }
            /*$correo=$this->_bd->valida->validaEmail($correo);
            if(empty($correo)){
                $correo="NULL";
            }else{
                $correo="'".$correo."'";
            }*/
            if(empty($direccion)){
                $direccion="NULL";
            }else{
                $direccion=$this->_bd->valida->SanitizarQuery($direccion);
            }

            if(!empty($_idCliente)&&!empty($nombre)){
                $modificacion = sprintf("update cliente set nombre=%s,direccion=%s WHERE idCliente=%s;",
                $nombre, $direccion, $_idCliente);

                $this->_bd->sql->consulta($modificacion,"modificarCliente($_idCliente,$nombre,$direccion) Cliente.class.php");
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
        
        public function modificaClienteCompleto($idCliente, $nombre, $email, $idDescuento, $direccion, $notas, $cat, $estado,
            $tipoTienda, $noTienda, $expo, $tipoDoc, $razSoc, $cb, $formaPago, $correoFac, $envioNombre, $fc, $tareas){
            $fc=$this->_bd->valida->validaCampoTexto($fc);
            $tareas=$this->_bd->valida->validaCampoTexto($tareas);
            $cat=$this->_bd->valida->validaCampoTexto($cat);
            $estado=$this->_bd->valida->validaCampoTexto($estado);
            $tipoTienda=$this->_bd->valida->validaCampoTexto($tipoTienda);
            $noTienda=$this->_bd->valida->validaCampoTexto($noTienda);
            $expo=$this->_bd->valida->validaCampoTexto($expo);
            $tipoDoc=$this->_bd->valida->validaCampoTexto($tipoDoc);
            $razSoc=$this->_bd->valida->validaCampoTexto($razSoc);
            $cb=$this->_bd->valida->validaCampoTexto($cb);
            $formaPago=$this->_bd->valida->validaCampoTexto($formaPago);
            $correoFac=$this->_bd->valida->validaCampoTexto($correoFac);
            $envioNombre=$this->_bd->valida->validaCampoTexto($envioNombre);
            $idCliente = $this->_bd->valida->validaCampoNumerico($idCliente);
            $idDescuento = $this->_bd->valida->validaCampoNumerico($idDescuento);
            $nombre = $this->_bd->valida->validaCampoTexto($nombre);
            $email = $this->_bd->valida->validaEmail($email);
            if(empty($direccion))
                $direccion = "NULL";
            else
                $direccion = $this->_bd->valida->SanitizarQuery($direccion);
            if(empty($notas))
                $notas = "NULL";
            else
                $notas = $this->_bd->valida->SanitizarQuery($notas);
            if(!empty($idCliente) && !empty($nombre)){
                $consulta = sprintf("update cliente set nombre='%s', email='%s', idDescuento=%d, direccion=%s, notasCliente=%s, categoria='%s', estado='%s', ".
                    "tipoTienda='%s', noTiendas='%s', expo='%s', tipoDocumento='%s', razonSocial='%s', cuentaBancaria='%s', formaPago='%s', ".
                    "correoFac='%s', envioNombre='%s', fechaContacto='%s', tareas='%s' where idCliente=%d", $nombre, $email, $idDescuento, $direccion, $notas,
                    $cat, $estado, $tipoTienda, $noTienda, $expo,$tipoDoc, $razSoc, $cb, $formaPago, $correoFac, $envioNombre, $fc, $tareas, $idCliente);
                $this->_bd->sql->consulta($consulta, "modificaClienteCompleto($idCliente, $nombre, $email) Cliente.class.php");
                if($this->_bd->sql->lastError() == "")
                    return true;
            }
            return false;
        }
        
        public function modificaTelefono($idTelefono, $label, $tel){
            $idTelefono = $this->_bd->valida->validaCampoNumerico($idTelefono);
            $label = $this->_bd->valida->validaCampoTexto($label);
            $tel = $this->_bd->valida->validaCampoTexto($tel);
            if(!empty($idTelefono)){
                if(empty($label) && empty($tel)){
                    $consulta = sprintf("delete from telefono where idTelefono=%d", $idTelefono);
                }else{
                    $consulta = sprintf("update telefono set label='%s', numero='%s' where idTelefono=%d", $label, $tel, $idTelefono);
                }
                $this->_bd->sql->consulta($consulta, "modificaTelefono($idTelefono, $label, $tel) Cliente.class.php");
            }
        }

        /**
            *Esta función filtra los clientes de la base de datos
            *
            *
            *La función filtra los registros en base en alguna coincidencia de $var en los campos correo, nombre o direccion 
            *
            *
            *<code>public function filtro($var) </code>
            *
            *@param string $var La cadena a buscar coincidencias
            *
            *@return {array} Devuelve el array de los elementos, false en caso de error
            * y -1 en caso de que no se haya encontrado ningun cliente con dicha coincidencia
        */
        public function filtro($var){

            $var=$this->_bd->valida->validaCampoTexto($var);

            if(!empty($var)){

                $var="%".$var."%";
                $query = sprintf("select idCliente, nombre, direccion FROM  cliente WHERE 
                 (nombre LIKE '%s') ;",$var,$var,$var);

                $this->_bd->sql->consulta($query, "filtro($var) Cliente.class.php");
                if($this->_bd->sql->lastError()==""){
                    if($this->_bd->sql->filasAfectadas()>0){
                        $_arre=array();
                        while($fila=$this->_bd->sql->extraerRegistro()){
                            $_arre[]=$fila;
                        }
                        return $_arre; 
                    }
                }else{
                    return false;
                }
            }
            return false;
        } 

        /**
            *Si se le envía un $idCliente, entonces devuelve los detalles del mismo
            *
            *
            *<code>public function getClientes([$idCliente]) </code>
            *
            *
            *<hr>Opcionales:
            *@param int $idCliente El identificador del cliente del que se desean sus telefonos
            *
            *
            *@return {array} Devuelve el array de los telefonos del cliente con id $idCliente
            *devuelve para cada fila: 
            *['idCliente'], ['numero'], ['label']
            *
            *Retorna false si se produce un error en la consulta, null si el arreglo está vacío.
        */
        public function getTelefonos($idCliente){
            $idCliente=$this->_bd->valida->validaCampoNumerico($idCliente);
            if(empty($idCliente)){
                $idCliente="NULL";
            }else{
                $idCliente="'".$idCliente."'";
            }
            
            if(!empty($idCliente)){
                $query="select idTelefono, idCliente, numero, label from telefono where idCliente=".$idCliente." order by idCliente;";

                $this->_bd->sql->consulta($query, "getTelefonos($idCliente) Cliente.class.php");
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
            }
            return false;
        }

        /**
            *
            *
            *<code>public function getClientes([$idCliente,] $n) </code>
            *
            *
            *
            *@param int idCliente El cliente del que se desean sus datos
            *@param int $n El número de página que se quiere de todos los registros (paginas de 30 registros)
            *
            *
            *@return {array} Devuelve el array de los telefonos del cliente indicado
            *devuelve para cada fila:
            *['idCliente'], ['nombre'], ['direccion']
            *
            *Retorna false si se produce un error en la consulta, null si el arreglo está vacío.
        */
        public function getClientes($idCliente=null){
            $idCliente=$this->_bd->valida->validaCampoNumerico($idCliente);
            if(!empty($idCliente)){
                $query = sprintf("select nombre,email,genero, direccion, idCliente, notasCliente, categoria, estado, ".
                    "tipoTienda, noTiendas, expo, tipoDocumento, razonSocial, rfc, cuentaBancaria, formaPago, correoFac, ".
                    "envioNombre, fechaContacto, tareas, idDescuento, saldo from cliente where idCliente=%d", $idCliente);
                $this->_bd->sql->consulta($query, "getClientes($idCliente) Cliente.class.php");
                $dato = $this->_bd->sql->extraerRegistro();
                return $dato;
            }
            return false;
        }
        
        public function accesoCliente($email, $contra){
            $email = $this->_bd->valida->validaEmail($email);
            $contraMD5 = md5("Ponla_dificil".$contra);
            if(!empty($email)){
                $consulta = sprintf("select idCliente from cliente where email='%s' and pass='%s'", $email, $contraMD5);
                $this->_bd->sql->consulta($consulta, "accesoCliente($email, $contra) Cliente.class.php");
                if($this->_bd->sql->lastError() == ""){
                    $dato = $this->_bd->sql->extraerRegistro();
                    return $dato['idCliente'];
                }
            }
            return false;
        }

        public function existeIdF($idF){
            $idF = $this->_bd->valida->validaCampoTexto($idF);
            if(!empty($idF)){
                $consulta = sprintf("select idCliente from cliente where idF='%s'", $idF);
                $this->_bd->sql->consulta($consulta, "existeIdF($idF) Cliente.class.php");
                if($this->_bd->sql->filasAfectadas()){
                    $dato = $this->_bd->sql->extraerRegistro();
                    return $dato['idCliente'];
                }
                return false;
            }
            return null;
        }
        
        public function asignarIdF($idCliente, $idF){
            $idCliente = $this->_bd->valida->validaCampoNumerico($idCliente);
            $idF = $this->_bd->valida->validaCampoTexto($idF);
            if(!empty($idCliente) && !empty($idF)){
                $consulta = sprintf("update cliente set idF='%s' where idCliente=%d", $idF, $idCliente);
                $this->_bd->sql->consulta($consulta, "asignarIdF($idCliente, $idF) Cliente.class.php");
                if($this->_bd->sql->filasAfectadas() == 1)
                    return true;
            }
            return false;
        }
        
        public function getClienteJson($opciones){
            $busqueda = $this->_bd->valida->validaCampoTexto($opciones['search']['value']);
            $pag = $this->_bd->valida->validaCampoNumerico($opciones['start']);
            $cant = $this->_bd->valida->validaCampoNumerico($opciones['length']);
            $draw = $this->_bd->valida->validaCampoNumerico($opciones['draw']);

            $consulta = "select count(*) from cliente where idCliente>0";        
            $this->_bd->sql->consulta($consulta, "getClienteJson() Cliente.class.php");
            $cuantos = $this->_bd->sql->extraerRegistro();
            $total = $cuantos['count(*)'];
            $filtrado = $total;
            if(!empty($busqueda)){
                $busqueda = sprintf(" and (nombre like '%%%s%%' or email like '%%%s%%')", $busqueda, $busqueda);
                $this->_bd->sql->consulta($consulta.$busqueda, "getClienteJson() Cliente.class.php");
                $cuantos = $this->_bd->sql->extraerRegistro();
                $filtrado = $cuantos['count(*)'];
            }

            $res['draw'] = $draw;
            $res['recordsTotal'] = $total;
            $res['recordsFiltered'] = $filtrado;

            $consult = "select idCliente, nombre, email, tiempo, categoria, tipoTienda, notasCliente from cliente where idCliente>0".$busqueda;
            $consult .= " order by nombre ".sprintf("limit %d, %d", $pag, $cant);
            $this->_bd->sql->consulta($consult, "getClienteJson() Cliente.class.php");
            $res['data'] = array();
            while($fila = $this->_bd->sql->extraerRegistro()){
                $res['data'][] = $fila;
            }
            return $res;
        }
        
        public function comprasConcretadas($idCliente){
            $idCliente = $this->_bd->valida->validaCampoNumerico($idCliente);
            if(!empty($idCliente)){
                $consulta = sprintf("select count(*) from carrito where idCliente=%d and entregado=1", $idCliente);
                $this->_bd->sql->consulta($consulta, "comprasConcretadas($idCliente) Cliente.class.php");
                $dato = $this->_bd->sql->extraerRegistro();
                return $dato['count(*)'];
            }
            return 0;
        }
        public function select2cliente($search=null, $pag=null, $tamPag=10){
            $consulta = "select idCliente as id, nombre as text, razonSocial, correoFac, idDescuento, saldo from cliente where idCliente>=0";
            $consultaTotal = "select count(*) as cuantos from cliente where idCliente>=0";
            $res = array();
            if(!empty($search)){
                $search = "%".$search."%";
                $consulta .= " and nombre like '%s'";
                $consultaTotal .= " and nombre like '%s'";
                $this->_bd->sql->consulta(sprintf($consultaTotal, $search));
            }else{
                $this->_bd->sql->consulta($consultaTotal);
            }

            $consulta .= " order by nombre";
            $cuantos = $this->_bd->sql->extraerRegistro();
            if(!empty($pag)){
                $consulta .= sprintf(" limit %d, %d", ($pag-1)*$tamPag, $tamPag);   
                $res['pagination']['count'] = $cuantos['cuantos'];
                $res['pagination']['more'] = $cuantos['cuantos'] > ($pag*$tamPag);
            }
            if(!empty($search)){
                $this->_bd->sql->consulta(sprintf($consulta, $search));
            }else{
                $this->_bd->sql->consulta($consulta);
            }
            $res['results'] = array();
            while($fila = $this->_bd->sql->extraerRegistro())
                $res['results'][] = $fila;
            return $res;
        }
    }
?>