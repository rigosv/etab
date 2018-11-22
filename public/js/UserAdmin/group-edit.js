$(document).ready(function () {

    var uniqid = $("[class=form-horizontal]").attr("action").split('=');
    var idcontrol = uniqid[1];
 
    

    $("#" + idcontrol + "_roles").attr("class", "list-group");
    $("#" + idcontrol + "_roles li").attr("class", "list-group-item");   
    
    $(".editable").html('<ul class="list-group" style="max-height:380px; overflow:auto;">' +
        $("#" + idcontrol + "_roles").html() +
        '</ul>' 
    );


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


    $(".popover").attr(
        "style",
        "top:-100%; margin-left:100%; width:600px; display: block; z-index:9999999999;"
    );

    hei = screen.height / 5;
    $("#" + idcontrol + "_1 .unstyled").attr("class", "unstyled span8");
    $("#field_dialog_" + idcontrol + "_groups .form-group").attr(
        "class",
        "col-lg-12"
    );
    $("#field_dialog_" + idcontrol + "_groups input[type=text]").attr(
        "class",
        "form-control"
    );
    $("#" + idcontrol + "_groups").attr("class", "unstyled col-lg-8");
    $(".span5").attr("class", "unstyled col-lg-12");

    $("#field_dialog_" + idcontrol + "_groups .editable ui").attr(
        "class",
        "list-group"
    );
    $("#field_dialog_" + idcontrol + "_groups .editable li").attr(
        "class",
        "list-group-item"
    );
    var ui = $("#field_dialog_" + idcontrol + "_groups .editable ul").html();

    
    $("#btn_close").click(function (e) {
        $(this)
            .closest(".ui-dialog-content")
            .dialog("close");
    });

    $('ul[id$="_roles"] span').each(function (i, nodo) {
        $(this).html(
            $(this)
                .html()
                .replace("ROLE_SONATA_ADMIN_", "")
        );
        $(this).html(
            $(this)
                .html()
                .replace("ROLE_SONATA_USER_ADMIN_", "")
        );
    });
});
