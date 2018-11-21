$(document).ready(function(){
    $('.btn-load').on('click', function () {

        $.get($(this).data('url'), function (resp) {
            $.notify({
                message: resp
            }, {animate:{
                    enter: "animated fadeInUp",
                    exit: "animated fadeOutDown"
                }});
        })
    })
});
