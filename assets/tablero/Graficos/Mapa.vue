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
import { Component, Vue, Mixins, Prop, Watch } from "vue-property-decorator";
import { Plotly } from "vue-plotly";
import numeral from "numeral";
import axios from "axios";
import { LMap, LTileLayer, LGeoJson, LControl } from "vue2-leaflet";

import GraficoMixin from "../Mixins/GraficoMixin";

@Component({
  components: { Plotly, LMap, LTileLayer, LGeoJson, LControl }
})
export default class Mapa extends Mixins(GraficoMixin) {
  @Prop({ default: {} }) indicador: any;
  @Prop() readonly index!: number;

  private mapaDatosCargados = false;
  private datosMapa: any = {};
  private info = "";
  private url = "https://{s}.tile.osm.org/{z}/{x}/{y}.png";

  private window_: any = window;

  get zoom() {
    return this.indicador.informacion.dimensiones[this.indicador.dimension]
      .escala;
  }

  get center() {
    return [
      this.indicador.informacion.dimensiones[this.indicador.dimension].origenX,
      this.indicador.informacion.dimensiones[this.indicador.dimension].origenY
    ];
  }

  get styleFunction() {
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
  }

  get options() {
    return {
      onEachFeature: this.onEachFeatureFunction
    };
  }

  get onEachFeatureFunction() {
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
  }

  get datos() {
    return this.datosMapa;
  }

  mounted() {
    this.cargarDatosMapa();
  }

  public cargarDatosMapa(): void {
    const nombre_mapa = this.indicador.informacion.dimensiones[
      this.indicador.dimension
    ].mapa;
    const url = "/js/Mapas/" + nombre_mapa;
    const vm = this;

    axios
      .get(url)
      .then(response => {
        if (response.status == 200) {
          vm.datosMapa = response.data;
          vm.mapaDatosCargados = true;
        }
      })
      .catch(function(error) {
        console.log(error);
        vm.indicador.error = "Error";
      });
  }

  public zoomUpdated(zoom: number): void {
    console.log("Zoom: " + zoom);
  }

  public centerUpdated(center: number): void {
    console.log("Center: " + center);
  }

  @Watch("indicador.full_screen")
  fullScreenChange() {
    //this.$refs["myMap" + this.index].mapObject.invalidateSize();
    console.log("fulll");
  }

  @Watch("indicador.dimension")
  dimensionChange() {
    this.cargarDatosMapa();
  }

  @Watch("indicador.filtros")
  filtrosChange() {
    this.cargarDatosMapa();
  }
}
</script>
