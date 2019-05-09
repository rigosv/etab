function openHelpPanel(urlBase, recurso) {

    if ( parseFloat($("#helpSidebar").css('width').replace('px')) == 0 ) {
        let width = parseFloat($("section.content").css('width').replace('px')) * 0.5;

        $("#helpSidebar DIV.content-help").hide();
        cargarHelpContent(urlBase, recurso);

        $("#helpSidebar").css('width', width + 'px');
        $("section.content-header").css('margin-right', width + 'px');
        $("section.content").css('margin-right', width + 'px');
    }
}

function cargarHelpContent(urlBase, recurso){
    $("#helpSidebar DIV.content-help").load(urlBase+recurso, function(){
        $('#helpSidebar').find('IMG').each(function (){
            $(this).attr('src', urlBase+$(this).attr('src'));
        });

        $('#helpSidebar').find('A').click(function (e) {
            e.preventDefault();
            cargarHelpContent(urlBase, $(this).attr('href'));
        });
        $("#helpSidebar .menu .span9").toggleClass('span9 col-md-9');
        $("#helpSidebar .menu .span3").toggleClass('span3 col-md-3');

        $("#helpSidebar DIV.content-help").show();
        $("#helpSidebar .navbtn").show();

        //Verificar si tiene un punto de enlace dentro de la página cargada
        var partes = recurso.split('#');
        if (partes.length > 1 ){
            var height =parseFloat( $('#helpSidebar').css('height').replace('px') );
            $('#helpSidebar').animate({scrollTop: $("#"+partes[1]).position().top - height*0.45},'slow');
        }
    });
}



function closeHelpPanel() {
    $("#helpSidebar").css('width', "0");
    $("section.content-header").css('margin-right', "0");
    $("section.content").css('margin-right', "0");
    $("#helpSidebar .navbtn").hide();
}

function maxresHelpPanel() {
    let mainWidth = parseFloat( $("section.content").css('width').replace('px') );
    let helpPanelWidth = parseFloat( $("#helpSidebar").css('width').replace('px') );

    if ( helpPanelWidth > mainWidth ) {
        $("#helpSidebar").css('width', mainWidth);
        $('#maxresBtn').attr('title', $('#maxresBtn').data('title-max'));
    } else {
        $("#helpSidebar").css('width', helpPanelWidth * 2 );
        $('#maxresBtn').attr('title', $('#maxresBtn').data('title-res'));
    }

    $('#maxresBtn').find('i').toggleClass('fa-window-maximize fa-window-restore');
}