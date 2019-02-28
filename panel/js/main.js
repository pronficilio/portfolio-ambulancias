var last_msg = [];
var ini = 0;
var fin = 0;
var cuenta = 0;
var _auto_cliente;
var _auto_producto;
var _auto_archivo;
var _as;
var primeraVez = true;
var identificadorPagina;

function number_format (number, decimals, dec_point, thousands_sep) {
  number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function (n, prec) {
      var k = Math.pow(10, prec)
      return '' + (Math.round(n * k) / k)
        .toFixed(prec)
    }
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
    .split('.')
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
  }
  if ((s[1] || '')
    .length < prec) {
    s[1] = s[1] || ''
    s[1] += new Array(prec - s[1].length + 1)
      .join('0')
  }
  return s.join(dec)
}

function dondeDeberiasEstar(url){
    $.ajax({
        cache: false,
        url: "ajax/acceso/dondeDeberiasEstar.php",
        type: "post",
        data: "url="+url,
        success: function(data){
            if(data != "")
                eval(data);
        }
    });
}

function scrollea(e, donde){
    $(donde).stop().animate({
        scrollTop: e.offset().top-70
    }, 300);
}

function scrollAca(e){
    scrollea(e, 'html, body');
}

function isEmail(email) {
    var regex = /^([a-zA-Z])+([a-zA-Z0-9_.+-])+\@(([a-zA-Z])+\.+?(com|co|in|org|net|edu|info|gob|mx|))\.?(com|co|in|org|net|edu|info|gob|mx)?$/;
    return regex.test(email.toLowerCase());
}

function testVelocidad(){
    var t0 = performance.now();
    //something
    var t1 = performance.now();
    console.log("Call to doSomething took " + (t1 - t0) + " milliseconds.");
}

function ponMensaje(mensaje, tipo, donde, dondeScroll){
    var mensajin = "<div class='alert "+tipo+" collapse'>";
    mensajin += "<span class='close'>&times;</span>";
    mensajin += mensaje;
    mensajin += "</div>";
    $(donde).append(mensajin);
    $(donde).children(".alert:last-child").slideDown("fast");
    last_msg[fin++] = $(donde).children(".alert:last-child");
    scrollea($(donde).children(".alert:last-child"), dondeScroll);
    setTimeout(function(){ last_msg[ini++].slideUp(2000); }, 5000);
}

function mensaje(mensaje, tipo){
    ponMensaje(mensaje, tipo, "#contenedorAlertas", 'html, body');
}

function ajaxToHtml(url, id, sync, datos){
    if(!sync)
        console.log("El llamado se hace async=false... "+url);
    var t0 = performance.now();
    $.ajax({
        async: sync,
        cache: false,
        url: "ajax/"+url,
        type: "post",
        data: datos,
        success: function(data){
            $(id).html(data);
            var t1 = performance.now();
            //console.log("url: "+url+"\nid: "+id+"\nsync: "+sync+"\ndatos: "+datos+"\nRespuesta: ...\nTiempo: "+(t1 - t0)+" ms.");
        }
    });
}

function ajaxFast(url, sync, datos){
    if(!sync)
        console.log("El llamado se hace async=false... "+url);
    $.ajax({
        async: sync,
        cache: false,
        url: "ajax/"+url,
        type: "post",
        data: datos,
        success: function(data){
            //console.log("Ajax Fast: "+data);
        }
    });
}

function validaVacio(e){
    var err = false;
    if($(e).val() == "" || $(e).val() == null){
        $(e).parent().addClass("has-error");
        $(e).parent().removeClass("has-success");
        err = true;
    }else{
        $(e).parent().removeClass("has-error");
        $(e).parent().addClass("has-success");
    }
    return err;
}

function camposVacios(cadena){
    var datos = cadena.split(",");
    var ultimo = "";
    var error = false;
    datos.forEach(function(e, i){
        if(validaVacio(e)){
            error = true;
            ultimo = e;
        }
    });
    if(error){
        scrollAca($(ultimo));
        $(ultimo).trigger("focus");
    }
    return error;
}

function limpiaFeedback(e){
    $(e).parent().removeClass("has-error");
    $(e).parent().removeClass("has-success");
}

function limpiaCampos(cadena, full){
    var datos = cadena.split(",");
    datos.forEach(function(e, i){
        limpiaFeedback(e);
        if(full == 1)
            $(e).val("");
    });
}

