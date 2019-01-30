/**
 * @ngdoc object
 * @name Tablero.TableroCtrl
 * @description
 * Controlador general que maneja el tablero
 */

App.controller("TableroPublicoCtrl", function(
  $scope,
  $http,
  $localStorage,
  $window,
  $filter,
  Crud
) {
  $scope.sala = { id: "", nombre: "" };
  $scope.abrio_sala = false;
  $scope.abrio_indicador = false;

  $scope.intento1 = 0;
  $scope.intento2 = 0;

  $scope.tamanoHeight = $window.innerHeight / 1.5;
  $scope.$watch(
    function() {
      return window.innerHeight;
    },
    function(value) {
      $scope.tamanoHeight = value / 1.5;
    }
  );

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
  $scope.cargarCatalogo = function(url, modelo, cargando) {
    modelo.length = 0;
    $scope[cargando] = true;
    Crud.lista(
      url,
      function(data) {
        if (data.status == 200) {
          $scope.intento1 = 0;
          angular.forEach(data.data, function(value, key) {
            modelo.push(value);
          });
        }
        $scope[cargando] = false;
      },
      function(e) {
        setTimeout(function() {
          if ($scope.intento1 < 1) {
            $scope.cargarCatalogo(url, modelo, cargando);
            $scope.intento1++;
          } else $scope[cargando] = false;
        }, 200);
      }
    );
  };

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
  $scope.comboDependiente = function(url, modelo, id, cargando) {
    modelo.length = 0;
    $scope[cargando] = true;
    Crud.lista(
      url + "?id=" + id,
      function(data) {
        if (data.status == 200) {
          $scope.intento2 = 0;
          if (data.data.length == 0) {
            angular.forEach(modelo, function(value, key) {
              delete modelo[key];
            });
            modelo.length = 0;
          }
          angular.forEach(data.data, function(value, key) {
            modelo.push(value);
          });
        }
        $scope[cargando] = false;
      },
      function(e) {
        $scope[cargando] = false;
        setTimeout(function() {
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
   * @name Tablero.TableroCtrl#agregarSala
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que cargar los indicadores de la sala
   * @param {item} item sala seleccionada de la lista
   */
  $scope.agregarSala = function(item) {   
    if (item) {
      $scope.sala = item;

      $("#modalSalas").modal("hide");
      $scope.abrio_sala = true;
      $scope.indicadores = [];
      angular.forEach(item.indicadores, function(element, key) {
        $scope.indicadores.push({
          cargando: true,
          filtros: [],
          error: "",
          informacion: {},
          data: [],
          id: element.indicador_id,
          nombre: "",
          es_favorito: false,
          dimensiones: [],
          dimension: 0,
          radial: false,
          termometro: false,
          mapa: false,
          posicion: element.posicion,
          sql: "",
          ficha: "",
          full_screen: false,
          configuracion: {
            width: "col-sm-4",
            height: "280",
            orden_x: "",
            orden_y: "",
            tipo_grafico: element.tipo_grafico.codigo,
            maximo: "",
            maximo_manual: ""
          },
          otros_filtros: {
            desde: "",
            hasta: "",
            elementos: []
          }
        });
      });
      
      angular.forEach(item.indicadores, function(element, index) {
        $scope.indicadores[index].cargando = true;
        if (element.orden != "" && element.orden != null) {
          $scope.indicadores[index].configuracion = JSON.parse(element.orden);
        }
        $scope.indicadores[index].index = index;
        $scope.indicadores[index].filtros =
          element.filtro != "" ? JSON.parse(element.filtro) : "";
        $scope.indicadores[index].otros_filtros = {
          desde: element.filtro_posicion_desde,
          hasta: element.filtro_posicion_hasta,
          elementos:
            element.filtro_elementos != "" && element.filtro_elementos != null
              ? element.filtro_elementos.split(",")
              : []
        };
        
        $scope.opcionesGraficas(index, $scope.indicadores[index].configuracion.tipo_grafico, element.dimension, "#", $scope.indicadores[index].configuracion.height);

        var json = {
          filtros: $scope.indicadores[index].filtros,
          ver_sql: false,
          tendencia: $scope.indicadores[index].tendencia
        };
        if (
          element.filtro_posicion_desde != "" ||
          element.filtro_posicion_hasta != "" ||
          (element.filtro_elementos != "" && element.filtro_elementos)
        ) {
          json.otros_filtros = $scope.indicadores[index].otros_filtros;
        }

        Crud.crear(
          "../../../api/v1/tablero/datosIndicador/" +
            $scope.indicadores[index].id +
            "/" +
            element.dimension,
          json,
          "application/json",
          function(data) {
            var dimension = -1;
            var dimensiones = [];
            if (data.status == 200) {
              var pos = 0;
              angular.forEach(data.informacion.dimensiones, function(v1, k1) {
                dimensiones.push(k1);
                if (k1 == element.dimension) dimension = pos;
                pos++;
              });

              $scope.indicadores[index].nombre =
                data.informacion.nombre_indicador;
              $scope.indicadores[index].es_favorito =
                data.informacion.es_favorito;
              $scope.indicadores[index].dimensiones = dimensiones;
              $scope.indicadores[index].dimension = dimension;
              $scope.indicadores[index].sql = "";
              $scope.indicadores[index].ficha = data.ficha;
              $scope.indicadores[index].full_screen = false;

              $scope.indicadores[index].informacion = data.informacion;
              $scope.indicadores[index].informacion.nombre =
                data.informacion.nombre_indicador;
            }

            $scope.repuestaIndicador(data, dimension, index);
            $scope.indicadores[index].cargando = false;
            $scope.actualizarsGrafica(index, false);
          },
          function(e) {
            $scope.indicadores[index].error = "Error";
            $scope.indicadores[index].cargando = false;
            setTimeout(function() {
              $scope.indicadores[index].error = "";
            }, 3000);
          }
        );
      });
    }
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#cerraSala
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que cierra la sala abierta previamente
   */
  $scope.cerraSala = function() {
    
  };

  $scope.sala_cargando = false;

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
  $scope.agregarIndicadorDimension = function(dimension, index) {
    var posicion = index;   
    if (
      !angular.isUndefined($scope.indicadores[index].dimensiones[dimension])
    ) {
      $scope.indicadores[index].cargando = true;
      if ($scope.indicadores[index].tendencia)
        $scope.opcionesGraficasTendencias(index, $scope.indicadores[index].configuracion.tipo_grafico, $scope.indicadores[index].dimensiones[dimension], $scope.indicadores[index].informacion.unidad_medida, $scope.indicadores[index].configuracion.height);
      else
        $scope.opcionesGraficas(index, $scope.indicadores[index].configuracion.tipo_grafico, $scope.indicadores[index].dimensiones[dimension], $scope.indicadores[index].informacion.unidad_medida, $scope.indicadores[index].configuracion.height);
      var json = { filtros: $scope.indicadores[index].filtros, ver_sql: false, tendencia: $scope.indicadores[index].tendencia};
      Crud.crear(
        "../../../api/v1/tablero/datosIndicador/" +
          $scope.indicadores[index].id +
          "/" +
          $scope.indicadores[index].dimensiones[dimension].trim(),
        json,
        "application/json",
        function(data) {
          $scope.repuestaIndicador(data, dimension, index);
        },
        function(e) {
          $scope.indicadores[index].dimension--;
          $scope.indicadores[index].error = "Error";
          $scope.indicadores[index].cargando = false;
          setTimeout(function() {
            $scope.indicadores[index].error = "";
          }, 3000);
        }
      );
    } else {
      $scope.indicadores[index].dimension =
        $scope.indicadores[index].dimensiones.length - 1;
      $scope.indicadores[index].error = "Success";
      setTimeout(function() {
        $scope.indicadores[index].error = "";
      }, 3000);
    }
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#filtrarIndicador
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que cargar el inidcador con la dimension y los filtros especificados
   * @param {index} index identificador de la posicion del grafico
   */
  $scope.filtrarIndicador = function(index) {
    var dimension = $scope.indicadores[index].dimension;
    if (
      !angular.isUndefined($scope.indicadores[index].dimensiones[dimension])
    ) {
      $scope.indicadores[index].cargando = true;
      if ($scope.indicadores[index].tendencia)
        $scope.opcionesGraficasTendencias(index, $scope.indicadores[index].configuracion.tipo_grafico, $scope.indicadores[index].dimensiones[dimension], $scope.indicadores[index].informacion.unidad_medida, $scope.indicadores[index].configuracion.height);
      else
        $scope.opcionesGraficas(index, $scope.indicadores[index].configuracion.tipo_grafico, $scope.indicadores[index].dimensiones[dimension], $scope.indicadores[index].informacion.unidad_medida, $scope.indicadores[index].configuracion.height);

      var json = {
        filtros: $scope.indicadores[index].filtros,
        ver_sql: false,
        otros_filtros: $scope.indicadores[index].otros_filtros
      };
      Crud.crear(
        "../../../api/v1/tablero/datosIndicador/" +
          $scope.indicadores[index].id +
          "/" +
          $scope.indicadores[index].dimensiones[dimension].trim(),
        json,
        "application/json",
        function(data) {
          $scope.repuestaIndicador(data, dimension - 1, index);
        },
        function(e) {
          $scope.indicadores[index].dimension--;
          $scope.indicadores[index].error = "Error";
          $scope.indicadores[index].cargando = false;
          setTimeout(function() {
            $scope.indicadores[index].error = "";
          }, 3000);
        }
      );
    } else {
      $scope.indicadores[index].dimension =
        $scope.indicadores[index].dimensiones.length - 1;
      $scope.indicadores[index].error = "Success";
      setTimeout(function() {
        $scope.indicadores[index].error = "";
      }, 3000);
    }
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#repuestaIndicador
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion procesa la respuesta para contruir los datos para el gráfico
   * @param {data} data respuesta del servidor
   * @param {dimension} dimension dimension actual de la gráfica
   * @param {index} index bandera de posicion
   */
  $scope.repuestaIndicador = function(data, dimension, index) {
    if (data.status == 200) {
      $scope.indicadores[index].data = data.data;

      var grafica = [];
      var tipo = 'DISCRETEBARCHART';
      if ($scope.indicadores[index].configuracion.tipo_grafico)
        tipo = $scope.indicadores[index].configuracion.tipo_grafico.toUpperCase();
      if (!$scope.indicadores[index].tendencia){
        // validar el tipo de grafica para asociar el dato 
        $scope.indicadores[index].radial = false;
        $scope.indicadores[index].termometro = false;
        $scope.indicadores[index].mapa = false;
        if (tipo == 'LINECHART' || tipo == 'LINEA' || tipo == 'LINEAS'){
          $scope.indicadores[index].tendencia = true;
          $scope.agregarIndicadorDimension(dimension, index);
        }
        if (tipo == 'DISCRETEBARCHART' || tipo == 'BARRA' || tipo == 'BARRAS' || tipo == 'COLUMNAS' || tipo == 'COLUMNA' ) {
          grafica[0] = {
            key: $scope.indicadores[index].dimensiones[dimension],
            values: []
          };
          angular.forEach(data.data, function(val, key) {
            color = "";
            angular.forEach(data.informacion.rangos, function(v1, k1) {
              if (val.measure >= v1.limite_inf && val.measure <= v1.limite_sup) {
                color = v1.color;
              }
            });

            grafica[0].values.push({
              color: color,
              label: val.category,
              value: parseFloat(val.measure),
              index: index,
              dimension: dimension
            });
          });
        } else if (tipo == 'PIECHART' || tipo == 'PIE' || tipo == 'PASTEL' || tipo == 'TORTA') {
          angular.forEach(data.data, function (val, key) {
            color = "";
            angular.forEach(data.informacion.rangos, function (v1, k1) {
              if (val.measure >= v1.limite_inf && val.measure <= v1.limite_sup) {
                color = v1.color;
              }
            }); 
            grafica.push({
              color: color,
              key: val.category,
              y: parseFloat(val.measure),
              index: index,
              dimension: dimension
            });
          });                  
        } else if(tipo == 'GAUGE' || tipo == 'VELOCIMETRO' || tipo == 'ODOMETRO'){          
          $scope.indicadores[index].radial = true;
          
          angular.forEach(data.data, function (val, key) {
            color = "";
            var rangos = [];
            angular.forEach(data.informacion.rangos, function (v1, k1) {
              if (val.measure >= v1.limite_inf && val.measure <= v1.limite_sup) {
                color = v1.color;
              }
              rangos.push({ "min": v1.limite_inf, "max": v1.limite_sup, "color": color })
            });

            var upperLimit = 0;
            if (data.informacion.meta) {
              if (parseFloat(val.measure) > parseFloat(data.informacion.meta)) {
                rangos.push({ "min": parseFloat(data.informacion.meta), "max": parseFloat(val.measure), "color": "#2196f3" });
                upperLimit = parseFloat(val.measure);
              } else {
                upperLimit = parseFloat(data.informacion.meta);
              }
            } else {
              upperLimit = parseFloat(val.measure);
            }
            if (data.informacion.rangos.length <= 0) {
              rangos = [
                { "min": 0, "max": (upperLimit * 0.40), "color": "#C50200" },
                { "min": (upperLimit * 0.40) + .1, "max": (upperLimit * 0.60), "color": "#FF7700" },
                { "min": (upperLimit * 0.60) + .1, "max": (upperLimit * 0.80), "color": "#FDC702" },
                { "min": (upperLimit * 0.80) + .1, "max": upperLimit, "color": "#8BC556" }
              ];
            }
            grafica.push({
              index: index,
              dimension: dimension,
              upperLimit: upperLimit,
              lowerLimit: 0,
              unit: data.informacion.unidad_medida,
              precision: 0,
              label: val.category,
              ranges: rangos,
              value: parseFloat(val.measure)
            });
          });
        } else if (tipo == "LINEARGAUGE" || tipo == "TERMOMETRO") {
          $scope.indicadores[index].termometro = true;
          var meta = data.informacion.meta ? parseFloat(data.informacion.meta) : 100;
          angular.forEach(data.data, function (val, key) {
            color = "";
            var rangos = [];

            angular.forEach(data.informacion.rangos, function (v1, k1) {
              if (val.measure >= v1.limite_inf && val.measure <= v1.limite_sup) {
                color = v1.color;
              }
              rangos.push(v1.limite_sup);
            });

            if (data.informacion.rangos.length <= 0) {
              var upperLimit = parseFloat(val.measure);
              rangos = [(upperLimit * 0.40), (upperLimit * 0.60), (upperLimit * 0.80), (upperLimit * 0.80)];
            }

            grafica.push({
              index: index,
              dimension: dimension,
              title: val.category,
              label: val.category,
              subtitle: data.informacion.unidad_medida,
              ranges: rangos,
              measures: [parseFloat(val.measure)],
              markers: [meta],
              color: color
            });
          });
        } else if (tipo == 'MAPA' || tipo == 'GEOLOCATION') {
          $scope.indicadores[index].mapa = true;
        }        
        // fin asociacion
        $scope.indicadores[index].grafica = grafica;

      }else{        
        $scope.indicadores[index].grafica = data.data;
      }
    } else {
      $scope.indicadores[index].dimension--;
      $scope.indicadores[index].error = "Warning";
    }
    $scope.indicadores[index].cargando = false;
    setTimeout(function() {
      $scope.indicadores[index].error = "";
    }, 3000);
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#actualizarsGrafica
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que actualiza el grafico
   * @param {index} index bandera de posicion
   * @param {hacer} hacer bandera para detyerminar si es para fullscreen
   */
  $scope.actualizarsGrafica = function(index, hacer = true) {
    if (hacer)
      $scope.indicadores[index].full_screen = !$scope.indicadores[index].full_screen;
    var tipo = 'DISCRETEBARCHART';
    if ($scope.indicadores[index].configuracion.tipo_grafico)
      tipo = $scope.indicadores[index].configuracion.tipo_grafico.toUpperCase();
    var grafica = [];
    var dimension = $scope.indicadores[index].dimension;
    // validar el tipo de grafica para asociar el dato 
    $scope.indicadores[index].radial = false;
    $scope.indicadores[index].termometro = false;
    $scope.indicadores[index].mapa = false;
    if ($scope.indicadores[index].tendencia && (tipo != 'LINECHART' && tipo != 'LINEA' && tipo != 'LINEAS')){
      $scope.indicadores[index].tendencia = false;
      $scope.agregarIndicadorDimension(dimension, index);
    }
    if (tipo == 'LINECHART' || tipo == 'LINEA' || tipo == 'LINEAS') {
      $scope.indicadores[index].tendencia = true;
      $scope.agregarIndicadorDimension(dimension, index);
    }
    if (tipo == 'DISCRETEBARCHART' || tipo == 'BARRA' || tipo == 'BARRAS' || tipo == 'COLUMNAS' || tipo == 'COLUMNA') {      
      grafica[0] = {
        key: $scope.indicadores[index].dimensiones[dimension],
        values: []
      };
      angular.forEach($scope.indicadores[index].data, function(val, key) {
        color = "";
        angular.forEach($scope.indicadores[index].informacion.rangos, function(v1, k1) {
          if (val.measure >= v1.limite_inf && val.measure <= v1.limite_sup) {
            color = v1.color;
          }
        });
      
        grafica[0].values.push({
          color: color,
          label: val.category,
          value: parseFloat(val.measure),
          index: index,
          dimension: dimension
        });        
      });
    } else if (tipo == 'PIECHART' || tipo == 'PIE' || tipo == 'PASTEL' || tipo == 'TORTA') {      
      angular.forEach($scope.indicadores[index].data, function(val, key) {
        color = "";
        angular.forEach($scope.indicadores[index].informacion.rangos, function(v1, k1) {
          if (val.measure >= v1.limite_inf && val.measure <= v1.limite_sup) {
            color = v1.color;
          }
        });
        grafica.push({
          color: color,
          key: val.category,
          y: parseFloat(val.measure),
          index: index,
          dimension: dimension
        });        
      });
    } else if (tipo == 'GAUGE' || tipo == 'VELOCIMETRO' || tipo == 'ODOMETRO') {
      $scope.indicadores[index].radial = true;
      var data = $scope.indicadores[index];
      angular.forEach(data.data, function (val, key) {
        color = "";
        var rangos = [];
        angular.forEach(data.informacion.rangos, function (v1, k1) {
          if (val.measure >= v1.limite_inf && val.measure <= v1.limite_sup) {
            color = v1.color;
          }
          rangos.push({ "min": v1.limite_inf, "max": v1.limite_sup, "color": color })
        });
        
        var upperLimit = 0;
        if(data.informacion.meta){
          if (parseFloat(val.measure) > parseFloat(data.informacion.meta)) {
            rangos.push({ "min": parseFloat(data.informacion.meta), "max": parseFloat(val.measure), "color": "#2196f3" });
            upperLimit = parseFloat(val.measure);
          } else {
            upperLimit = parseFloat(data.informacion.meta);
          }
        }else{
          upperLimit = parseFloat(val.measure);
        }
        if (data.informacion.rangos.length <= 0) {
          rangos = [
            { "min": 0,                        "max": (upperLimit * 0.40), "color": "#C50200" }, 
            { "min": (upperLimit * 0.40) + .1, "max": (upperLimit * 0.60), "color": "#FF7700" }, 
            { "min": (upperLimit * 0.60) + .1, "max": (upperLimit * 0.80), "color": "#FDC702" }, 
            { "min": (upperLimit * 0.80) + .1, "max": upperLimit,          "color": "#8BC556" }
          ];
        }
        grafica.push({
          index: index,
          dimension: dimension,
          upperLimit: upperLimit,
          lowerLimit: 0,
          unit: data.informacion.unidad_medida,
          precision: 0,
          label: val.category,
          ranges: rangos,
          value: parseFloat(val.measure)
        });
      });
    } else if (tipo == "LINEARGAUGE" || tipo == "TERMOMETRO") {
      $scope.indicadores[index].termometro = true;      
      var data = $scope.indicadores[index];
      var meta = data.informacion.meta ? parseFloat(data.informacion.meta) : 100;
      angular.forEach(data.data, function (val, key) {
        color = "";
        var rangos = [];
        
        angular.forEach(data.informacion.rangos, function (v1, k1) {
          if (val.measure >= v1.limite_inf && val.measure <= v1.limite_sup) {
            color = v1.color;
          }
          rangos.push(v1.limite_sup);
        });

        if (data.informacion.rangos.length <= 0) {
          var upperLimit = parseFloat(val.measure);
          rangos = [(upperLimit * 0.40),(upperLimit * 0.60), (upperLimit * 0.80), (upperLimit * 0.80)];
        }

        grafica.push({
          index: index,
          dimension: dimension,
          title: val.category,
          label: val.category,
          subtitle: data.informacion.unidad_medida,
          ranges: rangos,
          measures: [parseFloat(val.measure)],
          markers: [meta],
          color: color
        });
      });
    } else if (tipo == 'MAPA' || tipo == 'GEOLOCATION') {
      $scope.indicadores[index].mapa = true;
    }
    // fin asociacion    
    var tamano = $scope.indicadores[index].configuracion.height;
    $scope.indicadores[index].grafica = [];
    
    setTimeout(() => {
      $scope.indicadores[index].grafica = grafica;
      if ($scope.indicadores[index].tendencia)
        $scope.opcionesGraficasTendencias(index, $scope.indicadores[index].configuracion.tipo_grafico, $scope.indicadores[index].dimensiones[dimension], $scope.indicadores[index].informacion.unidad_medida, tamano);
      else
        $scope.opcionesGraficas(index, $scope.indicadores[index].configuracion.tipo_grafico, $scope.indicadores[index].dimensiones[dimension], $scope.indicadores[index].informacion.unidad_medida, tamano);
      document.getElementById("update" + index).click();
    }, 200);
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#ordenarArreglo
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que ordena los datos del gráfico
   * @param {index} index que identifica el area de gráfico
   * @param {ordenar_por} ordenar_por
   * @param {ordenmodo_ordenar_por} modo_orden (asc, desc)
   */
  $scope.ordenarArreglo = function(index, ordenar_por, modo_orden) {
    var data = $scope.indicadores[index].data;
    if (ordenar_por == "valor") {
      data.sort(function(a, b) {
        if (modo_orden == "asc") {
          if (parseFloat(a.measure) > parseFloat(b.measure)) {
            return 1;
          }
          if (parseFloat(a.measure) < parseFloat(b.measure)) {
            return -1;
          }
        }
        if (modo_orden == "desc") {
          if (parseFloat(a.measure) < parseFloat(b.measure)) {
            return 1;
          }
          if (parseFloat(a.measure) > parseFloat(b.measure)) {
            return -1;
          }
        }
        return 0;
      });
    }
    if (ordenar_por == "nombre") {
      data.sort(function(a, b) {
        if (modo_orden == "asc") {
          if (a.category > b.category) {
            return 1;
          }
          if (a.category < b.category) {
            return -1;
          }
        }
        if (modo_orden == "desc") {
          if (a.category < b.category) {
            return 1;
          }
          if (a.category > b.category) {
            return -1;
          }
        }
        return 0;
      });
    }
    var dimension = $scope.indicadores[index].dimension;
    $scope.indicadores[index].data = data;
    var grafica = [];
    var tipo = 'DISCRETEBARCHART';
    if ($scope.indicadores[index].configuracion.tipo_grafico)
      tipo = $scope.indicadores[index].configuracion.tipo_grafico.toUpperCase();
    // validar el tipo de grafica para asociar el dato 
    $scope.indicadores[index].radial = false;
    $scope.indicadores[index].termometro = false;
    $scope.indicadores[index].mapa = false;
    if (tipo == 'LINECHART' || tipo == 'LINEA' || tipo == 'LINEAS'){
      $scope.indicadores[index].tendencia = true;
      $scope.agregarIndicadorDimension(dimension, index);
    }
    if (tipo == 'DISCRETEBARCHART' || tipo == 'BARRA' || tipo == 'BARRAS' || tipo == 'COLUMNAS' || tipo == 'COLUMNA' ){
      grafica[0] = { key: $scope.indicadores[index].grafica[0].key, values: [] };

      angular.forEach(data, function(val, key) {
        color = "";
        angular.forEach($scope.indicadores[index].informacion.rangos, function(
          v1,
          k1
        ) {
          if (val.measure >= v1.limite_inf && val.measure <= v1.limite_sup) {
            color = v1.color;
          }
        });

        grafica[0].values.push({
          color: color,
          label: val.category,
          value: parseFloat(val.measure),
          index: index,
          dimension: dimension
        });        
        // fin asociacion
      });
    } else if (tipo == 'PIECHART' || tipo == 'PIE' || tipo == 'PASTEL' || tipo == 'TORTA'){
      angular.forEach(data, function (val, key) {
        color = "";
        angular.forEach($scope.indicadores[index].informacion.rangos, function (v1, k1) {
          if (val.measure >= v1.limite_inf && val.measure <= v1.limite_sup) {
            color = v1.color;
          }
        });
        grafica.push({
          color: color,
          key: val.category,
          y: parseFloat(val.measure),
          index: index,
          dimension: dimension
        });        
      });     
    } else if (tipo == 'GAUGE' || tipo == 'VELOCIMETRO' || tipo == 'ODOMETRO') {
      $scope.indicadores[index].radial = true;
      var data = $scope.indicadores[index];
      angular.forEach(data.data, function (val, key) {
        color = "";
        var rangos = [];
        angular.forEach(data.informacion.rangos, function (v1, k1) {
          if (val.measure >= v1.limite_inf && val.measure <= v1.limite_sup) {
            color = v1.color;
          }
          rangos.push({ "min": v1.limite_inf, "max": v1.limite_sup, "color": color })
        });

        var upperLimit = 0;
        if (data.informacion.meta) {
          if (parseFloat(val.measure) > parseFloat(data.informacion.meta)) {
            rangos.push({ "min": parseFloat(data.informacion.meta), "max": parseFloat(val.measure), "color": "#2196f3" });
            upperLimit = parseFloat(val.measure);
          } else {
            upperLimit = parseFloat(data.informacion.meta);
          }
        } else {
          upperLimit = parseFloat(val.measure);
        }
        if (data.informacion.rangos.length <= 0) {
          rangos = [
            { "min": 0, "max": (upperLimit * 0.40), "color": "#C50200" },
            { "min": (upperLimit * 0.40) + .1, "max": (upperLimit * 0.60), "color": "#FF7700" },
            { "min": (upperLimit * 0.60) + .1, "max": (upperLimit * 0.80), "color": "#FDC702" },
            { "min": (upperLimit * 0.80) + .1, "max": upperLimit, "color": "#8BC556" }
          ];
        }
        grafica.push({
          index: index,
          dimension: dimension,
          upperLimit: upperLimit,
          lowerLimit: 0,
          unit: data.informacion.unidad_medida,
          precision: 0,
          label: val.category,
          ranges: rangos,
          value: parseFloat(val.measure)
        });
      });
    } else if (tipo == "LINEARGAUGE" || tipo == "TERMOMETRO") {
      $scope.indicadores[index].termometro = true;
      var data = $scope.indicadores[index];
      var meta = data.informacion.meta ? parseFloat(data.informacion.meta) : 100;
      angular.forEach(data.data, function (val, key) {
        color = "";
        var rangos = [];

        angular.forEach(data.informacion.rangos, function (v1, k1) {
          if (val.measure >= v1.limite_inf && val.measure <= v1.limite_sup) {
            color = v1.color;
          }
          rangos.push(v1.limite_sup);
        });

        if (data.informacion.rangos.length <= 0) {
          var upperLimit = parseFloat(val.measure);
          rangos = [(upperLimit * 0.40), (upperLimit * 0.60), (upperLimit * 0.80), (upperLimit * 0.80)];
        }

        grafica.push({
          index: index,
          dimension: dimension,
          title: val.category,
          label: val.category,
          subtitle: data.informacion.unidad_medida,
          ranges: rangos,
          measures: [parseFloat(val.measure)],
          markers: [meta],
          color: color
        });
      });
    } else if (tipo == 'MAPA' || tipo == 'GEOLOCATION'){
      $scope.indicadores[index].mapa = true;
    }
    
    $scope.indicadores[index].grafica = grafica;
    $scope.actualizarsGrafica(index, false);
  };

  $scope.posicion = null;
  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#cargarIndicadores
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que cargar la informacion del sql
   * @param {index} index identificador de la posicion del grafico
   */
  $scope.verSQLIndicador = function(index) {
    $scope.posicion = index;
    $scope.indicadores[index].cargando = true;
    var json = { filtros: "", ver_sql: true, tendencia: false };
    var dimension = $scope.indicadores[index].dimension;
    Crud.crear(
      "../../../api/v1/tablero/datosIndicador/" +
        $scope.indicadores[index].id +
        "/" +
        $scope.indicadores[index].dimensiones[dimension],
      json,
      "application/json",
      function(data) {
        if (data.status == 200) {
          $scope.indicadores[index].sql = data.data;
          $("#sql_indicador").html(sqlFormatter.format(data.data));
          $("#modalSQL").modal("toggle");
        }
        $scope.indicadores[index].cargando = false;
      },
      function(e) {
        $scope.indicadores[index].cargando = false;
      }
    );
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#cargarIndicadores
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que cargar la informacion del sql
   * @param {index} index identificador de la posicion del grafico
   */
  $scope.verFichaIndicador = function(index) {
    $scope.posicion = index;
    $scope.indicadores[index].cargando = true;

    Crud.ver(
      "../../../api/v1/tablero/fichaIndicador",
      $scope.indicadores[index].id,
      function(data) {
        if (data.status == 200) {
          $scope.indicadores[index].ficha = data.data;
          $("#modalFicha").modal("toggle");
        }
        $scope.indicadores[index].cargando = false;
      },
      function(e) {
        $scope.indicadores[index].cargando = false;
      }
    );
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#verTablaDatosIndicador
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que muestra la informacion de los datos del indicador
   * @param {index} index identificador de la posicion del grafico
   */
  $scope.verTablaDatosIndicador = function(index) {
    $scope.posicion = index;
    $("#modalTablaDatos").modal("toggle");
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#verAlertasIndicador
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que muestra la informacion de las alertas del indicador
   * @param {index} index identificador de la posicion del grafico
   */
  $scope.verAlertasIndicador = function(index) {
    $scope.posicion = index;
    $("#modalAlertas").modal("toggle");
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#verConfiguracion
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que muestra la configuracion de los graficos
   * @param {index} index identificador de la posicion del grafico
   */
  $scope.verConfiguracion = function(index) {
    $scope.posicion = index;
    $("#modalConfiguracion").modal("toggle");
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#verFiltros
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que muestra las opciones de filtro
   * @param {index} index identificador de la posicion del grafico
   */
  $scope.verFiltros = function(index) {
    $scope.posicion = index;
    $("#modalFiltros").modal("toggle");
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#agregarFavorito
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion para agregar un indicador a la lista de favoritos
   * @param {item} item que corresponde al objeto indicador
   */
  $scope.agregarFavorito = function(item) {
    
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
  $scope.quitarIndicador = function(item, index) {
    
  };

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
  $scope.opcionesGraficas = function(index, tipo, labelx, labely, tamano) {
    if ($scope.indicadores[index].full_screen)
      tamano = $window.innerHeight / 1.28;
    var options = {};
    if(tipo)
      tipo = tipo.toUpperCase();
    else
      tipo = "DISCRETEBARCHART";
    if (tipo == 'PIECHART' || tipo == 'PIE' || tipo == 'PASTEL' || tipo == 'TORTA'){
      options = {
        chart: {
          type: "pieChart",
          height: tamano,
          x: function (d) {
            return d.key;
          },
          y: function (d) {
            return d.y;
          },
          showLabels: true,
          duration: 500,          
          labelThreshold: 0.01,
          labelSunbeamLayout: true,
          legend: {
            margin: {
              top: 5,
              right: 35,
              bottom: 5,
              left: 0
            }
          },
          callback: function (chart) {            
            chart.pie.dispatch.on("elementClick", function (e) {
              $scope.indicadores[index].dimension++;
              $scope.indicadores[index].filtros.push({
                codigo: $scope.indicadores[index].dimensiones[$scope.indicadores[index].dimension - 1].trim(),
                valor: e.data.key
              });
              $scope.agregarIndicadorDimension($scope.indicadores[index].dimension, e.data.index);
            });
          } 
        }
      };
    } else if (tipo == 'DISCRETEBARCHART' || tipo == 'BARRA' || tipo == 'BARRAS' || tipo == 'COLUMNAS' || tipo == 'COLUMNA' || tipo == 'LINECHART' || tipo == 'LINEA' || tipo == 'LINEAS'){
      options = {
        chart: {
          type: 'discreteBarChart',
          height: tamano,
          margin: { top: 20, right: 20, bottom: 50, left: 55 },
          x: function (d) {
            return d.label;
          },
          y: function (d) {
            return d.value;
          },
          showValues: true,
          valueFormat: function (d) {
            return d3.format(",.2f")(d);
          },
          duration: 500,
          xAxis: { axisLabel: labelx },
          yAxis: { axisLabel: labely }                  
        }
      };
      if (tipo == 'DISCRETEBARCHART' || tipo == 'BARRA' || tipo == 'BARRAS' || tipo == 'COLUMNAS' || tipo == 'COLUMNA'){
        options.chart.callback = function (chart) {
          chart.discretebar.dispatch.on("elementClick", function (e) {           
            $scope.indicadores[index].dimension++;
            $scope.indicadores[index].filtros.push({
              codigo: $scope.indicadores[index].dimensiones[$scope.indicadores[index].dimension - 1].trim(),
              valor: e.data.label
            });
            $scope.agregarIndicadorDimension($scope.indicadores[index].dimension,e.data.index);
          });
        }
      }
      if (tipo == 'LINECHART' || tipo == 'LINEA' || tipo == 'LINEAS'){
        options.chart.type = "lineChart";
        options.chart.useInteractiveGuideline = true;
        options.chart.dispatch = {
          stateChange: function (e) {
            console.log("stateChange");
          },
          changeState: function (e) {
            console.log("changeState");
          },
          tooltipShow: function (e) {
            console.log("tooltipShow");
          },
          tooltipHide: function (e) {
            console.log("tooltipHide");
          }
        };
      }
    } else if (tipo == "LINEARGAUGE" || tipo == "TERMOMETRO") {
      options = {
        chart: {
          type: 'bulletChart',
          height: 120,
          margin: { top: 20, right: 20, bottom: 50, left: 55 },          
          showValues: true,          
          duration: 500          
        }
      };
    }
    $scope.indicadores[index].options =options
  };

  $scope.opcionesGraficasTendencias = function (index, tipo, labelx, labely, tamano) {
    if($scope.indicadores[index].full_screen)
    tamano = $window.innerHeight / 1.28;

    $scope.indicadores[index].options = {
      chart: {
        type: "lineChart",
        height: tamano,
        margin: { top: 20, right: 20, bottom: 50, left: 55 },
        x: function (d) {
          return d.x;
        },
        y: function (d) {
          return d.y;
        },
        useInteractiveGuideline: true,
        dispatch: {
          stateChange: function (e) {
            console.log("stateChange");
          },
          changeState: function (e) {
            console.log("changeState");
          },
          tooltipShow: function (e) {
            console.log("tooltipShow");
          },
          tooltipHide: function (e) {
            console.log("tooltipHide");
          }
        },
        xAxis: {
          axisLabel: labelx,
          tickFormat: function (d) {
            return d3.time.format("%Y-%m")(new Date(d));
          },
          showMaxMin: false,
          staggerLabels: true,
          axisLabelDistance: 30
        },
        yAxis: {
          axisLabel: labely,
          tickFormat: function (d) {
            return d3.format(",.2f")(d);
          },
          axisLabelDistance: 20
        },
        callback: function (chart) {
        }
      }
    };
  }

  $scope.gaugeDimension = function (index, e){
    $scope.indicadores[index].dimension++;
    $scope.indicadores[index].filtros.push({
      codigo: $scope.indicadores[index].dimensiones[$scope.indicadores[index].dimension - 1].trim(),
      valor: e.label
    });
    $scope.agregarIndicadorDimension($scope.indicadores[index].dimension, e.index);
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
  $scope.imprimirMensaje = function(mensaje, tipo, id) {
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
    setTimeout(function() {
      $(id).html("");
    }, 6000);
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#exportarImagen
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que exporta el grafico al formato seleccionado
   * @param {index} index bandera de posicion
   * @param {tipo} tipo tipo de imagen SVG, PNG
   */
  $scope.exportarImagen = function(index, tipo) {
    $("#svg" + index + " svg").attr({
      version: "1.1",
      xmlns: "http://www.w3.org/2000/svg",
      id: "exportsvg" + index
    });
    var img = document.createElement("img");

    var valor =
      '<?xml version="1.0" encoding="utf-8"?>' +
      $("#svg" + index)
        .html()
        .replace("\\n", "");
    valor = window.btoa($scope.utf8_encode(valor));
    var titulo = $scope.indicadores[index].nombre;

    var a = document.createElement("a");
    if (tipo == "SVG") {
      img.setAttribute("src", "data:image/svg+xml;base64," + valor);

      img.onload = new function() {
        a.setAttribute("id", "imgsvg" + index);
        a.download = titulo + ".svg";
        a.href = "data:image/svg+xml;base64," + valor;
        a.click();
        $("#imgsvg" + index).remove();
      }();
    }
    if (tipo == "PNG") {
      saveSvgAsPng(
        document.getElementById("exportsvg" + index),
        titulo + ".png"
      );
    }
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#utf8_encode
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que codifica una cadena a utf8
   * @param {argString} string a convertir
   */
  $scope.utf8_encode = function(argString) {
    if (argString === null || typeof argString === "undefined") {
      return "";
    }

    var string = argString + ""; // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");
    var utftext = "",
      start,
      end,
      stringl = 0;

    start = end = 0;
    stringl = string.length;
    for (var n = 0; n < stringl; n++) {
      var c1 = string.charCodeAt(n);
      var enc = null;

      if (c1 < 128) {
        end++;
      } else if (c1 > 127 && c1 < 2048) {
        enc = String.fromCharCode((c1 >> 6) | 192, (c1 & 63) | 128);
      } else if ((c1 & 0xf800) != 0xd800) {
        enc = String.fromCharCode(
          (c1 >> 12) | 224,
          ((c1 >> 6) & 63) | 128,
          (c1 & 63) | 128
        );
      } else {
        // surrogate pairs
        if ((c1 & 0xfc00) != 0xd800) {
          throw new RangeError("Unmatched trail surrogate at " + n);
        }
        var c2 = string.charCodeAt(++n);
        if ((c2 & 0xfc00) != 0xdc00) {
          throw new RangeError("Unmatched lead surrogate at " + (n - 1));
        }
        c1 = ((c1 & 0x3ff) << 10) + (c2 & 0x3ff) + 0x10000;
        enc = String.fromCharCode(
          (c1 >> 18) | 240,
          ((c1 >> 12) & 63) | 128,
          ((c1 >> 6) & 63) | 128,
          (c1 & 63) | 128
        );
      }
      if (enc !== null) {
        if (end > start) {
          utftext += string.slice(start, end);
        }
        utftext += enc;
        start = end = n + 1;
      }
    }

    if (end > start) {
      utftext += string.slice(start, stringl);
    }

    return utftext;
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#exportar_excel
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que exporta a archivo xls
   * @param {id} id del elemento html que contiene el area a exportar
   * @param {titulo} titulo que contendra el elemento exportado
   */
  $scope.exportar_excel = function(id, titulo, nombre = true) {
    let colspan = $("#" + id).find("tr:first th").length;
    let excelData = nombre
      ? "<table><tr><th colspan='" +
        colspan +
        "'><h4>" +
        titulo +
        " <h4></th></tr></table>"
      : "";

    excelData += document.getElementById(id).innerHTML;
    let blob = new Blob([excelData], {
      type: "text/comma-separated-values;charset=utf-8"
    });
    saveAs(blob, titulo + ".xls");
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#exportar_pdf
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que exporta a archivo pdf
   * @param {id} id del elemento html que contiene el area a exportar
   * @param {titulo} titulo que contendra el elemento exportado
   */
  $scope.exportar_pdf = function(id, titulo, nombre = true) {
    var contenido = document.getElementById(id).innerHTML;
    html =
      '<html lang="es">' +
      " <head>" +
      ' <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' +
      ' <meta name="charset" content="UTF-8">' +
      ' <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">' +
      ' <meta name="apple-mobile-web-app-capable" content="yes">' +
      ' <title>PDF</title> <meta name="viewport" content="initial-scale=1" />' +
      ' <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">' +
      ' <style>.indicadores { margin: 0px; } .nv-x g .tick text { -webkit-transform: rotate(37deg); -moz-transform: rotate(37deg); -o-transform: rotate(37deg); transform: rotate(15deg); } </style>'
      " </head>" +
      " <body>";
    html += nombre ? "<h4>" + titulo + "</h4>" : "";
    html += contenido + " </body>" + " </html>";
    var iframe = document.createElement("iframe");
    iframe.setAttribute("id", "printf");

    document.body.appendChild(iframe);

    var mywindow = document.getElementById("printf");
    mywindow.contentWindow.document.write(html);
    setTimeout(() => {
      mywindow.contentWindow.print();
    }, 500);
    setTimeout(() => {
      document.body.removeChild(iframe);
    }, 2000);
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#exportar_csv
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que exporta un json a archivo csv
   * @param {titulo} titulo que contendra el elemento exportado
   * @param {json} json del elemento a exportar
   */
  $scope.exportar_csv = function(titulo, json) {
    var fields = Object.keys(json[0]);
    var replacer = function(key, value) {
      return value === null ? "" : value;
    };
    var csv = json.map(function(row) {
      return fields
        .map(function(fieldName) {
          return JSON.stringify(row[fieldName], replacer);
        })
        .join(",");
    });
    csv.unshift(fields.join(","));

    var csvData = csv.join("\r\n");

    let blob = new Blob([csvData], {
      type: "text/comma-separated-values;charset=utf-8"
    });
    saveAs(blob, titulo + ".csv");
  };

  $scope.medidas_width = [];
  for (i = 0; i < 12; i++) {
    $scope.medidas_width.push(i);
  }

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#agregarOtrosFiltros
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que agrega al los filtros extra los elementos existentes
   * @param {index} index que identifica el area de gráfico
   * @param {valor} valor del elemento seleccionado
   */
  $scope.agregarOtrosFiltros = function(index, valor) {
    if ($scope.indicadores[index].otros_filtros.elementos.indexOf(valor) > -1) {
      $scope.indicadores[index].otros_filtros.elementos.splice(
        $scope.indicadores[index].otros_filtros.elementos.indexOf(valor),
        1
      );
      $("#elemento" + index).removeAttr("checked");
    } else {
      $scope.indicadores[index].otros_filtros.elementos.push(valor);
      $("#elemento" + index).attr("checked", "checked");
    }
  };

 
  $scope.imprimirSala = function() {        
    var cont = 0; var div = 0;
    $("#paraimprimir").html(
      '<div class="col-md-12" ng-if="abrio_sala" style="top: -45px;" id="titulo_sala">'+
        '< div class= "page-header"  >'+
          '<h2 id="header_sala"><span class="glyphicon glyphicon-th"></span> '+$scope.sala.nombre+'</h2>'+
        '</div >'+
    '</div >'
    );
    $("#paraimprimir").append('<div id="salaN0" class="row" style="page-break-after:always;"></div>');
    angular.forEach($scope.indicadores, function(data, index) {
      if(cont % 2 == 0 && cont > 0){
        div++;
        $("#paraimprimir").append('<div id="salaN'+div+'" class="row" style="page-break-after:always;"></div>');        
      }
      $("#salaN" + div).append('<div class="col-sm-12 indicador '+index+'">' + $("#indicador" + index).html() + '</div>');
      $("#paraimprimir .row .indicador" + index).find("svg").attr("xmlns", "http://www.w3.org/2000/svg");
      $("#paraimprimir .row .indicador" + index).find("svg").attr("version", "1.1");
      $("#paraimprimir .row .indicador" + index + " .navbar").remove();
      $("#paraimprimir .row .indicador" + index + " .close_indicador").remove();
      cont++;
    });
    $scope.exportar_pdf("paraimprimir", $scope.sala.nombre, false) ;
  };

  $scope.opcionesExport = ["csv", "xls", "pdf"];

  $scope.td_tipo_exportar = "csv";
  $scope.asignarTipoExport = function(option) {
    $scope.td_tipo_exportar = option;
  };

  $scope.breadcum = function(index, item, link) {
    var filtros = item.filtros;
    $scope.indicadores[item.posicion - 1].dimension = index + 1;
    $scope.indicadores[item.posicion - 1].filtros = [];
    angular.forEach(filtros, function(value, key) {
      if (index >= key)
        $scope.indicadores[item.posicion - 1].filtros.push(value);
    });
    $scope.agregarIndicadorDimension(index + 1, item.posicion - 1);
  };

  /**
     * @ngdoc method
     * @name Tablero.TableroCtrl#exportarImagen
     * @methodOf Tablero.TableroCtrl
     *
     * @description
     * funcion que exporta el grafico al formato seleccionado
     */
  $scope.pngSala = function () {
    html2canvas($("#contenedor_tablero"), {
      onrendered: function (canvas) {
        theCanvas = canvas;
        canvas.toBlob(function (blob) {
          saveAs(blob, $scope.sala.nombre + ".png");
        });
      }
    });
  };
});
