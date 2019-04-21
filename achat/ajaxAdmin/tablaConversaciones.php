<?php
include("../../panel/admin/classes/Achat.class.php");
$chat = new Achat("ajax/verificaChat.php");
$datos = $chat->dameChatCerrados();
$contador = 1;
foreach($datos as $val){
    ?>
<tr>
    <td><?=$contador++;?></td>
    <td><?=$val['nombre'];?></td>
    <td><?=$val['email'];?></td>
    <td><p class='small'><?=$val['preguntaInicial'];?></p></td>
    <td>
        <?=date("d-m-Y", strtotime($val['tiempo']));?>
        <p class='text-muted'><?=date("H:i", strtotime($val['tiempo']));?></p>
    </td>
    <td>
        <button class="btn btn-primary" onclick="verChat(<?=$val['idChat'];?>, '<?=$val['nombre'];?>');">
            <span class="glyphicon glyphicon-comment"></span>
        </button>
    </td>
</tr>
    <?php
}