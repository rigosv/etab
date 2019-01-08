/**
   * @ngdoc object
   * @name Pivot.PivotCtrl
   * @description
   * Controlador general que maneja el tablero
   */

App.controller("PivotCtrl", function (
    $scope,
    $http,
    $localStorage,
    $window,
    $filter,
    Crud
) {
    $scope.abrio_indicador = false;

    $scope.intento1 = 0;
    $scope.intento2 = 0;

    $scope.tamanoHeight = $window.innerHeight / 1.5;
    $scope.$watch(function() {
        return window.innerHeight;
      }, function(value) {
        $scope.tamanoHeight = value / 1.5;
      });
    $scope.posicion = 0;
    $scope.indicadores = [];
    $scope.indicadores[0] = null;
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
     * @name Pivot.PivotCtrl#cargarCatalogo
     * @methodOf Pivot.PivotCtrl
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
     * @name Pivot.PivotCtrl#comboDependiente
     * @methodOf Pivot.PivotCtrl
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
     * @name Pivot.PivotCtrl#cargarIndicadores
     * @methodOf Pivot.PivotCtrl
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

    /**
     * @ngdoc method
     * @name Pivot.PivotCtrl#bsucarIndicador
     * @methodOf Pivot.PivotCtrl
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



    /**
     * @ngdoc method
     * @name Pivot.PivotCtrl#cargarIndicadores
     * @methodOf Pivot.PivotCtrl
     *
     * @description
     * funcion que cargar los datos del inidcador
     * @param {item} item inidcador seleccionado de la lista
     */
    $scope.agregarIndicador = function(item, dimension = "") {
      if (item) {        
        $scope.abrio_indicador = true;
        $scope.indicadores[0] = {
          cargando: true,
          filtros: [],
          error: "",
          id: item.id,
          nombre: item.nombre,
          es_favorito: item.es_favorito,
          dimensiones: item.campos_indicador.split(","),
          dimension: 0,
          posicion: 0,
          sql: "",
          ficha: ""
        };
        $scope.indicadores[0].posicion = 1;               

        var renderers = $.extend(
          $.pivotUtilities.renderers,
          $.pivotUtilities.gchart_renderers
        );
        
        var json = { filtros: "", ver_sql: false, tendencia: false };
        
        Crud.crear("../api/v1/tablero/datosPivot/" + item.id, json, "application/json", function(data) {
            if (data.status == 200) {
              $scope.indicadores[0].data = data.data;
              $scope.indicadores[0].informacion = data.informacion;

              $("#output").pivotUI(data.data, { renderers: renderers });
              $("#modalIndicadores").modal("toggle");
            } else {
              $scope.indicadores[0].error = "Warning";
            }
            $scope.indicadores[0].cargando = false;
            setTimeout(function() {
              $scope.indicadores[0].error = "";
            }, 3000);
          }, function(e) {
            $scope.indicadores[0].error = "Error";
            $scope.indicadores[0].cargando = false;
            setTimeout(function() {
              $scope.indicadores[0].error = "";
            }, 3000);
          });
      }
    };

    /**
     * @ngdoc method
     * @name Pivot.PivotCtrl#cargarIndicadores
     * @methodOf Pivot.PivotCtrl
     *
     * @description
     * funcion que cargar la informacion del sql
     */
    $scope.verFichaIndicador = function() {
      if ($scope.indicadores[0].ficha == "") {
        $scope.indicadores[0].cargando = true;

        Crud.ver("../api/v1/tablero/fichaIndicador", $scope.indicadores[0].id, function(data) {
            if (data.status == 200) {
              $scope.indicadores[0].ficha = data.data;
              $("#modalFicha").modal("toggle");
            }
            $scope.indicadores[0].cargando = false;
          }, function(e) {
            $scope.indicadores[0].cargando = false;
          });
      } else {
        $("#modalFicha").modal("toggle");
      }
    };

    /**
     * @ngdoc method
     * @name Pivot.PivotCtrl#verTablaDatosIndicador
     * @methodOf Pivot.PivotCtrl
     *
     * @description
     * funcion que muestra la informacion de los datos del indicador
     */
    $scope.verTablaDatosIndicador = function(index) {
      $("#modalTablaDatos").modal("toggle");
    };

    /**
     * @ngdoc method
     * @name Pivot.PivotCtrl#agregarFavorito
     * @methodOf Pivot.PivotCtrl
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
     * @name Pivot.PivotCtrl#imprimirMensaje
     * @methodOf Pivot.PivotCtrl
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
     * @name Pivot.PivotCtrl#exportarImagen
     * @methodOf Pivot.PivotCtrl
     *
     * @description
     * funcion que exporta el grafico al formato seleccionado
     */
    $scope.exportarImagen = function() {
      html2canvas($("#contenedor_tablero"), {
        onrendered: function(canvas) {
          theCanvas = canvas;

          canvas.toBlob(function(blob) {
            saveAs(blob, $scope.indicadores[0].nombre + ".png");
          });
        }
      });
    };

    /**
     * @ngdoc method
     * @name Pivot.PivotCtrl#exportar_excel
     * @methodOf Pivot.PivotCtrl
     *
     * @description
     * funcion que exporta a archivo xls
     * @param {id} id del elemento html que contiene el area a exportar
     * @param {titulo} titulo que contendra el elemento exportado
     */
    $scope.exportar_excel = function (id, titulo, nombre = true) {
      let colspan = $("#" + id).find("tr:first th").length;
      let excelData = nombre ? "<table><tr><th colspan='" + colspan + "'><h4>" + titulo + " <h4></th></tr></table>" : '';

      excelData += document.getElementById(id).innerHTML;
      let blob = new Blob([excelData], {
        type: "text/comma-separated-values;charset=utf-8"
      });
      saveAs(blob, titulo + ".xls");
    };

    /**
     * @ngdoc method
     * @name Pivot.PivotCtrl#exportar_pdf
     * @methodOf Pivot.PivotCtrl
     *
     * @description
     * funcion que exporta a archivo pdf
     * @param {id} id del elemento html que contiene el area a exportar
     * @param {titulo} titulo que contendra el elemento exportado
     */
    $scope.exportar_pdf = function(id, titulo, nombre = true) {
        
        var contenido = document.getElementById(id).innerHTML;
        html = '<html lang="es">' 
        + " <head>" 
        + ' <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' 
        + ' <meta name="charset" content="UTF-8">' 
        + ' <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">' 
        + ' <meta name="apple-mobile-web-app-capable" content="yes">' 
        + ' <title>PDF</title> <meta name="viewport" content="initial-scale=1" />' 
        + ' <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">' 
        + " </head>" 
        + " <body>" ;
        html += nombre ? "<h4>" + titulo + "</h4>" : '' ;
        html += contenido 
        + " </body>" 
        
        + " </html>";
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
     * @name Pivot.PivotCtrl#exportar_csv
     * @methodOf Pivot.PivotCtrl
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

    
    $scope.opcionesExport = ['csv', 'xls', 'pdf'];

    $scope.td_tipo_exportar = 'csv';
    $scope.asignarTipoExport = function(option) {
        $scope.td_tipo_exportar = option;
    };
  });
