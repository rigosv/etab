<template>
  <div class="container-fluid row" style="padding: 20px 0px 0px 0px; ">
    <b-card no-body class="col-12">
      <b-tabs pills card>
        <b-tab active>
          <template slot="title">
            <font-awesome-icon icon="code-branch" /> {{ $t("_dimensiones_") }}
          </template>
          <b-card-text>
            <b-form-group>
              <template slot="label">
                {{ $t("_elija_dimension_comparar_") }}
                <transition name="slide-fade">
                  <b-button
                    size="sm"
                    variant="danger"
                    v-if="indicador.configuracion.dimensionComparacion != ''"
                    @click="indicador.configuracion.dimensionComparacion = ''"
                  >
                    {{ $t("_quitar_comparacion_") }}
                  </b-button>
                </transition>
              </template>
              <b-form-radio-group
                v-model="indicador.configuracion.dimensionComparacion"
                :options="dimensionesComparar"
                buttons
                button-variant="outline-secondary"
                stacked
              ></b-form-radio-group>
            </b-form-group>
          </b-card-text>
        </b-tab>

        <b-tab>
          <template slot="title">
            <font-awesome-icon icon="clone" /> {{ $t("_indicadores_") }}
          </template>
          <b-card-text>
            <div
              class="col-sm-12 "
              v-if="indicador.dataComparar.length > 0"
              style="padding: 20px;"
            >
              <span class="text-primary">{{
                $t("_indicadores_agregados_")
              }}</span>

              <b-list-group style="max-height: 40vh; overflow: auto;">
                <b-list-group-item
                  class="d-flex justify-content-between align-items-center"
                  v-for="(item, key) in indicador.dataComparar"
                  :key="key"
                >
                  <font-awesome-icon
                    icon="check"
                    class="text-success"
                    size="2x"
                  ></font-awesome-icon>
                  {{ item.nombre.toUpperCase() }}
                  <b-badge variant="light">
                    <b-button
                      variant="outline-danger"
                      :title="$t('_borrar_')"
                      @click="quitarComparacion(item)"
                    >
                      <font-awesome-icon icon="times-circle" />
                    </b-button>
                  </b-badge>
                </b-list-group-item>
              </b-list-group>
            </div>

            <span>{{ $t("_agregar_indicadores_para_comparacion_") }}</span>
            <buscar
              v-model="filtroIndicadores"
              @buscar="buscarIndicadores"
              :enter="true"
              ref="buscarInput"
            ></buscar>

            <b-list-group style="max-height: 40vh; overflow: auto;">
              <b-list-group-item
                class="d-flex justify-content-between align-items-center"
                v-for="(item, key) in indicadoresFiltrados"
                :key="key"
              >
                {{ item.nombre.toUpperCase() }}
                <b-badge
                  variant="light"
                  v-if="!indicador.configuracion.agregados.includes(item.id)"
                >
                  <b-button
                    variant="outline-success"
                    :title="$t('_agregar_')"
                    @click="agregarIndicador(item)"
                  >
                    <font-awesome-icon icon="plus-square" />
                  </b-button>
                </b-badge>
              </b-list-group-item>
            </b-list-group>
          </b-card-text>
        </b-tab>
      </b-tabs>
    </b-card>
  </div>
</template>

<script lang="ts">
import { Component, Vue, Prop, Mixins, Watch } from "vue-property-decorator";
import axios from "axios";

import Buscar from "./Buscar.vue";
import IndicadorMixin from "../Mixins/IndicadorMixin";

@Component({
  components: { Buscar }
})
export default class OpcionesComparacion extends Mixins(IndicadorMixin) {
  @Prop({ default: {} }) indicador: any;
  @Prop() readonly index!: number;

  private filtroIndicadores = "";
  private indicadoresFiltrados: any[] = [];

  get dimensionesComparar() {
    const vm = this;

    const dimensiones = this.indicador.dimensiones.filter((dimension: any) => {
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
    });

    return dimensiones.map((d: any) => {
      return {
        text: this.indicador.informacion.dimensiones[d].descripcion,
        value: d
      };
    });
  }

