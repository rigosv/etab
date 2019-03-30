var idIndicadorActivo;
var esCalidad = false;
var xaggregatorName = "Suma";
var heatmapX = {};

$(document).ready(function() {
    var datos_ = '';
    var configuracion = '';
    var configuracion_guardada = '';
    var tipoElemento = '';
    var identificadorElemento = '';
    $('#myTab a').click(function(e) {
        e.preventDefault();
        $(this).tab('show');
    });
    function ajax_states() {
        $(document).bind("ajaxStart.mine", function() {
            $('#div_carga').show();
        });
        $(document).bind("ajaxStop.mine", function() {
            $('#div_carga').hide();
        });
    }
    ajax_states();
    $('#export').click(function(){
        $('.pvtTable').table2excel({
            exclude: ".excludeThisClass",
            name: $('#marco-sala').attr('data-content'),
            filename: $('#marco-sala').attr('data-content').trim() //do not include extension
        });
    });
    $('#export_grp').click(function() {
        var html = $('.pvtRendererArea').html();
        var svg = $('.main-svg');
        if (svg.length == 0){
            $('#export_grp').notify(trans._no_grafico_, {className: "info" });
            return;
        }

        $('#sql').html('<canvas id="canvasGrp" width="'+svg.attr('width')+'" height="'+svg.attr('height')+'"></canvas>');

        var canvas = document.getElementById("canvasGrp");

        rasterizeHTML.drawHTML(html, canvas)
            .then(function success(renderResult) {
                var link = document.createElement("a");
                canvas = document.getElementById("canvasGrp");
                link.href = canvas.toDataURL("image/png");

                link.download = 'grafico.png';
                document.body.appendChild(link);
                link.click();
            });
    });

    $('#guardarConf').click(function (){
        configuracion_guardada = configuracion;
        var conf = JSON.stringify(configuracion_guardada, undefined, 2);
        $.post(Routing.generate('pivotable_guardar_estado',
            {tipoElemento: tipoElemento, id:identificadorElemento, configuracion: conf}),
            function(datos) {

            }, 'json');
    });

    $('#cargarConf').click(function (){

        var renderers = $.extend($.pivotUtilities.renderers,
            $.pivotUtilities.gchart_renderers);
        if (configuracion_guardada == ''){
            $.post(Routing.generate('pivotable_obtener_estado',
                {tipoElemento: tipoElemento, id:identificadorElemento}),
                function(conf) {
                    if (conf === ''){
                        alert('No existe una configuración guardada');
                    }else {
                        configuracion_guardada = conf;
                        configuracion_guardada["renderers"] = renderers;
                        configuracion_guardada["onRefresh"] = onChangeTable;
                        $("#output").pivotUI(datos_, configuracion_guardada , true, 'es');
                    }
                }, 'json');
        } else{
            configuracion_guardada["renderers"] = renderers;
            configuracion_guardada["onRefresh"] = onChangeTable;
            $("#output").pivotUI(datos_, configuracion_guardada , true, 'es');
        }

    });

    $('#ver_ficha').click(function() {
        if (idIndicadorActivo != null){
            $.get(Routing.generate('get_indicador_ficha',
                {id: idIndicadorActivo}),
                function(resp) {
                    resp.replace('span12', 'span10');
                    $('#fichaTecnicaContent').html(resp);
                    $('#fichaTecnicaContent').html('<table>' + $('#fichaTecnicaContent table').html() + '</table>');
                    $('#fichaTecnica').modal('show');
                });
        }
    });

    $("#FiltroNoClasificados").searchFilter({targetSelector: ".grupo_indicadores", charCount: 2});
    $("#FiltroNoClasificados").searchFilter({targetSelector: ".indicador", charCount: 2});

    $('A.indicador').click(function() {
        esCalidad = false;
        var id_indicador = $(this).attr('data-id');
        var nombre_indicador = $(this).html();

        $.getJSON(Routing.generate('get_datos_indicador', {id: id_indicador}), function(mps) {
            var datos = [];
            var alertas = [];

            tipoElemento = 'indicador';
            identificadorElemento = id_indicador;
            datos = datos.concat(mps.datos);
            alertas = alertas.concat(mps.alertas);



            $('#marco-sala').attr('data-content', nombre_indicador);
            $('#myTab a:first').tab('show');
            idIndicadorActivo = id_indicador;

            var cargadas = 1;
            if (mps.total_partes != undefined && mps.total_partes > 1){
                for(var i = 2; i <= mps.total_partes ; i++){
                    $.getJSON(Routing.generate('get_datos_indicador', {id: id_indicador}), {parte: i}, function(mpsx) {
                        datos = datos.concat(mpsx.datos);
                        cargadas++;
                        if (cargadas == mps.total_partes){
                            cargarTablaDinamica(datos);
                        }
                    });
                }
            } else {
                cargarTablaDinamica(datos);
            }
            cargarDescripcionAtributos(id_indicador);
        });
    });

    $('A.elemento_costeo').click(function() {
        esCalidad = false;
        var codigo = $(this).attr('data-id');
        var nombre_elemento = $(this).html();

        $.getJSON(Routing.generate('get_datos_costeo', {codigo: codigo}), function(mps) {
            datos_ = mps;
            tipoElemento = 'costeo';
            identificadorElemento = codigo;
            cargarTablaDinamica(mps);
            $('#marco-sala').attr('data-content', nombre_elemento);
            $('#myTab a:first').tab('show');
        });
    });

    $('A.formulario_captura_datos').click(function() {
        esCalidad = false;
        var codigo = $(this).attr('data-id');
        var nombre_elemento = $(this).html();

        $.getJSON(Routing.generate('get_datos_formulario_captura', {codigo: codigo}), function(mps) {
            if (mps.estado == 'error'){
                $('#output').html('<div class="alert alert-warning" role="alert">'+mps.msj+'</div>');
            }
            else {
                datos_ = mps;
                tipoElemento = 'formulario';
                identificadorElemento = codigo;
                cargarTablaDinamica(mps);
            }
            $('#marco-sala').attr('data-content', nombre_elemento);
            $('#myTab a:first').tab('show');
        });
    });

    $('A.calidad_datos_item').click(function() {
        esCalidad = true;
        var nombre_elemento = $(this).html();
        var idFrm = $(this).attr('data-id');
        $.getJSON(Routing.generate('get_datos_evaluacion_calidad', {id: idFrm}), function(mps) {
            datos_ = mps;
            tipoElemento = 'calidad';
            identificadorElemento = 'calidad';
            cargarTablaDinamica(mps);

            $('#marco-sala').attr('data-content', nombre_elemento);
            $('#myTab a:first').tab('show');
        });
    });

    $('A.log_actividad_item').click(function() {
        esCalidad = false;
        $.getJSON(Routing.generate('get_log_actividad'), function(mps) {
            datos_ = mps;
            tipoElemento = 'log_actividad';
            identificadorElemento = 'log_actividad';
            cargarTablaDinamica(mps);

            $('#marco-sala').attr('data-content', 'Bitácora de actividad');
            $('#myTab a:first').tab('show');
        });
    });

    function cargarDescripcionAtributos(id_indicador) {
        $.getJSON(Routing.generate('get_campos_variables_indicador', {id: id_indicador}), function(datos) {
            $.each( datos, function(idx, descripcion){

                $('.pvtAttr:contains('+idx+')').attr('title', descripcion);
            });
        })
    }

    function cargarTablaDinamica(datos){
        var renderers = $.extend($.pivotUtilities.renderers, $.pivotUtilities.subtotal_renderers,
            $.pivotUtilities.plotly_renderers);
        var dataClass = $.pivotUtilities.SubtotalPivotData;

        var configPlotly = {
            displayModeBar: true,
            displaylogo: false,
            modeBarButtonsToRemove: ['select2d', 'lasso2d', 'resetScale2d', 'toggleSpikelines', 'hoverClosestCartesian', 'hoverCompareCartesian' ],
            responsive: true,
            locale: 'es',
            toImageButtonOptions: {
                format: 'png', // one of png, svg, jpeg, webp
                filename: 'grafico',
                height: 500,
                width: 700,
                scale: 1, // Multiply title/legend/axis/canvas sizes by this factor
            }
        };

        if (esCalidad){
            $("#output").pivotUI(datos, {
                renderers: renderers,
                dataClass: dataClass,
                menuLimit: 500,
                aggregatorName: xaggregatorName,
                unusedAttrsVertical: false,
                onRefresh: arreglarValores0,
                rendererOptions: {
                    arrowCollapsed: "[+] ",
                    arrowExpanded: "[-] ",
                    collapseRowsAt: 0,
                    plotlyConfig : configPlotly,
                    heatmap: {
                        colorScaleGenerator : function(values) {
                            var max, min;
                            min = Math.min.apply(Math, values);
                            max = Math.max.apply(Math, values);
                            return function(x) {
                                if (x < 59.9)
                                    return "#D73925";
                                else if (x < 79.9)
                                    return "#ffa500";
                                else
                                    return "#008D4C";
                            };
                        }
                    }
                }
            }, true, 'es');
        } else {
            $("#output").pivotUI(datos, {
                renderers: renderers,
                dataClass: dataClass,
                aggregatorName: xaggregatorName,
                menuLimit: 500,
                unusedAttrsVertical: false,
                heatmap: heatmapX,
                onRefresh: onChangeTable,
                rendererOptions: {
                    arrowCollapsed: "[+] ",
                    arrowExpanded: "[-] ",
                    collapseRowsAt: 0,
                    plotlyConfig : configPlotly,
                }
            }, true, 'es');
        }

    }

    var onChangeTable = (function(config) {
        var config_copy = JSON.parse(JSON.stringify(config));
        //delete some values which are functions
        delete config_copy["aggregators"];
        delete config_copy["renderers"];
        //delete some bulky default values
        delete config_copy["rendererOptions"];
        delete config_copy["localeStrings"];
        configuracion = config_copy;

        arreglarValores0();

        $div = ($('#container1').length) ?  $('#container1') : $('<DIV class="pvtTableContainer" id="container1"></DIV>');
        $('.pvtUnused').children().not('.pvtTableContainer').appendTo($div);
        $div.appendTo('.pvtUnused');

        $div2 = ($('#container2').length) ?  $('#container2') :  $('<DIV class="pvtTableContainer" id="container2"></DIV>');
        $('.pvtCols').children().not('.pvtTableContainer').appendTo($div2);
        $div2.appendTo('.pvtCols');

        $div3 = ($('#container3').length) ?  $('#container3') :  $('<DIV class="pvtTableContainer" id="container3"></DIV>');
        $div3.appendTo('.pvtRendererArea');
        $('.pvtTable').appendTo('#container3');

    });

    var arreglarValores0 = function(){
        $('.pvtVal[data-value="0"]').html('0.00');
        $('.pvtTotal[data-value="0"]').html('0.00');
        $('.pvtTotalLabel').html('Totales');
    };
});