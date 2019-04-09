/**
 * @ngdoc object
 * @name Matriz.MatrizConfiguracionCtrl
 * @description
 * Controlador general que maneja el tablero
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

  $scope.comboDependiente = function (url, modelo, id, cargando) {
    modelo.length = 0;
    $scope[cargando] = true;
    Crud.lista(
      $scope.url + url + "?id=" + id,
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

  $scope.cargarIndicadores = function (url, modelo, cargando) {
    modelo.length = 0;
    $scope[cargando] = true;
    Crud.lista(
      $scope.url + url,
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
   * @name Tablero.TableroCtrl#cargarIndicadores
   * @methodOf Tablero.TableroCtrl
   *
   * @description
   * funcion que cargar los datos del inidcador
   * @param {item} item inidcador seleccionado de la lista
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

  $scope.quitarFiltro = function(index, fila){
    $scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimension = 0;
    $scope.dato.indicadores_desempeno[index].indicador_etab[fila].filtros = [];
    $scope.agregarIndicadorDimension(0, index, fila);
  }

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

  $scope.descenderDimension = function(valor, index, fila){
    $scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimension++;
    if ($scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimension < $scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimensiones.length) {
      $scope.dato.indicadores_desempeno[index].indicador_etab[fila].filtros.push({
        codigo: $scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimensiones[$scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimension - 1].trim(),
        valor: valor
      });
      $scope.agregarIndicadorDimension($scope.dato.indicadores_desempeno[index].indicador_etab[fila].dimension, index, fila);
    }else{
      $scope.dato.indicadores_desempeno[index].indicador_etab[fila].error = "Success";
      setTimeout(function() {
        $scope.dato.indicadores_desempeno[index].indicador_etab[fila].error = "";
      }, 3000);
    }
  }

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
  $scope.repuestaIndicador = function (data, dimension, index, fila) {
    if (data.status == 200) {
      $scope.dato.indicadores_desempeno[index].indicador_etab[fila].data = data.data;      
    } else {
      $scope.dato.indicadores_desempeno[index].dimension--;
    }   
    $scope.cargando = false;
  };

  $scope.abrirModalIndicador = function (index) {
    $scope.desempeno_index = index;
    $("#modalIndicadores").modal("toggle");
  }

  $scope.verFiltros = function (num, index) {
    $scope.index = num;
    $scope.fila = index;
    $("#modalFiltros").modal("toggle");
  };  

  $scope.agregarDesempeno = function() {
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

  $scope.agregarIndicadorRelacion = function(index) {    
    $scope.dato.indicadores_desempeno[index].indicador_relacion.push({
      nombre: "",
      fuente: "",
      alertas: []
    });
  };

  $scope.agregarIndicadorEtab = function(index) {
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

  $scope.confirmarModelo = function(modelo, id, index = null) {
    $scope.modelo = modelo;
    $scope.id = id;
    $scope.index = index;
    $("#" + id).modal("toggle");
  };

  $scope.limpiarModelo = function() {
    if ($scope.modelo == "indicadores_desempeno"){
      $scope.dato.indicadores_desempeno = [];
    }
    else if ($scope.modelo == "indicador_relacion"){
      $scope.dato.indicadores_desempeno[$scope.index].indicador_relacion = [];
    } else if ($scope.modelo == "indicador_etab"){
      $scope.dato.indicadores_desempeno[$scope.index].indicador_etab = [];
    }
    $("#" + $scope.id).modal("hide");
  };
 
  $scope.confirmarFila = function(modelo, id, fila, index = null) {
    $scope.fila = fila;
    $scope.modelo = modelo;
    $scope.id = id;
    $scope.index = index;
    $("#" + id).modal("toggle");
  };

  $scope.eliminarFila = function() {    
    if ($scope.modelo == "indicadores_desempeno") {
      $scope.dato.indicadores_desempeno.splice($scope.fila, 1);
    }
    else if ($scope.modelo == "indicador_relacion") {
      $scope.dato.indicadores_desempeno[$scope.index].indicador_relacion.splice($scope.fila, 1);
    } else if ($scope.modelo == "indicador_etab"){
      $scope.tablero_indicador[$scope.index][$scope.dato.indicadores_desempeno[$scope.index].indicador_etab[$scope.fila].indicador]--;
      $scope.dato.indicadores_desempeno[$scope.index].indicador_etab.splice($scope.fila, 1);
    }
    $("#" + $scope.id).modal("hide");
  };

  $scope.abrirAlerta = function (index, fila, tipo){
    $scope.fila = fila;
    $scope.tipo = tipo;
    $scope.index = index;
    $("#alertas").modal("toggle");
  }

  $scope.agregarAlerta = function(index, fila, tipo) {
    $scope.dato.indicadores_desempeno[index][tipo][fila].alertas.push({
      limite_inferior: "",
      limite_superior: "",
      color: ""
    });
  };

  $scope.quitarAlerta = function (index, fila, num, tipo) {
    $scope.dato.indicadores_desempeno[index][tipo][fila].alertas.splice(num, 1);
  };

  $scope.limpiarAlerta = function (index, fila, tipo) {
    $scope.dato.indicadores_desempeno[index][tipo][fila].alertas = [];
  }

  $scope.cerrarAcordion = function (index){    
    angular.forEach($scope.dato.indicadores_desempeno, function (value, key) {
      if(key != index){
        value.abierto = false;
      }
    });
    $scope.dato.indicadores_desempeno[index].abierto = !$scope.dato.indicadores_desempeno[index].abierto;
  }

  $scope.includeArray = function (item, modelo) {
    var i = $.inArray(item, modelo);
    if (i > -1) {
      modelo.splice(i, 1);
    } else {
      modelo.push(item);
    }
  }

  $scope.inArray = function (item, modelo) {
    var i = $.inArray(item, modelo);
    if (i > -1) {
      return true;
    } else {
      return false;
    }
  }
  $scope.tipo_operacion = null;
  $scope.guardar = function () {
    $scope.tipo_operacion = "guardar";
    if ($scope.dato.id) {
      respuesta = $scope.actualizarMatriz();
    } else {
      $scope.crearMatriz();      
    }    
  };

  $scope.clonarMatriz = function () {
    $scope.tipo_operacion = "clonar";
    $scope.crearMatriz();      
  };

  $scope.guardarCerrar = function() {
    $scope.tipo_operacion = "cerrar";
    if($scope.dato.id){
      $scope.actualizarMatriz();      
    } else{
      $scope.crearMatriz();      
    }
  };

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

  $scope.cargarMatriz = function () {
    if ($scope.dato.id){
      $scope.cargando = true;
      Crud.ver($scope.ruta, $scope.dato.id, function (data) {  
        $scope.cargando = false;    
        if (data.status == 200) {
          $scope.intento3 = 0;        
          $scope.dato = data.data;
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

  $scope.eliminarMatriz = function(){
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

