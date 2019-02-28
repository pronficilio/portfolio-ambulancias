<?php
// parametros: ninguno
require("../../admin/classes/Categoria.class.php");
$cat = new Categoria("ajax/categoria/getCategorias.php");
$aux = $cat->getSubcategoriasJson($_GET);
$todo = $cat->getCategoriasFull();


function familia($t, $id){
    $texto = "";
    $nav = $id;
    $fam = array();
    while(!empty($nav)){
        $fam[] = $t[$nav]['nombre'];
        $nav = $t[$nav]['enlace'];
    }
    $cuenta = 0;
    $fam = array_reverse($fam);
    foreach($fam as $val){
        if($texto != "")
            $texto .= "<br>";
        for($i=0; $i<$cuenta; $i++)
            $texto .= "&nbsp;";
        $cuenta+=2;
        $texto .= $val;
    }
    return $texto;
}


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
            $lina = $cat->getLinaje($fila['idCategoria']);
            $lina[] = $fila['idCategoria'];
            //$u = urlencode(openssl_encrypt(implode(",", $lina), "blowfish", "Pronfi", 0, "cilio123"));
            $epa = '[
            "'.$contador++.'",
            ';
            $epa .= "\"<span class='text-muted'>".familia($todo, $fila['enlace'])."</span><br><span class='text-muted _tool' data-toggle='tooltip' title='Código de la subcategoria'><span class='glyphicon glyphicon-link'></span>".$u."</span>\",
            ";
            $epa .= "\"<div class='' data-original='".$fila['nombre']."'>";
            $epa .= "<input type='text' class='form-control' value='".$fila['nombre']."' disabled>";
            $epa .= "<span class='help-block'>Este campo es obligatorio</span></div>\",
            ";
            /*$epa .= "\"".$fila['nombre']."\",
            ";*/
            $epa .= "\"<div class='text-center'><div class='btn-group' role='group' data-idCategoria='".$fila['idCategoria']."'>";
            $epa .= "<button type='button' class='btn btn-default editaSubcategoria _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"0\\\"}' title='Clic para ver opciones de edición'>";
            $epa .= "<span class='glyphicon glyphicon-pencil'></span>";
            $epa .= "</button>";
            $epa .= "<button type='button' class='btn btn-default fade actualizaSubcategoria invisible _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"0\\\"}' title='Clic para aceptar cambios'>";
            $epa .= "<span class='glyphicon glyphicon-ok-sign'></span>";
            $epa .= "</button>";
            $epa .= "<button type='button' class='btn btn-default fade cancelaEdicionSubcategoria invisible _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"0\\\"}' title='Clic para descartar cambios'>";
            $epa .= "<span class='glyphicon glyphicon-remove-sign'></span>";
            $epa .= "</button>";
            $epa .= "<button type='button' class='btn btn-default fade borraSubcategoria invisible _tool' data-toggle='tooltip' data-delay='{\\\"show\\\":\\\"500\\\", \\\"hide\\\":\\\"0\\\"}' title='Clic para eliminar la subcategoría'>";
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