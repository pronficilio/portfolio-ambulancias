<?php
// parametros: ninguno
require("../../admin/classes/Categoria.class.php");
$cat = new Categoria("ajax/categoria/getCategorias.php");
$aux = $cat->getCategorias();
$msg = "Selecciona una categoría";
if(!empty($_POST['msg']))
    $msg = $_POST['msg'];
?>
<option value=""><?=$msg;?></option>
<?php

if(empty($aux)){
    ?>
<option value="">
    Aún no hay elementos ingresados.
</option>
    <?php
}else{
    foreach($aux as $fila){
        ?>
<option value="<?=$fila['idCategoria'];?>">
    <?=$fila['nombre'];?>
</option>
        <?php
    }
}
?>