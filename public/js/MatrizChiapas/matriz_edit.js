$(document).ready(function () {

    var uniqid = $("[class=form-horizontal]").attr("action").split('=');
    var idcontrol = uniqid[1];
 

    //$("#" + idcontrol + "_matrizIndicadoresEtab").attr("class", "list-group");
    //$("#" + idcontrol + "_matrizIndicadoresEtab li").attr("class", "list-group-item");
    //$("#" + idcontrol + "_matrizIndicadoresEtab").attr("style", "max-height:380px; overflow:auto;");


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
