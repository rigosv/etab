<template>
  <b-modal
    id="modalIndicadores"
    :title="$t('_seleccioneIndicador_')"
    ok-only
    size="lg"
  >
    <b-card no-body>
      <b-tabs card>
        <b-tab active>
          <template slot="title">
            <font-awesome-icon icon="tasks" />{{ $t("_clasificacion_") }}
            <b-badge variant="primary">{{
              $store.state.indicadoresClasificados.length
            }}</b-badge>
          </template>
          <ListadoIndicadoresClasificados />
        </b-tab>

        <b-tab>
          <template slot="title">
            <font-awesome-icon icon="ban" />{{ $t("_sinClasificacion_") }}
            <b-badge variant="primary">{{
              indicadores_no_clasificados.length
            }}</b-badge>
          </template>

          <ListadoIndicadores :indicadores="indicadores_no_clasificados" />
        </b-tab>

        <b-tab>
          <template slot="title">
            <font-awesome-icon icon="search" /> {{ $t("_busquedaLibre_") }}
            <b-badge variant="primary">{{ indicadores_libres.length }}</b-badge>
          </template>

          <div class="col-sm-12 " style="padding: 20px;">
            <buscar
              v-model="filtroLibre"
              @buscar="buscarIndicadoresLibre"
              :enter="true"
              ref="buscarInputLibre"
            ></buscar>

            <ListadoIndicadores
              :indicadores="indicadores_libres"
              :busqueda="false"
            />
          </div>
        </b-tab>

        <b-tab>
          <template slot="title">
            <font-awesome-icon icon="star" /> {{ $t("_favoritos_") }}
            <b-badge variant="primary">{{
              indicadores_favoritos.length
            }}</b-badge>
          </template>

          <ListadoIndicadores :indicadores="indicadores_favoritos" />
        </b-tab>
      </b-tabs>
    </b-card>
  </b-modal>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";

import ListadoIndicadoresClasificados from "../ListadoIndicadoresClasificados.vue";
import ListadoIndicadores from "../ListadoIndicadores.vue";
import Buscar from "../Buscar.vue";
import EventService from "../../services/EventService";

export default defineComponent({
  components: { ListadoIndicadoresClasificados, Buscar, ListadoIndicadores },
  data: () => ({
    indicadores_no_clasificados: [],
    indicadores_favoritos: [],
    indicadores_libres: [],
    filtroLibre: ""
  }),

  mounted() {
    this.$root.$on("bv::modal::show", (bvEvent: any, modalId: any) => {
      if (modalId === "modalIndicadores") {
        //Cargar indicadores favoritos
        EventService.getIndicadoresFavoritos()
          .then(response => {
            this.indicadores_favoritos = response.data.data;
          })
          .catch(() => {
            this.$snotify.error(this.$t("_errorConexion_") as string, "Error");
          });
      }
    });
    //Cargar la clasificación de uso
    EventService.getClasificacionUso()
      .then(response => {
        this.$store.state.clasificacionesUso = response.data.data;
      })
      .catch(() => {
        this.$snotify.error(this.$t("_errorConexion_") as string, "Error");
      });

    //Cargar indicadores no clasificados
    EventService.getIndicadoresNoClasificados()
      .then(response => {
        this.indicadores_no_clasificados = response.data.data;
      })
      .catch(() => {
        this.$snotify.error(this.$t("_errorConexion_") as string, "Error");
      });
  },

  methods: {
    buscarIndicadoresLibre(): void {
      EventService.getIndicadoresBusqueda(this.filtroLibre)
        .then(response => {
          if (response.data.status == 200) {
            this.indicadores_libres = response.data.data;
          } else {
            this.indicadores_libres = [];
            this.$snotify.info(this.$t("_datosNoEncontrados_") as string);
          }
        })
        .catch(error => {
          console.log(error);
          this.$snotify.error(this.$t("_errorConexion_") as string, "Error");
        });
    }
  }
});
</script>
