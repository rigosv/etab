<template>
  <b-form-group
    :label="$t('_clic_indicador_para_agregarlo_tablero_')"
    v-if="indicadores.length > 0"
  >
    <div style="max-height: 40vh; min-height: 70px; overflow: auto;">
      <b-input-group class="mt-3">
        <b-input-group-text slot="prepend">
          <font-awesome-icon icon="search" />
        </b-input-group-text>
        <b-form-input
          autocomplete="off"
          :placeholder="$t('_buscar_')"
          v-model="filtro"
        >
        </b-form-input>
      </b-input-group>

      <b-list-group>
        <b-list-group-item
          v-for="(ind, k) in indicadoresFiltrados"
          :key="k"
          @click.prevent="agregarIndicador(ind)"
          class="d-flex justify-content-between align-items-center"
          button
        >
          {{ ind.nombre }}
          <b-badge variant="primary" pill v-if="getConteo(ind.id) > 0">
            {{ getConteo(ind.id) }}
          </b-badge>
        </b-list-group-item>
      </b-list-group>
    </div>
  </b-form-group>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";

import IndicadorMixin from "../Mixins/IndicadorMixin";

export default defineComponent({
  props: {
    indicador: { default: {}, type: Object }
  },

  mixins: [IndicadorMixin],

  data: () => ({
    filtro: ""
  }),

  computed: {
    indicadoresFiltrados(): any {
      return this.filtrar(this.indicadores, this.filtro);
    }
  },

  methods: {
    agregarIndicador(indicador: any): void {
      //Buscar el maximo indice utilizado
      const index =
        this.$store.state.indicadores.length == 0
          ? 0
          : Math.max.apply(
              Math,
              this.$store.state.indicadores.map(function(o: any) {
                return o.index;
              })
            ) + 1;
      indicador.filtro = "";
      indicador.orden = "";
      indicador.indicador_id = indicador.id;
      indicador.posicion = 0;
      indicador.dimension = Object.keys(indicador.dimensiones)[0];

      const ind = this.inicializarIndicador(indicador, index);
      this.$store.commit("agregarIndicador", ind);
      this.cargarDatosIndicador(ind, index);
    },

    filtrar(listado: any, filtro: string): any {
      return listado.filter((ind: any) => {
        const base = this.normalizarDiacriticos(ind.nombre);
        const filtro_ = this.normalizarDiacriticos(filtro);
        return base.includes(filtro_);
      });
    },

    getConteo(id: string): any {
      return this.$store.state.indicadores.filter((x: any) => {
        return x.id == id;
      }).length;
    }
  }
});
</script>
