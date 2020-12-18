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
import { defineComponent } from "@vue/composition-api";

export default defineComponent ({
  props: {
    indicador: {default: {}, type: Object},
    index: Number
  },

  data : () => ({
    dismissSecs : 10,
    dismissCountDown : 0,
    showDismissibleAlert : false,    
  }),

  computed : {
    error(): any {
      return this.indicador.error;
    },
    
    mensajes(): object[] {
      return [
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
      ]
    }
  },

  methods : {
    countDownChanged(dismissCountDown: number): void {
      this.dismissCountDown = dismissCountDown;
    }
  },

  watch: {
    error() {
      if (this.indicador.mensaje != "") {
        this.dismissCountDown = this.dismissSecs;
      }
    }
  }
})
</script>
