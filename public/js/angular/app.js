/**
 * @ngdoc service
 * @name App.service:app
 * @description
 * Configuracion del archivo de cargas de vendors angulares
 * @example
   <example module="App">
     <file name="index.html">
         <script>
          var App = angular.module("App", ["ui.bootstrap", "ngStorage", "ngRadialGauge", "nvd3"], function () {});
         </script>
     </file>
    </example>
 */

var App = angular.module("App", ["ui.bootstrap", "ngStorage", "ngRadialGauge", "nvd3"], function (
    $interpolateProvider
) {
    $interpolateProvider.startSymbol("<%");
    $interpolateProvider.endSymbol("%>");
});

/**
   * @ngdoc service
   * @name App.service:Crud
   *
   * @description
   * Factory que se encarga de las peticiones a la api, este servicio se utiliza en los controladores
   * @param {$http} $http Carga los metodos http
     @example
     <example module="App">
       <file name="index.html">
           <script>
            App.controller("TableroCtrl", function(Crud){
              Crud.lista(
                url,
                function(data) {
                  exito;
                },
                function(e) {
                  error;
                }
              );
            });
           </script>
       </file>
      </example>
   */
App.factory("Crud", function($http) {
  return {
    lista: function(url, success, error) {
      $http
        .get(url)
        .success(success)
        .error(error);
    },
    ver: function(url, id, success, error) {
      $http
        .get(url + "/" + id)
        .success(success)
        .error(error);
    },
    crear: function(url, data, type, success, error) {
      $http
        .post(url, data, {
          headers: {
            "Content-Type": type ? type : "application/x-www-form-urlencoded"
          }
        })
        .success(success)
        .error(error);
    },
    editar: function(url, id, data, type, success, error) {
      $http
        .put(url + "/" + id, data, {
          headers: {
            "Content-Type": type ? type : "application/x-www-form-urlencoded"
          }
        })
        .success(success)
        .error(error);
    },
    consulta: function(url, success, error) {
      $http
        .get(url)
        .success(success)
        .error(error);
    },
    eliminar: function(url, id, success, error) {
      $http
        .delete(url + "/" + id)
        .success(success)
        .error(error);
    }
  };
})

  /**
   * @ngdoc service
   * @name App.service:exportarTabla
   *
   * @description
   * Directive que permite la exportacion de una tabla a xls mediante un elemento que tenga el id exportable
   *  @example
      <example module="App">
       <file name="index.html">
          <button type="button" class="btn btn-primary" exportar-tabla>
              <i class="glyphicon glyphicon-file"></i> Exportar a xls
          </button>
          <hr>
          <h2 id="tituloReporte">Titulo del reporte</h2>
          <hr>
          <div id="exportable">
              <table class="table">
                  <thead>
                      <tr>
                          <th>Titulo 1</th>
                          <th>Titulo 2</th>
                          <th>Titulo 3</th>
                      </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Ejemplo uno</td>
                      <td>Ejemplo dos</td>
                      <td>Ejemplo tres</td>
                    </tr>
                  </tbody>
              </table>
          </div>
        </file>
      </example>
   */
  .directive("exportarTabla", function() {
    return {
      restrict: "A",
      link: function(scope, element, attrs) {
        element.bind("click", function(evt) {
          evt.preventDefault();
          var titulo = $("#tituloReporte").html();
          var excelData =
            "<table><tr><th colspan='17'><h1>" +
            titulo +
            " <h1></th></tr></table>";

          excelData += document.getElementById("exportable").innerHTML;
          var blob = new Blob([excelData], {
            type: "text/comma-separated-values;charset=utf-8"
          });
          saveAs(blob, titulo + ".xls");
        });
      }
    };
  })
  /**
   * @ngdoc service
   * @name App.service:imprimirDiv
   * @description
   * Directive que permite la exportacion de una div (que contenga la clase imprimir) a pdf
   * @example
     <example module="App">
       <file name="index.html">
          <button type="button" class="btn btn-primary" imprimir-div=".imprimir">
              <i class="glyphicon glyphicon-file"></i> Exportar a pdf
          </button>
          <div class="imprimir">
            <hr>
            <h2 id="tituloReporte">Titulo del reporte</h2>
            <hr>
            <div id="exportable">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Titulo 1</th>
                            <th>Titulo 2</th>
                            <th>Titulo 3</th>
                        </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Ejemplo uno</td>
                        <td>Ejemplo dos</td>
                        <td>Ejemplo tres</td>
                      </tr>
                    </tbody>
                </table>
            </div>
          </div>
        </file>
      </example>
   */
  .directive("imprimirDiv", function() {
    return {
      restrict: "A",
      link: function(scope, element, attrs) {
        // evento que dispara el generador de impresion
        element.bind("click", function(evt) {
          evt.preventDefault();
          var elem = document.querySelector(attrs.imprimirDiv);
          PrintElem(elem);
        });
        // obtener el área a imprimir y se extrae su contenido html
        function PrintElem(elem) {
          PrintWithIframe(angular.element(elem).html());
        }
        // generar el ddocumento a imprimir
        function PrintWithIframe(data) {
          // comprobar que el contenedor de impresion no exista
          if (!angular.isUndefined(document.getElementById("printf"))) {
            // crear el contenedor para guardar el elemento a imprimir
            var iframe = document.createElement("iframe");
            iframe.setAttribute("id", "printf");
            iframe.setAttribute("style", "display:none");
            document.body.appendChild(iframe);

            var titulo = $("#tituloReporte").html();

            var mywindow = document.getElementById("printf");
            mywindow.contentWindow.document.write(
              '<html lang="es" ng-app="App">' +
                " <head>" +
                ' <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' +
                ' <meta name="charset" content="UTF-8">' +
                ' <meta name="viewport" content="wianioh=device-wianioh, initial-scale=1, maximum-scale=1">' +
                ' <meta name="apple-mobile-web-app-capable" content="yes">' +
                ' <title></title><link rel="stylesheet" href="' +
                attrs.ruta +
                'css/print.css"/>' +
                ' <title></title><link rel="stylesheet" href="' +
                attrs.ruta +
                '/bundles/sonatacore/vendor/bootstrap/dist/css/bootstrap.min.css" media="screen"/>' +
                ' <meta name="viewport" content="initial-scale=1" />' +
                " </head>" +
                ' <body ng-controller="MatrizCtrl">' +
                " <h1>" +
                titulo +
                " <h1>" +
                data +
                ' <script src="' +
                attrs.ruta +
                'js/angular/angular.js"></script>' +
                " </body>" +
                " </html>"
            );

            setTimeout(function() {
              // lanzar la sentencia imprimir
              mywindow.contentWindow.print();
            }, 500);
            setTimeout(function() {
              // remover el contenedor de impresión
              document.body.removeChild(iframe);
            }, 2000);
          }
          return true;
        }
      }
    };
  })

  /**
   * @ngdoc service
   * @name App.service:toHtml
   * @description
   * Directive que permite interpretar codigo html en un div
   * @example
     <example module="App">
       <file name="index.html">
        <div to-html="'<strong>texto con html</strong>'"></div>
       </file>
      </example>
   */
  .directive("toHtml", function() {
    return {
      restrict: "A",
      link: function(scope, el, attrs) {
        el.html(scope.$eval(attrs.toHtml));
      }
    };
  })

  /**
   * @ngdoc service
   * @name App.service:stringToNumber
   * @description
   * Directive que convierte esl texto entrante a un numero
   * @example
     <example module="App">
       <file name="index.html">
        <input type="number" min="0" step="0.01" class="form-control" string-to-number ng-model="real">
       </file>
      </example>
   
   */
  .directive("stringToNumber", function() {
    return {
      require: "ngModel",
      link: function(scope, element, attrs, ngModel) {
        ngModel.$parsers.push(function(value) {
          return "" + value;
        });
        ngModel.$formatters.push(function(value) {
          return parseFloat(value, 10);
        });
      }
    };
  });

