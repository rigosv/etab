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
        <h3>{{ $t("_configurar_indicador_") }}</h3>
        <h4>{{ indicador.nombre }}</h4>
      </template>

      <ConfiguracionIndicador :indicador="indicador" :index="index" />
    </b-modal>
  </div>
</template>

<script lang="ts">
import { Component, Vue, Prop } from "vue-property-decorator";
import ConfiguracionIndicador from "../ConfiguracionIndicador.vue";

@Component({
    components: { ConfiguracionIndicador }
})
export default class ModalConfiguracion extends Vue {  
  @Prop() indicador!: Object;
  @Prop() index!: Number;
};
</script>
