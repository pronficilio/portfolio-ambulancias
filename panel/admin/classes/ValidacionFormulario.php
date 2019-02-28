<?php
error_reporting(0);
class ValidacionFormulario
{
    
	public function SanitizarQuery($valor){
		// Retirar las barras si esta activado maqic_quotes
    	if (get_magic_quotes_gpc()) {
        $valor = stripslashes($valor);
    	}

    	// Colocar comillas si no es entero
    	if (!is_numeric($valor)) {
        $valor = "'" . mysql_real_escape_string($valor) . "'";
    	}
    
		return $valor;
	}
	
   	
	function validaCodigoHTML($cadena)
    {	    	
		return strip_tags($cadena);
    }
	
    public function limpiaAcentos($texto){
        $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                                    'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                                    'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                                    'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                                    'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
        $texto = strtr($texto, $unwanted_array );
        return $texto;
    }
	
	function strip_selected_tags($text, $tags = array()){
    $args = func_get_args();
    $text = array_shift($args);
    $tags = func_num_args() > 2 ? array_diff($args,array($text))  : (array)$tags;
    foreach ($tags as $tag){
        while(preg_match('/<'.$tag.'(|\W[^>]*)>(.*)<\/'. $tag .'>/iusU', $text, $found)){
            $text = str_replace($found[0],$found[2],$text);
        }
    }
	echo preg_replace('/(<('.join('|',$tags).')(|\W.*)\/>)/iusU', '', $text);
    return preg_replace('/(<('.join('|',$tags).')(|\W.*)\/>)/iusU', '', $text);
	}

    
    
    public function validaCampoTexto($cadena)
    {
        $cadena = trim($cadena);
        $cadenaLong = strlen($cadena);
        if (!empty($cadena)) {
            if (!eregi("SELECT", $cadena) && !eregi("INSERT", $cadena) && !eregi("UPDATE", $cadena) &&!eregi("DELETE", $cadena)) {
                if (!ereg("^([\]){".$cadenaLong."}",$cadena)) {
                    $cad = strip_tags($cadena);
                    return $cad;
                } else {
                    return false;
                } 
            }
        }
        return false;
    }
    
    
    function validaCampoMoneda($numero)
    {
         if (empty($numero)) {
              return false;
  		 } else {
              $numero = trim($numero);
              $chars= strlen($numero);
       		  if ($chars <= 18 ) {
         	       $arInvalid = array('$'=>'', ','=>'');
      			   $numero = strtr($numero,$arInvalid);  
      			   $newVal = floatval($numero);
      		       return $newVal;
              } else {
    		       return false;
              } 
		 }
   }  
	  
	  
    function validaCampoNumerico($numero)
    {
        $numero=str_replace("$","",$numero);
        $numero=str_replace(",","",$numero);
        $num=trim($numero);
        if (strlen($num)!=0) {
            if (!is_numeric($num)) {
                return 0;
            } else {
                return $num;
            }
        }
        return false;
    }
    
    
    function validaCodigoPostal($codigo_postal)
    {
        if (!empty($codigo_postal)) {
            $codigo_pp =(strlen(trim($codigo_postal)));
            $codigo_p = (strip_tags($codigo_postal));
            if (($codigo_pp > 5) or(ctype_alpha($codido_p) === true)) {
                return false;
            } else {
                return  $codigo_p;
            }
        }
    }
    
    function validaPassword($password,$tamanoMin)
    {
        if (empty($tamanoMin)) {
            return false;
        }
        if (!empty($password)) {
            if (ctype_space($password)) {
                return false;
            }
            $pass = (strip_tags($password));
            $passw = (strlen(trim($pass)));
            if ($passw < $tamanoMin) {
                return false;
            } else {
                return true;
            }
        }
        return false;
    }
    
    public function validaEmail($email)
    {
        $mail_correcto = 0;
        if ((strlen($email) >= 6) && (substr_count($email,"@") == 1) && (substr($email,0,1) != "@") && (substr($email,strlen($email)-1,1) != "@")) {
            if ((!strstr($email,"'")) && (!strstr($email,"\"")) && (!strstr($email,"\\")) && (!strstr($email,"$")) && (!strstr($email," "))) {
                if (substr_count($email,".")>= 1) {
                    $term_dom = substr(strrchr($email, '.'),1);
                    if (strlen($term_dom)>1 && strlen($term_dom)<5 && (!strstr($term_dom,"@")) ) {
                        $antes_dom = substr($email,0,strlen($email) - strlen($term_dom) - 1);
                        $caracter_ult = substr($antes_dom,strlen($antes_dom)-1,1);
                        if ($caracter_ult != "@" && $caracter_ult != ".") {
                            $mail_correcto = 1;
                        }
                    }
                }
            }
        }
        if ($mail_correcto) {
            return $email;
        } else {
            return "";
        }
    }
	
	
	//Filtra todos los caracteres excepto los alfanum�ricos y el "_"
	function campo_telefono(&$var) {
	$sinfiltrar = $var;
	$var = preg_replace("/[^0-9-]/", "", $var);		
		return $var;
	}
	
