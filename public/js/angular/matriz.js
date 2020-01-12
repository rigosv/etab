/**
 * @ngdoc object
 * @name Matriz.MatrizCtrl
 * @description
 * Controlador general que maneja la matriz de seguimiento, planificacion, real y reporte
 */

App.controller('MatrizCtrl', function($scope, $http, $localStorage, $window, $filter, Crud) {
    $scope.matriz = null;
    $scope.today = function() {
        $scope.anio = new Date();
    };
    $scope.today();

    $scope.clear = function() {
        $scope.anio = null;
    };

    $scope.open = function($event) {
        $scope.status.opened = true;
    };

    $scope.setDate = function(year, month, day) {
        $scope.anio = new Date(year, month, day);
    };

    $scope.dateOptions = {
        formatYear: 'yyyy',
        startingDay: 1,
        minMode: 'year',
        multidate: true
    };
    $scope.selectedDates = [];
    $scope.removeFromSelected = function (dt) {
        $scope.selectedDates.splice(dt, 1);
    }

    $scope.verP = false;
    $scope.verR = false;
    $scope.verV = false;
    $scope.verRes = true;

    $scope.cambiarVer = function(tipo){        
        $scope[tipo] = !$scope[tipo];
    }

    $scope.formats = ['yyyy'];
    $scope.format = $scope.formats[0];

    $scope.status = {
        opened: false
    };

    $scope.tamanoHeight = $window.innerHeight / 1.5;
    $scope.$watch(function() {
        return window.innerHeight;
    }, function(value) {
        $scope.tamanoHeight = value / 1.5;
    });

    $scope.selectedDates = [];

    $scope.agregarAnios = function(){        
        var anio = $filter('date')(value, "yyyy");        
        $scope.cargarCatalogo($scope.ruta + "?anio=" + anio + "&matrix=" + $scope.matriz, $scope.dato.matriz);
    }

    $scope.dato = {};
    $scope.intento = 0;
    $scope.noPlaneacion = false;
    $scope.meses = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];

    /**
     * @ngdoc method
     * @name Matriz.MatrizCtrl#cargarCatalogo
     * @methodOf Matriz.MatrizCtrl
     *
     * @description
     * funcion que carga datos de una URL de la API y la almacena en un modelo angular especifico
     * @param {url} url URL en la api para la peticion
     * @param {modelo} modelo modelo donde se carga el resultado
     */
    $scope.cargarCatalogo = function(url, modelo) {
        $scope.cargando = true;
        $scope.dato.matriz = [];        
        var anio = $filter('date')($scope.anio, "yyyy");
        if ($scope.matriz && anio != '')
        {
            Crud.lista(url, function(data) {

                if (data.status == 200) {
                    $scope.intento = 0;
                    $scope.dato.matriz = data.data;
                    var cambiar_mes = false;
                    
                    
                    angular.forEach($scope.dato.matriz, function (v1, k1) {
                        if (v1.indicadores_etab.length > 0) {
                            angular.forEach(v1.indicadores_etab, function (v2, k2) {
                                if (!angular.isUndefined(v2['january']) || !angular.isUndefined(v2['june']) || !angular.isUndefined(v2['december']))
                                    cambiar_mes = true;
                            })
                            
                        }
                        if (v1.indicadores_relacion.length > 0 && !cambiar_mes) {
                            angular.forEach(v1.indicadores_relacion, function (v2, k2) {
                                if (!angular.isUndefined(v2['january']) || !angular.isUndefined(v2['june']) || !angular.isUndefined(v2['december']))
                                    cambiar_mes = true;
                            })                        
                        }
                    });
                    
                    
                    if (cambiar_mes){
                        $scope.meses = [
                        "january",
                        "february",
                        "march",
                        "april",
                        "may",
                        "june",
                        "july",
                        "august",
                        "september",
                        "october",
                        "november",
                        "december"
                        ];
                    } 
                    var existe = false;
                    angular.forEach($scope.selectedDates, function(valor){
                        if(anio == valor.anio)
                            existe = true;
                    });
                    if(!existe)
                        $scope.selectedDates.push({ "anio": anio, "matriz": $scope.dato.matriz});
                    
                    $scope.noPlaneacion = false;
                    $scope.imprimir_mensaje(data.mensaje, 'success');
                } else {
                    $scope.noPlaneacion = true;
                    $scope.imprimir_mensaje(data.mensaje, 'danger');
                }
                $scope.cargando = false;
            }, function(e) {
                if ($scope.intento < 1) {
                    $scope.cargarCatalogo(url, modelo);
                    $scope.intento++;
                } else {
                    $scope.cargando = false;
                }
            });
        }
    };

    /**
     * @ngdoc method
     * @name Matriz.MatrizCtrl#cargarSelect
     * @methodOf Matriz.MatrizCtrl
     *
     * @description
     * funcion que carga datos de una URL de la API y la almacena en un modelo angular especifico
     * @param {url} url URL en la api para la peticion
     * @param {modelo} modelo modelo donde se carga el resultado
     * @param {callback} callback funcion a ejecutar after event
     */
    $scope.cargarSelect = function(url, modelo, callback) {
        $scope.cargando = true;
        Crud.lista(url, function(data) {

            if (data.status == 200) {
                $scope.intento1 = 0;
                angular.forEach(data.data, function(value, key) {
                    modelo.push(value);
                });
                if (url.search('matriz/matriz') > -1){
                    $scope.matriz = data.data[0].id;
                    var anio = $filter('date')($scope.anio, "yyyy");
                    $scope.cargarCatalogo($scope.ruta + "?anio=" + anio + "&matrix=" + $scope.matriz, $scope.dato.matriz);
                }
            } 
            $scope.cargando = false;
        }, function(e) {
            setTimeout(function() {
                if ($scope.intento1 < 1) {
                    $scope.cargarSelect(url, modelo, callback);
                    $scope.intento1++;
                }
                else $scope.cargando = false;
            }, 200);
        });
    };

    /**
     * @ngdoc method
     * @name Matriz.MatrizCtrl#crearPlaneacion
     * @methodOf Matriz.MatrizCtrl
     *
     * @description
     * funcion que crea el fromulario para configurar los datos de la planeacion
     * @param {url} url URL en la api para la peticion
     * @param {anio} anio anio a crear
     */
    $scope.crearPlaneacion = function(url, anio) {
        var anio = $filter('date')(anio, "yyyy");
        $scope.cargarCatalogo(url + "?anio=" + anio + "&matrix=" + $scope.matriz, $scope.dato.matriz);
    }

    /**
     * @ngdoc method
     * @name Matriz.MatrizCtrl#guardarPlaneacion
     * @methodOf Matriz.MatrizCtrl
     *
     * @description
     * funcion que guarda los datos de la planeacion configurada
     * @param {url} url URL en la api para la peticion
     * @param {anio} anio anio a cargar la información
     */
    $scope.guardarPlaneacion = function(url, anio) {
        var datos = $scope.dato;
        var anio = $filter('date')(anio, "yyyy");
        datos.anio = anio;
        datos.matrix = $scope.matriz;
        $scope.cargando = true;
        Crud.crear(url, $.param(datos), 'application/x-www-form-urlencoded;charset=utf-8;', function(data) {
            if (data.status == 200 || data.status == 201) {
                $scope.imprimir_mensaje(data.mensaje, 'success');
            } else {
                $scope.imprimir_mensaje(data.mensaje, 'warning');
            }
            $scope.cargando = false;
        }, function(e) {
            console.log(e);
            $scope.cargando = false;
        });
    }
    $scope.color = [];
    $scope.simbolo = [];
    $scope.statusx = [];
    $scope.acumular = [];
    $scope.incluir = [];
    $scope.formula = [];
    /**
     * @ngdoc method
     * @name Matriz.MatrizCtrl#valorAbsoluto
     * @methodOf Matriz.MatrizCtrl
     *
     * @description
     * funcion que obtiene el valor absoluto de la comparativa de lo real contra lo planeado
     * @param {inde} inde indicador 
     * @param {id} id del indicador
     * @param {k} k bandera para el mes
     */
    $scope.valorAbsoluto = function(inde, anio, id, k) {
        if (!angular.isUndefined(inde[k])) {
            if (angular.isUndefined($scope.statusx[anio])) {
                $scope.statusx[anio] = [];
                $scope.color[anio] = [];
                $scope.simbolo[anio] = [];
                $scope.acumular[anio] = false;
                $scope.formula[anio] = [];
            }

            if (angular.isUndefined($scope.statusx[anio][id])) {
                $scope.statusx[anio][id] = [];
                $scope.color[anio][id] = [];
                $scope.simbolo[anio][id] = [];
                $scope.acumular[anio][id] = false;
                $scope.formula[anio][id] = [];
            }
            if (angular.isUndefined($scope.statusx[anio][id][k])) {
                $scope.statusx[anio][id][k] = '';
                $scope.color[anio][id][k] = "white";
                $scope.simbolo[anio][id][k] = '';
                inde[k].simbolo = '';
                $scope.formula[anio][id][k] = 0;
            }
            if (inde[k].real != null && inde[k].real != '') {  
                if (inde[k].planificado == null || inde[k].planificado == '') {
                    $scope.statusx[anio][id][k] = null;
                } else if (inde[k].planificado == 0 ){                    
                    $scope.statusx[anio][id][k] = inde[k].real;                    
                } else{
                    var numerador = inde[k].real;
                    if(inde.es_formula){
                        numerador = (inde[k].real / inde[k].real_denominador) * 100;
                        $scope.formula[anio][id][k] = numerador;
                    }
                    $scope.statusx[anio][id][k] = (numerador / inde[k].planificado) * 100;  
                    $scope.simbolo[anio][id][k] = '%';   
                    inde[k].simbolo = '%';
                }
                
            } else {
                $scope.statusx[anio][id][k] = -1;
            }
            if (isNaN($scope.statusx[anio][id][k]))
                $scope.statusx[anio][id][k] = -1;
            var color = color = $scope.statusx[anio][id][k] > 100 ? "#0a3b0a" : "white";
            if (inde[k].planificado == null || inde[k].planificado == '') {                
                color = "gainsboro";
            } else {                
                angular.forEach(inde.alertas, function (v1, k1) {
                    if ($scope.statusx[anio][id][k] >= v1.limite_inferior && $scope.statusx[anio][id][k] <= v1.limite_superior ) {
                    color = v1.color.codigo;
                    }
                });
            }
            $scope.color[anio][id][k] = color;                                
        }        
    };
    $scope.temporal = [];
    var temporal = [];
    var temporalK = [];
    var temporald = [];

    /**
     * @ngdoc method
     * @name Matriz.MatrizCtrl#acumularAbsoluto
     * @methodOf Matriz.MatrizCtrl
     *
     * @description
     * funcion que acumula los valores absolutos
     * @param {ind} ind indicador
     * @param {index} identificador de la posicion 
     * @param {id} id bandera para la posicion
     */
    $scope.acumularAbsoluto = function(anio, ind, index, id) {
        var indTemp = {};
        indTemp.fuente = ind.fuente;
        indTemp.id = ind.id;
        indTemp.meta = ind.meta;
        indTemp.nombre = ind.nombre
        var meses = [];
        angular.forEach($scope.meses, function(v, k) {
            if (angular.isUndefined(meses[k]))
                meses[k] = [];
            if (!angular.isUndefined(ind[v])) {
                meses[k][v] = ind[v];
            }                        
        });
        indTemp.meses = meses;
        if (angular.isUndefined(temporal[anio])){
            temporal[anio] = [];
            temporalK[anio] = [];
            temporald[anio] = [];
        }
        
        if ($scope.acumular[anio][index + id + ind.id]) {
            var acumulado = 0;
            var acumuladod = 0;
            temporal[anio][index + id + ind.id] = ind;
            temporalK[anio][index + id + ind.id] = [];
            temporald[anio][index + id + ind.id] = [];
            
            angular.forEach(indTemp.meses, function(m, c) {
                var k = $scope.meses[c];
                var v = m[k];                
                if (!angular.isUndefined(v)) {
                    if (isNaN(v.real) || v.real == null || v.real == '' ) {
                        v.real = 0;
                    }
                    if (!angular.isUndefined(v.real_denominador)){
                        if (isNaN(v.real_denominador) || v.real_denominador == null || v.real_denominador == '') {
                            v.real_denominador = 0;
                        }
                    }
                    if (v.planificado == null || v.planificado == '') {
                        v.planificado = 0;
                    }
                    
                    temporalK[anio][index + id + ind.id][k] = v.real;
                    acumulado = acumulado + (v.real * 1);
                    if(v.real != null){
                        v.real = acumulado;
                        ind[k].real = acumulado;
                    }
                    if (!angular.isUndefined(v.real_denominador)) {
                        temporald[anio][index + id + ind.id][k] = v.real_denominador;
                        acumuladod = acumuladod + (v.real_denominador * 1);
                        if (v.real_denominador != null) {
                            v.real_denominador = acumuladod;
                            ind[k].real_denominador = acumuladod;
                        }
                    }
                                 
                    $scope.valorAbsoluto(ind, anio, (c + id + ind.id), k);
                }
            });
        } else {
            var indid = ind.id;
            ind = [];
            
            for (k in temporalK[anio][index + id + indid]) {  
                var x = $scope.meses.indexOf(k);                
                temporal[anio][index + id + indid][k].real = temporalK[anio][index + id + indid][k];                
                
                if (!angular.isUndefined(temporal[anio][index + id + indid][k].real_denominador)) {
                    temporal[anio][index + id + indid][k].real_denominador = temporald[anio][index + id + indid][k];
                }
                $scope.valorAbsoluto(temporal[anio][index + id + indid], anio, (x + id + temporal[anio][index + id + indid].id), k);  
            };
            ind = temporal[anio][index + id + indid];
        }
    }

    /**
     * @ngdoc method
     * @name Matriz.MatrizCtrl#imprimir_mensaje
     * @methodOf Matriz.MatrizCtrl
     *
     * @description
     * funcion que mustra los mensajes de las respuestas a la api
     * @param {mensaje} mensaje contiene el texto a mostrar
     * @param {tipo} tipo pinta el  color del mensaje
     * @param {id} id selecciona en que elemento se imprimira
     */
    $scope.imprimir_mensaje = function(mensaje, tipo, id) {
        id = angular.isUndefined(id) ? "#feedback_bar" : "#result_factura_test" + ", #" + id;

        $(id).html('<div class="alert alert-' + tipo + ' alert-dismissable" >' +
            '<i class="fa fa-' + tipo + '"></i> ' +
            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>' +
            '<b>Alert! </b>' + mensaje +
            '</div>');
        setTimeout(function() {
            $(id).html('');
        }, 6000);
    }


    var configPlotly = {
        displayModeBar: true,
        displaylogo: false,
        modeBarButtonsToRemove: ['select2d', 'lasso2d', 'resetScale2d', 'toggleSpikelines', 'hoverClosestCartesian', 'hoverCompareCartesian' ],
        responsive: true,
        toImageButtonOptions: {
            format: 'png', // one of png, svg, jpeg, webp
            filename: 'grafico',
            height: 500,
            width: 700,
            scale: 1, // Multiply title/legend/axis/canvas sizes by this factor
        }
    };

    var layoutPlotly = {
        legend: {
            orientation: 'h',
        }
    }

    /**
     * @ngdoc method
     * @name Matriz.MatrizCtrl#valorAbsoluto
     * @methodOf Matriz.MatrizCtrl
     *
     * @description
     * Graficar los datos del indicador
     * @param {ind} ind indicador
     *
     */
    $scope.mostrarexportarpivot = false;
    $scope.graficar = function(ind) {
        $("#tendencia").html('');
        let estatus = null;
        let datos = [];
        let real = [];
        let planificado = [];
        let meta = [];
        let etiquetas = [];
        let dataAlerta = [];
        let data = [];
        $scope.mostrarexportarpivot = false;
        angular.forEach($scope.meses, function(v, k) {
            estatus = null;
            if(!angular.isUndefined(ind[v])){
                if (ind[v].real != null && ind[v].planificado != null && ind[v].real != '' && ind[v].planificado != '') {
                    estatus = (ind[v].planificado == 0) ? ind[v].real :  ind[v].real / ind[v].planificado * 100;
                    
                    real.push(ind[v].real);
                    planificado.push(ind[v].planificado);
                    etiquetas.push(v);
                    datos.push(estatus);
                    meta.push(ind.meta);
                }
            }
        });               

        data = [{
                x: etiquetas,
                y: meta,
                type: 'scatter',
                name: jQuery('#metaEtq').val() + ': ' + meta[0]
            },
            {
                x: etiquetas,
                y: datos,
                type: 'scatter',
                name: jQuery('#resultadoEtq').val(),
                line: {
                    color: 'black',
                    width: 2
                }
            }];

        if ( $scope.verP ){
            data.push ({
                x: etiquetas,
                y: planificado,
                type: 'scatter',
                name: jQuery('#planeacionEtq').val()
            });
        }

        if ( $scope.verR ){
            data.push ({
                x: etiquetas,
                y: real,
                type: 'scatter',
                name: jQuery('#realEtq').val()
            });
        }

        if ($scope.verRes) {
            data.push({
                x: etiquetas,
                y: datos,
                type: 'scatter',
                name: jQuery('#realEtq').val()
            });
        }

        angular.forEach(ind.alertas, function(v, k) {
            dataAlerta.push({'limite_sup': v.limite_superior, 'color': v.color.codigo, 'limite_inf': v.limite_inferior });
        });

        angular.forEach(dataAlerta, function(v, k) {
            
            let dataAlertaGrp = [];
            angular.forEach(etiquetas, function(vv, kk) {
                dataAlertaGrp.push(v.limite_sup);
            });
            data.push ({
                x: etiquetas,
                y: dataAlertaGrp,
                type: 'scatter',
                name: v.limite_inf + ' -- ' + v.limite_sup,
                line: {
                    color: v.color,
                    dash: 'dot',
                    width: 3
                }
            });
        });

        Plotly.newPlot('tendencia', data, {}, configPlotly);

        jQuery('#tituloGrafico').html(ind.nombre);
        jQuery('#grafico').modal('show');

    };

    $scope.analisisGeneral = function() {
        $scope.mostrarexportarpivot = true;
        $("#tendencia").html('');
        let datos = [];
        angular.forEach($scope.selectedDates, function (dato, k1) {
            angular.forEach(dato.matriz, function(item, k) {
                angular.forEach(item.indicadores_relacion, function(ind, kk) {
                    angular.forEach($scope.meses, function(v, k) {
                        estatus = null;
                        if (angular.isUndefined($scope.incluir[ind.id])) {
                            $scope.incluir[ind.id] = false;
                        }
                        if (!angular.isUndefined(ind[v])) {
                            if ( ind[v] != undefined && ind[v].real != null && ind[v].planificado != null && ind[v].real != '' && ind[v].planificado != '') {
                                if (($scope.verR || $scope.verP) && !$scope.verRes) {
                                    estatus = $scope.verR ? ind[v].real : ind[v].planificado;
                                }else{
                                    estatus = (ind[v].planificado == 0) ? ind[v].real :  ind[v].real / ind[v].planificado * 100;
                                }
                                v = (k + 1) + " - " + v;
                                if ($scope.incluir[ind.id])
                                    datos.push({'indicador': ind.nombre, 'mes': v, 'anio': dato.anio, 'resultado': estatus});
                            }
                        }
                    });
                });
            });
        });

        var renderers = $.extend($.pivotUtilities.renderers, $.pivotUtilities.subtotal_renderers,
            $.pivotUtilities.plotly_renderers);


        $("#tendencia").pivotUI(datos, {
            renderers: renderers,
            aggregatorName: 'Average',
            vals : ['resultado'],
            rows: ['indicador'],
            cols: ['mes', 'anio'],
            sorters: null,
            rendererOptions: {
                plotlyConfig : configPlotly,
                plotly: layoutPlotly
            }
        }, true);

        jQuery('#tituloGrafico').html('');
        jQuery('#grafico').modal('show');
    }

    $scope.excelPivot = function (titulo) {
        let colspan = $(".pvtTable").find("tr:first th").length;
        let excelData =
            "<table><tr><th colspan='" +
            colspan +
            "'><h1>" +
            titulo +
            " <h1></th></tr></table>";

        excelData += $(".pvtRendererArea").html();
        let blob = new Blob([excelData], {
            type: "text/comma-separated-values;charset=utf-8"
        });
        saveAs(blob, titulo + ".xls");
    };        
})
