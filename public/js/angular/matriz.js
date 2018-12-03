App.controller('MatrizCtrl', function($scope, $http, $localStorage, $window, $filter, Crud) {
    $scope.matriz = 1;
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
        minMode: 'year'
    };

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

    $scope.$watch(function() {
        return $scope.anio;
    }, function(value) {
        var anio = $filter('date')(value, "yyyy");
        $scope.cargarCatalogo($scope.ruta + "?anio=" + anio + "&matrix=" + $scope.matriz, $scope.dato.matriz);
        $scope.intento = 0;
    });
    $scope.dato = {};
    $scope.intento = 0;
    $scope.noPlaneacion = false;
    $scope.meses = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];
    $scope.cargarCatalogo = function(url, modelo) {
        $scope.cargando = true;
        $scope.dato.matriz = [];
        $scope.statusx = [];
        Crud.lista(url, function(data) {

            if (data.status == 200) {
                $scope.intento = 0;
                $scope.dato.matriz = data.data;
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
    };
    $scope.cargarSelect = function(url, modelo, callback) {
        $scope.cargando = true;
        Crud.lista(url, function(data) {

            if (data.status == 200) {
                $scope.intento1 = 0;
                angular.forEach(data.data, function(value, key) {
                    modelo.push(value);
                });
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
    $scope.crearPlaneacion = function(url, anio) {
        var anio = $filter('date')(anio, "yyyy");
        $scope.cargarCatalogo(url + "?anio=" + anio + "&matrix=" + $scope.matriz, $scope.dato.matriz);
    }

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
    $scope.statusx = [];
    $scope.acumular = [];
    $scope.valorAbsoluto = function(inde, id, k) {
        if (!angular.isUndefined(inde[k])) {
            if (angular.isUndefined($scope.statusx[id])) {
                $scope.statusx[id] = [];
                $scope.acumular[id] = false;
            }
            if (angular.isUndefined($scope.statusx[id][k])) {
                $scope.statusx[id][k] = '';
            }
            if (inde[k].real != null && inde[k].planificado != null && inde[k].real != '' && inde[k].planificado != '') {
                $scope.statusx[id][k] = inde[k].real / inde[k].planificado * 100;
            } else {
                $scope.statusx[id][k] = -1;
            }
            if (isNaN($scope.statusx[id][k]))
                $scope.statusx[id][k] = -1;

        }
    }
    $scope.temporal = [];
    var temporal = [];
    var temporalK = [];
    $scope.acumularAbsoluto = function(ind) {
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
        if ($scope.acumular[ind.id]) {
            var acumulado = 0;
            temporal[ind.id] = ind;
            temporalK[ind.id] = [];
            angular.forEach(indTemp.meses, function(m, c) {
                var k = $scope.meses[c];
                var v = m[k];
                if (!angular.isUndefined(v)) {
                    if (!isNaN(v.real) && v != null && v != '' && v.planificado != null && v.planificado != '') {
                        temporalK[ind.id][k] = v.real;
                        acumulado = acumulado + (v.real * 1);
                        if(v.real != null){
                            v.real = acumulado;
                            ind[k].real = acumulado;
                        }
                    }
                    $scope.valorAbsoluto(ind, ind.id, k);
                }
            });
        } else {
            var id = ind.id;
            ind = [];
            angular.forEach(temporal[id], function(v, k) {
                v.real = temporalK[id][k];
                $scope.valorAbsoluto(temporal[id], temporal[id].id, k);
            });
            ind = temporal[id];
        }
    }

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
})
