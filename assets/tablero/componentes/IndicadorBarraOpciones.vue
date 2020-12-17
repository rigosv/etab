<template>
  <div>
    <b-navbar
      ttype="light"
      style="background-color: #F8F8F8; padding: 1px !important;"
    >
      <b-navbar-nav>
        <b-nav-item
          href="#"
          :title="$t('_favoritos_')"
          @click="$emit('agregar-favorito', indicador)"
        >
          <font-awesome-icon
            icon="star"
            :style="{ color: indicador.es_favorito ? 'orange' : '' }"
          />
        </b-nav-item>

        <b-nav-item
          href="#"
          :title="$t('_mostrar_tabla_datos_')"
          @click="indicador.configuracion.mostrarTablaDatos = true"
          v-if="!indicador.configuracion.mostrarTablaDatos"
        >
          <font-awesome-icon icon="table" />
        </b-nav-item>

        <b-nav-item
          href="#"
          :title="$t('_mostrar_grafico_')"
          @click="indicador.configuracion.mostrarTablaDatos = false"
          v-if="indicador.configuracion.mostrarTablaDatos"
        >
          <font-awesome-icon icon="chart-bar" />
        </b-nav-item>

        <b-nav-item
          href="#"
          @click="$emit('descargar-grafico')"
          :title="$t('_descargar_grafico_')"
          v-if="!indicador.configuracion.mostrarTablaDatos"
        >
          <font-awesome-icon icon="download" />
        </b-nav-item>

        <b-nav-item :title="$t('_refresh_')" @click="refrescar">
          <font-awesome-icon icon="sync-alt" />
        </b-nav-item>

        <b-nav-item
          :title="
            indicador.full_screen
              ? $t('_restaurar_tamanio_')
              : $t('_full_screen_')
          "
          @click="$emit('full-screen')"
        >
          <font-awesome-icon
            :icon="indicador.full_screen ? 'compress-arrows-alt' : 'expand'"
            :style="indicador.full_screen ? 'color: blue' : ''"
          />
        </b-nav-item>

        <b-nav-item
          :title="$t('_configurar_')"
          @click="configurar"
          v-if="!indicador.configuracion.mostrarTablaDatos"
        >
          <font-awesome-icon
            icon="cogs"
            :style="indicador.mostrar_configuracion ? 'color: green' : ''"
          />
        </b-nav-item>

        <b-nav-item
          :title="
            indicador.tendencia ? $t('_ver_grafica_') : $t('_ver_tendencia_')
          "
          @click="tendencia"
        >
          <font-awesome-icon icon="chart-line" v-if="!indicador.tendencia" />
          <font-awesome-icon icon="chart-bar" v-if="indicador.tendencia" />
        </b-nav-item>
      </b-navbar-nav>
    </b-navbar>
    <ModalConfiguracion :indicador="indicador" :index="index" />
  </div>
</template>

<script lang="ts">
import { Component, Vue, Prop, Mixins } from "vue-property-decorator";
import IndicadorMixin from "../Mixins/IndicadorMixin";
import ModalConfiguracion from "./Modal/ModalConfiguracion.vue";

@Component({
  components: { ModalConfiguracion }
})
export default class IndicadorBarraOpciones extends Mixins(IndicadorMixin) {
  @Prop({ default: {} }) indicador: any;
  @Prop() readonly index!: number;

  private grafico: any = {};

  public refrescar(): void {
    this.indicador.filtros = [];
    this.indicador.dimension = this.indicador.dimensiones[0];
    this.cargarDatosIndicador(this.indicador, this.index);
  }

  public tendencia(): void {
    this.indicador.tendencia = !this.indicador.tendencia;
    if (this.indicador.tendencia) {
      this.indicador.tipo_grafico_ant = this.indicador.configuracion.tipo_grafico;
      this.indicador.configuracion.tipo_grafico = "LINEAS";
    } else {
      this.indicador.configuracion.tipo_grafico = this.indicador.tipo_grafico_ant;
    }
    this.cargarDatosIndicador(this.indicador, this.index);
  }

  public configurar(): void {
    if (this.indicador.full_screen) {
      this.indicador.mostrar_configuracion = !this.indicador
        .mostrar_configuracion;
    } else {
      this.$bvModal.show("modalConfiguracion__" + this.index);
    }
  }
}
</script>
