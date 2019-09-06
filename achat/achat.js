var actualiza;
var longitud = 0;

function achat_actualizaMensajes(){
    $.ajax({
        cache: false,
        url:"achat/ajax/dameMensajes.php",
        success: function(data){
            $("#achat-contieneMensajes").html(data);
            if(data.length > longitud){
                longitud = data.length;
                var objDiv = document.getElementById("achat-contieneMensajes");
                objDiv.scrollTop = 10000;
            }else{
                if(data == ""){
                    clearInterval(actualiza);
                    $("#achat-contieneMensajes").addClass("achat-ocultamiento");
                    $("#achat-contieneMensajes").html("<div id='achat-instrucciones'><p>El chat ha finalizado</p></div>");
                }
            }
        }
    });
}

function achat_iniciaChat(){
    $.post("achat/ajax/iniciaChat.php",{
        "nombre": $("#achat-nombre_chat").val(),
        "email": $("#achat-email_chat").val(),
        "telefono": $("#achat-email_tel").val(),
        "pregunta": $("#achat-pregunta_chat").val()
    }, function(data){
        if(data == ""){
            $("#achat-contieneMensajes").removeClass("achat-ocultamiento");
            $("#achat-contieneMensajes").append("<div class='achat-mensaje'>"+
                                          "<p class='achat-yo'>"+$("#achat-pregunta_chat").val()+"</p>"+
                                          "</div>");
            actualiza = setInterval(achat_actualizaMensajes, 2000);
        }else{
            alert(data);
        }
    });
}

function achat_iniciaChat2(){
    $.ajax({
        cache: false,
        url: "achat/ajax/iniciaChat.php",
        type: "post",
        data: {
            "nombre": $("#achat-nombre_chat").val(),
            "email": $("#achat-email_chat").val(),
            "telefono": $("#achat-email_tel").val(),
            "pregunta": $("#achat-pregunta_chat").val(),
            "enviaMail": true
        },
        success: function(data){
            if(data == ""){
                $("#achat-instrucciones").html("<p class='media-middle'>Gracias, su solicitud será atendida a la brevedad</p>");
            }else{
                alert(data);
            }
        } 
    });
}

function achat_preparaChat(){
    var html = '<div id="achat-chat" class="achat-escondete"><p id="achat-cabecera">';
    $.get("achat/ajax/achatCabecera.php", function(cabecera){
        html += cabecera;
        html += '</p><div id="achat-contieneMensajes" class="achat-cuerpo achat-ocultamiento"><div id="achat-instrucciones">';
        $.get("achat/ajax/achatDisponible.json.php", function(disponible){
            if(disponible){
                html += 'Inicia el chat completando el siguiente formulario<label>Nombre:'+
                        '<input type="text" class="form-control" id="achat-nombre_chat"></label>'+
                        '<label>Email:<input type="email" class="form-control" id="achat-email_chat"></label>'+
                        '<label>Telefono:<input type="text" class="form-control" id="achat-email_tel"></label>'+
                        '<label>Pregunta:<input type="text" class="form-control" id="achat-pregunta_chat"></label>'+
                        '<p class="text-center"><button class="btn btn-primary" onclick="achat_iniciaChat();">Iniciar chat</button></p>';
            }else{
                html += 'Por favor deje sus datos para ser atendido más tarde.<label>Nombre:'+
                        '<input type="text" class="form-control" id="achat-nombre_chat"></label>'+
                        '<label>Email:<input type="email" class="form-control" id="achat-email_chat"></label>'+
                        '<label>Telefono:<input type="text" class="form-control" id="achat-email_tel"></label>'+
                        '<label>Pregunta:<input type="text" class="form-control" id="achat-pregunta_chat"></label>'+
                        '<p class="text-center"><button class="btn btn-primary" onclick="achat_iniciaChat2();">Enviar pregunta</button></p>';
            }
            html += '</div></div><div id="achat-mandaMensaje"><input type="text" class="form-control" '+
                    'id="achat-mandaMensaje_input" placeholder="Escribe un mensaje..."></div></div>';
            $("body").append(html);
            $.post("achat/ajax/dameMensajes.php", function(data){
                if(data != ""){
                    $("#achat-contieneMensajes").html(data);
                    $("#achat-contieneMensajes").removeClass("achat-ocultamiento");
                    var objDiv = document.getElementById("achat-contieneMensajes");
                    objDiv.scrollTop = 10000;
                    actualiza = setInterval(achat_actualizaMensajes, 2000);

                }
            });
        });
    });
}

$(function(){
    setTimeout(achat_preparaChat, 800);
    $(document).on("click", "#achat-cabecera", function(){
        if($(this).parent().hasClass("achat-escondete")){
            $(this).parent().removeClass("achat-escondete");
        }else{
            $(this).parent().addClass("achat-escondete");
        }
    });
    $(document).on("keypress", "#achat-mandaMensaje_input", function(e){
        if(e.which == 13) {
            $.post("achat/ajax/mandaMensaje.php", "mensaje="+$("#achat-mandaMensaje_input").val(), function(data){
                console.log(data);
                $("#achat-contieneMensajes").append("<div class='achat-mensaje'>"+
                                              "<p class='achat-yo'>"+$("#achat-mandaMensaje_input").val()+"</p>"+
                                              "</div>");
                $("#achat-mandaMensaje_input").val("");
                var objDiv = document.getElementById("achat-contieneMensajes");
                objDiv.scrollTop = 10000;
            });
        }
    });
});