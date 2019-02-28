<?php
// parametros: id
require("../../admin/classes/Categoria.class.php");
$cat = new Categoria("ajax/categoria/getCategorias.php");
$aux = $cat->getHijos($_POST['id']);
$msg = "Selecciona una subcategoría";
if(!empty($_POST['msg']))
    $msg = $_POST['msg'];
if(!empty($aux)){
    ?>
<option value=""><?=$msg;?></option>
    <?php
    foreach($aux as $fila){
        ?>
<option value="<?=$fila['idCategoria'];?>">
    <?=$fila['nombre'];?>
</option>
        <?php
    }
}
?>