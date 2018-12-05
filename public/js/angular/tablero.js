/**
   * @ngdoc object
   * @name Tablero.TableroCtrl
   * @description
   * Controlador general que maneja el tablero
   */

App.controller("TableroCtrl", function (
    $scope,
    $http,
    $localStorage,
    $window,
    $filter,
    Crud
) {
    $scope.abrio_sala = false;
    $scope.abrio_indicador = false;

    $scope.intento1 = 0;
    $scope.intento2 = 0;

    $scope.today = function () {
        $scope.anio = new Date();
    };
    $scope.today();

    $scope.clear = function () {
        $scope.anio = null;
    };

    $scope.open = function ($event) {
        $scope.status.opened = true;
    };

    $scope.setDate = function (year, month, day) {
        $scope.anio = new Date(year, month, day);
    };

    $scope.dateOptions = {
        formatYear: "yyyy",
        startingDay: 1,
        minMode: "year"
    };

    $scope.formats = ["yyyy"];
    $scope.format = $scope.formats[0];

    $scope.status = {
        opened: false
    };

    $scope.tamanoHeight = $window.innerHeight / 1.5;
    $scope.$watch(
        function () {
            return window.innerHeight;
        },
        function (value) {
            $scope.tamanoHeight = value / 1.5;
        }
    );
    $scope.indicadores = [];
    $scope.clasificacion_uso = '';
    $scope.clasificacion_tecnica = '';

    $scope.clasificaciones_usos = [];
    $scope.clasificaciones_tecnicas = [];

    $scope.inidcadores_clasificados = [];
    $scope.inidcadores_no_clasificados = [];
    $scope.inidcadores_busqueda = [];
    $scope.inidcadores_favoritos = [];

    /**
       * @ngdoc method
       * @name Tablero.TableroCtrl#cargarCatalogo
       * @methodOf Tablero.TableroCtrl
       *
       * @description
       * funcion que cargra datos de una URL de la API y la almacena en un modelo angular especifico
       * @param {url} url URL en la api para la peticion
       * @param {modelo} modelo modelo donde se carga el resultado
       * @param {cargando} cargando bandera para mostrar la animacion cargando
       */
    $scope.cargarCatalogo = function (url, modelo, cargando) {
        modelo.length = 0;
        $scope[cargando] = true;
        Crud.lista(
            url,
            function (data) {
                if (data.status == 200) {
                    $scope.intento1 = 0;
                    angular.forEach(data.data, function (value, key) {
                        modelo.push(value);
                    });
                }
                $scope[cargando] = false;
            },
            function (e) {
                setTimeout(function () {
                    if ($scope.intento1 < 1) {
                        $scope.cargarCatalogo(url, modelo, cargando);
                        $scope.intento1++;
                    } else $scope[cargando] = false;
                }, 200);
            }
        );
    };
    // cargar los catalogos para el indicador
    $scope.cargarCatalogo('../api/v1/tablero/clasificacionUso', $scope.clasificaciones_usos, 'cc_uso');
    $scope.cargarCatalogo("../api/v1/tablero/listaIndicadores?tipo=no_clasificados", $scope.inidcadores_no_clasificados, "cc_sin");
    $scope.cargarCatalogo("../api/v1/tablero/listaIndicadores?tipo=favoritos", $scope.inidcadores_favoritos, "cc_favprito");

    /**
       * @ngdoc method
       * @name Tablero.TableroCtrl#comboDependiente
       * @methodOf Tablero.TableroCtrl
       *
       * @description
       * funcion que cargar datos de una URL de la API y la almacena en un modelo angular especifico, esta funcion solicita un parametro padre
       * @param {url} url URL en la api para la peticion
       * @param {modelo} modelo modelo donde se carga el resultado
       * @param {id} id identificador del elemento padre a buscar
       * @param {cargando} cargando bandera para mostrar la animacion cargando
       */
    $scope.comboDependiente = function (url, modelo, id, cargando) {
        modelo.length = 0;
        $scope[cargando] = true;
        Crud.lista(
            url + "?id=" + id,
            function (data) {
                if (data.status == 200) {
                    $scope.intento2 = 0;
                    if (data.data.length == 0) {
                        angular.forEach(modelo, function (value, key) {
                            delete modelo[key];
                        });
                        modelo.length = 0;
                    }
                    angular.forEach(data.data, function (value, key) {
                        modelo.push(value);
                    });
                }
                $scope[cargando] = false;
            },
            function (e) {
                $scope[cargando] = false;
                setTimeout(function () {
                    if ($scope.intento2 < 1) {
                        $scope.comboDependiente(url, modelo, cargando);
                        $scope.intento2++;
                    }
                }, 200);
            }
        );
    };

    /**
       * @ngdoc method
       * @name Tablero.TableroCtrl#cargarIndicadores
       * @methodOf Tablero.TableroCtrl
       *
       * @description
       * funcion que cargar la lista de inidcadores, segun el filtro correspodiente, construido desde la vista 
       * @param {url} url URL en la api para la peticion
       * @param {modelo} modelo modelo donde se carga el resultado
       * @param {cargando} cargando bandera para mostrar la animacion cargando
       */
    $scope.cargarIndicadores = function (url, modelo, cargando) {
        modelo.length = 0;
        $scope[cargando] = true;
        Crud.lista(url, function (data) {
            if (data.status == 200) {
                $scope.intento1 = 0;
                angular.forEach(data.data, function (value, key) {
                    modelo.push(value);
                });
            }
            $scope[cargando] = false;
        }, function (e) {
            setTimeout(function () {
                if ($scope.intento1 < 1) {
                    $scope.cargarIndicadores(url, modelo, cargando);
                    $scope.intento1++;
                } else $scope[cargando] = false;
            }, 200);
        });
    };
    $scope.titulo_sala = '';
    $scope.salas = [];
    $scope.salas_grupos = [];
    $scope.salas_propias = [];

    /**
       * @ngdoc method
       * @name Tablero.TableroCtrl#cargarSalas
       * @methodOf Tablero.TableroCtrl
       *
       * @description
       * funcion que cargar la lista de salas disponibles
       */
    $scope.cargarSalas = function () {

        $scope["cc_salas"] = true;
        Crud.lista("../api/v1/tablero/listaSalas", function (data) {
            if (data.status == 200) {
                $scope.intento1 = 0;
                $scope.salas = data.data;
                $scope.salas_grupos = data.salas_grupos;
                $scope.salas_propias = data.salas_propias;
            }
            $scope["cc_salas"] = false;
        }, function (e) {
            setTimeout(function () {
                if ($scope.intento1 < 1) {
                    $scope.cargarSalas();
                    $scope.intento1++;
                } else $scope["cc_salas"] = false;
            }, 200);
        });
    };
    $scope.cargarSalas();

    /**
       * @ngdoc method
       * @name Tablero.TableroCtrl#bsucarIndicador
       * @methodOf Tablero.TableroCtrl
       *
       * @description
       * funcion para la busqueda libre de indicadores
       */
    $scope.bsucarIndicador = function (keyEvent) {
        if (keyEvent.which === 13) {
            $scope.inidcadores_busqueda = [];
            $scope["cc_buscar"] = true;
            Crud.lista("../api/v1/tablero/listaIndicadores?tipo=busqueda&busqueda=" + $scope.buscar_busqueda, function (data) {
                if (data.status == 200) {
                    $scope.intento1 = 0;
                    $scope.inidcadores_busqueda = data.data;
                    $scope.buscar_busqueda = '';
                }
                $scope["cc_buscar"] = false;
            }, function (e) {
                setTimeout(function () {
                    if ($scope.intento1 < 1) {
                        $scope.bsucarIndicador();
                        $scope.intento1++;
                    } else $scope["cc_buscar"] = false;
                }, 200);
            });
        }
    };

    $scope.tablero_indicador = [];

    /**
       * @ngdoc method
       * @name Tablero.TableroCtrl#cargarIndicadores
       * @methodOf Tablero.TableroCtrl
       *
       * @description
       * funcion que cargar los datos del inidcador
       * @param {item} item inidcador seleccionado de la lista
       */
    $scope.agregarIndicador = function (item) {
        if(item){
            if (angular.isUndefined($scope.tablero_indicador[item.id]))
                $scope.tablero_indicador[item.id] = 0;
            $scope.indicadores.push({ 
                cargando: true,
                breadcrumb: [],
                filtros:[],
                error: '',
                id: item.id,
                nombre: item.nombre,
                dimensiones: item.campos_indicador.split(","),
                dimension: 0
            });
            $scope.tablero_indicador[item.id]++; 
            var index = $scope.indicadores.length -  1;
            $scope.opcionesGraficas(index, "discreteBarChart", $scope.indicadores[index].dimensiones[0], item.unidad_medida);
            
            var json = {
                filtros: '',
                ver_sql: false
            };
            Crud.crear("../api/v1/tablero/datosIndicador/" + item.id + "/" + $scope.indicadores[index].dimensiones[0] , json, function(data) {
                if (data.status == 200) {
                    $scope.indicadores[index].data = data.data;
                    $scope.indicadores[index].informacion = data.informacion;
                    $scope.indicadores[index].grafica = [];
                    var grafica = [];
                    angular.forEach(data.data, function (val, key) {
                        color = '';
                        angular.forEach(data.informacion.rangos, function (v1, k1) {
                            if (val.measure >= v1.limite_inf && val.measure <= v1.limite_sup){
                                color = v1.color;
                            }
                        });
                        if(angular.isUndefined(grafica[val.category])){
                            grafica[val.category] = {
                                key: val.category,
                                values: []
                            }
                        }
                        grafica[val.category].values.push(
                            {
                                color: color,
                                label: val.category,
                                value: parseFloat(val.measure),
                                index: index,
                                dimension: 0
                            }
                        );                                         
                    });
                    $scope.indicadores[index].grafica = grafica;
                } else {
                    $scope.indicadores[index].error = "Warning";
                }
                $scope.indicadores[index].cargando = false;
              }, function(e) {
                $scope.indicadores[index].error = "Error";
                $scope.indicadores[index].cargando = false;
              });
        }
    };

    /**
       * @ngdoc method
       * @name Tablero.TableroCtrl#cargarIndicadores
       * @methodOf Tablero.TableroCtrl
       *
       * @description
       * funcion que cargar el inidcador con la dimension y los filtros especificados
       * @param {filtro} filtro filtros del indicador 
       * @param {dimension} dimension 
       * @param {index} index identificador de la posicion del grafico
       */
    $scope.agregarIndicadorDimension = function (dimension, index) {
        if (dimension) {     
            $scope.indicadores[index].cargando = true;
            $scope.indicadores[index].grafica = [];                   
            $scope.opcionesGraficas(index, "discreteBarChart", $scope.indicadores[index].dimension[dimension], $scope.indicadores[index].informacion.unidad_medida);
            var json = {
                filtros: $scope.indicadores[index].filtros,
                ver_sql: false
            };
            Crud.crear("../api/v1/tablero/datosIndicador/" + $scope.indicadores[index].id + "/" 
            + $scope.indicadores[index].dimensiones[dimension].trim(), json, function(data) {
                if (data.status == 200) {
                  $scope.indicadores[index].data = data.data;

                  var grafica = [];
                  angular.forEach(data.data, function(val, key) {
                    color = "";
                    angular.forEach(
                      data.informacion.rangos,
                      function(v1, k1) {
                        if (
                          val.measure >= v1.limite_inf &&
                          val.measure <= v1.limite_sup
                        ) {
                          color = v1.color;
                        }
                      }
                    );
                    if (angular.isUndefined(grafica[val.category])) {
                      grafica[val.category] = { key: val.category, values: [] };
                    }
                    grafica[val.category].values.push({
                      color: color,
                      label: val.category,
                      value: parseFloat(val.measure),
                      index: index,
                      dimension: dimension
                    });
                  });
                  $scope.indicadores[index].grafica = grafica;
                } else {
                  $scope.indicadores[index].error = "Warning";
                }
                $scope.indicadores[index].cargando = false;
              }, function(e) {
                $scope.indicadores[index].error = "Error";
                $scope.indicadores[index].cargando = false;
              });
        }
    };

    /**
       * @ngdoc method
       * @name Tablero.TableroCtrl#quitarIndicador
       * @methodOf Tablero.TableroCtrl
       *
       * @description
       * funcion que elimina un area de grafico
       * @param {item} item item a eliminar
       * @param {index} index posicion en la lista de graficas
       */
    $scope.quitarIndicador = function (item, index) {
        $scope.tablero_indicador[item.id]--;
        $scope.indicadores.splice(index, 1);                
    }

    /**
       * @ngdoc method
       * @name Tablero.TableroCtrl#opcionesGraficas
       * @methodOf Tablero.TableroCtrl
       *
       * @description
       * funcion que configura las opciones del grafico
       * @param {index} index bandera de posicion 
       * @param {tipo} tipo nombre del grafico
       * @param {labelx} labelx etiqueta para el eje X
       * @param {labely} labely etiqueta para el eje Y
       */
    $scope.opcionesGraficas = function(index, tipo, labelx, labely){
        $scope.indicadores[index].options = {
            chart: {
                type: tipo,
                height: 280,
                margin: {
                    top: 20,
                    right: 20,
                    bottom: 50,
                    left: 55
                },
                x: function (d) { return d.label; },
                y: function (d) { return d.value + (1e-10); },
                showValues: true,
                valueFormat: function (d) {
                    return d3.format(',.4f')(d);
                },
                duration: 500,
                xAxis: {
                    axisLabel: labelx
                },
                yAxis: {
                    axisLabel: labely,
                    axisLabelDistance: -10
                },
                callback: function (chart) {
                    chart.discretebar.dispatch.on(
                      "elementClick",
                      function(e) {
                          $scope.indicadores[index].dimension++;
                          $scope.indicadores[index].filtros.push({
                              codigo: $scope.indicadores[index].dimensiones[$scope.indicadores[index].dimension],
                              etiqueta: $scope.indicadores[index].dimensiones[$scope.indicadores[index].dimension],
                              valor: e.data.value
                          });
                          $scope.agregarIndicadorDimension($scope.indicadores[index].dimension, e.data.index);
                        console.log(
                          "elementClick in callback",
                          e.data
                        );
                      }
                    );
                }
            }
        };
    }

    /**
       * @ngdoc method
       * @name Tablero.TableroCtrl#imprimirMensaje
       * @methodOf Tablero.TableroCtrl
       *
       * @description
       * funcion que muestra los mensajes en los divs de alerta
       * @param {mensaje} mensaje a mostra
       * @param {tipo} tipo tipo de mensaje INFO, SUCCESS, WARNING, ERROR, DANGER
       * @param {id} id elemento donde se mostrara el mensaje
       */
    $scope.imprimirMensaje = function (mensaje, tipo, id) {
        id = angular.isUndefined(id)
            ? "#feedback_bar"
            : "#result_factura_test" + ", #" + id;

        $(id).html(
            '<div class="alert alert-' +
            tipo +
            ' alert-dismissable" >' +
            '<i class="fa fa-' +
            tipo +
            '"></i> ' +
            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>' +
            "<b>Alert! </b>" +
            mensaje +
            "</div>"
        );
        setTimeout(function () {
            $(id).html("");
        }, 6000);
    };
});
