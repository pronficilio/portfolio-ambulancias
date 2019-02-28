var tablaCategoria = "";
var tablaSubcategoria = "";
var tablaDescuento = "";

function actualizaListaCategoria(){
    if(tablaCategoria == ""){
        tablaCategoria = $("#categoria_tabla").DataTable( {
            serverSide: true,
            ajax: "ajax/categoria/getCategorias.json.php",
            pageLength: 5,
            ordering: false,
            lengthMenu: [5, 10, 15, 20, 50],
            language: {
                url: "locales/datatable.es.json"
            },
            fnDrawCallback: function(){
                $("#categoria_tabla ._tool").tooltip();
            },
            columns : [
                {width: "5%"},
                null,
                {width: "20%"}
            ]
        });
    }else{
        tablaCategoria.ajax.reload();
    }
}
function actualizaListaSubcategoria(){
    if(tablaSubcategoria == ""){
        tablaSubcategoria = $("#subcategoria_tabla").DataTable( {
            serverSide: true,
            ajax: "ajax/categoria/getSubcategorias.json.php",
            pageLength: 5,
            ordering: false,
            lengthMenu: [5, 10, 15, 20, 50],
            language: {
                url: "locales/datatable.es.json"
            },
            fnDrawCallback: function(){
                $("#subcategoria_tabla ._tool").tooltip();
            },
            columns: [
                {width: "5%"},
                null,
                null,
                {width: "20%"}
            ]
        });
    }else{
        tablaSubcategoria.ajax.reload();
    }
}

function actualizaListaDescuento(){
    if(tablaDescuento == ""){
        tablaDescuento = $("#descuentos_tabla").DataTable( {
            serverSide: true,
            ajax: "ajax/categoria/descuento/getDescuentos.json.php",
            pageLength: 5,
            ordering: false,
            lengthMenu: [5, 10, 15, 20, 50],
            language: {
                url: "locales/datatable.es.json"
            },
            fnDrawCallback: function(){
                $("#descuentos_tabla ._tool").tooltip();
            },
            columns: [
                {width: "5%"},
                null,
                null,
                {width: "25%"}
            ]
        });
    }else{
        tablaDescuento.ajax.reload();
    }
}
function agregaCategoria(){
    var error = 0;
    var datos = "nombre="+$("#agregaCat_nombre").val();
    if(!camposVacios("#agregaCat_nombre")){
        $.post("ajax/categoria/agregarCategoria.php", datos, function(data){
            if(data == ""){
                limpiaCampos("#agregaCat_nombre", 1);
                $("#agregaCategoria").collapse("hide");
                mensaje("Se ha agregado correctamente", "alert-success");
                actualizaListaCategoria();
            }else{
                mensaje(data, "alert-danger");
            }
        });
    }
}
function agregaSubcategoria(){
    var error = 0;
    var lastElegido = 0;
    var datos = "nombre="+$("#agregaSubcat_nombre").val();
    $(".select_subcategoria_subsub").each(function(n, e){
        if($(e).val() != "")
            lastElegido = $(e).val();
    });
    if(lastElegido == 0)
        datos += "&enlace="+$("#select_categoria_subcategoria").val();
    else
        datos += "&enlace="+lastElegido;
    if(!camposVacios("#agregaSubcat_nombre,#select_categoria_subcategoria")){
        $.post("ajax/categoria/agregarSubcategoria.php", datos, function(data){
            if(data == ""){
                limpiaCampos("#agregaSubcat_nombre,#select_categoria_subcategoria", 1);
                $("#agregaSubcategoria").collapse("hide");
                mensaje("Se ha agregado correctamente", "alert-success");
                actualizaListaSubcategoria();
                ajaxToHtml("categoria/arbol.php", "#arbol", true, "");
                $(".acomodador_subcategorias").eq(0).html("");
            }else{
                mensaje(data, "alert-danger");
            }
        });
    }
}
function agregaDescuento(){
    var error = 0;
    var datos = "nombre="+$("#agregaDescuento_nombre").val();
    datos += "&valor="+$("#agregaDescuento_valor").val();
    if(!camposVacios("#agregaDescuento_nombre,#agregaDescuento_valor")){
        $.post("ajax/categoria/descuento/agregarDescuento.php", datos, function(data){
            if(data == ""){
                limpiaCampos("#agregaDescuento_nombre,#agregaDescuento_valor", 1);
                $("#agregaDescuento").collapse("hide");
                mensaje("Se ha agregado correctamente", "alert-success");
                actualizaListaDescuento();
            }else{
                mensaje(data, "alert-danger");
            }
        });
    }
}

