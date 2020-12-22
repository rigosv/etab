<template>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb" style="background-color: #FFFFFF">
      <li
        class="breadcrumb-item"
        v-for="(link, indexF) in indicador.filtros"
        :key="indexF"
      >
        <a href="#" @click.prevent="breadcum(indexF)"
          >{{ link.codigo.toUpperCase() }}: {{ link.valor }}</a
        >
      </li>
      <li
        class="breadcrumb-item"
        v-if="indicador.filtros.length == 0"
        style="color: white"
      >
        _
      </li>
    </ol>
  </nav>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";

import useCargadorDatos from "../Compositions/useCargadorDatos";

export default defineComponent({
  props: {
    indicador: { default: {}, type: Object },
    index: { default: 0, type: Number }
  },

  setup(props, { root }) {
    const { cargarDatosIndicador } = useCargadorDatos(root);

    return {
      cargarDatosIndicador
    };
  },

  methods: {
    breadcum(indexF: any): void {
      //poner la nueva dimension
      this.indicador.dimension = this.indicador.filtros[indexF].codigo;
      this.indicador.filtros = this.indicador.filtros.slice(0, indexF);
      this.indicador.dimensionIndex = indexF;
      this.cargarDatosIndicador(this.indicador, this.index);
    }
  }
});
</script>
