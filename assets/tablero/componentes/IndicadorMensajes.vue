<template>
  <div>
    <b-alert
      v-for="(m, k) in mensajes"
      :key="k"
      dismissible
      fade
      :variant="m.variante"
      :show="indicador.error == m.error && dismissCountDown"
      @dismissed="
        dismissCountDown = 0;
        indicador.error = '';
      "
      @dismiss-count-down="countDownChanged"
    >
      {{ m.mensaje }}
      <b-progress
        animated
        :max="dismissSecs"
        :value="dismissCountDown"
        height="4px"
      ></b-progress>
    </b-alert>

    <b-alert
      variant="info"
      :show="
        (indicador.radial || indicador.termometro) &&
          !indicador.informacion.meta
      "
    >
      {{ $t("_grafico_aprecia_mejor_meta_") }}
    </b-alert>
  </div>
</template>

<script lang="ts">
import { Component, Vue, Prop, Watch } from "vue-property-decorator";

@Component
export default class IndicadorMensajes extends Vue {
  @Prop({ default: {} }) indicador: any;
  @Prop() readonly index!: number;

  private dismissSecs = 10;
  private dismissCountDown = 0;
  private showDismissibleAlert = false;
  private mensajes: any[] = [
    {
      variante: "success",
      error: "Success",
      mensaje: this.$t("_indicador_dimension_fin_") as string
    },
    {
      variante: "warning",
      error: "Warning",
      mensaje: this.$t("_indicador_warning_") as string
    },
    {
      variante: "danger",
      error: "Error",
      mensaje: this.$t("_indicador_error_") as string
    }
  ];

  public countDownChanged(dismissCountDown: number): void {
    this.dismissCountDown = dismissCountDown;
  }

  @Watch("indicador.error")
  public errorChange(newVal: any): void {
    if (this.indicador.mensaje != "") {
      this.dismissCountDown = this.dismissSecs;
    }
  }
}
</script>
