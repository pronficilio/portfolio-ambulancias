var auto_cliente_agenda = false;
var tablaActPen = "";
var tablaActCom = "";

function actualizaListaActPendientes(){
    if(tablaActPen == ""){
        tablaActPen = $("#tabla_act_pend").DataTable({
            serverSide: true,
            ajax: "ajax/agenda/getAgenda.json.php?hecho=0",
            pageLength: 10,
            lengthMenu: [10, 15, 20, 50],
            language: {
                url: "locales/datatable.es.json"
            },
            fnDrawCallback: function(){
                $("#tabla_act_pend ._tool").tooltip();
            },
            order: [ 3, 'asc' ],
            columns : [
                {orderable: true, width: "5%"},
                {orderable: true},
                {orderable: true},
                {orderable: true, width: "10%"},
                {orderable: false},
                {orderable: false}
            ]
        });
    }else{
        tablaActPen.ajax.reload();
    }
}

function actualizaListaActHechas(){
    if(tablaActCom == ""){
        tablaActCom = $("#tabla_act_com").DataTable({
            serverSide: true,
            ajax: "ajax/agenda/getAgenda.json.php?hecho=1",
            pageLength: 10,
            lengthMenu: [10, 15, 20, 50],
            language: {
                url: "locales/datatable.es.json"
            },
            fnDrawCallback: function(){
                $("#tabla_act_com ._tool").tooltip();
            },
            order: [ 3, 'asc' ],
            columns : [
                {orderable: true, width: "5%"},
                {orderable: true},
                {orderable: true},
                {orderable: true, width: "10%"},
                {orderable: false},
                {orderable: false}
            ]
        });
    }else{
        tablaActCom.ajax.reload();
    }
}

function actualizaHistorial(idCliente, histo){
    if(!$.isArray(histo)){
        $.post("ajax/cliente/getClienteById.json.php", "id="+idCliente, function(data){
            if($.isArray(data.historial))
                actualizaHistorial(idCliente, data.historial);
            else
                actualizaHistorial(idCliente, []);
        }, "json");
    }else{
        if(histo.length > 0){
            $("#modal_llamada_historial").html("<h4 class=''>Historial de llamadas</h4>");
            histo.forEach(function(e, i){
                var telefonoDatos = "";
                if(e.telefono != "")
                    telefonoDatos = "<p><span class='glyphicon glyphicon-earphone'></span> "+e.label+" - "+e.telefono+"</p>";
                $("#modal_llamada_historial").append("<blockquote>"+
                                                     "<i class='text-small'>"+e.comentario+"</i>"+
                                                     "<div><span class='glyphicon glyphicon-calendar'></span> "+e.fecha+"<br>"+telefonoDatos+"</div>"+
                                                     "</blockquote>");
            });
        }else{
            $("#modal_llamada_historial").html("<h4>Sin historial de llamadas</h4>");
        }
    }
}

function creaLlamada(idCliente){
    $.post("ajax/cliente/getClienteById.json.php", "id="+idCliente, function(data){
        $("#modal_llamada_nom").html(data.nombre);
        $("#modal_llamada_tel").html("<option value=''>Elige un teléfono</option>");
        $("#modal_llamada_idC").val(idCliente);
        $("#modal_llamada_fecha").val(data.fecha);
        if(data.tel != null){
            data.tel.forEach(function(e, i){
                $("#modal_llamada_tel").append("<option value='"+e.idTelefono+"'>("+e.label+") "+e.numero+"</option>");
            });
        }
        $("#modal_llamada_historial").html("<h4>Sin historial de llamadas</h4>");
        actualizaHistorial(idCliente, data.historial);
        $("#modal_llamada").modal("show");
    }, "json");
}

function guardaLlamada(){
    var datos = "idC="+$("#modal_llamada_idC").val()+"&idT="+$("#modal_llamada_tel").val();
    datos += "&fecha="+$("#modal_llamada_fecha").val()+"&comentario="+$("#modal_llamada_com").val();
    $.post("ajax/cliente/creaLlamada.php", datos, function(){
        actualizaHistorial($("#modal_llamada_idC").val(), "");
        $("#modal_llamada_com").val("");
    });
}

function activaAutoAgenda(){
    if(!auto_cliente_agenda){
        auto_cliente_agenda = true;
        $('#agenda_auto_cliente').typeahead({
            hint: true,
            highlight: true
        },{
            display: 'nombre',
            limit: Infinity,
            source: _auto_cliente
        });
        $('#agenda_auto_cliente').bind('typeahead:select', function(ev, suggestion) {
            $("#agenda_id_cliente").val(suggestion.idCliente);
        });
        $('#agenda_auto_cliente').bind('typeahead:open', function(ev, suggestion) {
            $("#agenda_id_cliente").val("");
        });
    }
}

