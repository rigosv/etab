<template>
    <div style="padding: 10px;">
        <b-form-group
                :label=" $t('_clasificacion_uso_') "
                label-for="clasificacion_uso"
                :state="state"
        >
            <b-input-group class="mt-3" >
                <b-input-group-text slot="prepend">
                    <font-awesome-icon icon="search" v-if="!cargando_uso" />
                    <strong class="text-success"><font-awesome-icon icon="sync" spin v-if="cargando_uso" /></strong>
                </b-input-group-text>                

                <v-select id="clasificacion_uso"
                    v-model="$store.state.clasificacion_uso"
                    :options="$store.state.clasificaciones_uso"
                    label = 'descripcion'
                    style="flex: 1 1 auto"
                    @input="getClasificacionesTecnica($event)"
                >
                </v-select>

                <b-form-invalid-feedback :state="invalidCU">
                    <b-spinner variant="success" type="grow" label="Spinning"></b-spinner>
                    1.-{{ $t('_elija_clasificacion_uso_') }}
                </b-form-invalid-feedback>
            </b-input-group>
        </b-form-group>

        <b-form-group v-if="$store.state.clasificaciones_tecnica.length > 0 && $store.state.clasificacion_uso != undefined"
                :label=" $t('_clasificacion_tecnica_') "
                label-for="clasificacion_tecnica"
                :state="stateT"
        >
            <b-input-group class="mt-3">
                <b-input-group-text slot="prepend">
                    <font-awesome-icon icon="search" v-if="!cargando_tecnica" />
                    <strong class="text-success"><font-awesome-icon icon="sync" spin v-if="cargando_tecnica" /></strong>
                </b-input-group-text>
                <v-select id="clasificacion_tecnica"
                          v-model="$store.state.clasificacion_tecnica"
                          :options="$store.state.clasificaciones_tecnica"
                          label = 'descripcion'
                          style="flex: 1 1 auto"
                          @input="getIndicadores($event)"
                >
                </v-select>
                <b-form-invalid-feedback :state="invalidCT">
                    <b-spinner variant="success" type="grow" label="Spinning"></b-spinner>
                    2.-{{ $t('_elija_clasificacion_tecnica_') }}
                </b-form-invalid-feedback>
            </b-input-group>
        </b-form-group>

        <ListadoIndicadores :indicadores="this.$store.state.indicadoresClasificados" v-if="$store.state.clasificacion_tecnica != undefined" />
    </div>
</template>

<script>
    import Vue from 'vue';
    import axios from 'axios';
    import vSelect from 'vue-select';

    import ListadoIndicadores from './ListadoIndicadores';

    export default {
        components : { vSelect, ListadoIndicadores},
        data : function () {
            return {
                filtro : '',
                cargando_uso: false,
                cargando_tecnica: false,
            }
        },        
        computed : {
            state() {
                return this.$store.state.clasificacion_uso != null ;
            },
            stateT() {
                return this.$store.state.clasificacion_tecnica != null ;
            },
            invalidCU() {
                return this.$store.state.clasificacion_uso != null 
            },
            invalidCT() {
                return this.$store.state.clasificacion_tecnica != null ;
            },
            indicadoresFiltrados() {
                return this.filtrar(this.$store.state.indicadoresClasificados, this.filtro);
            }
        },
        methods : {
            getClasificacionesTecnica: function ( clasificacionUso ) {
                let vm =  this;
                this.cargando_tecnica = true;
                axios.get( '/api/v1/tablero/clasificacionTecnica?id=' + clasificacionUso.id )
                    .then(function (response) {
                        

                        if ( response.data.data.length == 0 ) {
                            vm.$snotify.warning(vm.$t("_datos_no_encontrados_"));
                            vm.$store.state.clasificaciones_tecnica = [];
                        } else {
                            vm.$store.state.clasificaciones_tecnica = response.data.data;
                        }
                    }).catch( function( error)  {
                        vm.$snotify.error(vm.$t("_error_conexion_"), $t("_error_") );
                    }).finally( function () {
                        vm.cargando_tecnica = false;
                    })
                ;
            },

            getIndicadores : function ( clasificacionTecnica ) {
                let vm =  this;
                this.cargando_tecnica = true;
                axios.get( '/api/v1/tablero/listaIndicadores?tipo=clasificados&uso=' + this.$store.state.clasificacion_uso.id +'&tecnica=' + clasificacionTecnica.id )
                    .then(function (response) {                        

                        if ( response.data.data.length == 0 ) {
                            vm.$snotify.warning(vm.$t("_datos_no_encontrados_"));
                        } else {
                            vm.$store.state.indicadoresClasificados = response.data.data;
                        }
                        //vm.$emit("cant-clasificados");
                    }).catch( function( error)  {
                        vm.$snotify.error(vm.$t("_error_conexion_"), "Error");
                    }).finally( function () {
                        vm.cargando_tecnica = false;
                    })
                    ;


            },

            agregarIndicador : function ( indicador ){
                //vm.$emit("cant-clasificados", indicador);
            },

            filtrar : function (listado, filtro) {
                return listado.filter(ind => {
                    let base = Vue.filter('normalizarDiacriticos')(ind.nombre);
                    let filtro_ = Vue.filter('normalizarDiacriticos')(filtro);
                    return base.includes(filtro_);
                });
            }
        }
    }
</script>