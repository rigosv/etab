
var nombre;
var cargarTablaDinamica = (function (idContenedorTabla, urlDatos, urlEscenario = null, configuracion = null) {

    var conf = (configuracion !== null) ? configuracion : {'vals': [], 'rows': [], 'cols': [], 'aggregatorName': 'Suma',
        'rendererName': 'Table', 'exclusions': {}, 'inclusions': {},
        'rowOrder': 'key_a_to_z', 'colOrder': 'key_a_to_z'};
    if (configuracion === null && urlEscenario !== null) {
        $.getJSON(urlEscenario + '?callback=?', function (resp) {
            if (resp.state === 'ok') {
                conf = JSON.parse(resp.escenario);
            }
            cargarDatosTabla(urlDatos, idContenedorTabla, conf);
        });
    } else {
        cargarDatosTabla(urlDatos, idContenedorTabla, conf);
    }
});

var cargarDatosTabla = (function (urlDatos, idContenedorTabla, cfg) {
    var renderers = $.extend($.pivotUtilities.renderers, $.pivotUtilities.plotly_renderers, $.pivotUtilities.export_renderers);

    var xaggregatorName = "Suma";
    var idIndicador;

    $.getJSON(urlDatos + '?callback=?', function (mps) {
        var datos = [];
        if (mps.state === 'ok') {
            datos = mps.datos[0].filas;
            idIndicador = mps.datos[0].indicador_id;
            nombre = mps.datos[0].nombre;

            $.getJSON('/rest-service/indicador/' + idIndicador + '/campos' + '?callback=?', function (nombres) {
                var datos_ = JSON.stringify( datos );
                var cfg_ = JSON.stringify( cfg );
                var desc = '';
                $.each(JSON.parse(JSON.stringify(nombres)), function (idx, descripcion) {
                    desc = descripcion.replace('Identificador', '').trim();
                    desc = desc[0].toUpperCase() + desc.slice(1);
                    datos_ = datos_.split('"' + idx + '":').join('"' + desc + '":');
                    cfg_ = cfg_.split('"' + idx + '":').join('"' + desc + '":');
                });
                datos = JSON.parse(datos_);
                cfg = JSON.parse(cfg_);

                $("#" + idContenedorTabla).pivotUI(datos, {
                    aggregatorName: (cfg.aggregatorName == '') ? xaggregatorName : cfg.aggregatorName,
                    vals: (cfg.vals == []) ? xvals : cfg.vals,
                    rows: cfg.rows,
                    cols: cfg.cols,
                    rendererName: cfg.rendererName,
                    exclusions: cfg.exclusions,
                    inclusions: cfg.inclusions,
                    rowOrder: cfg.rowOrder,
                    colOrder: cfg.colOrder,
                    renderers: renderers,
                    menuLimit: 1000,
                    unusedAttrsVertical: false,
                    onRefresh: onChangeTable,
                    rendererOptions: {
                        arrowCollapsed: "[+] ",
                        arrowExpanded: "[-] ",
                        collapseRowsAt: 0,
                        plotlyConfig: {
                            displayModeBar: true,
                            displaylogo: false,
                            modeBarButtonsToRemove: ['select2d', 'lasso2d', 'resetScale2d', 'toggleSpikelines', 'hoverClosestCartesian', 'hoverCompareCartesian'],
                            responsive: true,
                            locale: 'es',
                            toImageButtonOptions: {
                                format: 'png', // one of png, svg, jpeg, webp
                                filename: 'grafico',
                                height: 500,
                                width: 700,
                                scale: 1, // Multiply title/legend/axis/canvas sizes by this factor
                            }
                        }
                    }
                }, true, 'es');

                $('#' + idContenedorTabla).prepend("<BUTTON type='button' id='exportar'>Exportar</BUTTON>");
                $('#' + idContenedorTabla).find('#exportar').click(function () {
                    $('.pvtTable').table2excel({
                        exclude: ".excludeThisClass",
                        name: nombre,
                        filename: nombre.trim() //do not include extension
                    });
                });
            });
        }
    });
});

var onChangeTable = (function (config) {

    arreglarValores0();

    $div = ($('#container1').length) ? $('#container1') : $('<DIV class="pvtTableContainer" id="container1"></DIV>');
    $('.pvtUnused').children().not('.pvtTableContainer').appendTo($div);
    $div.appendTo('.pvtUnused');

    $div2 = ($('#container2').length) ? $('#container2') : $('<DIV class="pvtTableContainer" id="container2"></DIV>');
    $('.pvtCols').children().not('.pvtTableContainer').appendTo($div2);
    $div2.appendTo('.pvtCols');

    $div3 = ($('#container3').length) ? $('#container3') : $('<DIV class="pvtTableContainer" id="container3"></DIV>');
    $div3.appendTo('.pvtRendererArea');
    $('.pvtTable').appendTo('#container3');

});

var arreglarValores0 = function () {
    $('.pvtVal[data-value="0"]').html('0.00');
    $('.pvtTotal[data-value="0"]').html('0.00');
    $('.pvtTotalLabel').html('Totales');
};