function bloqueaEdicionCategoria(nombre){
    nombre.attr("disabled", "disabled");
    limpiaFeedback(nombre);
    invisibiliza(nombre, 3, "1,2,3,4");
}
function bloqueaEdicionSubcategoria(nombre){
    nombre.attr("disabled", "disabled");
    limpiaFeedback(nombre);
    invisibiliza(nombre, 4, "1,2,3,4");
}
function bloqueaEdicionDescuento(nombre, valor){
    nombre.attr("disabled", "disabled");
    valor.attr("disabled", "disabled");
    limpiaFeedback(nombre);
    limpiaFeedback(valor);
    invisibiliza(nombre, 4, "1,2,3,4");
}

function editarCategoria(nombre, idCategoria){
    var datos = "id="+idCategoria;
    if(!validaVacio(nombre)){
        datos += "&nombre="+nombre.val();
        $.post("ajax/categoria/modificarCategoria.php", datos, function(data){
            if(data == ""){
                mensaje("Se ha actualizado correctamente", "alert-success");
                nombre.parent().attr("data-original", nombre.val());
                bloqueaEdicionCategoria(nombre);
            }else
                mensaje(data, "alert-danger");
        });
    }
}
function editarSubcategoria(nombre, idCategoria){
    var datos = "id="+idCategoria;
    if(!validaVacio(nombre)){
        datos += "&nombre="+nombre.val();
        $.post("ajax/categoria/modificarCategoria.php", datos, function(data){
            if(data == ""){
                mensaje("Se ha actualizado correctamente", "alert-success");
                nombre.parent().attr("data-original", nombre.val());
                bloqueaEdicionSubcategoria(nombre);
            }else
                mensaje(data, "alert-danger");
        });
    }
}
function editarDescuento(nombre, valor, idDescuento){
    var datos = "id="+idDescuento;
    if(!validaVacio(nombre) && !validaVacio(valor)){
        datos += "&nombre="+nombre.val();
        datos += "&valor="+valor.val();
        $.post("ajax/categoria/descuento/modificarDescuento.php", datos, function(data){
            if(data == ""){
                mensaje("Se ha actualizado correctamente", "alert-success");
                nombre.parent().attr("data-original", nombre.val());
                valor.parent().attr("data-original", valor.val());
                bloqueaEdicionDescuento(nombre, valor);
            }else
                mensaje(data, "alert-danger");
        });
    }
}
function eliminaCategoria(idCategoria){
    $.post("ajax/categoria/eliminaCategoria.php", "idCategoria="+idCategoria, function(data){
        actualizaListaCategoria();
        mensaje("La categoría ha sido eliminada correctamente", "alert-success");
    });
}
function eliminaSubcategoria(idCategoria){
    $.post("ajax/categoria/eliminaCategoria.php", "idCategoria="+idCategoria, function(data){
        actualizaListaSubcategoria();
        ajaxToHtml("categoria/arbol.php", "#arbol", true, "");
        mensaje("La subcategoría ha sido eliminada correctamente", "alert-success");
    });
}
function eliminaDescuento(idDescuento){
    $.post("ajax/categoria/descuento/eliminarDescuento.php", "idDescuento="+idDescuento, function(data){
        actualizaListaDescuento();
        mensaje("El descuento ha sido eliminado correctamente", "alert-success");
    });
}

