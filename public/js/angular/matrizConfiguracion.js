/**
 * @ngdoc object
 * @name Matriz.MatrizConfiguracionCtrl
 * @description
 * Controlador general que maneja la configuracion de la matriz de seguimiento
 */

App.controller("MatrizConfiguracionCtrl", function (
  $scope,
  $http,
  $localStorage,
  $window,
  $filter,
  Crud
) {
  $scope.num = null;
  $scope.tab = [];
  $scope.matriz = 1;
  $scope.today = function () {
    $scope.anio = new Date();
  };

  $scope.representaciones = [
    {
      id: 1,
      descripcion: "Mensual"
    },
    {
      id: 2,
      descripcion: "Bimestral"
    },
    {
      id: 3,
      descripcion: "Trimestral"
    },
    {
      id: 4,
      descripcion: "Cuatrimestral"
    },
    {
      id: 6,
      descripcion: "Semestral"
    }
  ];
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

  $scope.cambioEsFormula = function(id, relacion){    
    if(relacion.es_formula){  
      $("#" + id).parent().addClass("checked");
    }else{
      $("#" + id).parent().removeClass("checked");
    }
  }

  $scope.dato = {
    id: null,
    nombre: '',
    descripcion: '',
    indicadores_desempeno: [],
    usuarios: []
  };

  $scope.nuevo = 0;
  $scope.direccion = location.href;
  $scope.direccion = $scope.direccion.split("/");

  if ($scope.direccion[$scope.direccion.length - 1] == 'edit') {
    $scope.dato.id = $scope.direccion[$scope.direccion.length - 2];
  } else {
    $scope.nuevo = 1;
  }
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
  $scope.url = '';
  $scope.ruta = "";

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
   * @name Matriz.MatrizConfiguracionCtrl#cargarCatalogo
   * @methodOf Matriz.MatrizConfiguracionCtrl
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

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#comboDependiente
   * @methodOf Matriz.MatrizConfiguracionCtrl
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
      $scope.url + url + "?id=" + id,
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
   * @name Matriz.MatrizConfiguracionCtrl#cargarIndicadores
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion que cargar la lista de indicadores disponibles y clasificados
   * @param {url} url URL en la api para la peticion
   * @param {modelo} modelo modelo donde se carga el resultado
   * @param {cargando} cargando bandera para mostrar la animacion cargando
   */
  $scope.cargarIndicadores = function (url, modelo, cargando) {
    modelo.length = 0;
    $scope[cargando] = true;
    Crud.lista(
      $scope.url + url,
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
            $scope.cargarIndicadores(url, modelo, cargando);
            $scope.intento1++;
          } else $scope[cargando] = false;
        }, 200);
      }
    );
  };

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#asignarURL
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion que cargar los catalogos necesarios para empezar la configuracion de la matriz
   * @param {url} url URL en la api para la peticion
   * @param {ruta} ruta RUTA en la api para la peticion
   */

  $scope.asignarURL = function (url, ruta) {
    $scope.url = url;
    $scope.ruta = ruta;
    // cargar los catalogos para el indicador
    $scope.cargarCatalogo(
      $scope.url + "/api/v1/tablero/clasificacionUso",
      $scope.clasificaciones_usos,
      "cc_uso"
    );
    $scope.cargarCatalogo(
      $scope.url + "/api/v1/tablero/listaIndicadores?tipo=no_clasificados",
      $scope.inidcadores_no_clasificados,
      "cc_sin"
    );
    $scope.cargarCatalogo(
      $scope.url + "/api/v1/tablero/listaIndicadores?tipo=favoritos",
      $scope.inidcadores_favoritos,
      "cc_favprito"
    );
    $scope.cargarMatriz();
  };

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#cargarCatalogo
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion que carga datos de una URL de la API y la almacena en un modelo angular especifico
   * @param {url} url URL en la api para la peticion
   * @param {modelo} modelo modelo donde se carga el resultado
   * @param {callback} callback funcion a ejecutar after event
   */
  $scope.cargarSelect = function (url, modelo, callback) {
    $scope.cargando = true;
    Crud.lista(url, function (data) {

      if (data.status == 200) {
        $scope.intento1 = 0;
        angular.forEach(data.data, function (value, key) {
          modelo.push(value);
        });
      }
      $scope.cargando = false;
    }, function (e) {
      setTimeout(function () {
        if ($scope.intento1 < 1) {
          $scope.cargarSelect(url, modelo, callback);
          $scope.intento1++;
        }
        else $scope.cargando = false;
      }, 200);
    });
  };

  $scope.desempeno_index = null;
  $scope.tablero_indicador = [];
  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#cargarIndicadores
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion que cargar los datos del inidcador
   * @param {item} item inidcador seleccionado de la lista.
   */
  $scope.agregarIndicador = function (item, dimension = "") {
    if (item) {
      if (angular.isUndefined($scope.tablero_indicador[$scope.desempeno_index]))
        $scope.tablero_indicador[$scope.desempeno_index] = [];
      if (angular.isUndefined($scope.tablero_indicador[$scope.desempeno_index][item.id]))
        $scope.tablero_indicador[$scope.desempeno_index][item.id] = 0;
      var campos_indicador = item.campos_indicador.split(",");
      $scope.dato.indicadores_desempeno[$scope.desempeno_index].indicador_etab.push({
        filtros: [],
        alertas: [],
        error: "",
        indicador: item.id,
        nombre: item.nombre,
        es_favorito: item.es_favorito,
        dimensiones: campos_indicador,
        dimension: 0,
        posicion: 0,
        index: 0,
        otros_filtros: {
          desde: "",
          hasta: "",
          elementos: [],
          dimension_mostrar: '',
          representa: ''
        }
      });
      var index = $scope.dato.indicadores_desempeno[$scope.desempeno_index].indicador_etab.length - 1;
      var json = { filtros: "", ver_sql: false, tendencia: false };
      $scope.tablero_indicador[$scope.desempeno_index][item.id]++;
      Crud.crear(
        $scope.url + "/api/v1/tablero/datosIndicador/" +
        item.id +
        "/" +
        $scope.dato.indicadores_desempeno[$scope.desempeno_index].indicador_etab[index].dimensiones[0],
        json,
        "application/json",
        function (data) {
          if (data.status == 200) {
            $scope.dato.indicadores_desempeno[$scope.desempeno_index].indicador_etab[index].data = data.data;
            $scope.dato.indicadores_desempeno[$scope.desempeno_index].indicador_etab[index].informacion = data.informacion;

          }
        },
        function (e) {
          $scope.dato.indicadores_desempeno[index].error = "Error";
          $scope.dato.indicadores_desempeno[index].cargando = false;
          setTimeout(function () {
            $scope.dato.indicadores_desempeno[index].error = "";
          }, 3000);
        }
      );
    }
  };

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#cargarIndicadores
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion que cargar los datos del inidcador con una dimension especifica
   * @param {dimension} dimension a mostrar
   * @param {index} index posicion del inidcador en el arreglo
   * @param {fila} fila del elemento dentro del indicador
   */
  $scope.agregarIndicadorDimension = function (dimension, index, fila) {
    var posicion = index;
    if (
      !angular.isUndefined($scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimensiones[dimension])
    ) {
      $scope.cargando = true;
      var json = { filtros: $scope.dato.indicadores_desempeno[index].indicador_etab[fila].filtros, ver_sql: false, tendencia: false };
      Crud.crear(
        $scope.url + "/api/v1/tablero/datosIndicador/" +
        $scope.dato.indicadores_desempeno[index].indicador_etab[fila].indicador +
        "/" +
        $scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimensiones[dimension].trim(),
        json,
        "application/json",
        function (data) {
          $scope.repuestaIndicador(data, dimension, index, fila);
        },
        function (e) {
          $scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimension--;
          $scope.cargando = false;
        }
      );
    } else {
      $scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimension = $scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimensiones.length - 1;
    }
  };

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#quitarFiltro
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion que elimina el filtro
   * @param {index} index posicion del inidcador en el arreglo
   * @param {fila} fila del elemento dentro del indicador
   */
  $scope.quitarFiltro = function (index, fila) {
    $scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimension = 0;
    $scope.dato.indicadores_desempeno[index].indicador_etab[fila].filtros = [];
    $scope.agregarIndicadorDimension(0, index, fila);
  }

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#breadcum
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion que mustra el breadcum del inidcador
   * @param {index} index contador de posicion
   * @param {item} item elemento que se le hizo clic
   * @param {link} link del elemento dentro del indicador
   * @param {num} num del indicador de desempeño
   * @param {fila} fila del elemento dentro del indicador
   */
  $scope.breadcum = function (index, item, link, num, fila) {
    var filtros = item.filtros;
    $scope.dato.indicadores_desempeno[num].indicador_etab[fila].dimension = index + 1;
    $scope.dato.indicadores_desempeno[num].indicador_etab[fila].filtros = [];
    angular.forEach(filtros, function (value, key) {
      if (index >= key)
        $scope.dato.indicadores_desempeno[num].indicador_etab[fila].filtros.push(value);
    });
    $scope.agregarIndicadorDimension(index + 1, num, fila);
  };

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#filtrarIndicador
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion que agrega el filtro al indicador
   * @param {index} index posicion del inidcador en el arreglo
   * @param {fila} fila del elemento dentro del indicador
   */
  $scope.filtrarIndicador = function (index, fila) {
    var dimension = $scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimension;
    if (
      !angular.isUndefined($scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimensiones[dimension])
    ) {
      $scope.cargando = true;
      var json = {
        filtros: $scope.dato.indicadores_desempeno[index].indicador_etab[fila].filtros,
        ver_sql: false,
        otros_filtros: $scope.dato.indicadores_desempeno[index].indicador_etab[fila].otros_filtros
      };
      Crud.crear(
        $scope.url + "/api/v1/tablero/datosIndicador/" +
        $scope.dato.indicadores_desempeno[index].indicador_etab[fila].indicador +
        "/" +
        $scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimensiones[dimension].trim(),
        json,
        "application/json",
        function (data) {
          $scope.repuestaIndicador(data, dimension - 1, index, fila);
        },
        function (e) {
          $scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimension--;
          $scope.cargando = false;
        }
      );
    } else {
      $scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimension = $scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimensiones.length - 1;
    }
  };

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#agregarOtrosFiltros
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion que agrega el los otros tipos de filtro al indicador
   * @param {index} index posicion del inidcador en el arreglo
   * @param {fila} fila del elemento dentro del indicador
   * @param {valor} valor del filtro
   */
  $scope.agregarOtrosFiltros = function (index, fila, valor) {
    if ($scope.dato.indicadores_desempeno[index].indicador_etab[fila].otros_filtros.elementos.indexOf(valor) > -1) {
      $scope.dato.indicadores_desempeno[index].indicador_etab[fila].otros_filtros.elementos.splice(
        $scope.dato.indicadores_desempeno[index].indicador_etab[fila].otros_filtros.elementos.indexOf(valor),
        1
      );
      $("#elemento" + index).removeAttr("checked");
    } else {
      $scope.dato.indicadores_desempeno[index].indicador_etab[fila].otros_filtros.elementos.push(valor);
      $("#elemento" + index).attr("checked", "checked");
    }
  };

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#descenderDimension
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion que deciende niveles dentro del indicador al hacer click
   * @param {valor} valor del filtro
   * @param {index} index posicion del inidcador en el arreglo
   * @param {fila} fila del elemento dentro del indicador  
   */
  $scope.descenderDimension = function (valor, index, fila) {
    $scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimension++;
    if ($scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimension < $scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimensiones.length) {
      $scope.dato.indicadores_desempeno[index].indicador_etab[fila].filtros.push({
        codigo: $scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimensiones[$scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimension - 1].trim(),
        valor: valor
      });
      $scope.agregarIndicadorDimension($scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimension, index, fila);
    } else {
      $scope.dato.indicadores_desempeno[index].indicador_etab[fila].error = "Success";
      setTimeout(function () {
        $scope.dato.indicadores_desempeno[index].indicador_etab[fila].error = "";
      }, 3000);
    }
  }

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#repuestaIndicador
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion procesa la respuesta para construir los datos para el gráfico
   * @param {data} data respuesta del servidor
   * @param {dimension} dimension dimension actual de la gráfica
   * @param {index} index bandera de posicion
   * @param {fila} fila bandera de fila
   */
  $scope.repuestaIndicador = function (data, dimension, index, fila) {
    if (data.status == 200) {
      $scope.dato.indicadores_desempeno[index].indicador_etab[fila].data = data.data;
    } else {
      $scope.dato.indicadores_desempeno[index].dimension--;
    }
    $scope.cargando = false;
  };

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#abrirModalIndicador
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion que abre la modal de los filtros
   * @param {index} index bandera de posicion
   */
  $scope.abrirModalIndicador = function (index) {
    $scope.desempeno_index = index;
    $("#modalIndicadores").modal("toggle");
  }

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#verFiltros
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion que muestra los filtros aplicados
   * @param {num} num bandera de posicion
   * @param {index} index bandera de posicion
   */
  $scope.verFiltros = function (num, index) {
    $scope.index = num;
    $scope.fila = index;
    $("#modalFiltros").modal("toggle");
  };

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#agregarDesempeno
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion agregar un nuveo inidcador de desempeño
   */
  $scope.agregarDesempeno = function () {
    var tamano = $scope.dato.indicadores_desempeno.length;
    if (tamano - 1 >= 0) {
      $("#colapsar" + tamano - 1).click();
      $scope.dato.indicadores_desempeno[tamano - 1].abierto = false;
    }
    $scope.tab[tamano] = 1;
    $scope.dato.indicadores_desempeno.push({
      nombre: "",
      orden: tamano + 1,
      abierto: true,
      id_matriz: null,
      indicador_relacion: [],
      indicador_etab: []
    });
  };

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#agregarIndicadorRelacion
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion agregar un nuveo inidcador de relacion (no etab)
   */
  $scope.agregarIndicadorRelacion = function (index) {
    $scope.dato.indicadores_desempeno[index].indicador_relacion.push({
      nombre: "",
      fuente: "",
      alertas: []
    });
  };

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#agregarIndicadorEtab
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion agregar un nuveo inidcador de etab
   */
  $scope.agregarIndicadorEtab = function (index) {
    $scope.dato.indicadores_desempeno[index].indicador_etab.push({
      nombre: "",
      fuente: "",
      alertas: []
    });
  };

  $scope.id = null;
  $scope.fila = null;
  $scope.modelo = null;
  $scope.index = null;
  $scope.tipo = null;

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#confirmarModelo
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion para abrir una modal de confirmacion
   */
  $scope.confirmarModelo = function (modelo, id, index = null) {
    $scope.modelo = modelo;
    $scope.id = id;
    $scope.index = index;
    $("#" + id).modal("toggle");
  };

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#limpiarModelo
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion para limpiar el formulario
   */
  $scope.limpiarModelo = function () {
    if ($scope.modelo == "indicadores_desempeno") {
      $scope.dato.indicadores_desempeno = [];
    }
    else if ($scope.modelo == "indicador_relacion") {
      $scope.dato.indicadores_desempeno[$scope.index].indicador_relacion = [];
    } else if ($scope.modelo == "indicador_etab") {
      $scope.dato.indicadores_desempeno[$scope.index].indicador_etab = [];
    }
    $("#" + $scope.id).modal("hide");
  };

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#confirmarFila
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion para abrir una modal de confirmacion
   */
  $scope.confirmarFila = function (modelo, id, fila, index = null) {
    $scope.fila = fila;
    $scope.modelo = modelo;
    $scope.id = id;
    $scope.index = index;
    $("#" + id).modal("toggle");
  };

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#eliminarFila
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion para eliminar una fila
   */
  $scope.eliminarFila = function () {
    if ($scope.modelo == "indicadores_desempeno") {
      $scope.dato.indicadores_desempeno.splice($scope.fila, 1);
    }
    else if ($scope.modelo == "indicador_relacion") {
      $scope.dato.indicadores_desempeno[$scope.index].indicador_relacion.splice($scope.fila, 1);
    } else if ($scope.modelo == "indicador_etab") {
      $scope.tablero_indicador[$scope.index][$scope.dato.indicadores_desempeno[$scope.index].indicador_etab[$scope.fila].indicador]--;
      $scope.dato.indicadores_desempeno[$scope.index].indicador_etab.splice($scope.fila, 1);
    }
    $("#" + $scope.id).modal("hide");
  };

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#abrirAlerta
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion para abrir una modal de configuracion de alertas
   */
  $scope.abrirAlerta = function (index, fila, tipo) {
    $scope.fila = fila;
    $scope.tipo = tipo;
    $scope.index = index;
    $("#alertas").modal("toggle");
  }

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#agregarAlerta
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion para agregar una alerta a la lista
   */
  $scope.agregarAlerta = function (index, fila, tipo) {
    $scope.dato.indicadores_desempeno[index][tipo][fila].alertas.push({
      limite_inferior: "",
      limite_superior: "",
      color: ""
    });
  };

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#quitarAlerta
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion para quitar una alerta
   */
  $scope.quitarAlerta = function (index, fila, num, tipo) {
    $scope.dato.indicadores_desempeno[index][tipo][fila].alertas.splice(num, 1);
  };

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#limpiarAlerta
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion para borrar todas las alertas
   */
  $scope.limpiarAlerta = function (index, fila, tipo) {
    $scope.dato.indicadores_desempeno[index][tipo][fila].alertas = [];
  }

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#cerrarAcordion
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion abrir o cerra el acordion de inidcadores de desempeño
   */
  $scope.cerrarAcordion = function (index) {
    angular.forEach($scope.dato.indicadores_desempeno, function (value, key) {
      if (key != index) {
        value.abierto = false;
      }
    });
    $scope.dato.indicadores_desempeno[index].abierto = !$scope.dato.indicadores_desempeno[index].abierto;
  }

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#includeArray
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion para agregar un valor dentro de un array
   */
  $scope.includeArray = function (item, modelo) {
    var i = $.inArray(item, modelo);
    if (i > -1) {
      modelo.splice(i, 1);
    } else {
      modelo.push(item);
    }
  }

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#inArray
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion para determinar si un valor ya esta dentro de un array
   */
  $scope.inArray = function (item, modelo) {
    var i = $.inArray(item, modelo);
    if (i > -1) {
      return true;
    } else {
      return false;
    }
  }
  
  $scope.tipo_operacion = null;
  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#guardar
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion para guardar la matriz 
   */
  $scope.guardar = function () {
    $scope.tipo_operacion = "guardar";
    if ($scope.dato.id) {
      respuesta = $scope.actualizarMatriz();
    } else {
      $scope.crearMatriz();
    }
  };

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#clonarMatriz
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion para cloanr la matriz
   */
  $scope.clonarMatriz = function () {
    $scope.tipo_operacion = "clonar";
    $scope.crearMatriz();
  };

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#guardarCerrar
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion para guardar y cerrar
   */
  $scope.guardarCerrar = function () {
    $scope.tipo_operacion = "cerrar";
    if ($scope.dato.id) {
      $scope.actualizarMatriz();
    } else {
      $scope.crearMatriz();
    }
  };

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#crearMatriz
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion para guardar la matriz
   */
  $scope.crearMatriz = function () {
    var datos = $scope.dato;
    datos.tipo_operacion = $scope.tipo_operacion;
    $scope.cargando = true;
    Crud.crear($scope.ruta, $.param(datos), 'application/x-www-form-urlencoded;charset=utf-8;', function (data) {
      if (data.status == 200 || data.status == 201) {
        $scope.dato.id = data.data.id;
        if ($scope.tipo_operacion == "clonar")
          data.messages = data.messages + "(Cloned)";
        $scope.imprimir_mensaje(data.messages, 'success');

        if ($scope.tipo_operacion == "guardar" || $scope.tipo_operacion == "clonar")
          location.href = $scope.url + '/admin/app/matrizchiapas-matrizseguimientomatriz/' + $scope.dato.id + '/edit';
        else if (clonar$scope.tipo_operacion == "cerrar")
          location.href = $scope.url + '/admin/app/matrizchiapas-matrizseguimientomatriz/list';
        else
          $scope.cargando = false;

      } else {
        $scope.imprimir_mensaje(data.messages, 'warning');
      }
    }, function (e) {
      console.log(e);
      $scope.cargando = false;
    });
  }

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#actualizarMatriz
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion para actualizar la matriz
   */
  $scope.actualizarMatriz = function () {
    var datos = $scope.dato;
    $scope.cargando = true;
    Crud.editar($scope.ruta, $scope.dato.id, $.param(datos), 'application/x-www-form-urlencoded;charset=utf-8;', function (data) {
      $scope.cargando = false;
      if (data.status == 200 || data.status == 201) {
        $scope.dato.id = data.data.id;
        $scope.imprimir_mensaje(data.messages, 'success');

        if ($scope.tipo_operacion == "cerrar")
          location.href = $scope.url + '/admin/app/matrizchiapas-matrizseguimientomatriz/list';
        else
          $scope.cargando = false;
      } else {
        $scope.imprimir_mensaje(data.messages, 'warning');
      }
    }, function (e) {
      console.log(e);
      $scope.cargando = false;
    });
  }

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#cargarMatriz
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion para cargar los datos de la matriz
   */
  $scope.cargarMatriz = function () {
    if ($scope.dato.id) {
      $scope.cargando = true;
      Crud.ver($scope.ruta, $scope.dato.id, function (data) {
        $scope.cargando = false;
        if (data.status == 200) {
          $scope.intento3 = 0;
          $scope.dato = data.data;
          setTimeout(function(){
            angular.forEach(data.data.indicadores_desempeno, function(valor, num) {
              angular.forEach(valor.indicador_relacion, function (relacion, index) {
                $scope.cambioEsFormula('es_formula'+ num + index, relacion)
              });
            });
          }, 200);
          $scope.imprimir_mensaje(data.messages, "success");
        }
        else {
          $scope.imprimir_mensaje(data.messages, "warning");
        }
      }, function (e) {
        setTimeout(function () {
          if ($scope.intento3 < 2) {
            $scope.cargarMatriz(ruta);
            $scope.intento3++;
          }
          else {
            $scope.cargando = false;
            console.log(e);
          }
        }, 200);
      });
    }
  };

  /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#eliminarMatriz
   * @methodOf Matriz.MatrizConfiguracionCtrl
   *
   * @description
   * funcion para eliminar una matriz
   */
  $scope.eliminarMatriz = function () {
    $scope.cargando = true;
    Crud.eliminar($scope.ruta, $scope.dato.id, function (data) {

      if (data.status == 200) {
        $scope.imprimir_mensaje(data.messages, "success");
        location.href = $scope.url + '/admin/app/matrizchiapas-matrizseguimientomatriz/list';
      }
      else {
        $scope.imprimir_mensaje(data.messages, "warning");
      }
    }, function (e) {
      console.log(e);
      $scope.cargando = false;
    });
  };

   /**
   * @ngdoc method
   * @name Matriz.MatrizConfiguracionCtrl#imprimir_mensaje
   * @methodOf Matriz.MatrizCtrl
   *
   * @description
   * funcion que mustra los mensajes de las respuestas a la api
   * @param {mensaje} mensaje contiene el texto a mostrar
   * @param {tipo} tipo pinta el  color del mensaje
   * @param {id} id selecciona en que elemento se imprimira
   */
  $scope.imprimir_mensaje = function (mensaje, tipo, id) {
    id = angular.isUndefined(id) ? "#feedback_bar" : "#result_factura_test" + ", #" + id;

    $(id).html('<div class="alert alert-' + tipo + ' alert-dismissable" >' +
      '<i class="fa fa-' + tipo + '"></i> ' +
      '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>' +
      '<b>Alert! </b>' + mensaje +
      '</div>');
    setTimeout(function () {
      $(id).html('');
    }, 6000);
  }
})

