$(document).ready(function(){
    $('.btn-load').on('click', function () {

        $.get($(this).data('url'), function (resp) {
            $.notify(resp.mensaje, resp.estado);
        })
    })
});