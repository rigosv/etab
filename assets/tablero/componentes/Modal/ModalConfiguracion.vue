<template>
  <div style="position: absolute;">
    <b-modal
      :id="'modalConfiguracion__' + index"
      header-bg-variant="info"
      header-text-variant="light"
      style=" background: rgba(0,0,0,0.4); "
      ok-only
      size="lg"
    >
      <template slot="modal-title">
        <h3>{{ $t("_configurarIndicador_") }}</h3>
        <h4>{{ indicador.nombre }}</h4>
      </template>

      <ConfiguracionIndicador :indicador="indicador" :index="index" />
    </b-modal>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";
import ConfiguracionIndicador from "../ConfiguracionIndicador.vue";

export default defineComponent({
  components: { ConfiguracionIndicador },
  props: {
    indicador: Object,
    index: Number
  }
});
</script>
