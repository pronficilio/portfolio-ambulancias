function quiereCambiar(){
    $('#tabs a[href="#configuracion"]').tab('show');
    $('#subtab_configuracion a[href="#configuracion_perfil"]').tab('show');
}

$(function(){
    $(document).on("submit", "#form_datos_personales", function(e){
        e.preventDefault();
        if(!validaVacio("#user_nombre")){
            var error = false;
            var datos = $(this).serialize();
            if($("#user_contra1").val() != ""){
                error = validaVacio("#user_contra2");
                if(!error){
                    if($("#user_contra1").val() != $("#user_contra2").val()){
                        mensaje("Las contraseñas no coinciden", "alert-danger");
                        error = true;
                    }
                }
            }else{
                limpiaCampos("#user_contra1,#user_contra2", 1);
            }
            if(!error){
                $.post("ajax/acceso/cambiaDatos.php", datos, function(data){
                    if(data != ""){
                        toastr.error(data);
                    }else{
                        toastr.success("Se han cambiado los datos con éxito");
                        nombreUsuario();
                        limpiaCampos("#user_contra1,#user_contra2", 1);
                    }
                });
            }
        }
    });
})