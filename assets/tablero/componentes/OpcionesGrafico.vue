<template>
  <div class="container-fluid row">
    <b-form-group :label="$t('_cambiar_dimension_')" class="col-12">
      {{ $t("_dimension_actual_") }} : {{ dimension }}
      <b-form-select v-model="dimension" @change="cambiarDimension">
        <option
          v-for="(item, key) in dimensionesFiltradas"
          :key="key"
          :value="item"
        >
          {{ indicador.informacion.dimensiones[item].descripcion }} ({{ item }})
        </option>
      </b-form-select>
    </b-form-group>

    <transition name="slide-fade">
      <b-form-group
        :label="$t('_cambiar_orden_')"
        class="col-12"
        v-if="puedeOrdenar"
      >
        <b-row>
          <b-col
            v-for="(i, k) in seriesOrden"
            :key="k"
            class="row border rounded shadow flex border-primary "
            style=" margin: 20px 5px 20px 20px; padding: 15px; "
          >
            <b-row>
              <b-col cols="4" style="vertical-align: center">
                <b-button
                  pill
                  v-if="indicador.configuracion[i] == ''"
                  :title="$t('_sin_orden_')"
                  @click="cambiarOrden(k, 'asc')"
                >
                  <font-awesome-icon icon="sort" size="2x" />
                </b-button>
                <b-button
                  pill
                  variant="primary"
                  :title="$t('_ordenado_descendentemente_')"
                  @click="cambiarOrden(k, 'asc')"
                  v-if="indicador.configuracion[i] == 'desc'"
                >
                  <font-awesome-icon icon="sort-numeric-down-alt" size="2x" />
                </b-button>

                <b-button
                  pill
                  variant="success"
                  :title="$t('_ordenado_ascendentemente_')"
                  @click="cambiarOrden(k, 'desc')"
                  v-if="indicador.configuracion[i] == 'asc'"
                >
                  <font-awesome-icon icon="sort-numeric-up-alt" size="2x" />
                </b-button>
              </b-col>
              <b-col cols="8" class="text-middle" style="padding: 10px;">
                {{ $t("_ordenar_" + k + "_") }}
              </b-col>
            </b-row>
          </b-col>
        </b-row>
      </b-form-group>
    </transition>
    <b-form-group :label="$t('_seleccione_tipo_grafico_')">
      <b-container fluid class="p-4 tipos_graficos bg-light">
        <b-form-radio-group
          id="radio-group-2"
          name="radio-sub-component"
          v-model="indicador.configuracion.tipo_grafico"
        >
          <b-row class="mb-4">
            <b-col
              md="3"
              v-for="(item, key) in tiposGraficos"
              :key="key"
              style="margin-top: 5px; margin-bottom: 10px;"
            >
              <b-form-radio :value="item.codigo" class="text-center">
                <b-img
                  thumbnail
                  fluid
                  :src="getGrafico(item.codigo)"
                  :alt="item.descripcion"
                  style="margin-bottom: 10px;"
                ></b-img>
                {{ item.descripcion }}
              </b-form-radio>
            </b-col>
          </b-row>
        </b-form-radio-group>
      </b-container>
    </b-form-group>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";
import IndicadorMixin from "../Mixins/IndicadorMixin";

