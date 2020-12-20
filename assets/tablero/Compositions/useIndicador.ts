import axios from "axios";
//import alasql from 'alasql';

export default function() {
  const inicializarIndicador = (indicador: any, index: number): object => {
    const filtros = indicador.filtro != "" ? JSON.parse(indicador.filtro) : [];

    let conf =
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
    //Corregir formato antiguo
    if (Array.isArray(conf)) {
      conf = conf[0];
    }
    // si no tiene tipo de gráfico poner columnas por defecto
    conf.tipo_grafico =
      !Object.prototype.hasOwnProperty.call(conf, "tipo_grafico") ||
      conf.tipo_grafico == "" ||
      conf.tipo_grafico == undefined
        ? "columnas"
        : conf.tipo_grafico;
    conf.filtro_inicial = filtros;
    conf.width =
    Object.prototype.hasOwnProperty.call(conf, "width") && conf.width != ""
        ? conf.width
        : "col-sm-4";
    conf.orden_x = Object.prototype.hasOwnProperty.call(conf, "orden_x")
      ? conf.orden_x
      : "";
    conf.orden_y = Object.prototype.hasOwnProperty.call(conf, "orden_y")
      ? conf.orden_y
      : "";
    conf.height = Object.prototype.hasOwnProperty.call(conf, "height")
      ? conf.height
      : 280;
    conf.mostrarTablaDatos = Object.prototype.hasOwnProperty.call(
      conf,
      "mostrarTablaDatos"
    )
      ? conf.mostrarTablaDatos
      : false;
    conf.agregados = Object.prototype.hasOwnProperty.call(conf, "agregados")
      ? conf.agregados
      : [];
    conf.dimensionComparacion = Object.prototype.hasOwnProperty.call(
      conf,
      "dimensionComparacion"
    )
      ? conf.dimensionComparacion
      : "";

    const col = index % 3;
    const fila = Math.floor(index / 3);
    conf.layout = Object.prototype.hasOwnProperty.call(conf, "layout")
      ? conf.layout
      : { x: col * 4, y: fila * 14, w: 4, h: 14, i: index };

    const otrosFiltros = {
      desde: indicador.filtro_posicion_desde,
      hasta: indicador.filtro_posicion_hasta,
      elementos:
        indicador.filtro_elementos != "" && indicador.filtro_elementos != null
          ? indicador.filtro_elementos.split(",")
          : []
    };

    const datosIndicador = {
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
      otros_filtros: otrosFiltros,
      cargaCompletaIniciada: false
    };
    return datosIndicador;
  };

  return {
    inicializarIndicador
  };
}
