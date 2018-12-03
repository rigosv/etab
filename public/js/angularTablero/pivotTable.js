var idIndicadorActivo;
google.load("visualization", "1", {packages: ["corechart", "charteditor"]});
$(document).ready(function() {
    $('#myTab a').click(function(e) {
        e.preventDefault();
        $(this).tab('show');
    });
    function ajax_states(){
        $(document).bind("ajaxStart.mine", function() {
            $('#div_carga').show();
        });
        $(document).bind("ajaxStop.mine", function() {
            $('#div_carga').hide();
        });
    }
    ajax_states(); 

    $('#export').on('click',function(e) {
       var t = $('.pvtTable');
        tableToExcel(t[0],'indicador', $('#titulo_header').attr('data-content').trim()+'.xls');
    });
    $('#export_grp').on('click',function(e) {      
        $('#myModalLabel2').html(trans.guardar_imagen);
        $('#myModal2').modal('show');
    });
    
    $('#ver_ficha').on('click',function(e) {
        if (idIndicadorActivo != null){ 
            $.get(Routing.generate('get_indicador_ficha',{id: idIndicadorActivo}), function(resp) {
                resp.replace('span12', 'span10');
                $('#fichaTecnicaContent').html(resp);
                $('#fichaTecnicaTitle').html($('#fichaTecnicaContent').find(".sonata-ba-view-title").find("h2").html());
                $('#fichaTecnicaContent').find(".sonata-ba-view-title").find("h2").remove();
                $('#fichaTecnicaContent').html('<table>' + $('#fichaTecnicaContent table').html() + '</table>');
                $('#fichaTecnica').modal('show');
            });
        }
    });
    
});


var tableToExcel = (function() {
  var uri = 'data:application/vnd.ms-excel;base64,'
    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
  return function(table, name, filename) {
    if (table !== undefined){        
        if (!table.nodeType) table = document.getElementById(table);
        var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML};
        document.getElementById("dlink").href = uri + base64(format(template, ctx));
        document.getElementById("dlink").download = filename;
        document.getElementById("dlink").click();
    }
}
})();