export default defineComponent({
  props: {
    indicador: { default: {}, type: Object },
    index: Number
  },

  mixins: [IndicadorMixin],

  data: () => ({
    images: {
      columnas: require("../../images/bar.png"),
      lineas: require("../../images/lineas.png"),
      pastel: require("../../images/pastel.png"),
      mapa: require("../../images/mapa.png"),
      caja: require("../../images/cajas.png"),
      burbuja: require("../../images/burbuja.png")
    },
    dimension: ""
  }),

  computed: {
    comparaDimensiones(): boolean {
      return this.indicador.configuracion.dimensionComparacion != "";
    },

    comparaIndicadores(): boolean {
      return this.indicador.dataComparar.length > 0;
    },

    seriesOrden(): any {
      return this.comparaIndicadores || this.comparaDimensiones
        ? { x: "orden_x" }
        : { x: "orden_x", y: "orden_y" };
    },

    dimensionesFiltradas(): any {
      const vm = this;

      let dimensiones = this.indicador.dimensiones.filter(
        (dimension: string) => {
          //Verificar  que no sea la dimensión actual
          if (dimension != vm.indicador.dimension) {
            //Verificar que no esté en los filtros
            let esFiltro = false;
            for (const filtro of vm.indicador.filtros) {
              esFiltro = dimension == filtro.codigo ? true : esFiltro;
            }
            return !esFiltro;
          }
          return false;
        }
      );

      //Si hay indicadores cargados para comparación, mostrar solo las dimensiones comunes
      this.indicador.dataComparar.map((ind: any) => {
        dimensiones = dimensiones.filter((x: string) =>
          ind.dimensiones.includes(x)
        );
      });

      return dimensiones;
    },

    tiposGraficos(): any {
      let tipos = this.indicador.informacion.dimensiones[
        this.indicador.dimension
      ].graficos;

      if (this.comparaDimensiones || this.comparaIndicadores) {
        tipos = tipos.filter((tipo: any) => {
          return ![
            "PIECHART",
            "PIE",
            "PASTEL",
            "TORTA",
            "MAPA",
            "GEOLOCATION",
            "MAP"
          ].includes(tipo.codigo.toUpperCase());
        });

        if (this.comparaIndicadores) {
          //Agregar los tipos caja, burbuja y línea cuando se estén comparando indicadores
          tipos.push({ codigo: "box", descripcion: this.$t("_box_") });
          tipos.push({ codigo: "burbuja", descripcion: this.$t("_burbuja_") });
        }

        if (
          !tipos.find((tipo: any) => {
            return ["LINECHART", "LINEA", "LINEAS"].includes(
              tipo.codigo.toUpperCase()
            );
          })
        ) {
          tipos.push({ codigo: "lineas", descripcion: this.$t("_lineas_") });
        }
      }

      return tipos.sort((a: any, b: any) =>
        a.descripcion.localeCompare(b.descripcion)
      );
    },

    puedeOrdenar(): boolean {
      return [
        "BARRA",
        "BURBUJA",
        "BUBBLE",
        "BARRAS",
        "COLUMNAS",
        "COLUMNA",
        "DISCRETEBARCHART",
        "LINECHART",
        "LINEA",
        "LINEAS"
      ].includes(this.indicador.configuracion.tipo_grafico.toUpperCase());
    }
  },

  mounted() {
    this.dimension = this.indicador.dimension;
  },

  methods: {
    getGrafico(tipo: string): any {
      if (
        ["BARRA", "BARRAS", "COLUMNAS", "COLUMNA", "DISCRETEBARCHART"].includes(
          tipo.toUpperCase()
        )
      ) {
        return this.images.columnas.default;
      } else if (
        ["LINECHART", "LINEA", "LINEAS"].includes(tipo.toUpperCase())
      ) {
        return this.images.lineas.default;
      } else if (
        ["PIECHART", "PIE", "PASTEL", "TORTA"].includes(tipo.toUpperCase())
      ) {
        return this.images.pastel.default;
      } else if (["MAPA", "GEOLOCATION", "MAP"].includes(tipo.toUpperCase())) {
        return this.images.mapa.default;
      } else if (["BOX", "CAJA"].includes(tipo.toUpperCase())) {
        return this.images.caja.default;
      } else if (["BURBUJA", "BUBBLE"].includes(tipo.toUpperCase())) {
        return this.images.burbuja.default;
      }
    },

    asignarAncho(ancho: string): void {
      this.indicador.configuracion.width = "col-sm-" + ancho;
    },

    cambiarOrden(tipo: string, modo: string): void {
      if (tipo == "x") {
        this.indicador.configuracion.orden_y = "";
        this.indicador.configuracion.orden_x = modo;
      } else {
        this.indicador.configuracion.orden_x = "";
        this.indicador.configuracion.orden_y = modo;
      }
    },

    cambiarDimension(): void {
      this.indicador.dimension = this.dimension;
      this.indicador.otros_filtros.elementos = [];
      this.cargarDatosIndicador(this.indicador, this.index);

      this.cargarDatosComparacion();
    }
  }
});
</script>