	//Filtra todos los caracteres excepto los alfanum�ricos y el "_"
	function filtro_alfanumerico(&$var) {
	$sinfiltrar = $var;
	$var = preg_replace("/[^A-Za-z0-9_]/", " ", $var);		
		return $var;
	}
	
	/* Filtra todos los caracteres excepto los num�ricos */
	function filtro_numerico(&$var) {
	$sinfiltrar = $var;
	$var = preg_replace("/[^0-9.]/", "", $var);		
		return $var;
	}
	
	/* Sanitized Vars Routine */
	/* Sanitized Vars Routine by RoMaNSoFt (r0man@phreaker.net) */ 
	function sanitize_vars() {
	$magic_quotes = get_magic_quotes_gpc();
	foreach ($GLOBALS as $var => $value) {
		if (is_array($value)) {
			foreach ($value as $i => $j) {
				if ($magic_quotes)
				$GLOBALS[$var][$i] = htmlentities($j);
				else
				$GLOBALS[$var][$i] = addslashes(htmlentities($j));
				}
		} else {
			if ($magic_quotes)
			$GLOBALS[$var] = htmlentities($value);
			else
			$GLOBALS[$var] = addslashes(htmlentities($value));
		}
	}
	}
	
	
	function sanitize_vars_fixed() {
	$vars=$GLOBALS;
	
	if(count($vars)!=0){
		foreach ($GLOBALS as $var => $value) {
			if (is_array($value)) {
				foreach ($value as $i => $j) {	
						
				$j = preg_replace("/\\\\/", "", $j);
				
				$GLOBALS[$var][$i] = addslashes(htmlentities($j,ENT_QUOTES));
				}
			} else {
			$value = preg_replace("/\\\\/", "", $value);
			$GLOBALS[$var] = addslashes(htmlentities($value,ENT_QUOTES));
			}
		}
	}
	}    
	
	//Limpiar la variable de html y comillas
	function sanitizar($variable) {
	$value=trim($variable);			
		$value = preg_replace("/\\\\/", "", $value);
		$variable_sanitizada = addslashes(htmlentities($value,ENT_QUOTES,'utf-8'));
		return $variable_sanitizada;		
	
	}  
	
	//Limpiar la variable de html y comillas
	function sanitizar_panel($variable) {
	$value=trim($variable);
			
		$value = preg_replace("/\\\\/", "", $value);
		$variable_sanitizada = addslashes(htmlentities($value,ENT_QUOTES));
		return $variable_sanitizada;		
	
	}  
		
	//funci�n que sanea la variable
	function limpiar_variable($valor){
		//verificar si esta activa magic_quotes y si es as� se remueve el escape a la variable
		if(get_magic_quotes_gpc())
    		$valor = stripslashes($valor);	
			//se quitan las diagonales
			$value = preg_replace("/\\\\/", "", $valor);
			//se quitan las comillas simples			
			$value2 = preg_replace("/'/", "", $value);
			
			//se retorna el valor
		return strip_tags(trim($value2));
	
	}	
	
	//Otra funci�n para sanear datos, se utiliza actualmente en los formularios de index
	function limpiar_var($valor){
	//$res = htmlspecialchars(trim($valor),ENT_QUOTES);	
	/*if(!get_magic_quotes_gpc()) {
        $valor = addslashes($valor);    	
	return $valor;
	}else{*/
	return htmlentities(trim($valor),ENT_QUOTES);
	//}
	 /*$value = preg_replace("/\\\\/", "", $valor);
		$variable_sanitizada = addslashes(htmlentities($value,ENT_QUOTES));
		return $variable_sanitizada;*/
	}	
	
	
	
	
	function SanitizarEntry($valor){
		// Retirar las barras si esta activado maqic_quotes
    	if (!get_magic_quotes_gpc()) {
        $valor = addslashes(trim($valor));
    	}

    	// Colocar comillas si no es entero
    	
        $valor = "'" . mysql_real_escape_string($valor) . "'";
    	
		return $valor;
	}
	
	
	//Redireccionar a una url
	public function Redireccionar($url){
	echo '<script type="text/javascript">window.location="'.$url.'"</script>';
	//exit;
	}
		
	//Alerta javascript
	public function Alerta($aviso){
	echo '<script type="text/javascript">alert("'.$aviso.'");</script>';
	//exit;
	}  	

}
?>