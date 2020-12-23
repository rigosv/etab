import Vue from "vue";
import Vuex from "vuex";
import { Sala } from "./tablero/interfaces/Sala";

Vue.use(Vuex);
export interface ModuleState {
  sala: Sala;
  salaAcciones: object[];
  salaUsuarios: object[];
  salaComentarios: object[];
  salasPropias: object[];
  salaNombreIni: string;
  layout: object[];
  indicadores: object[];
  indicadoresAllData: object[];
  indicadoresDataTendencia: object[];
  indicadoresFichas: object[];
  indexActivo: number;
  abrioSala: boolean;
  abrioIndicador: boolean;
  indicadoresClasificados: object[];
  clasificacionesUso: object[];
  clasificacionUso: null | object;
  clasificacionesTecnica: object[];
  clasificacionTecnica: null | object;
  idSala: string;
  token: string;
}

export const store = new Vuex.Store({
  state: {
    sala: { nombre: "", id: "", indicadores: [] },
    salaAcciones: [],
    salaUsuarios: [],
    salaComentarios: [],
    salasPropias: [],
    salaNombreIni: "",
    layout: [],
    indicadores: [],
    indicadoresAllData: [],
    indicadoresDataTendencia: [],
    indicadoresFichas: [],
    indexActivo: 0,
    abrioSala: false,
    abrioIndicador: false,
    indicadoresClasificados: [],
    clasificacionesUso: [],
    clasificacionUso: null,
    clasificacionesTecnica: [],
    clasificacionTecnica: null,
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
    setSalaActiva(state, sala: Sala): void {
      state.sala = sala;
      state.salaNombreIni = sala.nombre;
      state.salaAcciones = [];
      state.abrioSala = true;
    },
    setClasificacionUso(state, clasificaciones: object[]): void {
      state.clasificacionesUso = clasificaciones;
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
      state.abrioIndicador = true;
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
        state.abrioIndicador = false;
      }
    }
  }
});
