<template>
  <fullscreen
    ref="fullscreen"
    class="fullscreen-wrapper"
    v-if="indicador != undefined"
    @change="fullscreenChange"
    :style="indicador.full_screen ? '' : 'height: 100%'"
  >
    <b-card-group
      deck
      :style="
        indicador.full_screen
          ? 'width: 100vw; flex: none; margin: 0'
          : 'height: 100%'
      "
    >
      <b-card
        class="indicador-container"
        :border-variant="indicador.full_screen ? 'success' : ''"
        :style="
          indicador.full_screen
            ? 'margin: 1px; padding: 0; height: 99vh; z-index: 1010; '
            : 'z-index:100; '
        "
        :class="
          indicador.mostrar_configuracion
            ? ' d-none d-md-block col-xs-2 col-sm-2 col-md-8 col-lg-8 col-xl-8'
            : ''
        "
        header-tag="header"
        footer-tag="footer"
      >
        <div slot="header" :class="'draggable-handler'">
          <h4 style="font-size: 18px;">
            {{ indicador.nombre.toUpperCase() }}
            <span
              class="dimensionTitulo"
              v-if="
                getConteo(indicador.id) > 1 &&
                  indicador.informacion.dimensiones != undefined &&
                  indicador.informacion.dimensiones[indicador.dimension] !=
                    undefined
              "
            >
              ({{
                indicador.informacion.dimensiones[indicador.dimension]
                  .descripcion
              }})
            </span>
          </h4>
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
            :title="$t('_restaurar_tamanio_')"
          >
            <font-awesome-icon icon="compress-arrows-alt" />
          </span>
        </div>

        <IndicadorBarraOpciones
          :indicador="indicador"
          :index="index"
          @agregar-favorito="agregarFavorito"
          @full-screen="fullscreen"
          @descargar-grafico="descargarGrafico"
        />

        <IndicadorBreadcum :indicador="indicador" :index="index" />

        <IndicadorMensajes :indicador="indicador" :index="index" />
        <InfoTablaDatosContenido
          :indicador="indicador"
          :sustituyeGrafico="true"
          v-if="indicador.configuracion.mostrarTablaDatos"
        />
        <div
          class="contenedor-grafico"
          v-if="!indicador.configuracion.mostrarTablaDatos"
        >
          <GraficoBasico
            v-if="
              [
                'BOX',
                'BURBUJA',
                'BUBBLE',
                'BARRA',
                'BARRAS',
                'COLUMNAS',
                'COLUMNA',
                'DISCRETEBARCHART',
                'LINECHART',
                'LINEA',
                'LINEAS',
                'PIECHART',
                'PIE',
                'PASTEL',
                'TORTA',
              ].includes(indicador.configuracion.tipo_grafico.toUpperCase()) &&
                indicador.data.length > 0
            "
            :indicador="indicador"
            :index="index"
            @filtar-posicion="filtrarPosicion($event)"
            @quitar-filtros="quitarFiltros()"
            @click-plot="clicGrafico($event)"
            ref="grafico"
          ></GraficoBasico>
          <Mapa
            v-if="
              ['MAPA', 'GEOLOCATION', 'MAP'].includes(
                indicador.configuracion.tipo_grafico.toUpperCase()
              ) && indicador.data.length > 0
            "
            :indicador="indicador"
            :index="index"
            @filtar-posicion="filtrarPosicion($event)"
            @quitar-filtros="quitarFiltros()"
            @click-plot="clicGrafico($event)"
          ></Mapa>
        </div>
        <div slot="footer">
          <div>
            <div class="float-left" :title="$t('_fecha_ultima_lectura_')">
              [{{ indicador.informacion.ultima_lectura }}]
            </div>
            <div class="float-right">
              {{ $t("_meta_") }}: {{ indicador.informacion.meta }}
            </div>
          </div>
        </div>

        <div
          class="container-fluid div_carga"
          align="center"
          v-if="indicador.cargando"
        >
          <button
            type="button"
            class="close close_indicador"
            style="margin-top:9px"
            aria-label="Close"
            @click="indicador.cargando = false"
          >
            <span aria-hidden="true">&times;</span>
          </button>
          <div align="center">
            <h4>{{ $t("_cargando_indicador_") }}</h4>
            <font-awesome-icon icon="sync" spin size="2x" />
          </div>
        </div>
      </b-card>
      <b-card
        header-tag="header"
        border-variant="primary"
        header-bg-variant="primary"
        header-text-variant="white"
        v-if="indicador.full_screen && indicador.mostrar_configuracion"
        :style="
          indicador.mostrar_configuracion
            ? 'margin: 1px; padding: 0; flex: none; height: 99vh'
            : ''
        "
        :class="
          indicador.mostrar_configuracion
            ? 'col-sm-12 col-xs-12 col-md-4 col-lg-4 col-xl-4'
            : ''
        "
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
  </fullscreen>
</template>

<script lang="ts">
import { Component, Vue, Mixins, Prop } from "vue-property-decorator";
import domtoimage from "dom-to-image";
import axios from "axios";

