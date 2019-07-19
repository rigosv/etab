/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');


import Vue from 'vue';
import Vuex from 'vuex'
import VueI18n from 'vue-i18n';
import messages from './tablero/locale/es.js'

import Tablero from './tablero/Tablero';

Vue.use(Vuex);
Vue.use(VueI18n);

const store = new Vuex.Store({
    state: {
        sala: {},
        indicadores: [],
        abrio_sala: false,
        abrio_indicador: false,
        colores_: ['rgb(31, 119, 180)', 'rgb(174, 199, 232)', 'rgb(255, 127, 14)', 'rgb(255, 233, 211)', 'rgb(163, 214, 163)', 'rgb(152, 223, 138)', 'rgb(214, 39, 40)', 'rgb(214, 39, 40)', 'rgb(255, 171, 169)', 'rgb(148, 103, 189)', 'rgb(197, 176, 213)', 'rgb(140, 86, 75)', 'rgb(196, 156, 148)', 'rgb(196, 156, 148)', 'rgb(227, 119, 194)', 'rgb(247, 182, 210)'],

    },
    mutations: {
        setIndicadores(state, indicadores) {
            state.indicadores = indicadores;
        },
        agregarIndicador(state, indicador) {
            state.indicadores.push(indicador);
        },
        agregarDatosIndicador(state, datos) {
            state.indicadores[datos.index] = datos.indicador;
            state.abrio_indicador = true;
        },
        quitarIndicador ( state, index ) {
            state.indicadores.splice(index, 1);

            if ( state.indicadores.length == 0 ){
                state.abrio_indicador = false;
            }
        },
        errorCargaIndicador(state, index) {
            state.indicadores[index].error = "Error";
            state.indicadores[index].cargando = false;

            setTimeout(function () {
                state.indicadores[index].error = "";
            }, 3000);
        },
        setSala(state, salaNueva) {
            if (state.sala.id == salaNueva.id ) {
                state.sala = null;
            }
            state.sala =  salaNueva;
            state.abrio_sala = true;
        }
    }
});

const i18n = new VueI18n({
    locale: 'es', // set locale
    messages, // set locale messages
});

Vue.filter('normalizarDiacriticos', function (value) {
    if (!value) return '';
    return value.toLowerCase().normalize('NFD')
        .replace(/([aeio])\u0301|(u)[\u0301\u0308]/gi, "$1$2")
        .normalize();
});

new Vue({
    //mixins: [IndicadorMixin],
    delimiters: ['<%', '%>'],
    data: {
        messsage: 'Hello Vue!'
    },
    components: { Tablero},
    watch: {
        '$store.state.sala': function (sala) {

            this.$bvModal.hide('modalSalas');

            let indicadores = sala.indicadores.map((indicador, index) => {
                return this.inicializarIndicador(indicador, index);
            });

            this.$store.commit('setIndicadores', indicadores);

            // Cargar los datos de los indicadores de la sala
            indicadores.forEach((indicador, index) => {
                this.cargarDatosIndicador(indicador, index);
            });
        }
    }
}).$mount('#app');


