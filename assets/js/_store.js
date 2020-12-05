import Vue from 'vue';
import Vuex from "vuex";

Vue.use(Vuex);

export const store = new Vuex.Store({
    state: {
        sala: {nombre: ''},
        sala_acciones : [],
        sala_usuarios : [],
        sala_comentarios : [],
        salas_propias : [],
        sala_nombre_ini : '',
        layout: [],
        indicadores: [],
        indicadoresAllData: [],
        indicadoresFichas: [],
        indexActivo : 0,
        abrio_sala: false,
        abrio_indicador: false,
        colores_: ['rgb(31, 119, 180)', 'rgb(174, 199, 232)', 'rgb(255, 127, 14)', 'rgb(163, 214, 163)', 'rgb(214, 39, 40)', 'rgb(255, 171, 169)', 'rgb(148, 103, 189)', 'rgb(197, 176, 213)', 'rgb(140, 86, 75)', 'rgb(196, 156, 148)',  'rgb(227, 119, 194)', 'rgb(247, 182, 210)', 'rgb(255, 233, 211)', 'rgb(152, 223, 138)'],
        indicadoresClasificados: [],
        clasificaciones_uso : [],
        clasificacion_uso : null,
        clasificaciones_tecnica : [],
        clasificacion_tecnica : null,
        idSala:'',
        token:''


    },
    mutations: {
        setIndicadores(state, indicadores) {
            state.indicadores = indicadores;
            state.layout = state.indicadores.map( ind => { return ind.configuracion.layout });

        },
        agregarIndicador(state, indicador) {
            state.indicadores.push(indicador);
            state.layout.push( indicador.configuracion.layout );

        },
        addDatosSalaPublica(state, datosSala) {
            state.idSala = datosSala.idSala;
            state.token = datosSala.token;
            console.log(state.token);
            console.log(state.idSala);
        },
        agregarDatosIndicador(state, datos) {
            let indicador = datos.indicador;
            state.indicadores[datos.index] = indicador;
            state.abrio_indicador = true;
        },
        quitarIndicador ( state, index ) {

            state.indicadores = state.indicadores.filter( ind => { return ind.index != index });
            state.indicadoresAllData = state.indicadoresAllData.filter( ind => { return ind.index != index });

            state.layout = state.indicadores.map( ind => { return ind.configuracion.layout });
            if ( state.indicadores.length == 0 ){
                state.abrio_indicador = false;
            }
        }
    }
});