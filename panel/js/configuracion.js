var conf_auto_producto = false;
var tablaListaUsuario = "";
var tablaListaPermisos = "";
var param_jstree;

function actualizaListaUsuariosAdmin(){
    if(tablaListaUsuario == ""){
        tablaListaUsuario = $("#configuracion_usuarios_lista").DataTable({
            serverSide: true,
            ajax: "ajax/acceso/listaUsuarios.json.php",
            pageLength: 5,
            lengthMenu: [5, 15, 20, 50],
            language: {
                url: "locales/datatable.es.json"
            },
            fnDrawCallback: function(){
                $("#configuracion_usuarios_lista ._tool").tooltip();
            },
            order: [0, 'asc'],
            columns : [
                {orderable: true},
                {orderable: true},
                {orderable: false},
                {orderable: false, width: "25%"}
            ]
        });
    }else{
        tablaListaUsuario.ajax.reload();
    }
}
function actualizaListaPermisos(){
    if(tablaListaPermisos == ""){
        tablaListaPermisos = $("#configuracion_permisos_lista").DataTable({
            serverSide: true,
            ajax: "ajax/acceso/listaPermisos.json.php",
            pageLength: 5,
            lengthMenu: [5, 15, 20, 50],
            language: {
                url: "locales/datatable.es.json"
            },
            fnDrawCallback: function(){
                $("#configuracion_permisos_lista ._tool").tooltip();
            },
            order: [0, 'asc'],
            columns : [
                {orderable: true},
                {orderable: true},
                {orderable: false, width: "25%"}
            ]
        });
    }else{
        tablaListaPermisos.ajax.reload();
    }
}

function nombreUsuario(){
    $.ajax({
        async: true,
        cache: false,
        url: "ajax/acceso/_user.php",
        success: function(nombre){
            $(".nombre_usuario").eq(0).html(nombre);
            $(".nombre_usuario").eq(1).html(nombre);
            $("#user_nombre").val(nombre);
        }
    })
}

function dameDatosPersonales(){
    $.get("ajax/acceso/dameDatosPersonales.json.php", {_: $.now()}, function(res){
        $("#form_datos_personales input[name='nombre']").val(res.name);
        $("#form_datos_personales input[name='email']").val(res.email);
        $("#form_datos_personales input[name='username']").val(res.nombre);
    });
}

function quiereCambiar(){
    $('#tabs a[href="#configuracion"]').tab('show');
    $('#subtab_configuracion a[href="#configuracion_perfil"]').tab("show");
}

function dameCV(){
    ajaxToHtml("configuracion/concretarVenta.php", "#concretarVenta", true, "");
}

function dameND(){
    ajaxToHtml("configuracion/noDisponible.php", "#noDisponible", true, "");
}

function actualizaCV(){
    ajaxFast("configuracion/modificarCV.php", true, "msg="+$("#concretarVenta").val());
    mensaje("Se ha actualizado la información", "alert-success");
}

function actualizaND(){
    ajaxFast("configuracion/modificarND.php", true, "msg="+$("#noDisponible").val());
    mensaje("Se ha actualizado la información", "alert-success");
}
function actualizaCE(){
    ajaxFast("configuracion/modificarCE.php", true, "cnt="+$("#costoEnvio").val());
    mensaje("Se ha actualizado la información", "alert-success");
}

function actualizaCM(){
    ajaxFast("configuracion/modificarCM.php", true, "cnt="+$("#compraMinima").val());
    mensaje("Se ha actualizado la información", "alert-success");
}

function actualizaTablaDestacados(){
    ajaxToHtml("configuracion/destacadoTabla.php", "#configuracion_tabla_destacados", true, "");
}

function damePermisosJstree(id){
    var param_jstree = new Array();
    $(id).jstree(true).get_checked(true).forEach(function(e, i){
        if($.inArray(e.id, param_jstree) == -1){
            param_jstree.push(e.id);
        }
        if(e.parents.length){
            e.parents.forEach(function(ee, ii){
                if(ee != "#"){
                    if($.inArray(ee, param_jstree) == -1){
                        param_jstree.push(ee);
                    }
                }
            });
        }
    });
    return param_jstree;
}

