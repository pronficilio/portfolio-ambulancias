<?php
// parametros: ninguno
require("../../admin/classes/Categoria.class.php");
$cat = new Categoria("ajax/categoria/getCategorias.php");
$aux=$cat->getCategorias();

if(empty($aux)){
    ?>
<div class="alert alert-info">
    Aún no hay elementos ingresados.
</div>
    <?php
}else{
    $tiempo = microtime(true);
    ?>
<table class="table table-striped table-hover has-feedback">
    <thead>
        <tr>
            <th></th>
            <th>Nombre</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php
    $contador = 1;
    for($x=1; $x<=50; $x++)
    foreach($aux as $fila){
        ?>
        <tr>
            <td>[<?=$contador++;?>]</td>
            <td class="form-group" data-original="<?=$fila['nombre'];?>">
                <input type="text" class="form-control" value="<?=$fila['nombre'];?>" disabled>
                <span class="help-block">Este campo es obligatorio</span>
            </td>
            <td class="text-center">
                <div class="btn-group" role="group" data-idCategoria="<?=$fila['idCategoria'];?>">
                    <button type="button" class="btn btn-default editaCategoria">
                        <span class="glyphicon glyphicon-pencil"></span>
                    </button>
                    <button type="button" class="btn btn-default fade in actualizaCategoria invisible">
                        <span class="glyphicon glyphicon-ok-sign"></span>
                    </button>
                    <button type="button" class="btn btn-default fade in cancelaEdicionCategoria invisible">
                        <span class="glyphicon glyphicon-remove-sign"></span>
                    </button>
                </div>
            </td>
        </tr>  
        <?php
    }
    ?>
    </tbody>
</table>
    <?php
    
    echo "tiempo PHP: ".(microtime(true)-$tiempo);
}
?>