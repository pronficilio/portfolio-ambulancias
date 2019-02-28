<?php
require("../../../admin/classes/Categoria.class.php");
$cat = new Categoria("ajax/categoria/color/getColores.json.php");
$aux = $cat->getDescuentoJson($_GET);
header('Content-Type: application/json; charset=utf-8'); 
?>
{
    "draw" : "<?=$aux['draw'];?>",
    "recordsTotal" : "<?=$aux['recordsTotal'];?>",
    "recordsFiltered" : "<?=$aux['recordsFiltered'];?>",
    "data" : [
        <?php
        $contador = 1;
        $total = array();
        foreach($aux['data'] as $fila){
            $epa = '[
            "'.$contador++.'",
            ';
            $epa .= "\"<div class='' data-original='".$fila['nombre']."'>";
            $epa .= "<input type='text' class='form-control' value='".$fila['nombre']."' disabled>";
            $epa .= "<span class='help-block'>Este campo es obligatorio</span></div>\",
            ";
            $epa .= "\"<div class='' data-original='".$fila['valor']."'>";
            $epa .= "<input type='number' class='form-control' value='".$fila['valor']."' disabled>";
            $epa .= "<span class='help-block'>Este campo es obligatorio</span></div>\",
            ";
            $epa .= "\"<div class='text-center'><div class='btn-group' role='group' data-idDescuento='".$fila['idDescuento']."'>";
            $epa .= "<button type='button' class='btn btn-default editaDescuento _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"0\\\"}' title='Clic para ver opciones de edición'>";
            $epa .= "<span class='glyphicon glyphicon-pencil'></span>";
            $epa .= "</button>";
            $epa .= "<button type='button' class='btn btn-default fade actualizaDescuento invisible _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"0\\\"}' title='Clic para aceptar cambios'>";
            $epa .= "<span class='glyphicon glyphicon-ok-sign'></span>";
            $epa .= "</button>";
            $epa .= "<button type='button' class='btn btn-default fade cancelaEdicionDescuento invisible _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"0\\\"}' title='Clic para descartar cambios'>";
            $epa .= "<span class='glyphicon glyphicon-remove-sign'></span>";
            $epa .= "</button>";
            $epa .= "<button type='button' class='btn btn-default fade borraDescuento invisible _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"0\\\"}' title='Clic para eliminar el descuento'>";
            $epa .= "<span class='glyphicon glyphicon-trash'></span>";
            $epa .= "</button>";
            $epa .= "</div></div>\"
        ]";
            $total[] = $epa;
        }
        echo implode(", ", $total);
        ?>
        
    ]
}