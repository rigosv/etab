<template>
  <b-card no-body>
    <b-tabs card>
      <b-tab active>
        <template slot="title">
          <font-awesome-icon icon="cog" />
          {{ $t("_opcionesGrafico_") }}
        </template>
        <OpcionesGrafico :indicador="indicador" :index="index" />
      </b-tab>
      <b-tab v-if="puedeFiltrar">
        <template slot="title">
          <font-awesome-icon
            icon="filter"
            :style="{
              color: indicador.otros_filtros.elementos.length > 0 ? 'green' : ''
            }"
          />
          {{ $t("_filtros_") }}
        </template>
        <OpcionesFiltro :indicador="indicador" :index="index" />
      </b-tab>
      <b-tab>
        <template slot="title">
          <font-awesome-icon icon="info-circle" />
          {{ $t("_informacion_") }}
        </template>
        <OpcionesInformacion :indicador="indicador" :index="index" />
      </b-tab>

      <b-tab>
        <template slot="title">
          <font-awesome-icon icon="ruler-combined" />
          {{ $t("_comparacion_") }}
        </template>
        <OpcionesComparacion :indicador="indicador" :index="index" />
      </b-tab>
    </b-tabs>
  </b-card>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";

import OpcionesFiltro from "./OpcionesFiltro.vue";
import OpcionesGrafico from "./OpcionesGrafico.vue";
import OpcionesInformacion from "./OpcionesInformacion.vue";
import OpcionesComparacion from "./OpcionesComparacion.vue";
import Buscar from "./Buscar.vue";

export default defineComponent({
  components: {
    OpcionesGrafico,
    OpcionesFiltro,
    OpcionesInformacion,
    OpcionesComparacion,
    Buscar
  },

  props: {
    indicador: { default: {}, type: Object },
    index: Number
  },

  computed: {
    puedeFiltrar(): boolean {
      const porTipo = [
        "BOX",
        "BURBUJA",
        "BUBBLE",
        "BARRA",
        "BARRAS",
        "COLUMNAS",
        "COLUMNA",
        "DISCRETEBARCHART",
        "LINECHART",
        "LINEA",
        "LINEAS",
        "PIECHART",
        "PIE",
        "PASTEL",
        "TORTA"
      ].includes(this.indicador.configuracion.tipo_grafico.toUpperCase());

      return porTipo && this.indicador.configuracion.dimensionComparacion == "";
    }
  }
});
</script>
