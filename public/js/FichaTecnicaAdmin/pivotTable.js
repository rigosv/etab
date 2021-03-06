var idIndicadorActivo;
var esCalidad = false;
var xaggregatorName = "Suma";
var escenarioActivo = null;
var alertas = [];
var formula = '';
var locale = 'es';

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
    
    if ($('#locale').length > 0 ) {
        locale = $('#locale').val();
    }
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
    /*$('#export_grp').click(function() {
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
    });*/
    
    $('#export_pdf').click(function() {
        
        var marginLeft = 2.5;
        
        var orientacion = $('#orientacion').val(); 
        var formato = $('#formato').val();
                
        var base64Image = $('#imagen').val().trim();
        var tipoImagen = $('#imagen_tipo').val(); 
        var imageHeight = parseFloat( $('#imagen_alto').val() );
        var imageWidth = parseFloat( $('#imagen_ancho').val() );
        var imageX = parseFloat( $('#imagen_coordenada_x').val() );
       
        
        var paginaText = $('#paginaText').val(); 
        var texto = $('#marco-sala').attr('data-content').trim();
        
        var topMargin = 2;
        var titleFontSize = 14;
        var fecha = new Date().format('d/m/Y h:i');
        
        var doc = new jsPDF(orientacion, 'cm', formato);
        var totalPagesExp = "{total_pages_count_string}";
        
        
        var splitTitle = doc.splitTextToSize(texto, doc.internal.pageSize.width - marginLeft * 2 );        
                
        doc.autoTable({
            html: $(".pvtTable")[0],
            didDrawPage: function (data) {
                // Header                
                doc.addImage(base64Image, tipoImagen, imageX, topMargin, imageWidth , imageHeight);

                doc.setFontSize(titleFontSize);
                doc.text(splitTitle, data.settings.margin.left, topMargin + imageHeight + 0.5);

                // Footer
                var str = paginaText + " " + doc.internal.getNumberOfPages()
                
                // Total page number plugin only available in jspdf v1.0+
                if (typeof doc.putTotalPages === 'function') {
                    str = str + " / " + totalPagesExp;
                }
                doc.setFontSize(10);

                // jsPDF 1.4+ uses getWidth, <1.4 uses .width
                var pageSize = doc.internal.pageSize;
                var pageHeight = pageSize.height ? pageSize.height : pageSize.getHeight();
                doc.text(str, data.settings.margin.left, pageHeight - 1);
                
                doc.text(fecha + ' (d/m/aaaa h:m)', pageSize.width - marginLeft * 3 , pageHeight - 1);
            },
            margin: {top: topMargin + imageHeight + 0.5 + 0.5 * splitTitle.length, left: marginLeft, right: marginLeft}
        });
        
        if (typeof doc.putTotalPages === 'function') {
            doc.putTotalPages(totalPagesExp);
        }
        
        window.open(doc.output('bloburl'), '_blank'); 
    });

    $('#guardarConf').click(function (){

        if (idIndicadorActivo != null){
            $.get(Routing.generate('pivotable_guardar_escenario_frm'),
                {id: idIndicadorActivo, tipoElemento: tipoElemento},
                function(resp) {
                    $('#myModal2').find('.modal-body').html(resp);
                    $('#myModal2').find('#btnGuardarConf').show();
                    $('#btnGuardarConf').click(function(){
                        guardarEscenario();
                    });


                    if ( escenarioActivo != null ){
                        $('#myModal2').find('#nombre').val(escenarioActivo.nombre);
                        $('#myModal2').find('#default').attr('checked', (escenarioActivo.pordefecto ==  true));
                    }
                    $('#myModal2').modal('show');
                });
        }
    });

    function guardarEscenario(){

        configuracion_guardada = configuracion;
        var conf = JSON.stringify(configuracion_guardada, undefined, 2);

        var nombre = $('#myModal2').find('#nombre').val();

        var defaultEscenario = ( $('#myModal2').find('#default') != undefined ) ? $('#myModal2').find('#default').is(':checked') : null;

        if (nombre != '') {
            $.post(Routing.generate('pivotable_guardar_estado'),
                {tipoElemento: tipoElemento, 'id': idIndicadorActivo, 'nombre': nombre, 'pordefecto': defaultEscenario, 'configuracion': conf},
                function (datos) {

                    if ( datos.estado == 'success' ){
                        $.notify(datos.mensaje, 'success');
                        $('#myModal2').modal('hide');
                        escenarioActivo = {tipoElemento: tipoElemento, 'id': idIndicadorActivo, 'nombre': nombre, 'pordefecto': defaultEscenario};
                        $('#nombre-escenario').html(nombre);
                        $('#div-nombre-escenario').show();
                    } else {
                        $('#myModal2').find('#btnGuardarConf').notify(datos.mensaje, {className: "error" });
                    }
                }, 'json');
        } else {
            $('#myModal2').find('#nombre').notify(trans.campo_requerido, {className: "error" });
        }
    }

    $('#cargarConf').click(function (){
        $.post(Routing.generate('pivotable_get_escenarios',
            {tipoElemento: tipoElemento, idElemento:identificadorElemento}),
            function(resp) {
                $('#myModal2').find('.modal-body').html(resp);
                $('#myModal2').find('#btnGuardarConf').hide();
                $('#myModal2').modal('show');

                $('[data-toggle="popover"]').popover()

                $('.escenario').click(function(){
                    var idEscenario = $(this).data('id');
                    $('#nombre-escenario').html($(this).html());
                    $('#div-nombre-escenario').show();
                    cargarTablaDinamica(datos_, alertas, formula, idEscenario);

                });
            }, 'html');
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
        escenarioActivo = null;
        alertas = [];
        formula = '';
        xaggregatorName = "Suma";
        $('#div-nombre-escenario').hide();
        var id_indicador = $(this).attr('data-id');
        var nombre_indicador = $(this).html();

        $.getJSON(Routing.generate('get_datos_indicador', {id: id_indicador}), function(mps) {
            var datos = [];

            tipoElemento = 'indicador';
            identificadorElemento = id_indicador;
            datos = datos.concat(mps.datos);

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
                            cargarTablaDinamica(datos, mpsx.alertas, mpsx.formula);
                        }
                    });
                }
            } else {
                cargarTablaDinamica(datos, mps.alertas, mps.formula);
            }
            alertas = mps.alertas;
            formula = mps.formula;
            //cargarDescripcionAtributos(id_indicador);
        });
    });


    $('A.formulario_captura_datos').click(function() {
        esCalidad = false;
        escenarioActivo = null;
        alertas = [];
        formula = '';
        xaggregatorName = "Suma";
        $('#div-nombre-escenario').hide();
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
        escenarioActivo = null;
        alertas = [];
        formula = '';
        xaggregatorName = "Suma";
        $('#div-nombre-escenario').hide();
        var nombre_elemento = $(this).html();
        var idFrm = $(this).attr('data-id');

        $.getJSON(Routing.generate('get_datos_evaluacion_calidad', {id: idFrm}), function(mps) {
            datos_ = mps;
            tipoElemento = 'calidad';
            identificadorElemento = idFrm;
            cargarTablaDinamica(mps);

            $('#marco-sala').attr('data-content', nombre_elemento);
            $('#myTab a:first').tab('show');
        });
    });

    $('A.log_actividad_item').click(function() {
        esCalidad = false;
        escenarioActivo = null;
        alertas = [];
        formula = '';
        xaggregatorName = "Suma";
        $('#div-nombre-escenario').hide();

        $.getJSON(Routing.generate('get_log_actividad'), function(mps) {
            datos_ = mps;
            tipoElemento = 'log_actividad';
            identificadorElemento = '1';
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

    function cargarTablaDinamica(datos, alertas = [], formula = '', idEscenario = 0){

        datos_ = datos;
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

        //Verificar si tiene escenario por defecto
        $.post(Routing.generate('pivotable_obtener_estado',
            {'tipoElemento': tipoElemento, 'id':identificadorElemento, 'idEscenario': idEscenario}),
            function(resp) {

                var cfgVals = [];
                var cfgRows = [];
                var cfgCols = [];
                var cfgAggregatorName = '';
                var cfgRendererName = 'Table';
                var cfgExclusions = {};
                var cfgInclusions = {};
                var cfgRowOrder = 'key_a_to_z';
                var cfgColOrder = 'key_a_to_z';
                var conf='';

                if (resp !== '{}'){
                    conf = JSON.parse(resp);
                    cfgVals = conf.vals;
                    cfgRows = conf.rows;
                    cfgCols = conf.cols;
                    cfgAggregatorName = conf.aggregatorName;
                    cfgRendererName = conf.rendererName;
                    cfgExclusions = conf.exclusions;
                    cfgInclusions = conf.inclusions;
                    cfgRowOrder = conf.rowOrder;
                    cfgColOrder = conf.colOrder;
                }
                
                if (esCalidad){

                    $("#output").pivotUI(datos, {
                        renderers: renderers,
                        dataClass: dataClass,
                        menuLimit: 1000,
                        aggregatorName: ( cfgAggregatorName == '') ? xaggregatorName : cfgAggregatorName,
                        vals : cfgVals,
                        rows: cfgRows,
                        cols: cfgCols,
                        rendererName: cfgRendererName,
                        exclusions: cfgExclusions,
                        inclusions: cfgInclusions,
                        rowOrder: cfgRowOrder,
                        colOrder: cfgColOrder,
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
                    }, true, locale);
                } else {

                    var factor, xvals='';

                    if ( formula != '' ){
                        var factorF = formula.toLowerCase().replace(/[{}]/g, '__').split('*');

                        factor = ( factorF.length > 1 && ( factorF[1] == 100 || factorF[1] == 1000 ) ) ? factorF[1] : 1;

                        var operandos = factorF[0].split('/');
                        if ( operandos.length > 1 ){
                            xaggregatorName = (locale == 'es') ? 'Cociente' : 'Sum over Sum';
                            xvals =  [operandos[0], operandos[1]];
                        } else {
                            xaggregatorName = (locale == 'es') ? 'Suma' : 'Sum';
                            xvals =  [operandos[0]];
                        }
                    }
                    $.getJSON(Routing.generate('get_campos_variables_indicador', {id: identificadorElemento}), function(nombres) {
                        var datos_ = JSON.stringify( datos );
                        var cfg_ = JSON.stringify( conf );
                        var desc = '';
                        $.each(JSON.parse(JSON.stringify(nombres)), function (idx, descripcion) {
                            desc = descripcion.replace('Identificador', '').trim();
                            desc = desc[0].toUpperCase() + desc.slice(1);
                            datos_ = datos_.split('"' + idx + '":').join('"' + desc + '":');
                            cfg_ = cfg_.split('"' + idx + '":').join('"' + desc + '":');
                        });
                        datos = JSON.parse(datos_);
                        let cfg = JSON.parse(cfg_);

                        $("#output").pivotUI(datos, {
                            renderers: renderers,
                            dataClass: dataClass,
                            aggregatorName: ( cfg.aggregatorName == '') ? xaggregatorName : cfg.aggregatorName,
                            vals : ( cfg.vals == [] ) ? xvals : cfg.vals,
                            rows: cfg.rows,
                            cols: cfg.rols,
                            rendererName: cfg.rendererName,
                            exclusions: cfg.exclusions,
                            inclusions: cfg.enclusions,
                            rowOrder: cfg.rowOrder,
                            colOrder: cfg.colOrder,
                            menuLimit: 1000,
                            unusedAttrsVertical: false,
                            onRefresh: onChangeTable,
                            rendererOptions: {
                                arrowCollapsed: "[+] ",
                                arrowExpanded: "[-] ",
                                collapseRowsAt: 0,
                                plotlyConfig : configPlotly,
                                heatmap: {
                                    colorScaleGenerator : function(values) {
                                        if ( alertas.length == 0){
                                            var max, min;
                                            min = Math.min.apply(Math, values);
                                            max = Math.max.apply(Math, values);
                                            return function(x) {
                                                var nonRed;
                                                nonRed = 255 - Math.round(255 * (x - min) / (max - min));
                                                return "rgb(255," + nonRed + "," + nonRed + ")";
                                            };
                                        } else {
                                            return function (x) {
                                                var rango, i;
                                                for (i in alertas) {
                                                    rango = alertas[i];

                                                    if ( Math.round(x * factor) >= rango.li && Math.round(x * factor) <= rango.ls) {
                                                        return rango.color;
                                                    }
                                                }
                                                return "white";
                                            };
                                        }
                                    }
                                },
                            }
                        }, true, locale);
                    });
                }


            }, 'json');



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

function borrarEscenario(id) {
    $.get(Routing.generate('borrar_escenario',
        {'id': id}),
        function(resp) {
            $('#myModal2').modal('hide');
        });
}
