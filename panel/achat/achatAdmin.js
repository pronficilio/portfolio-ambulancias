var chatsActuales = [];
var longi = [];
var achat_A;
var achat_B;

function actualizaMensajes(idChat){
    $.post("achat/ajaxAdmin/estadoChat.php", "idChat="+idChat, function(data){
        if(parseInt(data) == 1){
            $.post("achat/ajaxAdmin/dameMensajes.php", "idChat="+idChat, function(dat2){
                $("#chat_"+idChat).children(".contieneMensajes").html(dat2);
                var donde = $.inArray(idChat, chatsActuales);
                if(dat2.length > longi[donde]){
                    longi[donde] = dat2.length;
                    $("#chat_"+idChat).children(".contieneMensajes").scrollTop(10000);
                }
                setTimeout(function(){
                    actualizaMensajes(idChat);
                }, 5000);
            });
        }else{
            $("#chat_"+idChat).parent().remove();
        } 
    });
}

function actualizaChats(){
    $.ajax({
        cache: false,
        url: "achat/ajaxAdmin/dameChats.php",
        success: function(data){
            data.forEach(function(e, i){
                if($.inArray(e.idChat, chatsActuales) == -1){
                    $("#contenedorChats").append("<div class='col-md-4'>"+
                                                 "<div class='chat' data-idChat='"+e.idChat+"' id='chat_"+e.idChat+"'>"+
                                                 "<p class='cabecera'>"+e.nombre+"<br><small>"+e.email+
                                                 (e.tel!=""?" - "+e.tel:"")+"</small></p>"+
                                                 "<div class='contieneMensajes cuerpo'></div>"+
                                                 "<div class='mandaMensaje'>"+
                                                 "<input type='text' class='form-control inputmensaje' placeholder='Escribe un mensaje...'>"+
                                                 "</div></div><br>"+
                                                 "<p class='lead text-center'><button class='btn btn-primary' onclick='cierraChat("+e.idChat+");'>Finalizar chat</button></p>"+
                                                 "</div>");
                    chatsActuales.push(e.idChat);
                    longi.push(0);
                    actualizaMensajes(e.idChat);
                    console.log(e);
                    $.gritter.add({
                        title: "Chat iniciado",
                        text: "<big>"+e.preguntaInicial+"</big><br>Nombre: "+e.nombre+"<br><span class='text-muted'>"+e.email+"</span>"+
                              "<hr><i><span data-livestamp='"+e.tiempo+"'></span></i>",
                        sticky: true
                    });
                    var audio = {};
                    audio["walk"] = new Audio();
                    audio["walk"].src = "achat/chisme.mp3";
                    audio["walk"].play();
                    $("#achat-hayChats").removeClass("hidden");
                }
            });
        }
    });
}

function verificaChats(){
    $.post("achat/ajaxAdmin/verificaChat.php");
}

function cierraChat(idChat){
    $.post("achat/ajaxAdmin/cierraChat.php", "id="+idChat);
}

function verChat(idChat, nombre){
    $("#nombreHistorial").html(nombre);
    $.post("achat/ajaxAdmin/dameMensajes.php", "idChat="+idChat, function(data){
        $("#mensajesHistorial").html(data);
        $("#modal_historial").modal("show");
    });
}

$(function(){
    $(window).load(function(){
        verificaChats();
        actualizaChats();
        achat_A = setInterval(actualizaChats, 15000);
        achat_B = setInterval(verificaChats, 3600000);
        $("#tabs a[href='#home']").on('shown.bs.tab', function(e){
            verificaChats();
            actualizaChats();
            //achat_A = setInterval(actualizaChats, 30000);
            achat_B = setInterval(verificaChats, 3600000);
        });
        $("#tabs a[href='#home']").on('hidden.bs.tab', function(){
            console.log("removeIntervalAchat");
            //clearInterval(achat_A);
            clearInterval(achat_B);
        });
        $(document).on('shown.bs.tab', "#tabs a[href='#historial']", function(e){
            $.get("achat/ajaxAdmin/tablaConversaciones.php", function(data){
                $("#historialConversaciones").html(data);
            });
        });
        $(document).on("keypress", ".inputmensaje", function(e){
            var ele = $(this);
            if(e.which == 13) {
                $.post("achat/ajaxAdmin/mandaMensaje.php", "mensaje="+ele.val()+"&idChat="+ele.parent().parent().attr("data-idChat"), function(data){
                    ele.parent().parent().children(".contieneMensajes").append("<div class='mensaje'>"+
                                                  "<p class='yo'>"+ele.val()+"</p>"+
                                                  "</div>");
                    ele.val("");
                    ele.parent().parent().children(".contieneMensajes").scrollTop(10000);
                });
            }
        });
    });
});