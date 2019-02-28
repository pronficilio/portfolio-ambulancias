<?php
require_once("../../admin/classes/PHPExcel.php");
require_once("../../admin/classes/PHPExcel/IOFactory.php");
include("../../admin/classes/Cliente.class.php");
include("../../admin/classes/Carrito.class.php");
$cli = new Cliente("ajax/cliente/creaCliente.php");
$carr = new Carrito("ajax/cliente/creaCliente.php");
$aux = array();
$aux['file'] = $_FILES;
if(!empty($_FILES['importa_cli']['name'][0])){
    $aux['error'] = false;
    $direccion=$_FILES['importa_cli']['tmp_name'][0];
    $inputFileName = $direccion;
    $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
    $objeto = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
    if(!empty($objeto) && is_array($objeto)){
        if(count($objeto) > 2){
            $elPrimeroNo = 0;
            $_SESSION['ingreso'] = array();
            $aux['msg'] = "";
            foreach($objeto as $val){
                $elPrimeroNo++;
                if($elPrimeroNo > 2){
                    $direccion = "";
                    $aux['fila'][] = $val;

                    if(!empty($val['Q']))
                        $direccion = "Calle: ".$val['Q']."\n";
                    if(!empty($val['R']))
                        $direccion .= "Colonia: ".$val['R']."\n";
                    if(!empty($val['S']))
                        $direccion .= "Ciudad: ".$val['S']."\n";
                    if(!empty($val['E']))
                        $direccion .= "Estado: ".$val['E']."\n";
                    if(!empty($val['T']))
                        $direccion .= "C. P.: ".$val['T'];
                    $idCliente = $cli->agregarCliente($val['A'], $val['C'], 0, null, $direccion, $val['I'], $val['B'], $val['E'],
                                $val['F'], $val['G'], $val['H'], $val['J'], $val['K'], $val['L'], $val['M'],
                                $val['N'], $val['O'], $val['P'], $val['U'], $val['V']);
                    $aux['metidos'][] = $idCliente;
                    if(!empty($idCliente)){
                        if($idCliente != -1){
                            $tels = explode(",", $val['D']);
                            $labels = array("Movil", "Local", "Oficina");
                            foreach($tels as $i=>$v)
                                if(!empty($v))
                                    $cli->agregarTelefono($idCliente, $v, $labels[$i]);
                            
                        }else{
                            $aux['msg'] .= "Error en fila [".$elPrimeroNo."]: no se pudieron validar los datos.<br>";
                        }
                    }else{
                        $aux['msg'] .= "Error en fila [".$elPrimeroNo."]: el correo electrónico [".$val['C'].
                            "] está siendo usado por otra persona.<br>";
                    }
                }
            }
        }else{
            $aux['error'] = true;
            $aux['msg'] = "El archivo no tiene informacion";
        }
    }else{
        $aux['error'] = true;
        $aux['msg'] = "El archivo esta vacio";
    }

}else{
    $aux['error'] = true;
    $aux['msg'] = "No se recibio ningun archivo";
}

echo json_encode($aux);