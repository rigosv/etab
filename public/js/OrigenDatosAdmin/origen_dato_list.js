$(document).ready(function(){
    $('.btn-load').on('click', function () {

        $.get($(this).data('url'), function (resp) {
            $.notify({
                message: resp.mensaje
            }, {
                animate:{
                    enter: "animated fadeInUp",
                    exit: "animated fadeOutDown"
                },
                type: resp.estado
            });
        })
    })
});