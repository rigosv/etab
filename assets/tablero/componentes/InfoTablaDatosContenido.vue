<template>
  <DIV
    :style="
      indicador.configuracion.mostrarTablaDatos
        ? 'height: 80%; overflow-y: auto;'
        : ''
    "
    class="contenedor-tabla-datos"
  >
    <table
      class="table table-bordered table-striped"
      id="exportar_tabla_datos"
      data-cols-width="20,20,20,10,10"
      :style="
        indicador.configuracion.mostrarTablaDatos
          ? 'width: 100%;'
          : 'max-width:15cm'
      "
    >
      <thead>
        <tr
          class="table-secondary"
          v-if="!indicador.configuracion.mostrarTablaDatos"
        >
          <th
            :colspan="Object.keys(indicador.data[0]).length"
            data-f-bold="true"
            data-fill-color="FFD6D8DB"
          >
            {{ indicador.nombre }}
          </th>
        </tr>
        <tr bgcolor="#E1EFFB">
          <th
            v-for="(value, key) in indicador.data[0]"
            :key="key"
            data-f-bold="true"
            data-fill-color="FFE1EFFB"
          >
            {{ key }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(value, key) in indicador.data" :key="key">
          <td
            :class="k != 'category' ? 'text-right' : ''"
            v-for="(v, k) in value"
            :key="k"
            :style="
              k == 'measure' && getColor(v) != ''
                ? ' border-bottom: 2px solid ' + getColor(v) + ';'
                : ' '
            "
            :data-b-b-s="k == 'measure' && getColor(v) != '' ? 'thick' : ''"
            :data-b-a-c="
              k == 'measure' && getColor(v) != '' ? getColorExceljs_(v) : ''
            "
            :data-t="k != 'category' ? 'n' : ''"
          >
            {{ v }}
          </td>
        </tr>
      </tbody>
    </table>
  </DIV>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";
import useColor from "../compositions/useColor";

export default defineComponent({
  props: {
    indicador: { default: {}, type: Object }
  },

  setup(props) {
    const { getColorExceljs } = useColor();

    const getColor = (valor: number): string => {
      let color = "";
      for (const rango of props.indicador.informacion.rangos) {
        if (valor >= rango.limite_inf && valor <= rango.limite_sup) {
          color = rango.color;
        }
      }
      return color;
    };

    const getColorExceljs_ = (v: number): string => {
      const codigo = getColor(v);
      return getColorExceljs(codigo);
    };

    return { getColor, getColorExceljs_ };
  }
});
</script>
