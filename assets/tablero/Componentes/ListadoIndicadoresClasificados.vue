<template>
  <div style="padding: 10px;">
    <b-form-group
      :label="$t('_clasificacionUso_')"
      label-for="clasificacionUso"
      :state="state"
    >
      <b-input-group class="mt-3">
        <b-input-group-text slot="prepend">
          <font-awesome-icon icon="search" v-if="!cargando_uso" />
          <strong class="text-success"
            ><font-awesome-icon icon="sync" spin v-if="cargando_uso"
          /></strong>
        </b-input-group-text>

        <v-select
          id="clasificacionUso"
          v-model="$store.state.clasificacionUso"
          :options="$store.state.clasificacionesUso"
          label="descripcion"
          style="flex: 1 1 auto"
          @input="getClasificacionesTecnica($event)"
        >
        </v-select>

        <b-form-invalid-feedback :state="invalidCU">
          <b-spinner variant="success" type="grow" label="Spinning"></b-spinner>
          1.-{{ $t("_elijaClasificacionUso_") }}
        </b-form-invalid-feedback>
      </b-input-group>
    </b-form-group>

    <b-form-group
      v-if="
        $store.state.clasificacionesTecnica.length > 0 &&
          $store.state.clasificacionUso != undefined
      "
      :label="$t('_clasificacionTecnica_')"
      label-for="clasificacionTecnica"
      :state="stateT"
    >
      <b-input-group class="mt-3">
        <b-input-group-text slot="prepend">
          <font-awesome-icon icon="search" v-if="!cargando_tecnica" />
          <strong class="text-success"
            ><font-awesome-icon icon="sync" spin v-if="cargando_tecnica"
          /></strong>
        </b-input-group-text>
        <v-select
          id="clasificacionTecnica"
          v-model="$store.state.clasificacionTecnica"
          :options="$store.state.clasificacionesTecnica"
          label="descripcion"
          style="flex: 1 1 auto"
          @input="getIndicadores($event)"
        >
        </v-select>
        <b-form-invalid-feedback :state="invalidCT">
          <b-spinner variant="success" type="grow" label="Spinning"></b-spinner>
          2.-{{ $t("_elijaClasificacionTecnica_") }}
        </b-form-invalid-feedback>
      </b-input-group>
    </b-form-group>

    <ListadoIndicadores
      :indicadores="this.$store.state.indicadoresClasificados"
      v-if="$store.state.clasificacionTecnica != undefined"
    />
  </div>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";
import axios from "axios";
import vSelect from "vue-select";

import ListadoIndicadores from "./ListadoIndicadores.vue";

export default defineComponent({
  components: { vSelect, ListadoIndicadores },

  data: () => ({
    filtro: "",
    cargando_uso: false,
    cargando_tecnica: false
  }),

  computed: {
    state(): boolean {
      return this.$store.state.clasificacionUso != null;
    },

    stateT(): boolean {
      return this.$store.state.clasificacionTecnica != null;
    },

    invalidCU(): boolean {
      return this.$store.state.clasificacionUso != null;
    },

    invalidCT(): boolean {
      return this.$store.state.clasificacionTecnica != null;
    },

    indicadoresFiltrados(): any {
      return this.filtrar(
        this.$store.state.indicadoresClasificados,
        this.filtro
      );
    }
  },

  methods: {
    getClasificacionesTecnica(clasificacionUso: any): void {
      const vm = this;
      this.cargando_tecnica = true;
      axios
        .get("/api/v1/tablero/clasificacionTecnica?id=" + clasificacionUso.id)
        .then(function(response) {
          if (response.data.data.length == 0) {
            vm.$snotify.warning(vm.$t("_datosNoEncontrados_") as string);
            vm.$store.state.clasificacionesTecnica = [];
          } else {
            vm.$store.state.clasificacionesTecnica = response.data.data;
          }
        })
        .catch(function(error) {
          vm.$snotify.error(
            vm.$t("_errorConexion_") as string,
            vm.$t("_error_") as string
          );
        })
        .finally(function() {
          vm.cargando_tecnica = false;
        });
    },

    getIndicadores(clasificacionTecnica: any): void {
      const vm = this;
      this.cargando_tecnica = true;
      axios
        .get(
          "/api/v1/tablero/listaIndicadores?tipo=clasificados&uso=" +
            this.$store.state.clasificacionUso.id +
            "&tecnica=" +
            clasificacionTecnica.id
        )
        .then(function(response) {
          if (response.data.data.length == 0) {
            vm.$snotify.warning(vm.$t("_datosNoEncontrados_") as string);
          } else {
            vm.$store.state.indicadoresClasificados = response.data.data;
          }
          //vm.$emit("cant-clasificados");
        })
        .catch(function(error) {
          vm.$snotify.error(vm.$t("_errorConexion_") as string, "Error");
        })
        .finally(function() {
          vm.cargando_tecnica = false;
        });
    },

    agregarIndicador(indicador: any): void {
      //vm.$emit("cant-clasificados", indicador);
    },

    filtrar(listado: any, filtro: string): any {
      return listado.filter((ind: any) => {
        const base = this.normalizarDiacriticos(ind.nombre);
        const filtro_ = this.normalizarDiacriticos(filtro);
        return base.includes(filtro_);
      });
    },

    normalizarDiacriticos(value: string): string {
      if (!value || value == undefined) return "";

      return value
        .toLowerCase()
        .normalize("NFD")
        .replace(/([aeio])\u0301|(u)[\u0301\u0308]/gi, "$1$2")
        .normalize();
    }
  }
});
</script>
