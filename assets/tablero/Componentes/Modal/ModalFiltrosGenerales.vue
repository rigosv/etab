<template>
  <b-modal
    id="modalFiltrosGenerales"
    :title="$t('_filtrosGenerales_')"
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
        v-show="!filtroGeneralEsCatalogo && dimensionGeneral != undefined"
      >
        <label for="valorFiltro1" class="col-sm-2 control-label">{{
          $t("_valorFiltro_")
        }}</label>
        <div class="col-sm-10">
          <input type="text" id="valorFiltro1" v-model="valorFiltroGeneral" />
        </div>
      </div>

      <div class="form-group" v-show="filtroGeneralEsCatalogo">
        <label for="valorFiltro2" class="col-sm-2 control-label">{{
          $t("_valorFiltro_")
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
        {{ $t("_aplicarFiltro_") }}
      </button>
    </form>
  </b-modal>
</template>

<script lang="ts">
import { defineComponent, ref } from "@vue/composition-api";
import vSelect from "vue-select";

import useCargadorDatos from "../../Compositions/useCargadorDatos";
import EventService from "../../services/EventService";

interface Dimension {
  codigo: string;
  descripcion: string;
}
interface DatoCatalogo {
  id: number;
  descripcion: string;
}

export default defineComponent({
  components: { vSelect },
  setup(props, { root }) {
    const dimensionGeneral = ref<Dimension>({ codigo: "", descripcion: "" });
    const dimensionesGenerales = ref<Array<Dimension>>([]);
    const datosCatalogo = ref<Array<DatoCatalogo>>([]);
    const filtroGeneralEsCatalogo = ref(false);
    const valorFiltroGeneral = ref("");
    const valorFiltroGeneralCatalogo = ref<DatoCatalogo>({
      id: 0,
      descripcion: ""
    });

    const { cargarDatosIndicador } = useCargadorDatos(root);

    const iniciarModal = (): void => {
      const dimensionesExistentes: string[] = [];
      const dimensionesAux: Dimension[] = [];

      // Cargar los datos de los indicadores de la sala
      for (const indicador of root.$store.state.indicadores) {
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
      dimensionesGenerales.value = dimensionesAux.sort(
        (a: Dimension, b: Dimension) => {
          return a.descripcion.localeCompare(b.descripcion);
        }
      );
    };

    const recuperarValoresDimensionGeneral = (): void => {
      filtroGeneralEsCatalogo.value = false;

      if (dimensionGeneral.value.codigo.split("id_").length > 1) {
        filtroGeneralEsCatalogo.value = true;
        EventService.getDatosCatalogo(dimensionGeneral.value.codigo).then(
          response => {
            if (response.data.status == 200) {
              datosCatalogo.value = response.data.data;
            }
          }
        );
      }
    };

    const aplicarFiltroGeneral = (): void => {
      let nuevosFiltros = [];
      let existe = false;
      let etiqueta = "";
      const dimension = dimensionGeneral.value;
      const valor = filtroGeneralEsCatalogo.value
        ? valorFiltroGeneralCatalogo.value.descripcion
        : valorFiltroGeneral.value.trim();

      let index = 0;
      if (dimensionGeneral.value.codigo !== undefined && valor != "") {
        for (const ind of root.$store.state.indicadores) {
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
          ind.otros_filtros.elementos = [];
          cargarDatosIndicador(ind, index++);
        }
      }
    };

    return {
      dimensionGeneral,
      dimensionesGenerales,
      datosCatalogo,
      filtroGeneralEsCatalogo,
      valorFiltroGeneral,
      valorFiltroGeneralCatalogo,
      iniciarModal,
      recuperarValoresDimensionGeneral,
      aplicarFiltroGeneral
    };
  },

  methods: {}
});
</script>
