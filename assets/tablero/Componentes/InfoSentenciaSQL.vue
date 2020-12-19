<template>
  <div style="max-height:400px; max-width:100%; overflow:auto;">
    <pre>{{ indicador.sql }}</pre>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";
import axios from "axios";
import sqlFormatter from "sql-formatter";

export default defineComponent({
  props: {
    indicador: { default: {}, type: Object }
  },

  mounted() {
    const json = { filtros: this.indicador.filtros, ver_sql: true, tendencia: false };

    console.log(this.indicador.sql);
    if (this.indicador.sql === "") {
      axios
        .get(
          "/api/v1/tablero/datosIndicador/" +
            this.indicador.id +
            "/" +
            this.indicador.dimension,
          { params: json }
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
