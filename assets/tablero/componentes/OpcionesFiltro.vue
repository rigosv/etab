<template>
  <div class="container-fluid row">
    <div class="col-12">
      <b-card :title="$t('_filtrarPorElemento_')" footer-tag="footer">
        <h5>{{ $t("_elijaElementosMostrarGrafico_") }}</h5>
        <b-list-group
          style="max-height: 40vh; min-height: 70px; overflow: auto;"
        >
          <b-list-group-item
            v-for="(item, index) in categorias"
            :key="index"
            :style="
              indicador.otros_filtros.elementos.indexOf(item.category) > -1
                ? 'background-color: #c0f4c0'
                : ''
            "
            @click="agregarOtrosFiltros(item.category)"
          >
            {{ item.category }}
            <span
              class="badge badge-primary float-right "
              v-if="
                indicador.otros_filtros.elementos.indexOf(item.category) > -1
              "
            >
              <font-awesome-icon icon="check" />
            </span>
          </b-list-group-item>
        </b-list-group>
        <b-button
          slot="footer"
          variant="danger"
          @click="indicador.otros_filtros.elementos = []"
          v-if="indicador.otros_filtros.elementos.length > 0"
          >{{ $t("_quitarFiltro_") }}</b-button
        >
      </b-card>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";

export default defineComponent({
  props: {
    indicador: { default: {}, type: Object },
    index: Number
  },

  computed: {
    categorias(): any {
      return this.indicador.data.sort((a: any, b: any) =>
        a.category.localeCompare(b.category)
      );
    }
  },

  methods: {
    agregarOtrosFiltros(valor: string): void {
      if (this.indicador.otros_filtros.elementos.indexOf(valor) > -1) {
        this.indicador.otros_filtros.elementos.splice(
          this.indicador.otros_filtros.elementos.indexOf(valor),
          1
        );
      } else {
        this.indicador.otros_filtros.elementos.push(valor);
      }
    }
  }
});
</script>
