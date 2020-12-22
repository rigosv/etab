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

            <buscar v-model="searchFilter"></buscar>
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

            <buscar v-model="searchFilterP"></buscar>
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

            <buscar v-model="searchFilterG"></buscar>
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
import {
  computed,
  defineComponent,
  onMounted,
  ref
} from "@vue/composition-api";

import Buscar from "../Buscar.vue";
import ListadoSalas from "../ListadoSalas.vue";
import useIndicador from "../../Compositions/useIndicador";
import useCargadorDatos from "../../Compositions/useCargadorDatos";
import useCadena from "../../Compositions/useCadena";
import { Sala } from "../../Interfaces/Sala";
import { Indicador } from "../../Interfaces/Indicador";
import EventService from "../../services/EventService";

export default defineComponent({
  components: { Buscar, ListadoSalas },

  setup(props, { root }) {
    const salas = ref<Array<Sala>>([]);
    const salasPropias = ref<Array<Sala>>([]);
    const salasGrupos = ref<Array<Sala>>([]);
    const searchFilter = ref("");
    const searchFilterG = ref("");
    const searchFilterP = ref("");

    const { normalizarDiacriticos } = useCadena();
    const { inicializarIndicador } = useIndicador();
    const { cargarDatosIndicador } = useCargadorDatos(root);

    const activarSala = (sala: Sala): void => {
      root.$store.commit("setSalaActiva", sala);

      const indicadores = sala.indicadores.map(
        (indicador: Indicador, index: number) => {
          return inicializarIndicador(indicador, index);
        }
      );

      // Cargar los datos de los indicadores de la sala
      let index = 0;
      for (const indicador of indicadores) {
        cargarDatosIndicador(indicador, index++);
      }

      root.$store.commit("setIndicadores", indicadores);
      root.$store.state.indicadoresAllData = [];

      //Cargar las acciones de la sala
      //const vm = this;
      EventService.getSalaAcciones(sala.id).then(response => {
        if (response.data.status == 200) {
          root.$store.state.salaAcciones = response.data.data;
        }
      });

      //Cargar usuarios de la sala
      EventService.getSalaUsuarios(sala.id)
        .then(response => {
          root.$store.state.salaUsuarios = response.data.data;
        })
        .catch(() => {
          root.$snotify.error(root.$t("_errorConexion_") as string, "Error");
        });
      //Cargar los comentarios de la sala
      EventService.getSalaComentarios(sala.id)
        .then(response => {
          root.$store.state.salaComentarios = response.data.data;
        })
        .catch(() => {
          root.$snotify.error(
            root.$t("_errorConexionComentariosSala_") as string,
            "Error"
          );
        });
    };

    onMounted(() => {
      root.$snotify.async(root.$t("_cargandoSalas_") as string, () => {
        return new Promise((resolve, reject) => {
          const params = {
            id: root.$store.state.idSala,
            token: root.$store.state.token
          };

          return EventService.getSalas(params)
            .then(response => {
              salas.value = response.data.data;
              salasPropias.value = response.data.salas_propias;

              root.$store.state.salasPropias =
                salas.value.length > 0
                  ? salasPropias.value.map(sala => {
                      return sala.id;
                    })
                  : [];

              salasGrupos.value = response.data.salas_grupos;
              resolve({
                title: root.$t("_salas_") as string,
                body: root.$t("_salasCargadas_") as string,
                config: {
                  closeOnClick: true,
                  timeout: 3000,
                  showProgressBar: true
                  //position: 'rightTop'
                }
              });
              if (
                root.$store.state.token != "" &&
                root.$store.state.idSala != ""
              ) {
                //una sala pública, cargarla
                activarSala(salas.value[0]);
              }
            })
            .catch(error => {
              console.log(error);
              reject({
                title: root.$t("_error_"),
                body: root.$t("_errorConexion_"),
                config: {
                  closeOnClick: true,
                  showProgressBar: true,
                  timeout: 10000
                }
              });
            });
        });
      });
    });

    const filtrar = (listado: Sala[], filtro: string): Sala[] => {
      let listadoFiltrado: Sala[] = [];
      if (listado != undefined && listado.length > 0) {
        listadoFiltrado = listado.filter((sala: Sala) => {
          const base = normalizarDiacriticos(sala.nombre);
          const filtro_ = normalizarDiacriticos(filtro);
          return base.includes(filtro_);
        });
      }
      return listadoFiltrado;
    };

    const borrarSala = (sala: Sala): void => {
      salas.value = salas.value.filter((s: Sala) => {
        return s.id != sala.id;
      });
      salasPropias.value = salasPropias.value.filter((s: Sala) => {
        return s.id != sala.id;
      });
      salasGrupos.value = salasGrupos.value.filter((s: Sala) => {
        return s.id != sala.id;
      });
    };

    const salasFiltradas = computed((): Sala[] => {
      return filtrar(salas.value, searchFilter.value);
    });

    const salasPropiasFiltradas = computed((): Sala[] => {
      return filtrar(salasPropias.value, searchFilterP.value);
    });

    const salasGruposFiltradas = computed((): Sala[] => {
      return filtrar(salasGrupos.value, searchFilterG.value);
    });

    return {
      salas,
      salasPropias,
      salasGrupos,
      searchFilter,
      searchFilterG,
      searchFilterP,
      salasPropiasFiltradas,
      salasFiltradas,
      salasGruposFiltradas,
      borrarSala,
      activarSala
    };
  }
});
</script>
