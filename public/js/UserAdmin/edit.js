$(document).ready(function() {
    

    var uniqid = $("[class=form-horizontal]").attr("action").split('=');
    var idcontrol = uniqid[1];


    $("#" + idcontrol + "_groups").attr("class", "list-group");
    $("#" + idcontrol + "_groups li").attr("class", "list-group-item");
    $("#sonata-ba-field-container-" + idcontrol + "_groups ").attr("style", "padding: 0px 20px 0px 15px;");
    $("#" + idcontrol + "_groups").attr("style", "max-height:380px; overflow:auto;");
    $("#sonata-ba-field-container-" + idcontrol + "_groups ").html(
        '<div class="col-sm-12" >' +
        '<ul class="list-group">' +
        $("#field_container_" + idcontrol + "_groups").html() +
        '</ul>' +
        '</div>'
    );
    
    $("#" + idcontrol + "_realRoles ").attr("class", "list-group");
    $("#" + idcontrol + "_realRoles li").attr("class", "list-group-item");
    $("#sonata-ba-field-container-" + idcontrol + "_realRoles ").attr("style", "padding: 0px 20px 0px 15px;");
    $("#sonata-ba-field-container-" + idcontrol + "_realRoles ").html('<div class="col-sm-12" >' + '<ul class="list-group" style=" max-height:400px; overflow:auto; ">' + $("#" + idcontrol + "_realRoles").html() + "</ul>" + "</div>");

    $("#" + idcontrol + "_indicadores").attr("class", "list-group");
    $("#" + idcontrol + "_indicadores li").attr("class", "list-group-item");
    $("#sonata-ba-field-container-" + idcontrol + "_roles ").attr("style", "padding: 0px 20px 0px 15px;");
    $("#sonata-ba-field-container-" + idcontrol + "_indicadores ").html(
        '<div class="col-sm-12" >' +
        '<ul class="list-group" style="max-height:400px; overflow:auto;">' +
        $("#" + idcontrol + "_indicadores").html() +
        '</ul>' +
        '</div>'
    );

    $("#" + idcontrol + "_salas").attr("class", "list-group");
    $("#" + idcontrol + "_salas li").attr("class", "list-group-item");
    $("#sonata-ba-field-container-" + idcontrol + "_salas ").attr("style", "padding: 0px 20px 0px 15px;");
    $("#sonata-ba-field-container-" + idcontrol + "_salas ").html(
        '<div class="col-sm-12" >' +
        '<ul class="list-group" style="max-height:400px; overflow:auto;">' +
        $("#" + idcontrol + "_salas").html() +
        '</ul>' +
        '</div>'
    );

    
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
