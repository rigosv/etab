export interface Indicador {
  cargando: boolean;
  tendencia: boolean;
  tipo_grafico_ant: string;
  filtros: any[];
  error: string;
  informacion: object;
  index: number;
  mostrar_configuracion: boolean;
  data: any[];
  data_tendencia: any[];
  dataComparar: any[];
  id: string;
  nombre: string;
  es_favorito: boolean;
  dimensiones: any[];
  dimension: string;
  radial: boolean;
  termometro: false;
  mapa: false;
  posicion: number;
  sql: string;
  ficha: string;
  full_screen: boolean;
  configuracion: object;
  otros_filtros: object;
  cargaCompletaIniciada: boolean;
}