function invisibiliza(ele, col, whos){
    var quienes = whos.split(",");
    quienes.forEach(function(e, i){
        if(e == 1){
            ele.parent().parent().parent().children("td:nth-child("+col+")").children("div").children("div").children("button").eq(i).removeAttr("disabled");
        }else{
            ele.parent().parent().parent().children("td:nth-child("+col+")").children("div").children("div").children("button").eq(i).removeClass("in");
            ele.parent().parent().parent().children("td:nth-child("+col+")").children("div").children("div").children("button").eq(i).addClass("invisible");
        }
    });
}

function visibiliza(ele, col, whos){
    var quienes = whos.split(",");
    var cols = col.split(",");
    ele.attr("disabled", "disabled");
    quienes.forEach(function(e, i){
        ele.parent().children(e).removeClass("invisible");
        ele.parent().children(e).addClass("in");
    });
    cols.forEach(function(e, i){
        if(i == 0)
            ele.parent().parent().parent().parent().children("td:nth-child("+e+")").children("div").children("input").removeAttr("disabled").focus().select();
        else
            ele.parent().parent().parent().parent().children("td:nth-child("+e+")").children("div").children("input").removeAttr("disabled");
    });
}

function confirma(titulo, pregunta, accion){
    $("#modal_titulo").html("<span class='glyphicon glyphicon-info-sign'></span> "+
                            titulo+" <span class='glyphicon glyphicon-info-sign'></span>");
    $("#modal_texto").html(pregunta);
    $("#modal_accion").attr("onclick", accion);
    $("#modal_confirma").modal("show");
}
function mensajesParaHoy(){
    var cliente = "";
    var tiempo = "";
    var done, res;
    $.get("ajax/agenda/hoy.php", {pag: identificadorPagina}, function(data){
        if(data.length > 0){
            data.forEach(function(e, i){
                cliente = "";
                tiempo = "";
                done = "<li><p role='button' class='tareaHecha' ag='"+e.idAgenda+"'><span class='glyphicon glyphicon-unchecked'></span> "+
                       "Hecho</p></li>";
                if(e.idCliente != null){
                    cliente = "<li><p><span class='glyphicon glyphicon-user'></span> "+e.nombre+"</p></li>";
                    cliente += "<li><p role='button' onclick='creaLlamada("+e.idCliente+");'>"+
                               "<span class='glyphicon glyphicon-earphone'></span> Crear llamada"+
                               "</p></li>";
                }
                if(!(e.full && e.hoy)){
                    tiempo = "<br><i><span data-livestamp='"+e.fecha+"'></span></i>";
                }
                res = $.gritter.add({
                    title: '<span class="text-uppercase">'+e.tipo+' agendada.</span>',
                    text: e.notas+'<br>'+tiempo+"<hr><ul class='list-unstyled'>"+cliente+done+"</ul>",
                    sticky: true,
                    after_close: function(ee){
                        $.post("ajax/agenda/gritterRemove.php", {id: ee[0].id, pag: identificadorPagina});
                    }
                });
                $.post("ajax/agenda/gritterID.php", {id: res, idA: e.idAgenda, pag: identificadorPagina});
            });
        }
    }, "json");
}
function aquiSigo(){
    $.get("ajax/acceso/sesion.php", function(d){
        cuenta++;
        if(d == ":("){
            clearInterval(_as);
            dondeDeberiasEstar("panel");
        }else{
            if (primeraVez) {
                primeraVez = false;
                $("body").removeClass("ocultaTodo");
                mensajesParaHoy();
            }else{
                mensajesParaHoy();
            }
        }
    });
    cuenta++;

}

function cargaTab(nombre){
    if($("#"+nombre).html() == ""){
        $.ajax({
            cache: false,
            url: "tabs/"+nombre+".html",
            success: function(data){
                $("#"+nombre).html(data);
            }
        });
    }
}

$(function(){
    identificadorPagina = $.now();
    $(document).on("click", ".close", function(){
        if($(this).parent().hasClass("alert"))
            $(this).parent().slideUp();
    });
    $(document).on("click", "._tool", function(){
        $(this).tooltip("hide");
    });
    aquiSigo();
    _as = setInterval(aquiSigo, 60000);
    _auto_cliente = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('nombre'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'ajax/cliente/autocomplete.php?q=%QUERY',
            wildcard: '%QUERY'
        }
    });
    _auto_producto = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('nombre'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'ajax/producto/autocomplete.php?q=%QUERY',
            wildcard: '%QUERY'
        }
    });
    _auto_archivo = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('nombre'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'ajax/archivo/autocomplete.php?q=%QUERY',
            wildcard: '%QUERY'
        }
    });
});