<template>
  <b-modal
    id="modalIndicadores"
    :title="$t('_seleccione_indicador_')"
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
            <font-awesome-icon icon="ban" />{{ $t("_sin_clasificacion_") }}
            <b-badge variant="primary">{{
              indicadores_no_clasificados.length
            }}</b-badge>
          </template>

          <ListadoIndicadores :indicadores="indicadores_no_clasificados" />
        </b-tab>

        <b-tab>
          <template slot="title">
            <font-awesome-icon icon="search" /> {{ $t("_busqueda_libre_") }}
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
import { Component, Vue } from "vue-property-decorator";
import axios from "axios";

import ListadoIndicadoresClasificados from "../ListadoIndicadoresClasificados.vue";
import ListadoIndicadores from "../ListadoIndicadores.vue";
import Buscar from "../Buscar.vue";

@Component({
  components: { ListadoIndicadoresClasificados, Buscar, ListadoIndicadores },
})
export default class ModalIndicadores extends Vue {
  private indicadores_no_clasificados: any[] = [];
  private indicadores_favoritos: any[] = [];
  indicadores_libres: any[] = [];
  filtroLibre: string = "";

  mounted() {
    let vm = this;
    this.$root.$on("bv::modal::show", (bvEvent: any, modalId: any) => {
      //Cargar indicadores favoritos
      axios
        .get("/api/v1/tablero/listaIndicadores?tipo=favoritos")
        .then(function(response) {
          vm.indicadores_favoritos = response.data.data;
        })
        .catch(function(error) {
          vm.$snotify.error(vm.$t("_error_conexion_") as string, "Error");
        });
    });
    //Cargar la clasificación de uso
    axios
      .get("/api/v1/tablero/clasificacionUso")
      .then(function(response) {
        vm.$store.state.clasificaciones_uso = response.data.data;
      })
      .catch(function(error) {
        vm.$snotify.error(vm.$t("_error_conexion_") as string, "Error");
      });

    //Cargar indicadores no clasificados
    axios
      .get("/api/v1/tablero/listaIndicadores?tipo=no_clasificados")
      .then(function(response) {
        vm.indicadores_no_clasificados = response.data.data;
      })
      .catch(function(error) {
        vm.$snotify.error(vm.$t("_error_conexion_") as string, "Error");
      });
  }

  public buscarIndicadoresLibre(): void {
    let vm = this;
    axios
      .get(
        "/api/v1/tablero/listaIndicadores?tipo=busqueda&busqueda=" +
          this.filtroLibre
      )
      .then(function(response) {
        if (response.data.status == 200) {
          vm.indicadores_libres = response.data.data;
        } else {
          vm.indicadores_libres = [];
          vm.$snotify.info(vm.$t("_datos_no_encontrados_") as string);
        }
      })
      .catch(function(error) {
        console.log(error);
        vm.$snotify.error(vm.$t("_error_conexion_") as string, "Error");
      });
  }
}
</script>