function eliminaPermiso(id){
    $.post("ajax/acceso/eliminaPermiso.json.php", {id: id}, function(res){
        if(!res.error){
            toastr.success("Eliminación completada");
            actualizaListaPermisos();
        }else{
            toastr.error(res.msg);
        }
    })
}

function actualizaSelectPermisos(){
    $.post("ajax/acceso/damePermisosTodos.json.php", {_: $.now()}, function(res){
        $("#form_lista_usuario select[name='permisos']").html('<option value="">Selecciona una opción</option>');
        res.forEach(function(e, i){
            $("#form_lista_usuario select[name='permisos']").append('<option value="'+e.idPermiso+'">'+e.nombrePermiso+'</option>');
        });
    });
}

$(function(){
    $("#tabs a[href='#configuracion']").on('shown.bs.tab', function(e){
        cargaTab("configuracion");
        if($("#configuracion").html() == ""){
            $.get("tabs/configuracion.html", {"_": $.now()}, function(data){
                $("#configuracion").html(data);
                setTimeout(function(){
                    nombreUsuario();
                    dameDatosPersonales();
                    actualizaListaUsuariosAdmin();
                    actualizaListaPermisos();
                    $("#tree_permisos").html($("#tree").html()).jstree({
                        "core" : {
                            "themes" : {
                                "responsive": false
                            }            
                        },
                        "plugins": ["checkbox", "wholerow"]
                    });
                    $("#tree_permisos").jstree(true).close_all();
                    actualizaSelectPermisos();
                }, 500);
                revisaPermisos("#subtab_configuracion a");
            });
        }
    });
    $(document).on('shown.bs.tab', "#subtab_configuracion a[href='#configuracion_perfil']", function(e){
        nombreUsuario();
        dameDatosPersonales();
    });
    $(document).on('shown.bs.tab', "#subtab_configuracion a[href='#configuracion_mensaje']", function(e){
        dameCV();
        dameND();
    });
    $(document).on('shown.bs.tab', "#subtab_configuracion a[href='#configuracion_mensaje']", function(e){
        dameCV();
        dameND();
    });
    $(document).on('shown.bs.tab', "#subtab_configuracion a[href='#configuracion_costo']", function(e){
        $.post("ajax/configuracion/compraMinima.php", function(res){ $("#compraMinima").val(res); });
        $.post("ajax/configuracion/costoEnvio.php", function(res){ $("#costoEnvio").val(res); });
    });
    $(document).on("click", ".verPermisos", function(){
        $("#modal_tree").attr("data-idPermiso", $(this).parent().attr("data-perm"));
        $("#modal_nombrePermisos").html($(this).parent().parent().parent().parent().children("td").eq(1).html());
        if($("#modal_tree").html() == ""){
            $("#modal_tree").html($("#tree").html()).jstree({
                "core" : {
                    "themes" : {
                        "responsive": false
                    }            
                },
                "plugins": ["checkbox", "wholerow"]
            });
        }
        $('#modal_tree').jstree(true).deselect_all();
        $("#modal_tree").jstree(true).close_all();
        $.post("ajax/acceso/damePermisos.json.php", {id: $("#modal_tree").attr("data-idPermiso")}, function(res){
            console.log(res);
            res.datos.forEach(function(e, i){
                var nodo = $("#modal_tree").jstree(true).get_node("ni_"+e);
                if(nodo != false && nodo.children.length == 0)
                    $("#modal_tree").jstree(true).select_node("ni_"+e);
            });
            $("#modal_permisos").modal("show");
        });
    });
    $(document).on("click", ".guardaPermisos", function(){
        $.post("ajax/acceso/guardaPermiso.json.php", {
            id: $("#modal_tree").attr("data-idPermiso"),
            permisos: damePermisosJstree("#modal_tree")
        }, function(res){
            if(!res.error){
                $("#modal_permisos").modal("hide");
                toastr.success("Permiso modificado correctamente");
            }else{
                toastr.error(res.msg);
            }
        });
    });
    $(document).on("click", ".eliminaPermiso", function(){
        var id = $(this).parent().attr("data-perm");
        confirma("Eliminar permiso", "Se eliminará el permiso y los usuarios <i>"+
                 $(this).parent().parent().parent().parent().children("td").eq(1).html()+"</i> perderán sus privilegios.<br>¿Deseas continuar?",
                 "eliminaPermiso(" + id + ");");
    });
    $(document).on('shown.bs.tab', "#subtab_configuracion a[href='#configuracion_destacados']", function(e){
        actualizaTablaDestacados();
        if(conf_auto_producto == false){
            conf_auto_producto = true;
            $('#configuracion_autocomplete_productos').typeahead({
                hint: true,
                highlight: true
            }, {
                display: 'nombre',
                limit: Infinity,
                source: _auto_producto
            });
            $('#configuracion_autocomplete_productos').bind('typeahead:select', function(ev, suggestion) {
                $("#conf_auto_prod_aux").val(suggestion.idProducto);
                $("#configuracion_btn_destacar").removeAttr("disabled");
            });
            $('#configuracion_autocomplete_productos').bind('typeahead:open', function(ev, suggestion) {
                $("#conf_auto_prod_aux").val("");
                $("#configuracion_btn_destacar").attr("disabled", "disabled");
            });
        }
    });
    $(document).on("click", "#configuracion_btn_destacar", function(e){
        ajaxFast("configuracion/destacar.php", false, "id="+$("#conf_auto_prod_aux").val());
        actualizaTablaDestacados();
    });
    $(document).on("submit", "#form_lista_usuario", function(e){
        e.preventDefault();
        $.post("ajax/acceso/crearUsuario.json.php", $(this).serialize(), function(r){
            if(!r.error){
                $("#form_lista_usuario")[0].reset();
                actualizaListaUsuariosAdmin();
                toastr.success("Usuario agregado correctamente. Se envió por email su acceso");
            }else{
                toastr.error(r.msg);
            }
        });
    });
    $(document).on("submit", "#form_agregar_permiso", function(e){
        e.preventDefault();
        $.post("ajax/acceso/crearPermisos.json.php", {
            nombre: $("#form_agregar_permiso input[type='text']").val(),
            permisos: damePermisosJstree("#tree_permisos")
        }, function(res){
            if(!res.error){
                $('#tree_permisos').jstree(true).deselect_all();
                $("#tree_permisos").jstree(true).close_all();
                $('#tree_permisos').jstree(true).select_node('ni_configuracion_perfil');
                actualizaListaPermisos();
                toastr.success("Permiso agregado correctamente");
            }else{
                toastr.error(res.msg);
            }
        });
    });
    $(document).on("change", ".cambiarPermisos", function(e){
        $.post("ajax/acceso/cambiarPermiso.json.php", {
            id: $(this).attr("data-id"),
            perm: $(this).val()
        }, function(res){
            if(!res.error){
                toastr.success("Permiso cambiado");
            }else{
                toastr.error(res.msg);
            }
        });
    });
    $(document).on("click", ".reenviarContrasena", function(e){
        console.log($(this).parent().attr("data-us"));
        console.log($.post("ajax/acceso/enviarRecuperaContrasena.json.php", {
            id: $(this).parent().attr("data-us")
        }, function(res){
            console.log(res);
            if(!res.error){
                toastr.success("Mensaje de recuperación de contraseña enviado");
            }else{
                toastr.error(res.msg);
            }
        }));
    });
    $(document).on("click", ".eliminaUsuario", function(e){
        $.post("ajax/acceso/eliminaUsuario.json.php", {
            id: $(this).parent().attr("data-us")
        }, function(res){
            if(!res.error){
                toastr.success("Eliminación completada");
                actualizaListaUsuariosAdmin();
            }else{
                toastr.error(res.msg);
            }
        })
    })
});