function agregaActividad(){
    var validaEsto = "#agenda_select,#agenda_fecha";
    var datos = "";
    if(!$("#agenda_busca_cliente").hasClass("hidden"))
        validaEsto += ",#agenda_auto_cliente,#agenda_id_cliente";
    if(!camposVacios(validaEsto)){
        datos = "idC="+$("#agenda_id_cliente").val()+"&fecha="+$("#agenda_fecha").val()+"&hora="+$("#agenda_hora").val();
        datos += "&nota="+$("#agenda_notas").val()+"&act="+$("#agenda_select").val();
        $.post("ajax/agenda/agregaActividad.php", datos, function(data){
            if(data == ""){
                mensaje("Se ha agendado la actividad", "alert-success");
                limpiaCampos(validaEsto+",#agenda_hora,#agenda_notas", 1);
                $("#agenda_busca_cliente").addClass("hidden");
            }else
                mensaje(data, "alert-danger");
        });
    }
}

function preparaModalAct(id){
    $.post("ajax/agenda/getAgendaById.json.php", "id="+id, function(data){
        if(data != null){
            $("#modal_detalles_titulo").html(data.tipo+" agendada");
            $("#modal_detalles_act_id").val(data.idAgenda);
            $("#modal_d_a_footer").attr("data-ag", data.idAgenda);
            $("#modal_d_a_fecha").html(moment(data.fecha, "YYYY-MM-DD HH:mm:ss").format("D [de] MMMM [de] YYYY, h:mm a"));
            $("#modal_d_a_creacion").html(moment(data.fechaRegistro, "YYYY-MM-DD HH:mm:ss").format("D [de] MMMM [de] YYYY, h:mm a"));
            $("#modal_d_a_creador").html(data.user);
            if(data.nombre == null){
                $("#modal_d_a_c").addClass("hidden");
            }else{
                $("#modal_d_a_c").removeClass("hidden");
                $("#modal_d_a_c_u").html(data.nombre);
            }
            $("#modal_d_a_notas").val(data.notas);
            $("#agenda_fecha_reag").val("");
            $("#agenda_hora_reag").val("");
            if(data.mio == false){
                $("#reagendar").hide();
                $("#modal_d_a_notas").attr("disabled", "disabled");
                $("#modal_d_a_footer button").addClass("hidden");
                $("#modal_d_a_footer button.btn-default").removeClass("hidden");
            }else{
                $("#reagendar").show();
                $("#modal_d_a_footer button").removeClass("hidden");
                $("#modal_d_a_notas").removeAttr("disabled");    
                if(data.hecho == 1){
                    $("#modal_d_a_realizado").removeClass("hidden");
                    $("#reagendar").hide();
                    $("#modal_d_a_notas").attr("disabled", "disabled");
                    $("#modal_d_a_realizado_u").html(data.hp);
                    $("#modal_d_a_footer").children(".tareaHecha").children("span").removeClass("glyphicon-unchecked").addClass("glyphicon-check");
                }else{
                    $("#modal_d_a_realizado").addClass("hidden");
                    $("#modal_d_a_footer").children(".tareaHecha").children("span").removeClass("glyphicon-check").addClass("glyphicon-unchecked");
                }
            }
             $("#modal_detalles_act").modal("show");
        }
    }, "json");
}

function actualizaNotasModal(){
    $.post("ajax/agenda/actualizaNota.php", {
        id: $("#modal_detalles_act_id").val(),
        nota: $("#modal_d_a_notas").val(),
        fechaReag: $("#agenda_fecha_reag").val(),
        horaReag: $("#agenda_hora_reag").val()
    }, function(data){
        $("#agenda_fecha_reag").val("");
        $("#agenda_hora_reag").val("");
        if(tablaActPen != "")
            actualizaListaActPendientes();
    });
}

$(function(){
    $("#tabs a[href='#agenda']").on('shown.bs.tab', function(e){
        cargaTab("agenda");
    });
    $(document).on("click", ".tareaHecha", function(){
        if($(this).children("span").hasClass("glyphicon-unchecked")){
            $(this).children("span").removeClass("glyphicon-unchecked").addClass("glyphicon-check");
            var ag = $(this).attr("ag");
            if(ag == undefined)
                ag = $(this).parent().attr("data-ag");
            $.post("ajax/agenda/hecho.php", "id="+ag+"&estado=1");
            if(tablaActPen != "")
                actualizaListaActPendientes();
        }
    });
    $(document).on("change", "#agenda_select", function(){
        if($(this).val() == "" || $(this).val() == "tarea"){
            $("#agenda_busca_cliente").addClass("hidden");
        }else{
            $("#agenda_busca_cliente").removeClass("hidden");
            activaAutoAgenda();
        }
    });
    $(document).on('shown.bs.tab', "#subtab_cliente a[href='#agenda_pen_lista']", function(e){
        actualizaListaActPendientes();
    });
    $(document).on('shown.bs.tab', "#subtab_cliente a[href='#agenda_com_lista']", function(e){
        actualizaListaActHechas();
    });
    $(document).on("click", ".verDetallesAct", function(){
        preparaModalAct($(this).parent().attr("data-ag"));
    });
    $(document).on("click", ".eliminaActividad", function(){
        $.post("ajax/agenda/eliminaAct.php", "id="+$(this).parent().attr("data-ag"));
        if(tablaActPen != "")
            actualizaListaActPendientes();
        if(tablaActCom != "")
            actualizaListaActHechas();
    });
});