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
    $scope.valorAbsoluto = function(inde, k) {
        if (!angular.isUndefined(inde[k])) {           
            inde[k].acumular = false;
            if (inde[k].real != null && inde[k].real != '') {  
                if (inde[k].planificado == null || inde[k].planificado == '') {
                    inde[k].resultado = null;
                } else if (inde[k].planificado == 0 ){                    
                    inde[k].resultado = inde[k].real;                    
                } else{
                    var numerador = inde[k].real;
                    if(inde.es_formula){
                        numerador = (inde[k].real / inde[k].real_denominador) * 100;
                        inde[k].formula = numerador;
                    }
                    inde[k].resultado = (numerador / inde[k].planificado) * 100;  
                    inde[k].simbolo = '%';   
                }
                
            } else {
                inde[k].resultado = -1;
            }
            if (isNaN(inde[k].resultado))
                inde[k].resultado = -1;
            var color = color = inde[k].resultado > 100 ? "#0a3b0a" : "white";
            if (inde[k].planificado == null || inde[k].planificado == '') {                
                color = "gainsboro";
            } else {                
                angular.forEach(inde.alertas, function (v1, k1) {
                    if (inde[k].resultado >= v1.limite_inferior && inde[k].resultado <= v1.limite_superior ) {
                        color = v1.color.codigo;
                    }
                });
            }
            inde[k].color = color;                                
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
    $scope.acumularAbsoluto = function(inde) {        
        if(inde.acumular){
            var real = 0; var planificado = 0; var resultado = 0; var real_denominador = 0;
            angular.forEach($scope.meses, function (k, v) {
                if (!angular.isUndefined(inde[k])) { 

                    inde[k].realT = inde[k].real;
                    inde[k].planificadoT = inde[k].planificado;
                    inde[k].resultadoT = inde[k].resultado; 

                    real        += parseFloat(inde[k].real);
                    planificado += parseFloat(inde[k].planificado);
                    resultado += parseFloat(inde[k].resultado);
                    
                    inde[k].real = real;
                    inde[k].planificado = planificado;
                    inde[k].resultado = resultado; 

                    if(inde.es_formula){
                        inde[k].real_denominadorT = inde[k].real_denominador;
                        real_denominador += parseFloat(inde[k].real_denominador);
                        inde[k].real_denominador = real_denominador;
                    }
                }
            });
        }else{
            angular.forEach($scope.meses, function (k, v) {
                if (!angular.isUndefined(inde[k])) {

                    inde[k].real = inde[k].realT;
                    inde[k].planificado = inde[k].planificadoT;
                    inde[k].resultado = inde[k].resultadoT; 

                    if (inde.es_formula) {
                        inde[k].real_denominador = inde[k].real_denominadorT;
                    }

                    $scope.valorAbsoluto(inde, k);  
                }
            });
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
            if(angular.isUndefined(ind[v])){
                ind[v] = {};
                ind[v].real = '';
                ind[v].planificado = '';

                if (ind.es_formula) {
                    ind[v].real_denominador = '';
                }
            }

            if (ind.es_formula) {
                estatus = (ind[v].planificado == 0) ? ((ind[v].real / ind[v].real_denominador) * 100) : ((ind[v].real / ind[v].real_denominador) * 100) / ind[v].planificado * 100;
            }else{            
                estatus = (ind[v].planificado == 0) ? ind[v].real :  ind[v].real / ind[v].planificado * 100;
            }
            
            real.push(ind[v].real);
            planificado.push(ind[v].planificado);
            etiquetas.push(v);
            datos.push(estatus);
            meta.push(ind.meta);                        
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
                        if (angular.isUndefined(ind[v])) {
                            ind[v] = {};
                            ind[v].real = '';
                            ind[v].planificado = '';

                            if (ind.es_formula) {
                                ind[v].real_denominador = '';
                            }
                        }
                            
                        if (($scope.verR || $scope.verP) && !$scope.verRes) {
                            if (ind.es_formula) {
                                if ($scope.verR){
                                    estatus = ((ind[v].real / ind[v].real_denominador) * 100);
                                }
                                else{
                                    estatus = ind[v].planificado;
                                }
                            } else {
                                estatus = $scope.verR ? ind[v].real : ind[v].planificado;
                            }
                        }else{
                            if (ind.es_formula) {
                                estatus = (ind[v].planificado == 0) ? ((ind[v].real / ind[v].real_denominador) * 100) : ((ind[v].real / ind[v].real_denominador) * 100) / ind[v].planificado * 100;
                            } else{
                                estatus = (ind[v].planificado == 0) ? ind[v].real : ind[v].real / ind[v].planificado * 100;
                            }                            
                        }
                        v = (k + 1) + " - " + v;
                        if ($scope.incluir[ind.id])
                            datos.push({'indicador': ind.nombre, 'mes': v, 'anio': dato.anio, 'resultado': estatus});                            
                        
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
