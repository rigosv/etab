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
              indicadoresNoClasificados.length
            }}</b-badge>
          </template>

          <ListadoIndicadores :indicadores="indicadoresNoClasificados" />
        </b-tab>

        <b-tab>
          <template slot="title">
            <font-awesome-icon icon="search" /> {{ $t("_busquedaLibre_") }}
            <b-badge variant="primary">{{ indicadoresLibres.length }}</b-badge>
          </template>

          <div class="col-sm-12 " style="padding: 20px;">
            <buscar
              v-model="filtroLibre"
              @buscar="buscarIndicadoresLibre"
              :enter="true"
              ref="buscarInputLibre"
            ></buscar>

            <ListadoIndicadores
              :indicadores="indicadoresLibres"
              :busqueda="false"
            />
          </div>
        </b-tab>

        <b-tab>
          <template slot="title">
            <font-awesome-icon icon="star" /> {{ $t("_favoritos_") }}
            <b-badge variant="primary">{{
              indicadoresFavoritos.length
            }}</b-badge>
          </template>

          <ListadoIndicadores :indicadores="indicadoresFavoritos" />
        </b-tab>
      </b-tabs>
    </b-card>
  </b-modal>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from "@vue/composition-api";

import ListadoIndicadoresClasificados from "../ListadoIndicadoresClasificados.vue";
import ListadoIndicadores from "../ListadoIndicadores.vue";
import Buscar from "../Buscar.vue";
import EventService from "../../services/EventService";

export default defineComponent({
  components: { ListadoIndicadoresClasificados, Buscar, ListadoIndicadores },
  setup(props, { root }) {
    const indicadoresNoClasificados = ref([]);
    const indicadoresFavoritos = ref([]);
    const indicadoresLibres = ref([]);
    const filtroLibre = ref("");

    onMounted(() => {
      root.$on("bv::modal::show", (bvEvent: any, modalId: any) => {
        if (modalId === "modalIndicadores") {
          //Cargar indicadores favoritos
          EventService.getIndicadoresFavoritos()
            .then(response => {
              indicadoresFavoritos.value = response.data.data;
            })
            .catch(() => {
              root.$snotify.error(
                root.$t("_errorConexion_") as string,
                "Error"
              );
            });
        }
      });
      //Cargar la clasificación de uso
      EventService.getClasificacionUso()
        .then(response => {
          root.$store.commit("setClasificacionUso", response.data.data);
        })
        .catch(() => {
          root.$snotify.error(root.$t("_errorConexion_") as string, "Error");
        });

      //Cargar indicadores no clasificados
      EventService.getIndicadoresNoClasificados()
        .then(response => {
          indicadoresNoClasificados.value = response.data.data;
        })
        .catch(() => {
          root.$snotify.error(root.$t("_errorConexion_") as string, "Error");
        });
    });

    const buscarIndicadoresLibre = (): void => {
      EventService.getIndicadoresBusqueda(filtroLibre.value)
        .then(response => {
          if (response.data.status == 200) {
            indicadoresLibres.value = response.data.data;
          } else {
            indicadoresLibres.value = [];
            root.$snotify.info(root.$t("_datosNoEncontrados_") as string);
          }
        })
        .catch(error => {
          console.log(error);
          root.$snotify.error(root.$t("_errorConexion_") as string, "Error");
        });
    };

    return {
      buscarIndicadoresLibre,
      indicadoresNoClasificados,
      indicadoresFavoritos,
      indicadoresLibres,
      filtroLibre
    };
  }
});
</script>
