<template>

    <div id="sala" >
        <b-input-group size="md" class="mt-3" v-if="$store.state.abrio_sala || $store.state.abrio_indicador">
            <b-input-group-text slot="prepend" ><font-awesome-icon icon="th" /> {{ $t('_sala_')}}</b-input-group-text>
            <b-form-input 
                v-model="nombreSala" 
                :state="nameState"
                :placeholder="$t('_debe_proporcionar_nombre_sala_')"
                trim
                :readonly="esSalaPublica"
            >
            </b-form-input>
            <b-input-group-append class="d-print-none">
                <b-button variant="outline-primary" @click="guardarSala('guardar')"
                          :title="$t('_guardar_sala_' )"
                          v-if="nameState && !esSalaPublica && $store.state.salas_propias.includes($store.state.sala.id)" >
                    <font-awesome-icon icon="save" /><span class="d-none d-md-block float-right">{{ $t("_guardar_sala_" ) }}</span>
                </b-button>
                <b-button variant="outline-info" @click="guardarSala('guardar_como')"
                          :title="$t('_guardar_sala_como_' )"
                          v-if="nameState && !esSalaPublica" >
                    <font-awesome-icon icon="share-alt" /><span class="d-none d-md-block float-right">{{ $t("_guardar_sala_como_" ) }}</span>
                </b-button>
                <b-button variant="outline-danger" v-if="!esSalaPublica" @click="cerrarSala()"
                          :title="$t('_cerrar_sala_' )"
                >
                    <font-awesome-icon icon="times" /><span class="d-none d-md-block float-right">{{ $t("_cerrar_sala_" ) }}</span>
                </b-button>
            </b-input-group-append>
        </b-input-group>
        <grid-layout
            v-if= "$store.state.indicadores.length > 0 "
            :layout.sync="$store.state.layout"
            :row-height="30"
            :responsive="true"
            :autoSize="false"
        >

            <grid-item v-for="item in $store.state.layout" class="grid-item  rounded"
                    :key="item.i"
                    :x="item.x"
                    :y="item.y"
                    :w="item.w"
                    :h="item.h"
                    :i="item.i"
                    :dragAllowFrom="'.draggable-handler'"
                    @resized="resizedEvent"
                    @moved="movedEvent"
            >
                <IndicadorC :ref="'indicador'+item.i" :indicador="$store.state.indicadores.filter( ind => { return ind.index == item.i})[0]" :index="item.i" @full-screen="fullscreen($event)"/>
            </grid-item>
        </grid-layout>
    </div>
</template>

<script>
    import axios from 'axios';

    import IndicadorC from './IndicadorC';
    import VueGridLayout from 'vue-grid-layout';



    export default  {
        components: { IndicadorC, GridLayout: VueGridLayout.GridLayout,
           GridItem: VueGridLayout.GridItem },
        computed: {
            nameState() {                
                return this.nombreSala.length > 0 ? true : false
            },
            nombreSala : {                
                get: function () {                    
                    return this.$store.state.sala.nombre;
                },
                set: function (newValue) {
                    this.$store.state.sala.nombre = newValue;
                }
            },
            esSalaPublica : function() {
                return ( token != '' && idSala != '' );
            }
        },
        methods : {
            movedEvent: function(i, newX, newY){
                console.log("MOVED i=" + i + ", X=" + newX + ", Y=" + newY);
                this.$store.state.indicadores.map( ind => {
                    if ( ind.index == i ){
                        ind.configuracion.layout.x = newX;
                        ind.configuracion.layout.y = newY;
                    }
                });

                console.log(this.$store.state.indicadores);
            },
            /**
             * 
             * @param i the item id/index
             * @param newH new height in grid rows 
             * @param newW new width in grid columns
             * @param newHPx new height in pixels
             * @param newWPx new width in pixels
             * 
             */
            resizedEvent: function(i, newH, newW, newHPx, newWPx){
                console.log("RESIZED i=" + i + ", H=" + newH + ", W=" + newW + ", H(px)=" + newHPx + ", W(px)=" + newWPx);
                this.$store.state.indicadores.map( ind => {
                    if ( ind.index == i ){
                        ind.configuracion.layout.w = newW;
                        ind.configuracion.layout.h = newH;
                    }
                });

                console.log(this.$store.state.indicadores);
            },

            guardarSala : function ( tipo ) {
                let salaDatos = (tipo == 'guardar') ? this.$store.state.sala: {id : '', nombre: this.$store.state.sala.nombre };
                let json = { sala: salaDatos, indicadores: this.$store.state.indicadores };                
                this.$store.state.sala_cargando = true;
                let vm = this;

                if ( tipo == 'guardar_como' && this.$store.state.sala.nombre == this.$store.state.sala_nombre_ini ){
                    vm.$snotify.warning(vm.$t('_guardar_sala_error_nombre_diferente_'), 'Error');
                } else {
                    axios.post( '/api/v1/tablero/guardarSala', json )
                        .then(function (response) {
                            if ( response.data.status == 200 ){
                                vm.$store.state.abrio_sala = true;
                                vm.$store.state.sala.id = response.data.data;
                                vm.$store.state.sala_nombre_ini = vm.$store.state.sala.nombre;
                                vm.$store.state.salas_propias.push(response.data.data);
                                vm.$snotify.success(vm.$t('_sala_guardada_'));                            

                            } else {
                                vm.$snotify.error(vm.$t('_guardar_sala_error_'), 'Error', { timeout: 10000 });
                            }
                        }).catch(function (error) {
                            vm.$snotify.error(vm.$t('_guardar_sala_error_'), 'Error', { timeout: 10000 });
                            console.log(error);
                        }).finally( function(){
                            vm.$store.state.sala_cargando = false;
                        });
                }
            },

            cerrarSala : function () {
                this.$store.commit('setIndicadores', []);
                this.$store.state.indicadoresAllData = [];
                this.$store.state.sala = {nombre: ''};
                this.$store.state.abrio_sala = false;
                this.$store.state.abrio_indicador = false;
            },

        }
    }
</script>