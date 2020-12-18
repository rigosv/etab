<template>
  <div>
    <b-modal id="modalSalas" :title="$t('_seleccioneSala_')" ok-only size="lg">
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
              {{ $t("_salasPropias_") }}
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
              {{ $t("_salasGrupos_") }}
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
import { defineComponent } from "@vue/composition-api";
import axios from "axios";

import Buscar from "../Buscar.vue";
import ListadoSalas from "../ListadoSalas.vue";
import IndicadorMixin from "../../Mixins/IndicadorMixin";

export default defineComponent({
  components: { Buscar, ListadoSalas },
  data: () => ({
    salas: [],
    salasPropias: [],
    salas_grupos: [],
    filtroSalas: "",
    filtroSalasPropias: "",
    filtroSalasGrupos: "",
    searchFilter: "",
    searchFilterG: "",
    searchFilterP: ""
  }),

  mixins: [IndicadorMixin],

  mounted() {
    //const vm = this;
    this.$snotify.async(this.$t("_cargandoSalas_") as string, () => {
      return new Promise((resolve, reject) => {
        const url = "/api/v1/tablero/listaSalas";
        const params = {
          id: this.$store.state.idSala,
          token: this.$store.state.token
        };

        return axios
          .get(url, { params: params })
          .then(response => {
            this.salas = response.data.data;
            this.salasPropias = response.data.salasPropias;

            this.$store.state.salasPropias =
              this.salas.length > 0
                ? this.salasPropias.map(sala => {
                    return sala.id;
                  })
                : [];
            this.salas_grupos = response.data.salas_grupos;
            resolve({
              title: this.$t("_salas_") as string,
              body: this.$t("_salasCargadas_") as string,
              config: {
                closeOnClick: true,
                timeout: 3000,
                showProgressBar: true
                //position: 'rightTop'
              }
            });
            if (
              this.$store.state.token != "" &&
              this.$store.state.idSala != ""
            ) {
              //Es una sala pública, cargarla
              this.activarSala(this.salas[0]);
            }
          })
          .catch(() => {
            reject({
              title: this.$t("_error_"),
              body: this.$t("_errorConexion_"),
              config: {
                closeOnClick: true,
                showProgressBar: true,
                timeout: 10000
              }
            });
          });
      });
    });
  },

  computed: {
    salasFiltradas() {
      return this.filtrar(this.salas, this.filtroSalas);
    },

    salasPropiasFiltradas() {
      return this.filtrar(this.salasPropias, this.filtroSalasPropias);
    },

    salasGruposFiltradas() {
      return this.filtrar(this.salas_grupos, this.filtroSalasGrupos);
    }
  },

  methods: {
    activarSala(sala: any): void {
      this.$store.state.sala = sala;
      this.$store.state.salaNombreIni = sala.nombre;
      this.$store.state.salaAcciones = [];
      this.$store.state.abrioSala = true;

      this.$store.commit("setIndicadores", []);

      const indicadores = sala.indicadores.map(
        (indicador: any, index: number) => {
          return this.inicializarIndicador(indicador, index);
        }
      );
      // Cargar los datos de los indicadores de la sala
      let index = 0;
      for (const indicador of indicadores) {
        this.cargarDatosIndicador(indicador, index++);
      }

      this.$store.commit("setIndicadores", indicadores);
      this.$store.state.indicadoresAllData = [];

      //Cargar las acciones de la sala
      //const vm = this;
      axios.get("/api/v1/tablero/salaAccion/" + sala.id).then(response => {
        if (response.data.status == 200) {
          this.$store.state.salaAcciones = response.data.data;
        }
      });

      //Cargar usuarios de la sala
      axios
        .get("/api/v1/tablero/usuariosSala/" + this.$store.state.sala.id)
        .then(response => {
          this.$store.state.salaUsuarios = response.data.data;
        })
        .catch(() => {
          this.$snotify.error(this.$t("_errorConexion_") as string, "Error");
        });
      //Cargar los comentarios de la sala
      axios
        .get("/api/v1/tablero/comentarioSala/" + this.$store.state.sala.id)
        .then(response => {
          this.$store.state.salaComentarios = response.data.data;
        })
        .catch(() => {
          this.$snotify.error(
            this.$t("_errorConexionComentariosSala_") as string,
            "Error"
          );
        });
    },

    filtrar(listado: any, filtro: string): any {
      let listadoFiltrado: any[] = [];
      if (listado != undefined && listado.length > 0) {
        listadoFiltrado = listado.filter((sala: any) => {
          const base = this.normalizarDiacriticos(sala.nombre);
          const filtro_ = this.normalizarDiacriticos(filtro);
          return base.includes(filtro_);
        });
      }
      return listadoFiltrado;
    },

    borrarSala(sala: any): void {
      const idSalaBorrada = sala.id;
      this.salas = this.salas.filter(s => {
        return s.id != sala.id;
      });
      this.salasPropias = this.salasPropias.filter(s => {
        return s.id != sala.id;
      });
      this.salas_grupos = this.salas_grupos.filter(s => {
        return s.id != sala.id;
      });
    }
  }
});
</script>
