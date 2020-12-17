<template>
  <div>
    <b-modal id="modalSalas" :title="$t('_seleccione_sala_')" ok-only size="lg">
      <b-card no-body>
        <b-tabs card>
          <b-tab active>
            <template slot="title">
              <font-awesome-icon icon="th-large" />{{ $t("_salas_") }}
              <b-badge variant="primary">{{ salasFiltradas.length }}</b-badge>
            </template>

            <buscar
              v-model="searchFilter"
              @input="filtroSalas = searchFilter"
            ></buscar>
            <listado-salas
              @activarSala="activarSala"
              :salas="salasFiltradas"
            ></listado-salas>
          </b-tab>

          <b-tab>
            <template slot="title">
              <font-awesome-icon icon="th-list" />
              {{ $t("_salas_propias_") }}
              <b-badge variant="primary">{{
                salasPropiasFiltradas.length
              }}</b-badge>
            </template>

            <buscar
              v-model="searchFilterP"
              @input="filtroSalasPropias = searchFilterP"
            ></buscar>
            <listado-salas
              @activarSala="activarSala"
              :salas="salasPropiasFiltradas"
              :borrar="true"
              @borrarSala="borrarSala"
            ></listado-salas>
          </b-tab>

          <b-tab>
            <template slot="title">
              <font-awesome-icon icon="th" />
              {{ $t("_salas_grupos_") }}
              <b-badge variant="primary">{{
                salasGruposFiltradas.length
              }}</b-badge>
            </template>

            <buscar
              v-model="searchFilterG"
              @input="filtroSalasGrupos = searchFilterG"
            ></buscar>
            <listado-salas
              @activarSala="activarSala"
              :salas="salasGruposFiltradas"
            ></listado-salas>
          </b-tab>
        </b-tabs>
      </b-card>
    </b-modal>
  </div>
</template>

<script lang="ts">
import { Component, Vue, Mixins } from "vue-property-decorator";
import axios from 'axios';

import Buscar from '../Buscar.vue';
import ListadoSalas from '../ListadoSalas.vue';
import IndicadorMixin from '../../Mixins/IndicadorMixin';

@Component({
    components: { Buscar, ListadoSalas }
})
export default class ModalSalas extends Mixins( IndicadorMixin ) {
    private salas:any[] = [];
    private salas_propias:any[] = [];
    private salas_grupos:any[] = [];
    private filtroSalas:string =  '';
    private filtroSalasPropias:string = '';
    private filtroSalasGrupos:string = '';
    private searchFilter:string = '';
    private searchFilterG:string = '';
    private searchFilterP:string = '';

    // lifecycle hook
    mounted() {
        let vm =  this;
        vm.$snotify.async( vm.$t('_cargando_salas_') as string, () => {
            return new Promise((resolve, reject) => {
                const url = '/api/v1/tablero/listaSalas';
                let params = {'id' : vm.$store.state.idSala, 'token': vm.$store.state.token};

                return axios.get( url , { params: params })
                    .then(function (response) {
                        vm.salas = response.data.data;
                        vm.salas_propias = response.data.salas_propias;

                        vm.$store.state.salas_propias = (vm.salas.length > 0 ) ?  vm.salas_propias.map( sala => { return sala.id }) : [];
                        vm.salas_grupos = response.data.salas_grupos;
                        resolve({
                            title: vm.$t('_salas_') as string,
                            body: vm.$t('_salas_cargadas_') as string,
                            config: {
                                closeOnClick: true,
                                timeout: 3000,
                                showProgressBar: true,
                                //position: 'rightTop'
                            }
                        });
                        if ( vm.$store.state.token != '' && vm.$store.state.idSala != '' ){
                            //Es una sala pública, cargarla
                            vm.activarSala( vm.salas[0] );

                        }
                    })
                    .catch(function (error) {
                        reject({
                            title:  vm.$t('_error_'),
                            body: vm.$t('_error_conexion_'),
                            config: {
                                closeOnClick: true,
                                showProgressBar: true,
                                timeout: 10000,
                            }
                        })
                    });
            });
        });
    }

    get salasFiltradas():any {
        return this.filtrar(this.salas, this.filtroSalas);
    }

    get salasPropiasFiltradas():any {
        return this.filtrar(this.salas_propias, this.filtroSalasPropias);
    }

    get salasGruposFiltradas():any {
        return this.filtrar(this.salas_grupos, this.filtroSalasGrupos);
    }



    public activarSala ( sala:any ): void {

        this.$store.state.sala =  sala;
        this.$store.state.sala_nombre_ini = sala.nombre;
        this.$store.state.sala_acciones = [];
        this.$store.state.abrio_sala = true;

        this.$store.commit('setIndicadores', []);

        let indicadores = sala.indicadores.map((indicador:any, index:number) => {
            return this.inicializarIndicador(indicador, index);
        });
        // Cargar los datos de los indicadores de la sala
        let index = 0;
        for (let indicador of indicadores) {
            this.cargarDatosIndicador(indicador, index++);
        };

        this.$store.commit('setIndicadores', indicadores);
        this.$store.state.indicadoresAllData = [];

        //Cargar las acciones de la sala
        let vm = this;
        axios.get("/api/v1/tablero/salaAccion/" + sala.id )
            .then(function (response) {
                if ( response.data.status == 200 ){
                    vm.$store.state.sala_acciones = response.data.data;
                }
            });

        //Cargar usuarios de la sala
        axios.get( '/api/v1/tablero/usuariosSala/'+this.$store.state.sala.id )
            .then(function (response) {
                vm.$store.state.sala_usuarios = response.data.data;
            }).catch( function( error)  {
                vm.$snotify.error(vm.$t("_error_conexion_") as string, "Error");
            });
        //Cargar los comentarios de la sala
        axios.get( '/api/v1/tablero/comentarioSala/'+this.$store.state.sala.id )
            .then(function (response) {
                vm.$store.state.sala_comentarios = response.data.data;
            }).catch( function( error)  {
                vm.$snotify.error(vm.$t("_error_conexion_comentarios_sala_") as string, "Error");
            });
    }

    public filtrar(listado:any, filtro:string):any {
        let listadoFiltrado: any[] = [];
        if ( listado != undefined && listado.length > 0 ){
            listadoFiltrado = listado.filter( (sala:any) => {
                let base = this.normalizarDiacriticos(sala.nombre);
                let filtro_ = this.normalizarDiacriticos(filtro);
                return base.includes(filtro_);
            });
        };
        return listadoFiltrado
    }
    
    public borrarSala( sala:any ) : void {
        let idSalaBorrada = sala.id;
        this.salas = this.salas.filter( s => { return s.id != sala.id});
        this.salas_propias = this.salas_propias.filter( s => { return s.id != sala.id});
        this.salas_grupos = this.salas_grupos.filter( s => { return s.id != sala.id});
    }
}
</script>