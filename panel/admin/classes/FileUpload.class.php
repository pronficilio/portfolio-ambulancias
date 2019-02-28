<?php
/**
 * File upload class
 *
 */
class FileUpload {
	
	const UPLOAD_ERROR_DIR_NOT_FOUND = 3;
	const UPLOAD_ERROR = 2;
	const UPLOAD_OK = 1;
	
	public $files = null;
	public $validtypes = array('jpg', 'png', 'jpeg', 'gif');
	
	public $uploaded_files = array();
    public $error_files = array();
	/**
	 * @param Array $file_ref
	 */
    public function __construct(&$file_ref) {
    	$this->files = $file_ref;
    }
    
    /**
     * set available files extensions
     *
     * @param Array $types
     */
    public function setValidTypes($types){
    	$this->validtypes = $types;
    }
    
    public function compress($source, $destination, $quality=75) {
        $info = getimagesize($source);
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
        }
        if ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($source);
        }
        if ($info['mime'] == 'image/png'){
            copy($source, $destination);
            return;
        }
        imagejpeg($image, $destination, $quality);
    }
    

    /**
     * check all files, i one file is invalid return false
     * 
     * @return boolean
     */
    public function checkAllFiles($nameEspecifico = ""){
        if (is_array($this->files) && !empty($this->files)){
            $files = 0;
            foreach ($this->files as $key => $item){
                if($nameEspecifico != "")
                    if($key != $nameEspecifico)
                        continue;
                foreach($item['name'] as $ind=>$val){
                    if (!empty($val)){ 
                        if (!$this->checkFile($val)){
                            return false;
                        }else {
                            $files++;
                        }
                    }
                }
            }
            if ($files>0){
                return true;
            }
        }
        return false;
    }
    
    /**
     * save all files to directory
     *
     * @param string $dir
     * @param boolean $replace
     * @return int
     */
    public function saveTo($dir, $nameEspecifico = "", $replace = false){
    	$this->error_files = array();
        global $aux;
    	if(file_exists($dir)){
    		if(is_array($this->files)){
	    		foreach($this->files as $key => $item){
                    $aux['aqui'][] = $key;
                    if($nameEspecifico != "")
                        if($key != $nameEspecifico)
                            continue;
                    
                    foreach($item['name'] as $ind=>$val){
                        if($this->checkFile($val)){
                            $val = $this->prepareFileName($val);
                            $aux['preparado'] = 1;
                            if(file_exists($dir.'/'.$val) && !$replace){ // jei toks failas jau egsistuoja keiciam pavadinima
                                $val = time().$val;
                            }
                            if (move_uploaded_file($item['tmp_name'][$ind],$dir.'/'.$val)){
                                chmod($dir.'/'.$val, 0666);
                                $this->uploaded_files[] = array(
                                    'path' => "admin/archivos/".$val,
                                    'pathReal' => $dir.'/'.$val,
                                    'name' => $val
                                );
                            }else {
                                $this->error_files[] = $val.": No se pudo guardar el archivo.";
                            }
                        }else{
                            $this->error_files[] = $val." Formato incorrecto";
                        }
                    }			
	    		}
    		}
    		if(count($this->error_files)){
    			return FileUpload::UPLOAD_ERROR;
    		}else {
    			return FileUpload::UPLOAD_OK;
    		}
    	}else {
    		return FileUpload::UPLOAD_ERROR_DIR_NOT_FOUND; 
    	}
    }
    
    /**
     * create directory if not exists
     */
    public function createDirIfNotExists($dir){
        if (!file_exists($dir)){
    		if (mkdir ($dir,0755,true)){
    			return true;
    		}else {
    			return false;
    		}
    	}
    	return true; 
    }
    
    /**
     * get uploaded files info
     */
    public function getUploadedFilesInfo(){
    	return $this->uploaded_files;
    }
    
    public function cleanUploadedFilesInfo(){
        $this->uploaded_files = array();
    }
    
    public function getErrorFilesInfo(){
        return $this->error_files;
    }
    /**
     * check one file
     */
    private function checkFile($name){
    	if (preg_match('/\.('.implode('|',$this->validtypes).')$/i',$name)){
    		return true;
    	}else {
    		return false;
    	}
    }

    /**
     *prepare file namee
     */
    public function prepareFileName($name){
    	  $matches = preg_split('/\./',$name);
    	  $filename_ext = $matches[count($matches)-1];
    	  unset($matches[count($matches)-1]);
    	  $filename_body = implode('_',$matches);
    	  $name = ($filename_body).'.'.$filename_ext;
    	  return $name;
    }
}
?>
