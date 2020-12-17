<template>
  <div class="col-12">
    <table class="table table-bordered table-striped">
      <caption>
        {{
          $t("_indicador_alertas_")
        }}
      </caption>
      <thead>
        <tr bgcolor="#E1EFFB">
          <th>{{ $t("_color_") }}</th>
          <th>{{ $t("_alerta_limite_inferior_") }}</th>
          <th>{{ $t("_alerta_limite_superior_") }}</th>
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
import { Component, Vue, Prop } from "vue-property-decorator";

@Component
export default class InfoRangosAlerta extends Vue {
  @Prop({ default: {} }) indicador: any;
}
</script>
