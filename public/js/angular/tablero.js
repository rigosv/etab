//controlador
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
    $scope.cargarCatalogo('../api/v1/tablero/clasificacionUso', $scope.clasificaciones_usos, 'cc_uso');
    $scope.cargarCatalogo("../api/v1/tablero/listaIndicadores?tipo=no_clasificados", $scope.inidcadores_no_clasificados, "cc_sin");
    $scope.cargarCatalogo("../api/v1/tablero/listaIndicadores?tipo=favoritos", $scope.inidcadores_favoritos, "cc_favprito");

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
    $scope.agregarIndicador = function (item) {
        if(item){
            if (angular.isUndefined($scope.tablero_indicador[item.id]))
                $scope.tablero_indicador[item.id] = 0;
            $scope.indicadores.push({ 
                cargando: true,
                breadcrumb: [],
                error: '',
                id: item.id,
                nombre: item.nombre,
                dimension: item.campos_indicador.split(",")
            });
            $scope.tablero_indicador[item.id]++; 
            var index = $scope.indicadores.length -  1;
            
            Crud.lista("../api/v1/tablero/datosIndicador/" + item.id + "/" + $scope.indicadores[index].dimension[0] + "?filtros=&ver_sql=false", function(data) {
                if (data.status == 200) {
                  angular.forEach(data.data, function(value, key) {
                    modelo.push(value);
                  });
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

    $scope.quitarIndicador = function (item, index) {
        $scope.tablero_indicador[item.id]--;
        $scope.indicadores.splice(index, 1);                
    }

    $scope.imprimir_mensaje = function (mensaje, tipo, id) {
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
