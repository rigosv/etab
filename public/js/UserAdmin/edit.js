$(document).ready(function() {
    

    var uniqid = $("[class=form-horizontal]").attr("action").split('=');
    var idcontrol = uniqid[1];

    $("#" + idcontrol + "_realRoles").attr("class", "list-group").attr("style", "max-height:380px; overflow:auto;");
    $("#" + idcontrol + "_realRoles li").attr("class", "list-group-item");

    $("#" + idcontrol + "_indicadores").attr("class", "list-group").attr("style", "max-height:380px; overflow:auto;");
    $("#" + idcontrol + "_indicadores li").attr("class", "list-group-item");

    $("#" + idcontrol + "_salas").attr("class", "list-group").attr("style", "max-height:380px; overflow:auto;");
    $("#" + idcontrol + "_salas li").attr("class", "list-group-item");

    $("#" + idcontrol + "_groups").attr("class", "list-group").attr("style", "max-height:380px; overflow:auto;");
    $("#" + idcontrol + "_groups li").attr("class", "list-group-item");

    $("#sonata-ba-field-container-" + idcontrol + "_groups").find("label:first").remove();
    $("#sonata-ba-field-container-" + idcontrol + "_salas").find("label:first").remove();
    $("#sonata-ba-field-container-" + idcontrol + "_realRoles").find("label:first").remove();
    $("#sonata-ba-field-container-" + idcontrol + "_realRoles").find("h4:first").remove();
    $("#sonata-ba-field-container-" + idcontrol + "_indicadores").find("label:first").remove();

    $(".sonata-ba-field-standard-natural").attr("class", "sonata-ba-field col-sm-12 sonata-ba-field-standard-natural");

    $("#tab_" + idcontrol + "_3").find(".sonata-ba-collapsed-fields").find(".col-md-12:first").attr("class", "sonata-ba-field col-md-6");
    
    
    $(".editable").attr("class", "");

    $(".popover").attr("style", "top:-100%; margin-left:100%; width:600px; display: block; z-index:9999999999;");




    //Ocultar los indicadores asignados
    $('div[id$="_gruposIndicadores"]').hide();

    // Recuperar los indicadores del usuario
    $('div[id$="_gruposIndicadores"] span').each(function (i, nodo) {
        //alert($(nodo).html().trim());
        $('div[id$="salas"] input:checkbox[value=' + $(nodo).html().trim() + ']').attr('checked', true);

    });

    //Mandar al servidor a guardar o borrar la asignación de sala al usuario
    $('div[id$="salas"] input:checkbox').change(function () {
        var accion;
        if ($(this).is(':checked'))
            accion = 'add';
        else
            accion = 'remove';
        $.get(Routing.generate('usuario_asignar_sala',
            { id: $('input[id$="_id"]').val(), id_sala: $(this).val(), accion: accion }));
    });

    $('ul[id$="_realRoles"] span').each(function (i, nodo) {

        $(this).html($(this).html().replace('ROLE_SONATA_ADMIN_', ''));
        $(this).html($(this).html().replace('ROLE_SONATA_USER_ADMIN_', ''));
    });

});
