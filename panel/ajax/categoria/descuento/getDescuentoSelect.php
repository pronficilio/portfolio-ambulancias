<?php
// parametros: ninguno
require("../../../admin/classes/Categoria.class.php");
$cat = new Categoria("ajax/categoria/descuento/getColorSelect.php");
$aux = $cat->getDescuento();
?>
<option value="0" data-valor="0">Sin descuento (0%)</option>
<?php

if(!empty($aux)){
    foreach($aux as $fila){
        ?>
<option value="<?=$fila['idDescuento'];?>" data-valor="<?=$fila['valor'];?>">
    <?=$fila['nombre'];?> - (<?=$fila['valor'];?>%)
</option>
        <?php
    }
}
?>