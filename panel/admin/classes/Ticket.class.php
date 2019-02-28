<?php 
	
	class Ticket{
		private $_bd;

		function __construct($permisos){
			require_once("permisos.php");
			$this->_bd=new Permisos($permisos);
		}

		/**
            *Permite agregar un ticket
            *a la base de datos
            *
            *<code>public function agregarTicket($idCliente, $idUsuario[ ,$estado, $total])</code>
            *
            *@param int $idCliente Identificador del cliente que genera el ticket
            *@param int $idUsuario Identificador del empleado que genera el ticket
            *@param boolean $estado Estado del ticket: 0 para abierto, 1 para cerrado
            *@param int $total Monto total del ticket
            *@param string $altaTicket Fecha en que se dió de alta el ticket
            *
            *
            *@return {int} En caso de haber insertado con éxito regresa el id del ticket. 
            *En caso contrario regresa false
        */
		public function agregarTicket($idCliente, $idUsuario ,$estado=null, $total=null){

			$idCliente=$this->_bd->valida->validaCampoNumerico($idCliente);
			if(empty($idCliente)){
				$idCliente="NULL";
			}
			$idUsuario=$this->_bd->valida->validaCampoNumerico($idUsuario);
			if(empty($idUsuario)){
				$idTicket="NULL";
			}
			$estado=$this->_bd->valida->validaCampoNumerico($estado);
			if(empty($estado)){
				$estado="NULL";
			}
			$total=$this->_bd->valida->validaCampoNumerico($total);
			if(empty($total)){
				$total="NULL";
			}

			if(!empty($idCliente)&&!empty($idUsuario)){
				$query = sprintf("insert into ticket (idCliente, idUsuario, estado, total) values 
					(%s, %s, %s, %s)", $idCliente, $idUsuario, $estado, $total);
                $this->_bd->sql->consulta($query, "agregarTicket($idCliente, $idUsuario ,$estado, $total) Ticket.class.php");
                if($this->_bd->sql->filasAfectadas()== 1){
                    return $this->_bd->sql->ultimoId();
                }
			}
			return false;
		}


		/**
            *Permite modificar los datos de un ticket
            *
            *
            *<code>public function modificarTicket($idTicket,$idCliente, $idUsuario [,$estado, $total])</code>
            *
            *@param int $idTicket Identificador único del ticket
            *@param int $idCliente Identificador del cliente que genera el ticket
            *@param int $idUsuario Identificador del empleado que genera el ticket
            *@param boolean $estado Estado del ticket: 0 para abierto, 1 para cerrado
            *@param int $total Monto total del ticket
            *@param string $altaTicket Fecha en que se dió de alta el ticket
            *
            *@return {int} En caso de haber modificado con éxito regresa el id del ticket. En caso de error regresa false
            *La funcion retorna -1 si no se afecto ninguna fila
        */
		public function modificarTicket($idTicket, $idCliente, $idUsuario, $estado=0, $total=0){

			$idTicket=$this->_bd->valida->validaCampoNumerico($idTicket);
			if(empty($idTicket)){
				$idTicket="NULL";
			}
			$idCliente=$this->_bd->valida->validaCampoNumerico($idCliente);
			if(empty($idCliente)){
				$idCliente="NULL";
			}
			$idUsuario=$this->_bd->valida->validaCampoNumerico($idUsuario);
			if(empty($idUsuario)){
				$idUsuario="NULL";
			}


			///opcionales
			$estado=$this->_bd->valida->validaCampoNumerico($estado);
			if(empty($estado)){
				$estado=0;
			}
			$total=$this->_bd->valida->validaCampoNumerico($total);
			if(empty($total)){
				$total=0;
			}

			if(!empty($idTicket)&&!empty($idCliente)&&!empty($idUsuario)){
				$query = sprintf("update ticket set idCliente=%s, idUsuario=%s, estado=%s, total=%s Where idTicket=%s;"
					,$idCliente, $idUsuario, $estado, $total, $idTicket);
                $this->_bd->sql->consulta($query, "modificarTicket($idCliente $idUsuario $estado $total) Ticket.class.php");
                if($this->_bd->sql->lastError()==""){
                    if($this->_bd->sql->filasAfectadas()== 1){
                        return $this->_bd->sql->ultimoId();
                    }else{
                        return -1;
                    }
                }
                return false;
			}
			return -1;
		}


		/**
            *Esta función cierra el estado de un ticket
            *
            *<code>public function cerrarTicket($idTicket) </code>
            *
            *@param int $idTicket El identificador del ticket a cerrar
            *
            *@return {int} Devuelve el id del ticket que se cerro
            *, false si hubo un error y -1 si no se logro leer el idTicket
        */
        public function cerrarTicket($idTicket){
			$idTicket=$this->_bd->valida->validaCampoNumerico($idTicket);
        	if(!empty($idTicket)){
        		$query = sprintf("update ticket set estado=1 Where idTicket=%s;",$idTicket);
            	$this->_bd->sql->consulta($query, "cerrarTicket($idTicket) ticket.class.php");
                if($this->_bd->sql->lastError()==""){
                    return $idTicket;
                }
                return false;
        	}
            return -1;
        }

        /**
            *Esta función abre el estado de un ticket
            *
            *<code>public function abrirTicket($idTicket) </code>
            *
            *@param int $idTicket El identificador del ticket a cerrar
            *
            *
            *@return {int} Devuelve el id del ticket que se cerro, -1 si no se pudo leer el idTicket
            *o false si hubo un error
        */
        public function abrirTicket($idTicket=null){
			$idTicket=$this->_bd->valida->validaCampoNumerico($idTicket);
        	if(!empty($idTicket)){
        		$query = sprintf("update ticket set estado=0 Where idTicket=%s;",$idTicket);
            	$this->_bd->sql->consulta($query,"abrirTicket($idTicket) ticket.class.php");
                if($this->_bd->sql->lastError()==""){
                    return $idTicket;
                }
                return false;
        	}
            return -1;
        }


        /**
            *Esta función regresalos tickets segun se solicite:
            *
            *Si se le envía un $idUsuario retorna todos los tickes de ese empleado
            *
            *Si se le envía un $idCliente retorna todos los ticket 
            *
            *Si se le envía un $idTicket retorna los datos de ese $idTicket
            *
            *<code>public function getTickets([$idTicket, $idCliente, $idUsuario]) </code>
            *
            *@param int $idTicket El identificador del ticket del que se quieren sus datos
            *@param int $idCliente El cliente del que se quieren los tickets
            *@param int $idUsuario El empleado del que se quieren los tickets
            *
            *
            *@return {array} Devuelve el array de los elementos solicitados
            *Para cada fila: ['idTicket'], ['idCliente'],['cliente'], ['idUsuario'], ['usuario'], ['estado'], ['total'], ['altaTicket']
        */
        public function getTickets($idTicket=null, $idCliente=null, $idUsuario=null){
            $idTicket=$this->_bd->valida->validaCampoNumerico($idTicket);
            $idCliente=$this->_bd->valida->validaCampoNumerico($idCliente);
            $idUsuario=$this->_bd->valida->validaCampoNumerico($idUsuario);

            $query="select ticket.idTicket as idTicket, ticket.idCliente as idCliente, ticket.idUsuario as idUsuario, ticket.estado as estado, 
            ticket.total as total, ticket.altaTicket as altaTicket, cliente.nombre as cliente, empleado.nombre as usuario 
            from ticket inner join cliente on ticket.idCliente=cliente.idCliente left join empleado on ticket.idUsuario=empleado.idEmpleado 
            where ticket.estado=0 ";

            if(!empty($idUsuario)){
                $query.=sprintf("and ticket.idUsuario=%s ",$idUsuario);
            }else{
                if(!empty($idCliente)){
                    $query.=sprintf("and ticket.idCliente=%s ",$idCliente);
                }else{
                    if(!empty($idTicket)){
                        $query.=sprintf("and ticket.idTicket=%s ",$idTicket);
                    }
                }
            }
            ///se insertan en orden cronológico y por tanto se retornan en ese orden (altaTicket)

            $this->_bd->sql->consulta($query, "getTickets($idTicket, $idCliente, $idUsuario) Ticket.class.php");
            if($this->_bd->sql->filasAfectadas()>0){
                $arreglo=array();
                while($fila=$this->_bd->sql->extraerRegistro()){                  
                    $arreglo[]=$fila;
                }
            }
            if(count($arreglo)==0){
                return null;
            }
            return $arreglo;

        }

	}

?>