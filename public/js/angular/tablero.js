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

    $scope.dateOptions = { formatYear: "yyyy", startingDay: 1, minMode: "year" };

    $scope.formats = ["yyyy"];
    $scope.format = $scope.formats[0];

    $scope.status = { opened: false };

    $scope.tamanoHeight = $window.innerHeight / 1.5;
    $scope.$watch(function() {
        return window.innerHeight;
      }, function(value) {
        $scope.tamanoHeight = value / 1.5;
      });
    $scope.indicadores = [];
    $scope.clasificacion_uso = "";
    $scope.clasificacion_tecnica = "";

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
    $scope.cargarCatalogo = function(url, modelo, cargando) {
      modelo.length = 0;
      $scope[cargando] = true;
      Crud.lista(url, function(data) {
          if (data.status == 200) {
            $scope.intento1 = 0;
            angular.forEach(data.data, function(value, key) {
              modelo.push(value);
            });
          }
          $scope[cargando] = false;
        }, function(e) {
          setTimeout(function() {
            if ($scope.intento1 < 1) {
              $scope.cargarCatalogo(url, modelo, cargando);
              $scope.intento1++;
            } else $scope[cargando] = false;
          }, 200);
        });
    };
    // cargar los catalogos para el indicador
    $scope.cargarCatalogo("../api/v1/tablero/clasificacionUso", $scope.clasificaciones_usos, "cc_uso");
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
    $scope.comboDependiente = function(url, modelo, id, cargando) {
      modelo.length = 0;
      $scope[cargando] = true;
      Crud.lista(url + "?id=" + id, function(data) {
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
        }, function(e) {
          $scope[cargando] = false;
          setTimeout(function() {
            if ($scope.intento2 < 1) {
              $scope.comboDependiente(url, modelo, cargando);
              $scope.intento2++;
            }
          }, 200);
        });
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
      Crud.lista(url, function(data) {
          if (data.status == 200) {
            $scope.intento1 = 0;
            angular.forEach(data.data, function(value, key) {
              modelo.push(value);
            });
          }
          $scope[cargando] = false;
        }, function(e) {
          setTimeout(function() {
            if ($scope.intento1 < 1) {
              $scope.cargarIndicadores(url, modelo, cargando);
              $scope.intento1++;
            } else $scope[cargando] = false;
          }, 200);
        });
    };
    $scope.titulo_sala = "";
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
      Crud.lista("../api/v1/tablero/listaSalas", function(data) {
          if (data.status == 200) {
            $scope.intento1 = 0;
            $scope.salas = data.data;
            $scope.salas_grupos = data.salas_grupos;
            $scope.salas_propias = data.salas_propias;
          }
          $scope["cc_salas"] = false;
        }, function(e) {
          setTimeout(function() {
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
    $scope.bsucarIndicador = function(keyEvent) {
      if (keyEvent.which === 13) {
        $scope.inidcadores_busqueda = [];
        $scope["cc_buscar"] = true;
        Crud.lista("../api/v1/tablero/listaIndicadores?tipo=busqueda&busqueda=" + $scope.buscar_busqueda, function(data) {
            if (data.status == 200) {
              $scope.intento1 = 0;
              $scope.inidcadores_busqueda = data.data;
              $scope.buscar_busqueda = "";
            }
            $scope["cc_buscar"] = false;
          }, function(e) {
            setTimeout(function() {
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
    $scope.agregarIndicador = function(item) {
      if (item) {
        if (angular.isUndefined($scope.tablero_indicador[item.id])) $scope.tablero_indicador[item.id] = 0;

        $scope.indicadores.push({
          cargando: true,
          breadcrumb: [],
          filtros: [],
          error: "",
          id: item.id,
          nombre: item.nombre,
          es_favorito: item.es_favorito,
          dimensiones: item.campos_indicador.split(","),
          dimension: 0,
          sql: "",
          ficha: "",
          full_screen: false,
          tipo_grafica: "discreteBarChart"
        });
        $scope.tablero_indicador[item.id]++;
        var index = $scope.indicadores.length - 1;
        $scope.opcionesGraficas(index, "discreteBarChart", $scope.indicadores[index].dimensiones[0], item.unidad_medida, "280");

        var json = { filtros: "", ver_sql: false };
        Crud.crear("../api/v1/tablero/datosIndicador/" + item.id + "/" + $scope.indicadores[index].dimensiones[0], json, "application/json", function(data) {
            if (data.status == 200) {
              $scope.indicadores[index].data = data.data;
              $scope.indicadores[index].informacion = data.informacion;
              $scope.indicadores[index].grafica = [];
              var grafica = [];
              angular.forEach(data.data, function(val, key) {
                color = "";
                angular.forEach(data.informacion.rangos, function(v1, k1) {
                  if (val.measure >= v1.limite_inf && val.measure <= v1.limite_sup) {
                    color = v1.color;
                  }
                });
                if (angular.isUndefined(grafica[val.category])) {
                  grafica[key] = { key: val.category, values: [] };
                }
                grafica[key].values.push({
                  color: color,
                  label: val.category,
                  value: parseFloat(val.measure),
                  index: index,
                  dimension: 0
                });
              });
              $scope.indicadores[index].grafica = grafica;
            } else {
              $scope.indicadores[index].error = "Warning";
            }
            $scope.indicadores[index].cargando = false;
            setTimeout(function() {
              $scope.indicadores[index].error = "";
            }, 3000);
          }, function(e) {
            $scope.indicadores[index].error = "Error";
            $scope.indicadores[index].cargando = false;
            setTimeout(function() {
              $scope.indicadores[index].error = "";
            }, 3000);
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
    $scope.agregarIndicadorDimension = function(dimension, index) {
      if (!angular.isUndefined($scope.indicadores[index].dimensiones[dimension])) {
        $scope.indicadores[index].cargando = true;
        $scope.opcionesGraficas(index, $scope.indicadores[index].tipo_grafica, $scope.indicadores[index].dimensiones[dimension], $scope.indicadores[index].informacion.unidad_medida, "280");
        var json = { filtros: $scope.indicadores[index].filtros, ver_sql: false };
        Crud.crear("../api/v1/tablero/datosIndicador/" + $scope.indicadores[index].id + "/" + $scope.indicadores[index].dimensiones[dimension].trim(), json, "application/json", function(data) {
            if (data.status == 200) {
              $scope.indicadores[index].data = data.data;

              var grafica = [];
              angular.forEach(data.data, function(val, key) {
                color = "";
                angular.forEach(data.informacion.rangos, function(v1, k1) {
                  if (val.measure >= v1.limite_inf && val.measure <= v1.limite_sup) {
                    color = v1.color;
                  }
                });
                if (angular.isUndefined(grafica[val.category])) {
                  grafica[key] = { key: val.category, values: [] };
                }
                grafica[key].values.push({
                  color: color,
                  label: val.category,
                  value: parseFloat(val.measure),
                  index: index,
                  dimension: dimension
                });
              });
              $scope.indicadores[index].grafica = grafica;
            } else {
              $scope.indicadores[index].dimension--;
              $scope.indicadores[index].error = "Warning";
            }
            $scope.indicadores[index].cargando = false;
            setTimeout(function() {
              $scope.indicadores[index].error = "";
            }, 3000);
          }, function(e) {
            $scope.indicadores[index].dimension--;
            $scope.indicadores[index].error = "Error";
            $scope.indicadores[index].cargando = false;
            setTimeout(function() {
              $scope.indicadores[index].error = "";
            }, 3000);
          });
      } else {
        $scope.indicadores[index].dimension = $scope.indicadores[index].dimensiones.length - 1;
        $scope.indicadores[index].error = "Success";
        setTimeout(function() {
          $scope.indicadores[index].error = "";
        }, 3000);
      }
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
      if ($scope.indicadores[index].sql == "") {
        $scope.indicadores[index].cargando = true;
        var json = { filtros: "", ver_sql: true };
        var dimension = $scope.indicadores[index].dimension;
        Crud.crear("../api/v1/tablero/datosIndicador/" + $scope.indicadores[index].id + "/" + $scope.indicadores[index].dimensiones[dimension], json, "application/json", function(data) {
            if (data.status == 200) {
              $scope.indicadores[index].sql = data.data;
              $("#sql_indicador").html(sqlFormatter.format(data.data));
              $("#modalSQL").modal("toggle");
            }
            $scope.indicadores[index].cargando = false;
          }, function(e) {
            $scope.indicadores[index].cargando = false;
          });
      } else {
        $("#sql_indicador").html(sqlFormatter.format($scope.indicadores[index].sql));
        $("#modalSQL").modal("toggle");
      }
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
      if ($scope.indicadores[index].ficha == "") {
        $scope.indicadores[index].cargando = true;

        Crud.ver("../api/v1/tablero/fichaIndicador", $scope.indicadores[index].id, function(data) {
            if (data.status == 200) {
              $scope.indicadores[index].ficha = data.data;
              $("#modalFicha").modal("toggle");
            }
            $scope.indicadores[index].cargando = false;
          }, function(e) {
            $scope.indicadores[index].cargando = false;
          });
      } else {
        $("#modalFicha").modal("toggle");
      }
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
        Crud.crear("../api/v1/tablero/indicadorFavorito", json, "application/json", function(data) {
            if (data.status == 200) {
              item.es_favorito = data.data;
            }
          }, function(e) {});
      }
    };

    /**
     * @ngdoc method
     * @name Tablero.TableroCtrl#filtrarIndicador
     * @methodOf Tablero.TableroCtrl
     *
     * @description
     * funcion para filtrar la información del indicador
     * @param {index} index posicion en la lista de graficas
     * @param {filtrar} filtrar bandera para agregar o quitar el filtro
     */
    $scope.filtrarIndicador = function(index, filtrar) {
      if (item) {
        var json = { id: item.id, es_favorito: item.es_favorito };
        Crud.crear("../api/v1/tablero/indicadorFavorito", json, "application/json", function(data) {
            if (data.status == 200) {
              item.es_favorito = data.data;
            }
          }, function(e) {});
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
      if ($scope.indicadores[index].full_screen) tamano = $window.innerHeight / 1.28;
      $scope.indicadores[index].options = { chart: { type: tipo, height: tamano, margin: { top: 20, right: 20, bottom: 50, left: 55 }, x: function(d) {
            return d.label;
          }, y: function(d) {
            return d.value + 1e-10;
          }, showValues: true, valueFormat: function(d) {
            return d3.format(",.4f")(d);
          }, duration: 500, xAxis: { axisLabel: labelx }, yAxis: { axisLabel: labely, axisLabelDistance: -10 }, callback: function(chart) {
            chart.discretebar.dispatch.on("elementClick", function(e) {
              $scope.indicadores[index].dimension++;
              $scope.indicadores[index].filtros.push({
                codigo: $scope.indicadores[index].dimensiones[
                  $scope.indicadores[index].dimension - 1
                ].trim(),
                valor: e.data.label
              });
              $scope.agregarIndicadorDimension($scope.indicadores[index].dimension, e.data.index);
            });
          } } };
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
      if (hacer) $scope.indicadores[index].full_screen = !$scope.indicadores[index].full_screen;

      var tamano = 280;
      let grafica = $scope.indicadores[index].grafica;
      $scope.indicadores[index].grafica = [];
      let dimension = $scope.indicadores[index].dimension;
      setTimeout(() => {
        $scope.indicadores[index].grafica = grafica;
        $scope.opcionesGraficas(index, $scope.indicadores[index].tipo_grafica, $scope.indicadores[index].dimensiones[dimension], $scope.indicadores[index].informacion.unidad_medida, tamano);
        document.getElementById("update" + index).click();
      }, 200);
    };

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
      id = angular.isUndefined(id) ? "#feedback_bar" : "#result_factura_test" + ", #" + id;

      $(id).html('<div class="alert alert-' + tipo + ' alert-dismissable" >' + '<i class="fa fa-' + tipo + '"></i> ' + '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>' + "<b>Alert! </b>" + mensaje + "</div>");
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

      var valor = '<?xml version="1.0" encoding="utf-8"?>' + $("#svg" + index)
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
        saveSvgAsPng(document.getElementById("exportsvg" + index), titulo + ".png");
      }
    };

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
          enc = String.fromCharCode((c1 >> 12) | 224, ((c1 >> 6) & 63) | 128, (c1 & 63) | 128);
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
          enc = String.fromCharCode((c1 >> 18) | 240, ((c1 >> 12) & 63) | 128, ((c1 >> 6) & 63) | 128, (c1 & 63) | 128);
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

    $scope.exportar_excel = function(id, titulo) {
      let colspan = $("#" + id).find("tr:first th").length;
      let excelData = "<table><tr><th colspan='" + colspan + "'><h4>" + titulo + " <h4></th></tr></table>";

      excelData += document.getElementById(id).innerHTML;
      let blob = new Blob([excelData], {
        type: "text/comma-separated-values;charset=utf-8"
      });
      saveAs(blob, titulo + ".xls");
    };

    $scope.exportar_pdf = function(id, titulo) {
      var html = document.getElementById(id).innerHTML;
      html = '<html lang="es">' + " <head>" + ' <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' + ' <meta name="charset" content="UTF-8">' + ' <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">' + ' <meta name="apple-mobile-web-app-capable" content="yes">' + ' <title>PDF</title> <meta name="viewport" content="initial-scale=1" />' + ' <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">' + " </head>" + " <body>" + "<h4>" + titulo + "</h4>" + html + " </body>" + " </html>";
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
  });
