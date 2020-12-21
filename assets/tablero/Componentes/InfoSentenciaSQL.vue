<template>
  <div style="max-height:400px; max-width:100%; overflow:auto;">
    <pre>{{ indicador.sql }}</pre>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";
import sqlFormatter from "sql-formatter";
import EventService from "../services/EventService";

export default defineComponent({
  props: {
    indicador: { default: {}, type: Object }
  },

  mounted() {
    const json = {
      filtros: this.indicador.filtros,
      ver_sql: true,
      tendencia: false
    };
    if (this.indicador.sql === "") {
      EventService.getDatosIndicador(
        this.indicador.id,
        this.indicador.dimension,
        json
      )
        .then(response => {
          if (response.status == 200) {
            this.indicador.sql = sqlFormatter.format(response.data.data);
          }
          this.indicador.cargando = false;
        })
        .catch(error => {
          console.log(error);
          this.indicador.cargando = false;
        });
    }
  }
});
</script>
