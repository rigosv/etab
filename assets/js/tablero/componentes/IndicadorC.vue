<template>
    <b-card-group
        deck
        :style="(indicador.full_screen) ? 'width: 100vw; flex: none; margin: 0' : 'padding-bottom: 10px;' "
    >
        <b-card
          class="indicador-container"
          :border-variant="( indicador.full_screen) ? 'success' : '' "
          :style="(indicador.full_screen) ? 'margin: 1px; padding: 0; height: 99vh;' : '' "
          :class="(indicador.mostrar_configuracion) ? ' d-none d-md-block col-md-8 col-lg-8 col-xl8' : '' "
          header-tag="header"
          footer-tag="footer"
        >
            <div slot="header">
                <h4 style="font-size: 18px;">{{ indicador.nombre.toUpperCase() }}</h4>
                <button
                    type="button"
                    class="close close_indicador"
                    aria-label="Close"
                    @click="quitarIndicador"
                    v-if="!indicador.full_screen"
                >
                    <span aria-hidden="true">&times;</span>
                </button>

                <span
                    aria-hidden="true"
                    @click="fullscreen"
                    v-else
                    class="close restaurar-tamanio-indicador"
                    aria-label="Close"
                    :title=" $t('_restaurar_tamanio_')"
                >
                    <i class="fas fa-compress-arrows-alt"></i>
                </span>
            </div>

          <IndicadorBarraOpciones
              :indicador="indicador"
              :index="index"
              @agregar-favorito="agregarFavorito"
              @full-screen="fullscreen"
          />

          <IndicadorBreadcum :indicador="indicador" :index="index" />

          <IndicadorMensajes :indicador="indicador" :index="index" />

          <GraficoBasico
              v-if="['BARRA', 'BARRAS', 'COLUMNAS', 'COLUMNA', 'DISCRETEBARCHART', 'LINECHART', 'LINEA', 'LINEAS', 'PIECHART', 'PIE', 'PASTEL', 'TORTA'].includes(indicador.configuracion.tipo_grafico.toUpperCase() ) 
                      && indicador.cargando == false "
              :indicador="indicador"
              :index="index"
              @filtar-posicion="filtrarPosicion($event)"
              @quitar-filtros="quitarFiltros()"
              @click-plot="clicGrafico($event)"
          ></GraficoBasico>
          <Mapa
              v-if="[ 'MAPA', 'GEOLOCATION', 'MAP' ].includes(indicador.configuracion.tipo_grafico.toUpperCase() ) 
                      && indicador.cargando == false "
              :indicador="indicador"
              :index="index"
              @filtar-posicion="filtrarPosicion($event)"
              @quitar-filtros="quitarFiltros()"
              @click-plot="clicGrafico($event)"
          ></Mapa>

          <div slot="footer">
              <div>
                  <div
                      class="float-left"
                      :title=" $t('_fecha_ultima_lectura_')"
                  >[{{ indicador.informacion.ultima_lectura }}]</div>
                  <div class="float-right">{{ $t('_meta_') }}: {{ indicador.informacion.meta }}</div>
              </div>
          </div>

          <div class="container-fluid div_carga" align="center" v-if="indicador.cargando">
              <button
                type="button"
                class="close close_indicador"
                style="margin-top:9px"
                aria-label="Close"
                ng-click="indicador.cargando = false"
              >
                  <span aria-hidden="true">&times;</span>
              </button>
              <div align="center">
                  <h4>{{ $t('_cargando_indicador_') }}</h4>
                  <i class="fas fa-sync fa-spin fa-2x"></i>
              </div>
          </div>
        </b-card>
        <b-card
            header-tag="header"
            border-variant="primary"
            header-bg-variant="primary"
            header-text-variant="white"
            v-if="indicador.full_screen && indicador.mostrar_configuracion"
            :style="(indicador.mostrar_configuracion) ? 'margin: 1px; padding: 0; flex: none; height: 99vh' : '' "
            :class="(indicador.mostrar_configuracion) ? 'col-sm-12 col-xs-12 col-md-4 col-lg-4 col-xl4' : '' "
        >
            <DIV slot="header">
                <i class="fas fa-cogs fa-2x"></i>
                <span style="font-size: 20pt;">
                    <B>{{ $t("_configuracion_grafico_") }}</B>
                </span>
                <button
                    type="button"
                    class="close close_configuracion"
                    aria-label="Close"
                    @click="cerrarConfiguracion"
                >
                    <span aria-hidden="true">&times;</span>
                </button>
            </DIV>
            <ConfiguracionIndicador :indicador="indicador" :index="index" />
        </b-card>
    </b-card-group>
