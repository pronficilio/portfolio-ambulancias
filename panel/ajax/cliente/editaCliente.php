<?php
include("../../admin/classes/Cliente.class.php");
$cli = new Cliente("ajax/cliente/creaCliente.php");

$direccion = "";
if(!empty($_POST['calle']))
    $direccion = "Calle: ".$_POST['calle']."\n";
if(!empty($_POST['entrecalle']))
    $direccion .= "Entre calles: ".$_POST['entrecalle']."\n";
if(!empty($_POST['colonia']))
    $direccion .= "Colonia: ".$_POST['colonia']."\n";
if(!empty($_POST['ciudad']))
    $direccion .= "Ciudad: ".$_POST['ciudad']."\n";
if(!empty($_POST['estado']))
    $direccion .= "Estado: ".$_POST['estado']."\n";
if(!empty($_POST['cp']))
    $direccion .= "C. P.: ".$_POST['cp'];

$i = 0;
while($i < count($_POST['label'])){
    if(!empty($_POST['idTel'][$i])){
        $cli->modificaTelefono($_POST['idTel'][$i], $_POST['label'][$i], $_POST['telefono'][$i]);    
    }else{
        if(!empty($_POST['telefono'][$i]))
            $cli->agregarTelefono($_POST['idCliente'], $_POST['telefono'][$i], $_POST['label'][$i]);
    }
    $i++;
}
if(!$cli->modificaClienteCompleto($_POST['idCliente'], $_POST['nombre'], $_POST['email'], $_POST['descuento'], $direccion, $_POST['notas'], $_POST['categoria'], $_POST['estado'],
            $_POST['tipoTienda'], $_POST['noTienda'], $_POST['expo'], $_POST['tipoDoc'], $_POST['razSoc'], $_POST['cb'],
            $_POST['formaPago'], $_POST['correoFac'], $_POST['envioNombre'], $_POST['fechaContacto'], $_POST['tareas'])){
    echo "Error: No se pudieron validar algunos datos";
}

/*$idCliente = $cli->editaCliente($_POST['nombre'], $_POST['email'], null, $direccion, $_POST['notas']);

if(!empty($idCliente)){
    if($idCliente != -1){
        foreach($_POST['telefono'] as $ind=>$val)
            if(!empty($val))
                $cli->agregarTelefono($idCliente, $val, $_POST['label'][$ind]);
    }else{
        echo "Error: no se pudieron validar los datos.";
    }
}else{
    echo "Error: el correo electrónico está siendo usado por otra persona";
}*/