import GraficoBasico from "../Graficos/GraficoBasico.vue";
import Mapa from "../Graficos/Mapa.vue";
import IndicadorBarraOpciones from "./IndicadorBarraOpciones.vue";
import IndicadorBreadcum from "./IndicadorBreadcum.vue";
import IndicadorMensajes from "./IndicadorMensajes.vue";
import ConfiguracionIndicador from "./ConfiguracionIndicador.vue";
import InfoTablaDatosContenido from "./InfoTablaDatosContenido.vue";
import IndicadorMixin from "../Mixins/IndicadorMixin";

@Component({
  components: {
    GraficoBasico,
    IndicadorBarraOpciones,
    IndicadorBreadcum,
    IndicadorMensajes,
    ConfiguracionIndicador,
    Mapa,
    InfoTablaDatosContenido,
  },
})
export default class IndicadorC extends Mixins(IndicadorMixin) {
  @Prop({ default: {} }) indicador: any;
  @Prop() readonly index!: number;

  get ancho(): number {
    return this.indicador.configuracion.width.split("-")[2];
  }

  get anchopx(): number {
    return (parseFloat(this.ancho()) * window.width) / 12;
  }

  get alto(): number {
    return this.$store.state.layout[this.index].h * 30;
  }

  public quitarIndicador(): void {
    this.$store.commit("quitarIndicador", this.index);
  }

  public clicGrafico(valor: number): void {
    let vm = this;
    if (
      parseInt(this.indicador.dimensionIndex) + 1 ==
      this.indicador.dimensiones.length
    ) {
      this.indicador.error = "Success";
    } else {
      this.indicador.otros_filtros.elementos = [];
      //Agregar la dimensión actual como un filtro
      let datos_dimension = this.indicador.informacion.dimensiones[
        this.indicador.dimension
      ];
      this.indicador.filtros.push({
        codigo: this.indicador.dimension,
        etiqueta: datos_dimension.descripcion,
        valor: valor,
      });

      //Moverse a la siguiente dimension
      this.indicador.dimensionIndex++;
      this.indicador.dimension = this.indicador.dimensiones[
        this.indicador.dimensionIndex
      ];

      //Recargar datos del gráfico
      this.cargarDatosIndicador(this.indicador, this.index);

      this.cargarDatosComparacion();
    }
  }

  public filtrarPosicion(elementos: any): void {
    if (elementos.length > 0) {
      this.indicador.otros_filtros.elementos = [];
      elementos.map((e: any) => {
        this.indicador.otros_filtros.elementos.push(e.x);
      });

      this.$snotify.info(this.$t("_se_ha_aplicado_filtro_info_") as string, {
        timeout: 5000,
      });
    } else {
      this.indicador.otros_filtros.elementos = [];
    }
  }

  public quitarFiltros(): void {
    this.indicador.otros_filtros.elementos = [];
  }

  public cerrarConfiguracion(index: number): void {
    this.indicador.mostrar_configuracion = false;
    //this.indicador.full_screen = false;
  }

  public fullscreen(): void {
    this.$refs["fullscreen"].toggle();
  }

  public fullscreenChange(fullscreen: boolean): void {
    //this.fullscreen = fullscreen
    this.indicador.full_screen = fullscreen;
    this.indicador.mostrar_configuracion = this.indicador.full_screen
      ? this.indicador.mostrar_configuracion
      : false;
  }

  public agregarFavorito(): void {
    let vm = this;
    axios
      .post("/api/v1/tablero/indicadorFavorito", {
        id: vm.indicador.id,
        es_favorito: vm.indicador.es_favorito,
      })
      .then(function(response) {
        if (response.data.status == 200) {
          vm.indicador.es_favorito = response.data.data;
          vm.indicador.es_favorito
            ? vm.$snotify.info(vm.$t("_agregado_favorito_") as string)
            : vm.$snotify.warning(vm.$t("_eliminado_favorito_") as string);
        }
      })
      .catch(function(error) {
        console.log(error);
        vm.$snotify.error(vm.$t("_error_conexion_") as string, "Error", {
          timeout: 10000,
        });
      });
  }

  public move(data: any): void {
    this.height = data.height;
  }

  public end(): void {
    this.indicador.configuracion.height = this.height;
  }

  public graficoImagen(options: any): void {
    if (
      !["MAPA", "GEOLOCATION", "MAP"].includes(
        this.indicador.configuracion.tipo_grafico.toUpperCase()
      )
    ) {
      return this.$refs.grafico.toImage(options);
    } else {
      return domtoimage.toPng(
        document.querySelector("#grafico-" + this.index),
        options
      );
    }
  }

  public descargarGrafico(): void {
    if (
      ["MAPA", "GEOLOCATION", "MAP"].includes(
        this.indicador.configuracion.tipo_grafico.toUpperCase()
      )
    ) {
      let vm = this;
      domtoimage
        .toPng(document.querySelector("#grafico-" + this.index), {
          width: 800,
          height: 600,
        })
        .then((dataUrl: string) => {
          let filename = vm.indicador.nombre;
          let link = document.createElement("a");

          if (typeof link.download === "string") {
            link.href = dataUrl;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
          } else {
            window.open(uri);
          }
        })
        .catch(function(error: any) {
          console.error("oops, something went wrong!", error);
        });
    } else {
      this.$refs.grafico.downloadImage({
        format: "png",
        width: 800,
        height: 600,
        filename: this.indicador.nombre,
      });
    }
  }

  public getConteo(id: string): void {
    return this.$store.state.indicadores.filter((x: any) => {
      return x.id == id;
    }).length;
  }
}
</script>