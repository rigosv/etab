/**
 * @ngdoc object
 * @name Tablero.TableroCtrl
 * @description
 * Controlador general que maneja el tablero
 */

App.controller("TableroCtrl", function(
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
  $scope.indicadores = [];
  $scope.clasificacion_uso = "";
  $scope.clasificacion_tecnica = "";

  $scope.clasificaciones_usos = [];
  $scope.clasificaciones_tecnicas = [];

  $scope.inidcadores_clasificados = [];
  $scope.inidcadores_no_clasificados = [];
  $scope.inidcadores_busqueda = [];
  $scope.inidcadores_favoritos = [];

  $scope.valorFiltroGeneral = '';
  $scope.valorFiltroGeneralCatalogo = '';
  $scope.dimensionesGenerales = [];
  $scope.dimensionGeneral = '';
  $scope.filtroGeneralEsCatalogo = false;
  $scope.datosCatalogo = [];

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#cargarCatalogo
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que carga datos de una URL de la API y la almacena en un modelo angular especifico
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
  // cargar los catalogos para el indicador
  $scope.cargarCatalogo(
    "../api/v1/tablero/clasificacionUso",
    $scope.clasificaciones_usos,
    "cc_uso"
  );
  $scope.cargarCatalogo(
    "../api/v1/tablero/listaIndicadores?tipo=no_clasificados",
    $scope.inidcadores_no_clasificados,
    "cc_sin"
  );
  $scope.cargarCatalogo(
    "../api/v1/tablero/listaIndicadores?tipo=favoritos",
    $scope.inidcadores_favoritos,
    "cc_favprito"
  );

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
   * @name Tablero.TableroCtrl#cargarIndicadores
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que cargar la lista de inidcadores, segun el filtro correspodiente, construido desde la vista
   * @param {url} url URL en la api para la peticion
   * @param {modelo} modelo modelo donde se carga el resultado
   * @param {cargando} cargando bandera para mostrar la animacion cargando
   */
  $scope.cargarIndicadores = function(url, modelo, cargando) {
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
            $scope.cargarIndicadores(url, modelo, cargando);
            $scope.intento1++;
          } else $scope[cargando] = false;
        }, 200);
      }
    );
  };
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
  $scope.cargarSalas = function() {
    $scope["cc_salas"] = true;
    Crud.lista(
      "../api/v1/tablero/listaSalas",
      function(data) {
        if (data.status == 200) {
          $scope.intento1 = 0;
          $scope.salas = data.data;
          $scope.salas_grupos = data.salas_grupos;
          $scope.salas_propias = data.salas_propias;
        }
        $scope["cc_salas"] = false;
      },
      function(e) {
        setTimeout(function() {
          if ($scope.intento1 < 1) {
            $scope.cargarSalas();
            $scope.intento1++;
          } else $scope["cc_salas"] = false;
        }, 200);
      }
    );
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
  $scope.bsucarIndicador = function(keyEvent) {
    if (keyEvent.which === 13) {
      $scope.inidcadores_busqueda = [];
      $scope["cc_buscar"] = true;
      Crud.lista(
        "../api/v1/tablero/listaIndicadores?tipo=busqueda&busqueda=" +
          $scope.buscar_busqueda,
        function(data) {
          if (data.status == 200) {
            $scope.intento1 = 0;
            $scope.inidcadores_busqueda = data.data;
            $scope.buscar_busqueda = "";
          }
          $scope["cc_buscar"] = false;
        },
        function(e) {
          setTimeout(function() {
            if ($scope.intento1 < 1) {
              $scope.bsucarIndicador();
              $scope.intento1++;
            } else $scope["cc_buscar"] = false;
          }, 200);
        }
      );
    }
  };

  $scope.tablero_indicador = [];

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
          tendencia: false,
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
            tipo_grafico: element.tipo_grafico,
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
        $scope.indicadores[index].filtros = element.filtro != "" ? JSON.parse(element.filtro) : "";
        $scope.indicadores[index].otros_filtros = {
          desde: element.filtro_posicion_desde,
          hasta: element.filtro_posicion_hasta,
          elementos: element.filtro_elementos != "" && element.filtro_elementos != null ? element.filtro_elementos.split(",") : []
        };
        
        $scope.opcionesGraficas(index, $scope.indicadores[index].configuracion.tipo_grafico, element.dimension, "#", $scope.indicadores[index].configuracion.height);
        
        var json = {
          filtros: $scope.indicadores[index].filtros,
          ver_sql: false,
          tendencia: angular.isUndefined($scope.indicadores[index].tendencia) ? false : $scope.indicadores[index].tendencia
        };
        var linea = $scope.indicadores[index].hasOwnProperty('linea') ? $scope.indicadores[index].linea : false;
        if (linea){
          json.linea = true;
        }
        if (
          element.filtro_posicion_desde != "" ||
          element.filtro_posicion_hasta != "" ||
          (element.filtro_elementos != "" && element.filtro_elementos)
        ) {
          json.otros_filtros = $scope.indicadores[index].otros_filtros;
        }

        Crud.crear(
          "../api/v1/tablero/datosIndicador/" +
          $scope.indicadores[index].id +
            "/" +
          element.dimension.replace(/\r\n/g, "").replace(/ /g, ""),
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

              $scope.indicadores[index].nombre = data.informacion.nombre_indicador;
              $scope.indicadores[index].es_favorito = data.informacion.es_favorito;
              $scope.indicadores[index].dimensiones = dimensiones;
              $scope.indicadores[index].dimension = dimension;
              $scope.indicadores[index].sql = "";
              $scope.indicadores[index].ficha = data.ficha;
              $scope.indicadores[index].full_screen = false;

              $scope.indicadores[index].informacion = data.informacion;
              $scope.indicadores[index].informacion.nombre = data.informacion.nombre_indicador;
            }

            $scope.respuestaIndicador(data, dimension, index);
            $scope.indicadores[index].cargando = false;
            $scope.indicadores[index].tendencia = json.tendencia;
            $scope.actualizarsGrafica(index, false, json.tendencia);
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
      $scope.listaAccionSala();
      $scope.usuariosSala();
      $scope.comentarioSala();
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
    $scope.abrio_sala = false;
    $scope.indicadores = [];
    $scope.sala = { id: "", nombre: "" };
  };
  $scope.sala_cargando = false;

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#cerraSala
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que borra la sala seleccionada
   * @param {item} item sala seleccionada de la lista
   */
  $scope.borrarSala = function(item) {
    if (confirm($("#confirmar_sala").html())) {
      var json = { id: item.id };
      $scope.sala_cargando = true;
      Crud.crear(
        "../api/v1/tablero/borrarSala",
        json,
        "application/json",
        function(data) {
          if (data.status == 200) {
            $scope.cerraSala();
            $scope.cargarSalas();
            alert($("#elimina_ok_sala").html());
          } else {
            alert($("#elimina_error_sala").html());
          }
          $scope.sala_cargando = false;
        },
        function(e) {
          $scope.sala_cargando = false;
          alert($("#elimina_error_sala").html());
        }
      );
    }
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#cerraSala
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que guarda o actualiza la informacion de la sala y la lista de indicadores
   * @param {item} item sala seleccionada de la lista
   */
  $scope.guardarSala = function(item) {
    var json = { sala: item, indicadores: $scope.indicadores };
    $scope.sala_cargando = true;
    Crud.crear(
      "../api/v1/tablero/guardarSala",
      json,
      "application/json",
      function(data) {
        if (data.status == 200) {
          $scope.abrio_sala = true;
          $scope.sala.id = data.data;
          $scope.cargarSalas();
          $("#modalGuardarSala").modal("hide");
          alert($("#guardar_sala_ok").html());          
        } else {
          alert($("#guardar_sala_error").html());
        }
        $scope.sala_cargando = false;
      },
      function(e) {
        alert($("#guardar_sala_error").html());
        $scope.sala_cargando = false;
      }
    );
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#cargarIndicadores
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que cargar los datos del inidcador
   * @param {item} item inidcador seleccionado de la lista
   */
  $scope.agregarIndicador = function(item, dimension = "") {
    if (item) {
      if (angular.isUndefined($scope.tablero_indicador[item.id]))
        $scope.tablero_indicador[item.id] = 0;
      $scope.abrio_indicador = true;
      var campos_indicador = item.campos_indicador.split(",");
      $scope.indicadores.push({
        cargando: true,
        filtros: [],
        error: "",
        id: item.id,
        nombre: item.nombre,
        es_favorito: item.es_favorito,
        dimensiones: campos_indicador,
        dimension: 0,
        posicion: 0,
        index: 0,
        tendencia: false,
        radial: false,
        termometro: false,
        mapa: false,
        sql: "",
        ficha: "",
        full_screen: false,
        configuracion: {
          width: "col-sm-4",
          height: "280",
          orden_x: "",
          orden_y: "",
          tipo_grafico: item.dimensiones[campos_indicador[0]].graficos[0].codigo,
          maximo: "",
          maximo_manual: ""
        },
        otros_filtros: {
          desde: "",
          hasta: "",
          elementos: []
        }
      });
      $scope.tablero_indicador[item.id]++;
      var index = $scope.indicadores.length - 1;
      $scope.indicadores[index].posicion = index;
      $scope.indicadores[index].posicion = index + 1;
      
      $scope.opcionesGraficas(index, $scope.indicadores[index].configuracion.tipo_grafico, $scope.indicadores[index].dimensiones[0], item.unidad_medida, "280");
        
      var json = { filtros: "", ver_sql: false, tendencia: false };
      Crud.crear(
        "../api/v1/tablero/datosIndicador/" +
        item.id +
          "/" +
        $scope.indicadores[index].dimensiones[0].replace(/\r\n/g, "").replace(/ /g, ""),
        json,
        "application/json",
        function(data) {
          if (data.status == 200) {
            $scope.indicadores[index].data = data.data;
            $scope.indicadores[index].informacion = data.informacion;
            $scope.indicadores[index].ficha = data.ficha;
            $scope.indicadores[index].grafica = [];
            
            $scope.respuestaIndicador(data, $scope.indicadores[index].dimensiones[0], index);

          } else {
            $scope.indicadores[index].error = "Warning";
          }
          $scope.indicadores[index].cargando = false;
          setTimeout(function() {
            $scope.indicadores[index].error = "";
          }, 3000);
        },
        function(e) {
          $scope.indicadores[index].error = "Error";
          $scope.indicadores[index].cargando = false;
          setTimeout(function() {
            $scope.indicadores[index].error = "";
          }, 3000);
        }
      );
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
   * @param {dimension} dimension del indicador
   * @param {index} index identificador de la posicion del grafico
   */
  $scope.agregarIndicadorDimension = function(dimension, index) {
    var posicion = index;  
    var tipo = 'DISCRETEBARCHART';
    if ($scope.indicadores[index].configuracion.tipo_grafico)
      tipo = $scope.indicadores[index].configuracion.tipo_grafico.toUpperCase(); 
    if ( !angular.isUndefined($scope.indicadores[index].dimensiones[dimension]) ) {
      $scope.indicadores[index].cargando = true;
      
      if ($scope.indicadores[index].tendencia){        
        $scope.opcionesGraficasTendencias(index, $scope.indicadores[index].configuracion.tipo_grafico, $scope.indicadores[index].dimensiones[dimension], $scope.indicadores[index].informacion.unidad_medida, $scope.indicadores[index].configuracion.height);
      }
      else{
        if (tipo != 'MAPA' && tipo != 'GEOLOCATION' && tipo != 'MAP')
          $scope.opcionesGraficas(index, $scope.indicadores[index].configuracion.tipo_grafico, $scope.indicadores[index].dimensiones[dimension], $scope.indicadores[index].informacion.unidad_medida, $scope.indicadores[index].configuracion.height);
      }
      var json = { filtros: $scope.indicadores[index].filtros, ver_sql: false, tendencia: $scope.indicadores[index].tendencia};
      
      Crud.crear(
        "../api/v1/tablero/datosIndicador/" +
        $scope.indicadores[index].id +
          "/" +
        $scope.indicadores[index].dimensiones[dimension].replace(/\r\n/g, "").replace(/ /g, ""),
        json,
        "application/json",
        function(data) {
          $scope.respuestaIndicador(data, dimension, index);
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

    var tipo = 'DISCRETEBARCHART';
    if ($scope.indicadores[index].configuracion.tipo_grafico)
      tipo = $scope.indicadores[index].configuracion.tipo_grafico.toUpperCase();

    if (
      !angular.isUndefined($scope.indicadores[index].dimensiones[dimension])
    ) {
      $scope.indicadores[index].cargando = true;
      var linea = $scope.indicadores[index].hasOwnProperty('linea') ? $scope.indicadores[index].linea : false;
      if ($scope.indicadores[index].tendencia)
        $scope.opcionesGraficasTendencias(index, $scope.indicadores[index].configuracion.tipo_grafico, $scope.indicadores[index].dimensiones[dimension], $scope.indicadores[index].informacion.unidad_medida, $scope.indicadores[index].configuracion.height);
      else{
        if (tipo != 'MAPA' && tipo != 'GEOLOCATION' && tipo != 'MAP')
          $scope.opcionesGraficas(index, $scope.indicadores[index].configuracion.tipo_grafico, $scope.indicadores[index].dimensiones[dimension], $scope.indicadores[index].informacion.unidad_medida, $scope.indicadores[index].configuracion.height);
      }

      var json = {
        filtros: $scope.indicadores[index].filtros,
        ver_sql: false,
        otros_filtros: $scope.indicadores[index].otros_filtros
      };
      Crud.crear(
        "../api/v1/tablero/datosIndicador/" +
        $scope.indicadores[index].id +
          "/" +
        $scope.indicadores[index].dimensiones[dimension].replace(/\r\n/g, "").replace(/ /g, ""),
        json,
        "application/json",
        function(data) {
          $scope.respuestaIndicador(data, dimension - 1, index);
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
   * @name Tablero.TableroCtrl#respuestaIndicador
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion procesa la respuesta para contruir los datos para el gráfico
   * @param {data} data respuesta del servidor
   * @param {dimension} dimension dimension actual de la gráfica
   * @param {index} index bandera de posicion
   */
  $scope.respuestaIndicador = function(data, dimension, index) {
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
          var mayor = 0;
          $scope.indicadores[index].labels = [0];
          $scope.indicadores[index].series = ['INDICADOR'];
          $scope.indicadores[index].colors = [
            {
              backgroundColor: "rgba(159,204,0, 0.2)",
              pointBackgroundColor: "rgba(159,204,0, 1)",
              pointHoverBackgroundColor: "rgba(159,204,0, 0.8)",
              borderColor: "rgba(159,204,0, 1)",
              pointBorderColor: '#fff',
              pointHoverBorderColor: "rgba(159,204,0, 1)",
              fill: false
            }
          ];
          $scope.indicadores[index].valores = [];
          
          var datos_linea = [];
          angular.forEach(data.data, function(valor){
            
            $scope.indicadores[index].series.push(valor.category);
  
            $scope.indicadores[index].labels.push(valor.category);
            
            if(angular.isUndefined(datos_linea[valor.category]))
              datos_linea[valor.category] = [];
            
            datos_linea[valor.category].push(valor.measure);
            color = ""; 
            angular.forEach($scope.indicadores[index].informacion.rangos, function(v1, k1) {
              if (valor.measure >= v1.limite_inf && valor.measure <= v1.limite_sup) {
                color = v1.color;
              }
            });
            if(color == '')
              color = "rgba(" + Math.floor(Math.random()*256) + "," + Math.floor(Math.random()*256) + ", 255, 0.9)";
            else{
              var rgba = $scope.hexToRgb(color);
              color = "rgba(" + rgba[0] + "," + rgba[1] + "," + rgba[2] +"," + rgba[3] +")";
            }
            $scope.indicadores[index].colors.push(
              {
                backgroundColor: color,
                pointBackgroundColor: color,
                pointHoverBackgroundColor: "rgba(159,204,0, 0.8)",
                borderColor: color,
                pointBorderColor: '#fff',
                pointHoverBorderColor: color,
                fill: false
              }
            );
          });
          var newvalor = [0];
          if(angular.isObject(datos_linea)){
            var values = [];
            for (var key in datos_linea) {
              values.push(datos_linea[key])
            }
            datos_linea = values;
          }
          angular.forEach(datos_linea, function(valor, clave){
            
            angular.forEach(valor, function(v){
              newvalor.push(v);
            })
          });
          $scope.indicadores[index].valores.push(newvalor);                 
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
        } 

        if (tipo == 'MAPA' || tipo == 'GEOLOCATION' || tipo == 'MAP') {
          angular.forEach(data.data, function (val, key) {
            color = "";
            angular.forEach(data.informacion.rangos, function (v1, k1) {
              if (val.measure >= v1.limite_inf && val.measure <= v1.limite_sup) {
                color = v1.color;
              }
            });
            var nombre = $scope.eliminarDiacriticos(val.category);
            nombre = nombre.toUpperCase();
            grafica[nombre] = {
              color: color,
              label: val.category,
              value: parseFloat(val.measure),
              index: index,
              dimension: dimension
            };
          });
          $scope.indicadores[index].grafica = grafica;
          $scope.actualizarMapa(index);
        } 

        // fin asociacion
        $scope.indicadores[index].grafica = grafica;

      }else{        
        $scope.indicadores[index].grafica = data.data_tendencia;
        $scope.indicadores[index].data_tendencia = data.data_tendencia;
        $scope.opcionesGraficasTendencias(index, $scope.indicadores[index].configuracion.tipo_grafico, $scope.indicadores[index].dimensiones[dimension], $scope.indicadores[index].informacion.unidad_medida, $scope.indicadores[index].configuracion.height);
      }
    } else {
      $scope.indicadores[index].dimension--;    
      delete $scope.indicadores[index].filtros[$scope.indicadores[index].filtros.length - 1];        
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
  $scope.actualizarsGrafica = function(index, hacer = true, tendencia = true) {
    if (hacer){
      $scope.indicadores[index].full_screen = !$scope.indicadores[index].full_screen;
      if (!$scope.indicadores[index].full_screen){
        $scope.zoom[index] = undefined;
        $scope.horizontal[index] = undefined;
        $scope.vertical[index] = undefined;
      }
    }
    var tipo = 'DISCRETEBARCHART';
    if ($scope.indicadores[index].configuracion.tipo_grafico)
      tipo = $scope.indicadores[index].configuracion.tipo_grafico.toUpperCase();
    var grafica = [];
    
    var dimension = $scope.indicadores[index].dimension;
    $scope.indicadores[index].linea = false;
    // validar el tipo de grafica para asociar el dato 
    $scope.indicadores[index].radial = false;
    $scope.indicadores[index].termometro = false;
    $scope.indicadores[index].mapa = false;
    
    if ((tipo != 'LINECHART' && tipo != 'LINEA' && tipo != 'LINEAS') && $scope.indicadores[index].tendencia){
      $scope.indicadores[index].tendencia = tendencia;
      $scope.indicadores[index].linea = false;
      $scope.agregarIndicadorDimension(dimension, index);
    }
    else if (tipo == 'LINECHART' || tipo == 'LINEA' || tipo == 'LINEAS') {
      $scope.indicadores[index].tendencia = false;
      $scope.indicadores[index].linea = true;
      $scope.agregarIndicadorDimension(dimension, index);
    }
    if (tipo == 'DISCRETEBARCHART' || tipo == 'BARRA' || tipo == 'BARRAS' || tipo == 'COLUMNAS' || tipo == 'COLUMNA' ) {      
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
    } 
    if (tipo == 'MAPA' || tipo == 'GEOLOCATION' || tipo == 'MAP') {
      angular.forEach($scope.indicadores[index].data, function (val, key) {
        color = "";
        angular.forEach($scope.indicadores[index].informacion.rangos, function (v1, k1) {
          if (val.measure >= v1.limite_inf && val.measure <= v1.limite_sup) {
            color = v1.color;
          }
        });
        var nombre = $scope.eliminarDiacriticos(val.category);
        nombre = nombre.toUpperCase();
        grafica[nombre] = {
          color: color,
          label: val.category,
          value: parseFloat(val.measure),
          index: index,
          dimension: dimension
        };
      });
    } 

    // fin asociacion    
    var tamano = $scope.indicadores[index].configuracion.height;
    if (!$scope.indicadores[index].tendencia)
      $scope.indicadores[index].grafica = [];
    
    setTimeout(() => {
      if (!$scope.indicadores[index].tendencia)
        $scope.indicadores[index].grafica = grafica;
      
      if (tipo == 'MAPA' || tipo == 'GEOLOCATION' || tipo == 'MAP') {
        $scope.dibujarMapa(index, true);
      } else{
        
        if ($scope.indicadores[index].tendencia){
          $scope.opcionesGraficasTendencias(index, $scope.indicadores[index].configuracion.tipo_grafico, $scope.indicadores[index].dimensiones[dimension], $scope.indicadores[index].informacion.unidad_medida, tamano);
        }
        else {
          $scope.opcionesGraficas(index, $scope.indicadores[index].configuracion.tipo_grafico, $scope.indicadores[index].dimensiones[dimension], $scope.indicadores[index].informacion.unidad_medida, tamano);   
        }
      }
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
   * @param {ordenar_por} ordenar por elemento
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
      if (!$scope.indicadores[index].hasOwnProperty('tendencia'))
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
    } else if (tipo == 'MAPA' || tipo == 'GEOLOCATION' || tipo == 'MAP'){
      $scope.indicadores[index].mapa = true;
    }
    if (!$scope.indicadores[index].tendencia)
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
      "../api/v1/tablero/datosIndicador/" +
      $scope.indicadores[index].id +
        "/" +
      $scope.indicadores[index].dimensiones[dimension].replace(/\r\n/g, "").replace(/ /g, ""),
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
      "../api/v1/tablero/fichaIndicador",
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
    if (item) {
      var json = { id: item.id, es_favorito: item.es_favorito };
      Crud.crear(
        "../api/v1/tablero/indicadorFavorito",
        json,
        "application/json",
        function(data) {
          if (data.status == 200) {
            item.es_favorito = data.data;
          }
        },
        function(e) {}
      );
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
  $scope.quitarIndicador = function(item, index) {
    $scope.tablero_indicador[item.id]--;
    $scope.indicadores.splice(index, 1);
    if ($scope.indicadores.length <= 0) {
      $scope.abrio_indicador = false;
    }
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
              if ($scope.indicadores[index].dimension < $scope.indicadores[index].dimensiones.length){
                $scope.indicadores[index].filtros.push({
                  codigo: $scope.indicadores[index].dimensiones[$scope.indicadores[index].dimension - 1].trim(),
                  valor: e.data.key
                });
                $scope.agregarIndicadorDimension($scope.indicadores[index].dimension, e.data.index);
              }else{
                $scope.finDimension(index);
              }
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
            if ($scope.indicadores[index].dimension < $scope.indicadores[index].dimensiones.length) {
              $scope.indicadores[index].filtros.push({
                codigo: $scope.indicadores[index].dimensiones[$scope.indicadores[index].dimension - 1].trim(),
                valor: e.data.label
              });
              $scope.agregarIndicadorDimension($scope.indicadores[index].dimension,e.data.index);
            }else{
              $scope.finDimension(index);
            }
          });
        }
      }
      if (tipo == 'LINECHART' || tipo == 'LINEA' || tipo == 'LINEAS'){
        var mayor = 0;
        $scope.indicadores[index].labels = [];
        $scope.indicadores[index].series = ['INDICADOR'];
        $scope.indicadores[index].colors = [
          {
            backgroundColor: "rgba(159,204,0, 0.2)",
            pointBackgroundColor: "rgba(159,204,0, 1)",
            pointHoverBackgroundColor: "rgba(159,204,0, 0.8)",
            borderColor: "rgba(159,204,0, 1)",
            pointBorderColor: '#fff',
            pointHoverBorderColor: "rgba(159,204,0, 1)",
            fill: false
          }
        ];
        $scope.indicadores[index].valores = [];
        
        var datos_linea = [];
        angular.forEach($scope.indicadores[index].data, function(valor){
          
          $scope.indicadores[index].series.push(valor.category);

          $scope.indicadores[index].labels.push(valor.category);
          
          if(angular.isUndefined(datos_linea[valor.category]))
            datos_linea[valor.category] = [];
          
          datos_linea[valor.category].push(valor.measure);
          color = ""; 
          angular.forEach($scope.indicadores[index].informacion.rangos, function(v1, k1) {
            if (valor.measure >= v1.limite_inf && valor.measure <= v1.limite_sup) {
              color = v1.color;
            }
          });
          if(color == '')
            color = "rgba(" + Math.floor(Math.random()*256) + "," + Math.floor(Math.random()*256) + "," + Math.floor(Math.random()*256) +",0.9)";
          else{
            var rgba = $scope.hexToRgb(color);
            color = "rgba(" + rgba[0] + "," + rgba[1] + "," + rgba[2] +"," + rgba[3] +")";
          }
          $scope.indicadores[index].colors.push(
            {
              backgroundColor: "rgba(254,254,254, 0.9)",
              pointBackgroundColor: color,
              pointHoverBackgroundColor: "rgba(159,204,0, 0.8)",
              borderColor: color,
              pointBorderColor: '#fff',
              pointHoverBorderColor: color,
              fill: false
            }
          );
        });
        var newvalor = [0];
        if(angular.isObject(datos_linea)){
          var values = [];
          for (var key in datos_linea) {
            values.push(datos_linea[key])
          }
          datos_linea = values;
        }
        angular.forEach(datos_linea, function(valor, clave){
          
          angular.forEach(valor, function(v){
            newvalor.push(v);
          })
        });
        console.log($scope.indicadores[index]);
        $scope.indicadores[index].valores.push(newvalor);
        /*
        angular.forEach(datos_linea, function(valor, clave){
          var newvalor = [];
          angular.forEach($scope.indicadores[index].labels, function(v1, c1){
            if(clave == v1){
              angular.forEach(valor, function(v){
                newvalor.push(v);
              })
            }
            else{
                newvalor.push(0);
            }
          });
          $scope.indicadores[index].valores.push(newvalor);
        });*/
        
        options = {
          legend: {
            display: true
          },
          elements: {
            line: {
                tension: 0
            }
          },
          scales: {
          yAxes: [{
            scaleLabel: {
              display: true,
              labelString: $scope.indicadores[index].informacion.unidad_medida
            }
          }],
          xAxes: [{
            scaleLabel: {
              display: true,
              labelString: $scope.indicadores[index].dimensiones[$scope.indicadores[index].dimension]
            }
          }] 
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
    } else if (tipo == 'MAPA' || tipo =='GEOLOCATION' || tipo == 'MAP'){
      $scope.dibujarMapa(index, true);
    }

    if (tipo != 'MAPA' && tipo != 'GEOLOCATION' && tipo != 'MAP'){
      $scope.indicadores[index].options =options
    }
  };

  $scope.onLineClick = function (points, evt) {
    
    if(points){
      var indice = points[0]._index ? points[0]._index : points[0].a._index;
      if(indice){
        var dimension = points[0]._xScale.ticks[indice] ? points[0]._xScale.ticks[indice] : points[0].a._xScale.ticks[indice];
        var index = parseInt(evt.path[4].id.replace("indicador", ""));
        $scope.indicadores[index].dimension++;
        if ($scope.indicadores[index].dimension < $scope.indicadores[index].dimensiones.length) {
          $scope.indicadores[index].filtros.push({
            codigo: $scope.indicadores[index].dimensiones[$scope.indicadores[index].dimension - 1].trim(),
            valor: dimension
          });
          $scope.agregarIndicadorDimension($scope.indicadores[index].dimension, index);
        }else{
          $scope.finDimension(index);
        }
      }
    }
  };

  $scope.hexToRgb = function (color) {
    if (!color)
		return;
    if (color.toLowerCase() === 'transparent')
      return [0, 0, 0, 0];
    if (color[0] === '#')
    {
      if (color.length < 7)
      {
        // convert #RGB and #RGBA to #RRGGBB and #RRGGBBAA
        color = '#' + color[1] + color[1] + color[2] + color[2] + color[3] + color[3] + (color.length > 4 ? color[4] + color[4] : '');
      }
      return [parseInt(color.substr(1, 2), 16),
        parseInt(color.substr(3, 2), 16),
        parseInt(color.substr(5, 2), 16),
        color.length > 7 ? parseInt(color.substr(7, 2), 16)/255 : 1];
    }
    if (color.indexOf('rgb') === -1)
    {
      // convert named colors
      var temp_elem = document.body.appendChild(document.createElement('fictum')); // intentionally use unknown tag to lower chances of css rule override with !important
      var flag = 'rgb(1, 2, 3)'; // this flag tested on chrome 59, ff 53, ie9, ie10, ie11, edge 14
      temp_elem.style.color = flag;
      if (temp_elem.style.color !== flag)
        return; // color set failed - some monstrous css rule is probably taking over the color of our object
      temp_elem.style.color = color;
      if (temp_elem.style.color === flag || temp_elem.style.color === '')
        return; // color parse failed
      color = getComputedStyle(temp_elem).color;
      document.body.removeChild(temp_elem);
    }
    if (color.indexOf('rgb') === 0)
    {
      if (color.indexOf('rgba') === -1)
        color += ',1'; // convert 'rgb(R,G,B)' to 'rgb(R,G,B)A' which looks awful but will pass the regxep below
      return color.match(/[\.\d]+/g).map(function (a)
      {
        return +a
      });
    }
  }

  $scope.finDimension = function(index){
    $scope.indicadores[index].error = "Success";
    setTimeout(function() {
      $scope.indicadores[index].error = "";
    }, 3000);
  }

  $scope.meses = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];
  $scope.mesen = ["january", "february", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december" ];

  $scope.opcionesGraficasTendencias = function (index, tipo, labelx, labely, tamano) {
    if($scope.indicadores[index].full_screen)
    tamano = $window.innerHeight / 1.28;
    var tf = true;
    var meses = $scope.mesen; var xlabel = "Time";
    if ($(".dropdown-user").find(".active").find("a").text() == 'Español'){
      meses = $scope.meses;
      xlabel = "Tiempo";
    }

    var mayor = 0;
        $scope.indicadores[index].labels = [];
        $scope.indicadores[index].series = [];
        $scope.indicadores[index].colors = [];
        $scope.indicadores[index].valores = [];
        
        var datos_linea = [];
        if(!angular.isUndefined($scope.indicadores[index]['data_tendencia'])) {
          angular.forEach($scope.indicadores[index]["data_tendencia"][0].values, function(valor){
            if(!$scope.indicadores[index].series.includes(valor.category))
              $scope.indicadores[index].series.push(valor.category);

            var label = valor.x;
            if(!$scope.indicadores[index].labels.includes(label))
              $scope.indicadores[index].labels.push(label);
            
            if(angular.isUndefined(datos_linea[valor.category]))
              datos_linea[valor.category] = [];
            
            datos_linea[valor.category].push(valor.y);
            
          });
        
          var newvalor = [];
          for(v in datos_linea ){
            var valor = datos_linea[v];
            newvalor.push(valor);
            var total  = 0;
            angular.forEach(valor, function(v){
              total += v;
            });
            total = total / 12;

            color = "rgba(" + Math.floor(Math.random()*180) + "," + Math.floor(Math.random()*180) + ", " + Math.floor(Math.random()*180) + ", 0.9)";
            
            $scope.indicadores[index].colors.push(
              {
                backgroundColor: color,
                pointBackgroundColor: color,
                pointHoverBackgroundColor: "rgba(159,204,0, 0.8)",
                borderColor: color,
                pointBorderColor: '#fff',
                pointHoverBorderColor: color,
                fill: false
              }
            );
          }
          $scope.indicadores[index].valores = newvalor;
          
          
          $scope.indicadores[index].options = {
            legend: {
              display: true
            },
            elements: {
              line: {
                  tension: 0
              }
            },
            scales: {
            yAxes: [{
              scaleLabel: {
                display: true,
                labelString: $scope.indicadores[index].informacion.unidad_medida
              }
            }],
            xAxes: [{
              scaleLabel: {
                display: true,
                labelString: $scope.indicadores[index].dimensiones[$scope.indicadores[index].dimension]
              }
            }] 
            }
          };
        }
        console.log($scope.indicadores[index]);
      
    /*
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
          axisLabel: xlabel,
          tickFormat: function (d) { 
            //return meses[d - 1];        
            return d                          
          },
          showMaxMin: tf,
          staggerLabels: tf,
          axisLabelDistance: 5
        },
        yAxis: {
          axisLabel: labely,
          tickFormat: function (d) {
            return d3.format(",.2f")(d);
          },
          axisLabelDistance: 20
        },
        callback: function (chart) {
          chart.lines.dispatch.on("elementClick", function (e) {
            $scope.indicadores[index].dimension++;
            if ($scope.indicadores[index].dimension < $scope.indicadores[index].dimensiones.length) {              
              $scope.indicadores[index].filtros.push({
                codigo: $scope.indicadores[index].dimensiones[$scope.indicadores[index].dimension - 1].trim(),
                valor: e.point.x
              });
              $scope.agregarIndicadorDimension($scope.indicadores[index].dimension, e.data.index);
            } else {
              $scope.finDimension(index);
            }
          });
        }
      }
    };*/
  }


  $scope.zoom = [];
  $scope.horizontal = [];
  $scope.vertical = [];
  $scope.topology;
  $scope.dibujarMapa = function(index, hacer = true) {
    var tamano = $scope.indicadores[index].configuracion.height;
    if ($scope.indicadores[index].full_screen){
      tamano = $window.innerHeight / 1.28;
      if(hacer){
        $scope.zoom[index] = $scope.zoom[index] * 2; 
        $scope.horizontal[index] = $scope.topology.transform.translate[0];
        $scope.vertical[index] = $scope.topology.transform.translate[1] + 1;
      }
    }
    
    $scope.indicadores[index].mapa = true;
    $("#mapa" + index).html('');
    if($scope.indicadores[index].informacion){
      
      setTimeout(function() {
        var width = $("#mapa" + index).width(), height = tamano;        

        var dimension = $scope.indicadores[index].dimensiones[$scope.indicadores[index].dimension].replace(/\r\n/g, "").replace(/ /g, "");
        var url_mapa = $scope.indicadores[index].informacion.dimensiones[dimension].mapa;
        if (url_mapa){
          var url = $scope.ruta + "/js/Mapas/" + url_mapa + ".json";
          d3.json(url, function(error, topology) {
            if (error) throw error;   
            if (topology.transform){
              $scope.topology = topology;
              if (topology.transform.translate) {
                if (angular.isUndefined($scope.zoom[index])) {
                  $scope.zoom[index] = 9000;
                }
                if (angular.isUndefined($scope.horizontal[index])) {
                  $scope.horizontal[index] = topology.transform.translate[0] + 2.7;
                }
                if (angular.isUndefined($scope.vertical[index])) {
                  $scope.vertical[index] = topology.transform.translate[1];
                }
              }
            }
            var projection = d3.geo.mercator()
              .scale($scope.zoom[index])
              .center([$scope.horizontal[index], $scope.vertical[index]]);
            var svg = d3.select(document.getElementById("mapa" + index)).append("svg")
              .attr("width", width)
              .attr("height", height);
            var g = svg.append("g");

            var div = d3.select(document.getElementById("mapa" + index)).append("div")
              .attr("class", "tooltip")
              .style("opacity", 0);

            g.selectAll("path")
              .data(topojson.object(topology, topology.objects.elementos).geometries)
              .enter().append("path")
              .attr("d", d3.geo.path().projection(projection))              
              .style("stroke", "#333")
              .attr('fill', function (d, i) {    
                var nombre = $scope.eliminarDiacriticos(d.properties.NAME);
                nombre = nombre.toUpperCase();     
                if ($scope.indicadores[index].grafica[nombre])   
                return $scope.indicadores[index].grafica[nombre].color;
                else return "#FFFFFF";
              })
              .on("mouseover", function (d) {
                var nombre = $scope.eliminarDiacriticos(d.properties.NAME);
                nombre = nombre.toUpperCase();
                var valor = $scope.indicadores[index].grafica[nombre] ? $scope.indicadores[index].grafica[nombre].value : "--";
                var menosX = $scope.indicadores[index].full_screen ? 0 : 200;
                var menosY = $scope.indicadores[index].full_screen ? 0 : 150;

                d3.select(this).transition().duration(300).style("opacity", 1);
                div.transition().duration(300)
                  .style("opacity", 1)
                div.text(d.properties.NAME + ": " + valor)
                  .style("left", (d3.event.pageX - menosX) + "px")
                  .style("top",  (d3.event.pageY - menosY) + "px");
              })
              .on("mouseout", function () {
                d3.select(this)
                  .transition().duration(300)
                  .style("opacity", 0.8);
                div.transition().duration(300)
                  .style("opacity", 0);
              }).on("click", function (d) {
                var nombre = $scope.eliminarDiacriticos(d.properties.NAME);
                nombre = nombre.toUpperCase();
                var valor = $scope.indicadores[index].grafica[nombre] ? $scope.indicadores[index].grafica[nombre] : null;

                if(valor){
                  $scope.indicadores[index].dimension++;
                  $scope.indicadores[index].filtros.push({
                    codigo: $scope.indicadores[index].dimensiones[$scope.indicadores[index].dimension - 1].trim(),
                    valor: valor.label
                  });
                  $scope.agregarIndicadorDimension($scope.indicadores[index].dimension, valor.index);
                }
              });


          });
        }
      }, 100);
    }
    
  };

  $scope.actualizarMapa = function(index){
    $scope.dibujarMapa(index, false);
  }

  $scope.eliminarDiacriticos = function(texto) {
    return texto.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
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
    setTimeout(function() {
      mywindow.contentWindow.print();
    }, 500);
    setTimeout(function() {
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

  $scope.quitarFiltro = function(posicion) {
    $scope.indicadores[posicion].dimension = 0;
    $scope.indicadores[posicion].filtros = [];
    $scope.agregarIndicadorDimension(0, posicion);
  };
  $scope.accion = {
    acciones: "",
    observaciones: "",
    responsables: "",
    lista: []
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#guardarAccionSala
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que guarda las acciones de la sala
   */
  $scope.guardarAccionSala = function() {
    $scope.sala_cargando = true;
    Crud.crear(
      "../api/v1/tablero/salaAccion/" + $scope.sala.id,
      $scope.accion,
      "application/json",
      function(data) {
        if (data.status == 200) {
          $scope.accion.lista = data.data;
          alert($("#guardar_sala_accion_ok").html());
        } else {
          alert($("#guardar_sala_accion_error").html());
        }
        $scope.sala_cargando = false;
      },
      function(e) {
        alert($("#guardar_sala_accion_error").html());
        $scope.sala_cargando = false;
      }
    );
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#listaAccionSala
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que muestra la lista de las accines de la sala
   */
  $scope.listaAccionSala = function() {
    $scope.sala_cargando = true;
    Crud.lista(
      "../api/v1/tablero/salaAccion/" + $scope.sala.id,
      function(data) {
        if (data.status == 200) {
          $scope.accion.lista = data.data;
        }
        $scope.sala_cargando = false;
      },
      function(e) {
        $scope.sala_cargando = false;
      }
    );
  };
  $scope.comentarios_compartir;
  $scope.compartir = {
    usuarios_con_cuenta: [],
    usuarios_sin_cuenta: "",
    lista_usuarios: [],
    comentarios: "",
    correo: 0,
    tiempo_dias: 1,
    es_permanente: false
  };
  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#usuariosSala
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que muestra la lista de usuarios para compartir la sala
   */
  $scope.usuariosSala = function() {
    $scope.sala_cargando = true;
    Crud.lista(
      "../api/v1/tablero/usuariosSala/" + $scope.sala.id,
      function(data) {
        if (data.status == 200) {
          $scope.compartir.lista_usuarios = data.data;
        }
        $scope.sala_cargando = false;
      },
      function(e) {
        $scope.sala_cargando = false;
      }
    );
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#comentarioSala
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que muestra la lista comentarios de la sala compartida
   */
  $scope.comentarioSala = function() {
    $scope.sala_cargando = true;
    Crud.lista(
      "../api/v1/tablero/comentarioSala/" + $scope.sala.id,
      function(data) {
        if (data.status == 200) {
          $scope.comentarios_compartir = data.html;
        }
        $scope.sala_cargando = false;
      },
      function(e) {
        $scope.sala_cargando = false;
      }
    );
  };

  /**
   * @ngdoc method
   * @name Tablero.TableroCtrl#comentarioSalaGuardar
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que guarda las acciones de la sala
   */
  $scope.comentarioSalaGuardar = function() {
    $scope.sala_cargando = true;
    Crud.crear(
      "../api/v1/tablero/comentarioSala/" + $scope.sala.id,
      $scope.compartir,
      "application/json",
      function(data) {
        if (data.status == 200) {
          $scope.comentarios_compartir += data.html;
          alert($("#guardar_sala_compartir_ok").html());
        } else {
          alert($("#guardar_sala_compartir_error").html());
        }
        $scope.sala_cargando = false;
      },
      function(e) {
        alert($("#guardar_sala_compartir_error").html());
        $scope.sala_cargando = false;
      }
    );
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

  $scope.cambiarPermanencia = function(){
    $scope.compartir.es_permanente = !$scope.compartir.es_permanente;
  };

  $scope.moverIndicador = function (index, item) {
    if(item.index < index)
      index--;
    if (!angular.isUndefined(index)) {      
      setTimeout(function() {
        var tamanio = $scope.indicadores[index].configuracion.height;
        $scope.indicadores[index].configuracion.height = 100;
        $scope.actualizarsGrafica(index, false);
        setTimeout(function() {
          $scope.indicadores[index].configuracion.height = tamanio;
          $scope.actualizarsGrafica(index, false);
        }, 100);
      }, 100);  
        
    }
    return item;
  };
  $scope.ruta = '';
  $scope.asignarURL = function (ruta) {
    $scope.ruta = ruta;
  }



  $scope.abrirModalFiltrosGenerales =  () => {

      let dimensionesExistentes = [];
      let dimensionesAux = [];
      angular.forEach( $scope.indicadores, function(ind) {
          angular.forEach( ind.informacion.dimensiones, function(dimension, key) {
            
            if ( !dimensionesExistentes.includes(key) ) {
                dimensionesExistentes.push(key);
                dimensionesAux.push({'datos': dimension, 'codigo': key});
            }
          });
      });

      $scope.dimensionesGenerales = dimensionesAux.sort((a,b) => { return a.datos.descripcion.localeCompare(b.datos.descripcion) });
      jQuery('#modalFiltrosGenerales').modal('show');
  };

  $scope.recuperarValoresDimensionGeneral =  () => {
      $scope.filtroGeneralEsCatalogo = false;
      if( $scope.dimensionGeneral.split('id_').length > 1 ){
          $scope.filtroGeneralEsCatalogo = true;

          $scope.cargarCatalogo(
              "../api/v1/tablero/datosCatalogo/"+$scope.dimensionGeneral,
              $scope.datosCatalogo,
              "cc_uso"
          );
      }
  };
  $scope.limpiarSelect = function(){
    document.getElementById('select2-chosen-2').innerHTML = '';
  }
  $scope.aplicarFiltroGeneral = () => {
      
      let nuevosFiltros = [];
      let existe = false;
      let etiqueta = '';
      let dimension = $scope.dimensionGeneral;
      let valor = ( $scope.filtroGeneralEsCatalogo ) ? jQuery('#valorFiltro2').val() : jQuery('#valorFiltro1').val();

      if ( dimension != '?' && valor.trim() !='' ) {
          angular.forEach($scope.indicadores, function (ind) {
              nuevosFiltros = [];
              angular.forEach(ind.filtros, function (filtro) {
                  //Ya tenía el filtro, modificar su valor
                  
                  if (filtro.codigo == dimension) {
                      filtro.valor = valor;
                      existe = true;
                      etiqueta = filtro.etiqueta;
                  }
                  nuevosFiltros.push(filtro);
              });

              //Si no existe agregarlo al principio
              if (!existe) {
                  //Verificar primero si existe en el listado de dimensiones del indicador
                  if ( ind.dimensiones.includes(dimension)) {
                      nuevosFiltros.unshift({
                          'codigo': dimension,
                          'etiqueta': etiqueta,
                          'valor': valor
                      });
                  }
              }

              ind.filtros = nuevosFiltros;
              $scope.agregarIndicadorDimension(ind.dimension, ind.posicion - 1);
          });
      }
    };
});