$(function(){
    $("#tabs a[href='#catalogos']").on('shown.bs.tab', function(e){
        if($("#catalogos").html() == ""){
            $.get("tabs/catalogos.html", {"_": $.now()}, function(data){
                $("#catalogos").html(data);
                actualizaListaCategoria();
                revisaPermisos("#subtab_catalogo a");
            });
        }
    });
    $(document).on('shown.bs.tab', "#subtab_catalogo a[href='#catalogo_subcategorias']", function(e){
        ajaxToHtml("categoria/getCategoriasSelect.php", "#select_categoria_subcategoria", true, "");
        actualizaListaSubcategoria();
        ajaxToHtml("categoria/arbol.php", "#arbol", true, "");
    });
    $(document).on('shown.bs.tab', "#subtab_catalogo a[href='#catalogo_descuentos']", function(e){
        actualizaListaDescuento();
    });
    //prepara para editar
    $(document).on("click", ".editaCategoria", function(){
        visibiliza($(this), "2", ".cancelaEdicionCategoria,.actualizaCategoria,.borraCategoria");
    });
    $(document).on("click", ".editaSubcategoria", function(){
        visibiliza($(this), "3", ".cancelaEdicionSubcategoria,.actualizaSubcategoria,.borraSubcategoria");
    });
    $(document).on("click", ".editaDescuento", function(){
        visibiliza($(this), "2,3", ".cancelaEdicionDescuento,.actualizaDescuento,.borraDescuento");
    });
    // edita
    $(document).on("click", ".actualizaCategoria", function(){
        var nombre = $(this).parent().parent().parent().parent().children("td:nth-child(2)").children("div").children("input");
        editarCategoria(nombre, $(this).parent().attr("data-idCategoria"));
    });
    $(document).on("click", ".actualizaSubcategoria", function(){
        var nombre = $(this).parent().parent().parent().parent().children("td:nth-child(3)").children("div").children("input");
        editarSubcategoria(nombre, $(this).parent().attr("data-idCategoria"));
    });
    $(document).on("click", ".actualizaDescuento", function(){
        var nombre = $(this).parent().parent().parent().parent().children("td:nth-child(2)").children("div").children("input");
        var valor = $(this).parent().parent().parent().parent().children("td:nth-child(3)").children("div").children("input");
        console.log(nombre);
        console.log(valor);
        editarDescuento(nombre, valor, $(this).parent().attr("data-idDescuento"));
    });
    // cancela
    $(document).on("click", ".cancelaEdicionCategoria", function(){
        var nombre = $(this).parent().parent().parent().parent().children("td:nth-child(2)").children("div").children("input");
        nombre.val(nombre.parent().attr("data-original"));
        bloqueaEdicionCategoria(nombre);
    });
    $(document).on("click", ".cancelaEdicionSubcategoria", function(){
        var nombre = $(this).parent().parent().parent().parent().children("td:nth-child(3)").children("div").children("input");
        nombre.val(nombre.parent().attr("data-original"));
        bloqueaEdicionSubcategoria(nombre);
    });
    $(document).on("click", ".cancelaEdicionDescuento", function(){
        var nombre = $(this).parent().parent().parent().parent().children("td:nth-child(2)").children("div").children("input");
        var valor = $(this).parent().parent().parent().parent().children("td:nth-child(3)").children("div").children("input");
        nombre.val(nombre.parent().attr("data-original"));
        valor.val(valor.parent().attr("data-original"));
        bloqueaEdicionDescuento(nombre, valor);
    });
    // borra
    $(document).on("click", ".borraCategoria", function(){
        confirma("Eliminar categoría", "Al borrar una categoría, las subcategorias de esa categoría se perderán y los productos con esta categoría quedarán sin categoría.<br>¿Deseas continuar?",
                 "eliminaCategoria("+$(this).parent().attr("data-idCategoria")+");");
    });
    $(document).on("click", ".borraSubcategoria", function(){
        confirma("Eliminar subcategoría", "Al borrar una subcategoría, los productos con esta subcategoría quedarán sin categoría.<br>¿Deseas continuar?",
                 "eliminaSubcategoria("+$(this).parent().attr("data-idCategoria")+");");
    });
    $(document).on("click", ".borraDescuento", function(){
        confirma("Eliminar descuento", "Se borrará el descuento. Esto <strong>no</strong> afecta ningún precio en los productos<br>¿Deseas continuar?",
                 "eliminaDescuento("+$(this).parent().attr("data-idDescuento")+");");
    });
    
    $(document).on("change", "#select_categoria_subcategoria", function(){
        var nuevo = "";
        if($(this).val() != ""){
            $.post("ajax/categoria/getSubcategoriaSelect.php", "id="+$(this).val(), function(data){
                if(data != ""){
                    nuevo = "<label>Selecciona una subcategoría si así lo deseas</label>";
                    nuevo += "<select class='select_subcategoria_subsub form-control'>"+data+"</select>";
                    nuevo += "<div class='acomodador'></div>";
                }
                $(".acomodador_subcategorias").eq(0).html(nuevo);
            });
        }
    });
    $(document).on("change", ".select_subcategoria_subsub", function(){
        var nuevo = "";
        var who = $(this).parent().children(".acomodador");
        if($(this).val() != ""){
            $.post("ajax/categoria/getSubcategoriaSelect.php", "id="+$(this).val(), function(data){
                if(data != ""){
                    nuevo = "<label>Selecciona una subcategoría si así lo deseas</label>";
                    nuevo += "<select class='select_subcategoria_subsub form-control'>"+data+"</select>";
                    nuevo += "<div class='acomodador'></div>";
                }
                who.html(nuevo);
            });   
        }
    });
});