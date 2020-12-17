<template>
  <b-modal
    id="modalFiltrosGenerales"
    :title="$t('_filtros_generales_')"
    ok-only
    :ok-title="$t('_cancelar_')"
    @show="iniciarModal"
    ok-variant="secondary"
    size="lg"
  >
    <form class="form-horizontal">
      <div class="form-group">
        <label for="dimensionGeneralSelect" class="col-sm-2 control-label">
          {{ $t("_dimension_") }}
        </label>
        <div class="col-sm-10">
          <b-form-select
            v-model="dimensionGeneral"
            @change="recuperarValoresDimensionGeneral"
          >
            <option
              v-for="(item, key) in dimensionesGenerales"
              :key="key"
              :value="item"
            >
              {{ item.descripcion }}
            </option>
          </b-form-select>
        </div>
      </div>

      <div
        class="form-group"
        v-show="
          !filtroGeneralEsCatalogo &&
            dimensionGeneral != '' &&
            dimensionGeneral != '?'
        "
      >
        <label for="valorFiltro1" class="col-sm-2 control-label">{{
          $t("_valor_filtro_")
        }}</label>
        <div class="col-sm-10">
          <input type="text" id="valorFiltro1" v-model="valorFiltroGeneral" />
        </div>
      </div>

      <div class="form-group" v-show="filtroGeneralEsCatalogo">
        <label for="valorFiltro2" class="col-sm-2 control-label">{{
          $t("_valor_filtro_")
        }}</label>
        <div class="col-sm-10">
          <b-form-select v-model="valorFiltroGeneralCatalogo" id="valorFiltro2">
            <option
              v-for="(item, key) in datosCatalogo"
              :key="key"
              :value="item"
            >
              {{ item.descripcion }}
            </option>
          </b-form-select>
        </div>
      </div>
      <button
        class="btn btn-primary"
        type="button"
        @click="aplicarFiltroGeneral"
      >
        {{ $t("_aplicar_filtro_") }}
      </button>
    </form>
  </b-modal>
</template>

<script lang="ts">
import { Component, Vue, Mixins } from "vue-property-decorator";
import axios from "axios";
import vSelect from "vue-select";
import IndicadorMixin from "../../Mixins/IndicadorMixin";

@Component({
  components: { vSelect }
})
export default class ModalFiltrosGenerales extends Mixins(IndicadorMixin) {
  private dimensionGeneral: any = "";
  private filtroGeneralEsCatalogo = false;
  private dimensionesGenerales: string[] = [];
  private datosCatalogo: any[] = [];
  private valorFiltroGeneral = "";
  private valorFiltroGeneralCatalogo: any = {};

  public iniciarModal(): void {
    const dimensionesExistentes: string[] = [];
    const dimensionesAux: any[] = [];

    const sala = this.$store.state.sala;

    // Cargar los datos de los indicadores de la sala
    for (const indicador of this.$store.state.indicadores) {
      const dims = indicador.informacion.dimensiones;
      for (const codigo of Object.keys(dims)) {
        if (!dimensionesExistentes.includes(codigo)) {
          dimensionesExistentes.push(codigo);
          dimensionesAux.push({
            descripcion: dims[codigo].descripcion,
            codigo: codigo
          });
        }
      }
    }
    this.dimensionesGenerales = dimensionesAux.sort((a: any, b: any) => {
      return a.descripcion.localeCompare(b.descripcion);
    });
  }

  public recuperarValoresDimensionGeneral(): void {
    this.filtroGeneralEsCatalogo = false;

    if (this.dimensionGeneral.codigo.split("id_").length > 1) {
      this.filtroGeneralEsCatalogo = true;
      const vm = this;
      axios
        .get("/api/v1/tablero/datosCatalogo/" + this.dimensionGeneral.codigo)
        .then(function(response) {
          if (response.data.status == 200) {
            vm.datosCatalogo = response.data.data;
          }
        });
    }
  }

  public aplicarFiltroGeneral(): void {
    let nuevosFiltros = [];
    const vm = this;
    let existe = false;
    let etiqueta = "";
    const dimension = this.dimensionGeneral;
    const valor = this.filtroGeneralEsCatalogo
      ? this.valorFiltroGeneralCatalogo.descripcion
      : this.valorFiltroGeneral.trim();

    let index = 0;
    if (dimension != "" && valor != "") {
      for (const ind of this.$store.state.indicadores) {
        nuevosFiltros = [];
        for (const filtro of ind.filtros) {
          //Ya tenía el filtro, modificar su valor
          if (filtro.codigo == dimension.codigo) {
            filtro.valor = valor;
            existe = true;
            etiqueta = filtro.etiqueta;
          }
          nuevosFiltros.push(filtro);
          console.log(filtro);
        }

        //Si no existe agregarlo al principio
        if (!existe) {
          //Verificar primero si existe en el listado de dimensiones del indicador
          if (ind.dimensiones.includes(dimension.codigo)) {
            nuevosFiltros.unshift({
              codigo: dimension.codigo,
              etiqueta: dimension.descripcion,
              valor: valor
            });
          }
        }

        ind.filtros = nuevosFiltros;
        //$scope.agregarIndicadorDimension(ind.dimension, ind.posicion - 1);
        ind.otros_filtros.elementos = [];
        vm.cargarDatosIndicador(ind, index++);

        //vm.cargarDatosComparacion();
      }
    }
  }
}
</script>