</template>

<script>

  import GraficoBasico from "../Graficos/GraficoBasico";
  import Mapa from "../Graficos/Mapa";
  import IndicadorBarraOpciones from "./IndicadorBarraOpciones";
  import IndicadorBreadcum from "./IndicadorBreadcum";
  import IndicadorMensajes from "./IndicadorMensajes";
  import ConfiguracionIndicador from "./ConfiguracionIndicador";

  import IndicadorMixin from "../Mixins/IndicadorMixin";

  export default {
      props: {
          indicador: Object,
          index: Number
      },
      components: {
          GraficoBasico,
          IndicadorBarraOpciones,
          IndicadorBreadcum,
          IndicadorMensajes,
          ConfiguracionIndicador,
          Mapa
      },
      mixins: [IndicadorMixin],
      methods : {
          quitarIndicador : function() {
            this.$store.commit("quitarIndicador", this.index);
          },

          clicGrafico : function (valor) {
              let vm = this;
              if ( parseInt(this.indicador.dimensionIndex) + 1 == this.indicador.dimensiones.length ) {
                  vm.$snotify.warning(vm.$t("_ultima_dimension_"), vm.$t("_alerta_"), {
                    position: "rightTop",
                    timeout: 5000
                  });
              } else {
                  this.indicador.otros_filtros.elementos = [];
                  //Agregar la dimensión actual como un filtro
                  let datos_dimension = this.indicador.informacion.dimensiones[this.indicador.dimension];
                  this.indicador.filtros.push({
                      codigo: this.indicador.dimension,
                      etiqueta: datos_dimension.descripcion,
                      valor: valor
                  });

                  //Moverse a la siguiente dimension
                  this.indicador.dimensionIndex++;
                  this.indicador.dimension = this.indicador.dimensiones[this.indicador.dimensionIndex];

                  //Recargar datos del gráfico
                  this.cargarDatosIndicador(this.indicador, this.index);
              }
          },

          filtrarPosicion : function (elementos) {
              if (elementos.length > 0) {
                  elementos.map(e => { this.indicador.otros_filtros.elementos.push(e.x) });
                  
                  this.$snotify.info(this.$t("_se_ha_aplicado_filtro_info_"), {
                      position: "rightTop",
                      timeout: 10000
                  });
              } else {
                  this.indicador.otros_filtros.elementos = [];
              }
          },

          quitarFiltros : function() {
              this.indicador.otros_filtros.elementos = [];
          },

          cerrarConfiguracion : function(index) {
              this.indicador.mostrar_configuracion = false;
              this.indicador.full_screen = false;
          },

          fullscreen : function() {
              this.indicador.full_screen = !this.indicador.full_screen;
              this.indicador.mostrar_configuracion = this.indicador.full_screen ? this.indicador.mostrar_configuracion : false;
          },

          agregarFavorito: function () {
              let vm = this;
              axios.post("/api/v1/tablero/indicadorFavorito", { id: vm.indicador.id, es_favorito: vm.indicador.es_favorito })
                  .then(function(response) {
                      if (response.data.status == 200) {
                          vm.indicador.es_favorito = response.data.data;
                      }
                  })
                  .catch(function(error) {
                      console.log(error);
                      vm.$snotify.error(vm.$t("_error_conexion_"), "Error", {
                          position: "rightTop",
                          timeout: 10000
                  });
              });
          }
      }
    
  }
</script>