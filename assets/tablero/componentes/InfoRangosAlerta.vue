<template>
  <div class="col-12">
    <table class="table table-bordered table-striped">
      <caption>
        {{
          $t("_indicadorAlertas_")
        }}
      </caption>
      <thead>
        <tr bgcolor="#E1EFFB">
          <th>{{ $t("_color_") }}</th>
          <th>{{ $t("_alertaLimiteInferior_") }}</th>
          <th>{{ $t("_alertaLimiteSuperior_") }}</th>
          <th>{{ $t("_comentarios_") }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(v, k) in indicador.ficha.alertas" :key="k">
          <td :style="{ background: v.color.codigo }">{{ v.color.color }}</td>
          <td>{{ v.limite_inf }}</td>
          <td>{{ v.limite_sup }}</td>
          <td>{{ v.comentario }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";

export default defineComponent({
  props: {
    indicador: { default: {}, type: Object }
  }
});
</script>
