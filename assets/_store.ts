import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);
export interface ModuleState {
  sala: object;
  sala_acciones: object[];
  sala_usuarios: object[];
  sala_comentarios: object[];
  salas_propias: object[];
  sala_nombre_ini: string;
  layout: object[];
  indicadores: object[];
  indicadoresAllData: object[];
  indicadoresDataTendencia: object[];
  indicadoresFichas: object[];
  indexActivo: number;
  abrio_sala: boolean;
  abrio_indicador: boolean;
  colores_: string[];
  indicadoresClasificados: object[];
  clasificaciones_uso: object[];
  clasificacion_uso: null | object;
  clasificaciones_tecnica: object[];
  clasificacion_tecnica: null | object;
  idSala: string;
  token: string;
}

export const store = new Vuex.Store({
  state: {
    sala: { nombre: "" },
    sala_acciones: [],
    sala_usuarios: [],
    sala_comentarios: [],
    salas_propias: [],
    sala_nombre_ini: "",
    layout: [],
    indicadores: [],
    indicadoresAllData: [],
    indicadoresDataTendencia: [],
    indicadoresFichas: [],
    indexActivo: 0,
    abrio_sala: false,
    abrio_indicador: false,
    colores_: [
      "rgb(31, 119, 180)",
      "rgb(174, 199, 232)",
      "rgb(255, 127, 14)",
      "rgb(163, 214, 163)",
      "rgb(214, 39, 40)",
      "rgb(255, 171, 169)",
      "rgb(148, 103, 189)",
      "rgb(197, 176, 213)",
      "rgb(140, 86, 75)",
      "rgb(196, 156, 148)",
      "rgb(227, 119, 194)",
      "rgb(247, 182, 210)",
      "rgb(255, 233, 211)",
      "rgb(152, 223, 138)"
    ],
    indicadoresClasificados: [],
    clasificaciones_uso: [],
    clasificacion_uso: null,
    clasificaciones_tecnica: [],
    clasificacion_tecnica: null,
    idSala: "",
    token: ""
  } as ModuleState,
  mutations: {
    setIndicadores(state, indicadores: object[]): void {
      state.indicadores = indicadores;
      state.layout = state.indicadores.map((ind: any) => {
        return ind.configuracion.layout;
      });
    },
    agregarIndicador(state, indicador: any): void {
      state.indicadores.push(indicador);
      state.layout.push(indicador.configuracion.layout);
    },
    addDatosSalaPublica(state, datosSala: any): void {
      state.idSala = datosSala.idSala;
      state.token = datosSala.token;
    },
    agregarDatosIndicador(state, datos: any): void {
      const indicador = datos.indicador;
      state.indicadores[datos.index] = indicador;
      state.abrio_indicador = true;
    },
    quitarIndicador(state, index: number): void {
      state.indicadores = state.indicadores.filter((ind: any) => {
        return ind.index != index;
      });
      state.indicadoresAllData = state.indicadoresAllData.filter((ind: any) => {
        return ind.index != index;
      });

      state.layout = state.indicadores.map((ind: any) => {
        return ind.configuracion.layout;
      });
      if (state.indicadores.length == 0) {
        state.abrio_indicador = false;
      }
    }
  }
});