  public buscarIndicadores(): void {
    if (this.filtroIndicadores.length >= 3) {
      const vm = this;
      axios
        .get(
          "/api/v1/tablero/listaIndicadores?tipo=busqueda&busqueda=" +
            this.filtroIndicadores
        )
        .then(function(response) {
          if (response.data.status == 200) {
            vm.indicadoresFiltrados = response.data.data;
          }
        })
        .catch(function(error) {
          console.log(error);
        });
    }
  }

  public agregarIndicador(indicadorC: any): void {
    //Sacar las dimensiones del indicador a agregar
    const dimensiones: any[] = [];
    for (const prop in indicadorC.dimensiones) {
      dimensiones.push(prop);
    }

    //Verificar si el nuevo indicador tiene la dimension actual
    const existeD = dimensiones.includes(this.indicador.dimension);

    //Valores iniciales del indicador
    const indicadorCompleto = {
      id: indicadorC.id,
      dimensiones: dimensiones,
      nombre: indicadorC.nombre,
      data: [],
      informacion: {}
    };

    if (existeD) {
      //Quitar la comparación por dimensión por si existe
      this.indicador.configuracion.dimensionComparacion = "";

      this.indicador.configuracion.agregados.push(indicadorC.id);
      //leer los datos del indicador
      const vm = this;
      const json = {
        filtros: vm.indicador.filtros,
        ver_sql: false,
        tendencia: false
      };
      vm.indicador.cargando = true;

      axios
        .post(
          "/api/v1/tablero/datosIndicador/" +
            indicadorC.id +
            "/" +
            vm.indicador.dimension,
          json
        )
        .then(function(response) {
          if (response.data.status == 200) {
            const data = response.data;
            indicadorCompleto.informacion = data.informacion;
            indicadorCompleto.data = data.data;
            vm.indicador.dataComparar.push(indicadorCompleto);
          }
        })
        .catch(function(error) {
          console.log(error);
        })
        .finally(function() {
          vm.indicador.cargando = false;
        });
    } else {
      //Verificar si tienen alguna dimensión en común
      const dimensionesC = this.indicador.dimensiones.filter((x: any) =>
        dimensiones.includes(x)
      );

      if (dimensionesC.length == 0) {
        this.$snotify.warning(
          this.$t("_indicadores_no_tienen_dimensiones_en_comun_") as string
        );
      } else {
        //Quitar la comparación por dimensión por si existe
        this.indicador.configuracion.dimensionComparacion = "";
        this.$snotify.warning(
          this.$t("_indicador_no_tiene_dimension_actual_") as string
        );
        this.indicador.configuracion.agregados.push(indicadorC.id);
        indicadorCompleto.informacion = { rangos: [] };
        this.indicador.dataComparar.push(indicadorCompleto);
      }
    }
  }

  public quitarComparacion(indicadorC: any): void {
    this.indicador.dataComparar = this.indicador.dataComparar.filter(
      (ind: any) => {
        return ind.id != indicadorC.id;
      }
    );
    this.indicador.configuracion.agregados = this.indicador.configuracion.agregados.filter(
      (id: string) => {
        return id != indicadorC.id;
      }
    );
  }

  @Watch("indicador.configuracion.dimensionComparacion")
  dimensionCoparacionChange() {
    // Quitar la comparación de indicadores por si existiera
    this.indicador.configuracion.agregados = [];
    this.indicador.dataComparar = [];

    this.cargarDatosIndicador(this.indicador, this.index);

    // Si no tiene un tipo adecuado poner lineas por defecto
    if (
      ![
        "BARRA",
        "BARRAS",
        "COLUMNAS",
        "COLUMNA",
        "DISCRETEBARCHART",
        "LINECHART",
        "LINEA",
        "LINEAS"
      ].includes(this.indicador.configuracion.tipo_grafico.toUpperCase())
    ) {
      this.indicador.configuracion.tipo_grafico = "lineas";
    }
  }
}
</script>
