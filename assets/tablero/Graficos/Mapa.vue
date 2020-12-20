<template>
  <div
    style="width: 100%; overflow:  hidden"
    :id="'grafico-' + index"
    :style="{
      height: indicador.full_screen
        ? window_.innerHeight / 1.18 + 'px'
        : parseFloat(indicador.configuracion.layout.h) * 30 - 100 + 'px'
    }"
  >
    <l-map
      :ref="'myMap' + index"
      style="width: 2024px; height : 1024px"
      :zoom="zoom"
      :center="center"
      @update:zoom="zoomUpdated"
      @update:center="centerUpdated"
      v-if="mapaDatosCargados"
    >
      <l-control position="topleft">
        <DIV class="info">
          <H4>{{ nombreDimension }}</H4>
          {{ info }}
        </DIV>
      </l-control>

      <l-geo-json
        :geojson="datosMapa"
        :options="options"
        :options-style="styleFunction"
      >
      </l-geo-json>
      <l-tile-layer :url="url"></l-tile-layer>
    </l-map>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";
import { Plotly } from "vue-plotly";
import numeral from "numeral";
import axios from "axios";
import { LMap, LTileLayer, LGeoJson, LControl } from "vue2-leaflet";

import useGrafico from "../Compositions/useGrafico";
import useColor from "../Compositions/useColor";
import useClicEvents from "../Compositions/useClicEvents";

export default defineComponent({
  components: { Plotly, LMap, LTileLayer, LGeoJson, LControl },

  props: {
    indicador: { default: {}, type: Object },
    index: Number
  },

  setup(props, ctx) {
    return {
      ...useGrafico(props.indicador),
      ...useColor(),
      ...useClicEvents(props.indicador, ctx)
    };
  },

  data: () => ({
    mapaDatosCargados: false,
    datosMapa: {},
    info: "",
    url: "https://{s}.tile.osm.org/{z}/{x}/{y}.png",
    window_: window
  }),

  computed: {
    zoom(): any {
      return this.indicador.informacion.dimensiones[this.indicador.dimension]
        .escala;
    },

    center(): any {
      return [
        this.indicador.informacion.dimensiones[this.indicador.dimension]
          .origenX,
        this.indicador.informacion.dimensiones[this.indicador.dimension].origenY
      ];
    },

    styleFunction(): any {
      return (feature: any) => {
        const itemGeoJSONID = feature.properties.ID;
        const data = this.indicador.data;

        const item = data.find((x: any) => x.id == itemGeoJSONID);
        if (!item) {
          return { weight: 1, opacity: 0.6, color: "black" };
        }

        return {
          weight: 1,
          opacity: 0.6,
          color: "black",
          dashArray: "3",
          fillOpacity: 0.5,
          fillColor: this.getColor(
            item.measure,
            this.indicador.informacion.rangos
          )
        };
      };
    },

    options() {
      return {
        onEachFeature: this.onEachFeatureFunction
      };
    },

    onEachFeatureFunction() {
      return (feature: any, layer: any) => {
        const itemGeoJSONID = feature.properties.ID;
        const data = this.indicador.data;
        const item = data.find((x: any) => x.id == itemGeoJSONID);

        if (item) {
          layer.on({
            click: () => {
              this.click({ points: [{ x: item.category }] });
            },
            mouseover: () => {
              this.info =
                item.category +
                ": " +
                numeral(item.measure).format("0,0." + "0".repeat(this.dec)) +
                this.indicador.informacion.unidad_medida;
            },
            mouseout: () => {
              this.info = "";
            },
            dblclick: () => {
              console.log("doble clic");
              this.doubleClickTime = Date.now();
            }
          });
        }
      };
    },

    datos(): any {
      return this.datosMapa;
    },

    fullsreen(): any {
      return this.indicador.full_screen;
    },

    dimension(): any {
      return this.dimension;
    },

    filtros(): any {
      return this.filtros;
    }
  },

  mounted() {
    this.cargarDatosMapa();
  },

  methods: {
    cargarDatosMapa(): void {
      const nombreMapa = this.indicador.informacion.dimensiones[
        this.indicador.dimension
      ].mapa;
      const url = "/js/Mapas/" + nombreMapa;

      axios
        .get(url)
        .then(response => {
          if (response.status == 200) {
            this.datosMapa = response.data;
            this.mapaDatosCargados = true;
          }
        })
        .catch(error => {
          console.log(error);
          this.indicador.error = "Error";
        });
    },

    zoomUpdated(zoom: number): void {
      console.log("Zoom: " + zoom);
    },

    centerUpdated(center: number): void {
      console.log("Center: " + center);
    }
  },

  /*watch: {
    fullsreen() {
      //this.$refs["myMap" + this.index].mapObject.invalidateSize();
      console.log("fulll");
    },

    dimension() {
      this.cargarDatosMapa();
    },

    filtros() {
      this.cargarDatosMapa();
    }
  }*/
});
</script>
