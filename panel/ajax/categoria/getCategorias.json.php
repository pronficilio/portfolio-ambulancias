<?php
// parametros: ninguno
require("../../admin/classes/Categoria.class.php");
$cat = new Categoria("ajax/categoria/getCategorias.php");
$aux = $cat->getCategoriasJson($_GET);
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
            /*$epa .= "\"".$fila['nombre']."\",
            ";*/
            $epa .= "\"<div class='text-center'><div class='btn-group' role='group' data-idCategoria='".$fila['idCategoria']."'>";
            $epa .= "<button type='button' class='btn btn-default editaCategoria _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"0\\\"}' title='Clic para ver opciones de edición'>";
            $epa .= "<span class='glyphicon glyphicon-pencil'></span>";
            $epa .= "</button>";
            $epa .= "<button type='button' class='btn btn-default fade actualizaCategoria invisible _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"0\\\"}' title='Clic para aceptar cambios'>";
            $epa .= "<span class='glyphicon glyphicon-ok-sign'></span>";
            $epa .= "</button>";
            $epa .= "<button type='button' class='btn btn-default fade cancelaEdicionCategoria invisible _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"0\\\"}' title='Clic para descartar cambios'>";
            $epa .= "<span class='glyphicon glyphicon-remove-sign'></span>";
            $epa .= "</button>";
            $epa .= "<button type='button' class='btn btn-default fade borraCategoria invisible _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"0\\\"}' title='Clic para eliminar la categoría'>";
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