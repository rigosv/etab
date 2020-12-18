import { defineComponent } from "@vue/composition-api";
import axios from "axios";
//import alasql from 'alasql';

export default defineComponent ({
  methods : {
    inicializarIndicador(indicador: any, index: number): object {
      const filtros = indicador.filtro != "" ? JSON.parse(indicador.filtro) : [];

      const conf =
        indicador.orden != "" && indicador.orden != null
          ? JSON.parse(indicador.orden)
          : {
              width: "col-sm-4",
              height: "280",
              orden_x: "asc",
              orden_y: "",
              tipo_grafico: indicador.tipo_grafico,
              maximo: "",
              maximo_manual: ""
            };

      // si no tiene tipo de gráfico poner columnas por defecto
      conf.tipo_grafico =
        !conf.hasOwnProperty("tipo_grafico") ||
        conf.tipo_grafico == "" ||
        conf.tipo_grafico == undefined
          ? "columnas"
          : conf.tipo_grafico;
      conf.filtro_inicial = filtros;
      conf.width =
        conf.hasOwnProperty("width") && conf.width != ""
          ? conf.width
          : "col-sm-4";
      conf.height = conf.hasOwnProperty("height") ? conf.height : 280;
      conf.mostrarTablaDatos = conf.hasOwnProperty("mostrarTablaDatos")
        ? conf.mostrarTablaDatos
        : false;
      conf.agregados = conf.hasOwnProperty("agregados") ? conf.agregados : [];
      conf.dimensionComparacion = conf.hasOwnProperty("dimensionComparacion")
        ? conf.dimensionComparacion
        : "";

      const col = index % 3;
      const fila = Math.floor(index / 3);
      conf.layout = conf.hasOwnProperty("layout")
        ? conf.layout
        : { x: col * 4, y: fila * 14, w: 4, h: 14, i: index };

      const otros_filtros = {
        desde: indicador.filtro_posicion_desde,
        hasta: indicador.filtro_posicion_hasta,
        elementos:
          indicador.filtro_elementos != "" && indicador.filtro_elementos != null
            ? indicador.filtro_elementos.split(",")
            : []
      };

      const datos_indicador = {
        cargando: true,
        tendencia: false,
        tipo_grafico_ant: "",
        filtros: filtros,
        error: "",
        informacion: {},
        index: index,
        mostrar_configuracion: false,
        data: [],
        data_tendencia: [],
        dataComparar: [],
        id: indicador.indicador_id,
        nombre: "",
        es_favorito: false,
        dimensiones: [],
        dimension: indicador.dimension.trim(),
        radial: false,
        termometro: false,
        mapa: false,
        posicion: indicador.posicion,
        sql: "",
        ficha: "",
        full_screen: false,
        configuracion: conf,
        otros_filtros: otros_filtros,
        cargaCompletaIniciada: false
      };
      return datos_indicador;
    },

    cargarDatosIndicador(indicador: any, index: number): void {
      //Verificar si ya se ha hecho una carga completa de los datos, de ser así, tomar de ahí los datos
      const dataInd = this.$store.state.indicadoresAllData.filter(
        (ind: any) => ind.id == indicador.id
      );
      const ind = this.$store.state.indicadores.find(i => i.id == indicador.id);
      this.cargarFromServer(indicador, index);
      /*if ( ind && dataInd.length >  0 && ![ 'MAPA', 'GEOLOCATION', 'MAP' ].includes(indicador.configuracion.tipo_grafico.toUpperCase()) && !indicador.tendencia){
                      this.cargarFromLocal(indicador, dataInd[0].data);
                  } else {
                      this.cargarFromServer(indicador, index);
                      if ( indicador.cargaCompletaIniciada === false ){
                          indicador.cargaCompletaIniciada = true;
                          this.cargarDataCompleta(indicador);
                      }
                  }*/
    },

    cargarDatosComparacion(): void {
      //Verificar si hay indicadores de comparación
      for (const indC of this.indicador.dataComparar) {
        const vm = this;
        const json = {
          filtros: vm.indicador.filtros,
          ver_sql: false,
          tendencia: false
        };
        vm.indicador.cargando = true;

        axios
          .post(
            "/api/v1/tablero/datosIndicador/" +
              indC.id +
              "/" +
              vm.indicador.dimension,
            json
          )
          .then(function(response) {
            if (response.data.status == 200) {
              indC.data = response.data.data;
            }
          })
          .catch(function(error) {
            console.log(error);
          })
          .finally(function() {
            vm.indicador.cargando = false;
          });
      }
    },

    cargarFromServer(indicador: any, index: number): void {
      const indicadorCompleto = indicador;
      indicador.error = "";
      const json = {
        filtros: indicador.filtros,
        ver_sql: false,
        tendencia: indicador.tendencia
      };

      /*if (
                      indicador.filtro_posicion_desde != "" ||
                      indicador.filtro_posicion_hasta != "" ||
                      (indicador.filtro_elementos != "" && indicador.filtro_elementos)
                  ) {
                      json.otros_filtros = indicador.otros_filtros;
                  }*/

      const vm = this;
      indicador.cargando = true;
      axios
        .get(
          "/api/v1/tablero/datosIndicador/" +
            indicador.id +
            "/" +
            indicador.dimension,
          { params: json }
        )
        .then(function(response) {
          if (response.data.status == 200) {
            const data = response.data;
            let dimension = -1;
            const dimensiones = [];
            let pos = 0;

            if (data.informacion) {
              for (const prop in data.informacion.dimensiones) {
                dimensiones.push(prop);
                if (prop == indicador.dimension) dimension = pos;
                pos++;
              }
            }

            indicadorCompleto.nombre = data.informacion.nombre_indicador;
            indicadorCompleto.es_favorito = data.informacion.es_favorito;
            indicadorCompleto.dimensiones = dimensiones;
            indicadorCompleto.dimensionIndex = dimension;
            indicadorCompleto.ficha = data.ficha;

            indicadorCompleto.informacion = data.informacion;
            indicadorCompleto.informacion.nombre =
              data.informacion.nombre_indicador;

            if (data.data_tendencia !== undefined) {
              indicadorCompleto.data_tendencia = data.data;
            } else {
              indicadorCompleto.data = data.data;
            }
            indicadorCompleto.cargando = false;

            if (
              !vm.$store.state.indicadoresFichas.find(f => f.id == indicador.id)
            ) {
              vm.$store.state.indicadoresFichas.push({
                id: indicador.id,
                ficha: data.ficha,
                informacion: data.informacion
              });
            }

            vm.$store.commit("agregarDatosIndicador", {
              indicador: indicadorCompleto,
              index: index
            });
          }
        })
        .catch(function(error) {
          console.log(error);
          indicador.error = "Warning";
        })
        .finally(function() {
          indicador.cargando = false;
        });
    },

    normalizarDiacriticos(value: string): string {
      if (!value || value == undefined) return "";

      return value
        .toLowerCase()
        .normalize("NFD")
        .replace(/([aeio])\u0301|(u)[\u0301\u0308]/gi, "$1$2")
        .normalize();
    }
    /*
    cargarDataCompleta : function (indicador) {

        //Obtener todos los datos del indicador
        //Si el indicador está varias veces, cargarlo solo una
        let vm = this;
        if (!vm.$store.state.indicadoresAllData.find ( ind => ind.id == indicador.id ) ){
            axios.get( '/rest-service/data/'+indicador.id )
                .then ( function(response) {
                    let datos = [];
                    let cargadas = 1;
                    let mps = response.data;
                    if (mps.total_partes > 2){
                        // No cargar en local si tiene muchos datos porque traba el navegador
                        return;
                    }
                    datos = datos.concat(mps.datos);
                    // Se obtiene en partes de 50,000 por limitación de tamaño en respuesta de peticiones post
                    if ( mps.total_partes != undefined && mps.total_partes > 1) {
                        for(var i = 2; i <= mps.total_partes ; i++) {
                            axios.get( '/rest-service/data/'+indicador.id+'?parte='+i)
                                .then( function(response) {
                                    let mpsx = response.data;
                                    datos = datos.concat(mpsx.datos);
                                    cargadas++;

                                    if (cargadas == mps.total_partes){
                                        vm.$store.state.indicadoresAllData.push ( {id : indicador.id, data: datos});
                                        console.log(indicador.configuracion.dimensionComparacion);
                                    }
                            });
                        }
                    } else {
                        vm.$store.state.indicadoresAllData.push ( {id : indicador.id, data: datos});
                        if (indicador.configuracion.dimensionComparacion != '') {
                            vm.cargarFromLocal(indicador, datos);
                        }
                    }
                });
        }
    },

    cargarFromLocal ( indicador, data ) {

        //Agregar la otra información, por si se está cargando el indicador nuevamente
        if (indicador.ficha == null || indicador.ficha == "") {
            let data_ = this.$store.state.indicadoresFichas.filter(f => f.id == indicador.id)[0];
            let dimension = -1;
            let dimensiones = [];
            let pos = 0;

            if ( data_.informacion  ) {
                for (var prop in data_.informacion.dimensiones) {
                    dimensiones.push(prop);
                    if (prop == indicador.dimension) dimension = pos;
                    pos++;
                }
            }

            indicador.nombre = data_.informacion.nombre_indicador;
            indicador.es_favorito = data_.informacion.es_favorito;
            indicador.dimensiones = dimensiones;
            indicador.dimensionIndex = dimension;
            indicador.ficha = data_.ficha;
            indicador.informacion = data_.informacion;
            indicador.informacion.nombre = data_.informacion.nombre_indicador;
        }

        const cantidad_decimales = (indicador.ficha.cantidad_decimales == null) ? 2 : indicador.ficha.cantidad_decimales;

        let formula = indicador.ficha.formula.toLowerCase().split(' ').join('').replace(/{/g, 'SUM(__').replace(/}/g, '__::NUMBER)');
        //Si tenía otro tipo de operadores corregir
        formula = formula.replace('COUNTSUM', 'COUNT').replace('AVERAGESUM', 'AVERAGE');

        const filtros = indicador.filtros.map(filtro => ' AND ' + filtro.codigo + " = '" + filtro.valor + "'").join(' ');
        // separamos los operadores por los operandos permitidos (* y /), luego tomamos los que empiecen por letra
        // para quitar los valores que solo son numéricos, luego se agrega el AS y nombre de variable
        let variables = formula.split(/[\/\*]+/).filter(v => /^[a-zA-Z]/.test(v)).map(v => v + ' AS ' + v.split('__')[1]).join(', ');

        // Si la dimensión es un catálogo, agregar el id
        // Esto se usa para los mapas
        let idCatalogo = '';
        let grpCatalogo = '';
        if (/^id/.test(indicador.dimension)) {
            idCatalogo = indicador.dimension + '_ AS id, ';
            grpCatalogo = ', ' + indicador.dimension + '_';
        }

        let subCategory = '';
        let grpSubCategory = '';
        if ( indicador.configuracion.dimensionComparacion != '') {
            subCategory = indicador.configuracion.dimensionComparacion + ' AS subcategory, ';
            grpSubCategory = ', ' + indicador.configuracion.dimensionComparacion;
        }

        //Hacer la consulta sobre los datos completos
        const query = ' SELECT ' + indicador.dimension + ' AS category, ' +  subCategory +
            idCatalogo + variables + ', ROUND(' + formula + ',' + cantidad_decimales + ') AS measure ' +
            ' FROM ? ' +
            ' WHERE 1 = 1 ' + filtros +
            ' GROUP BY ' + indicador.dimension + grpSubCategory + grpCatalogo +
            ' ORDER BY ' + indicador.dimension + grpSubCategory + grpCatalogo
        ;

        let res = alasql(query, [data]);

        indicador.data = res;
        indicador.cargando = false;

    }*/
  }
})
