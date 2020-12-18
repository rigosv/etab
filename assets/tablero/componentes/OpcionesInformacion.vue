<template>
  <div class="container-fluid row" style="padding: 20px 0px 0px 0px; ">
    <b-card no-body class="col-12">
      <b-tabs pills card>
        <b-tab active>
          <template slot="title">
            <font-awesome-icon icon="clipboard-list" />
            {{ $t("_fichaTecnica_") }}
          </template>
          <b-card-text>
            <InfoFichaTecnica :indicador="indicador" />
          </b-card-text>
        </b-tab>
        <b-tab>
          <template slot="title">
            <font-awesome-icon icon="table" /> {{ $t("_tablaDatos_") }}
          </template>
          <b-card-text>
            <InfoTablaDatos :indicador="indicador" />
          </b-card-text>
        </b-tab>
        <b-tab v-if="indicador.informacion.rangos.length > 0">
          <template slot="title">
            <font-awesome-icon icon="bell" /> {{ $t("_rangosAlerta_") }}
          </template>
          <b-card-text>
            <InfoRangosAlerta :indicador="indicador" />
          </b-card-text>
        </b-tab>
        <b-tab>
          <template slot="title">
            <font-awesome-icon icon="bolt" /> {{ $t("_sentenciaSQL_") }}
          </template>
          <b-card-text>
            <InfoSentenciaSQL :indicador="indicador" />
          </b-card-text>
        </b-tab>
      </b-tabs>
    </b-card>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";
import InfoFichaTecnica from "./InfoFichaTecnica.vue";
import InfoTablaDatos from "./InfoTablaDatos.vue";
import InfoSentenciaSQL from "./InfoSentenciaSQL.vue";
import InfoRangosAlerta from "./InfoRangosAlerta.vue";

export default defineComponent({
  components: {
    InfoFichaTecnica,
    InfoTablaDatos,
    InfoSentenciaSQL,
    InfoRangosAlerta
  },

  props: {
    indicador: { default: {}, type: Object },
    index: Number
  }
});
</script>
