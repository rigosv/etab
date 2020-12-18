<template>
  <div id="sala">
    <b-input-group
      size="md"
      class="mt-3"
      v-if="$store.state.abrioSala || $store.state.abrioIndicador"
    >
      <b-input-group-text slot="prepend"
        ><font-awesome-icon icon="th" /> {{ $t("_sala_") }}</b-input-group-text
      >
      <b-form-input
        v-model="$store.state.sala.nombre"
        :state="nameState"
        :placeholder="$t('_debeProporcionarNombreSala_')"
        trim
        :readonly="esSalaPublica"
      >
      </b-form-input>
      <b-input-group-append class="d-print-none">
        <b-button
          variant="outline-primary"
          @click="guardarSala('guardar')"
          :title="$t('_guardarSala_')"
          v-if="
            nameState &&
              !esSalaPublica &&
              $store.state.salasPropias.includes($store.state.sala.id)
          "
        >
          <font-awesome-icon icon="save" /><span
            class="d-none d-md-block float-right"
            >{{ $t("_guardarSala_") }}</span
          >
        </b-button>
        <b-button
          variant="outline-info"
          @click="guardarSala('guardar_como')"
          :title="$t('_guardarSalaComo_')"
          v-if="nameState && !esSalaPublica"
        >
          <font-awesome-icon icon="share-alt" /><span
            class="d-none d-md-block float-right"
            >{{ $t("_guardarSalaComo_") }}</span
          >
        </b-button>
        <b-button
          variant="outline-danger"
          v-if="!esSalaPublica"
          @click="cerrarSala()"
          :title="$t('_cerrarSala_')"
        >
          <font-awesome-icon icon="times" /><span
            class="d-none d-md-block float-right"
            >{{ $t("_cerrarSala_") }}</span
          >
        </b-button>
      </b-input-group-append>
    </b-input-group>
    <grid-layout
      v-if="$store.state.indicadores.length > 0"
      :layout.sync="$store.state.layout"
      :row-height="30"
      :responsive="true"
      :autoSize="false"
    >
      <grid-item
        v-for="item in $store.state.layout"
        class="grid-item  rounded"
        :key="item.i"
        :x="item.x"
        :y="item.y"
        :w="item.w"
        :h="item.h"
        :i="item.i"
        :dragAllowFrom="'.draggable-handler'"
        @resized="resizedEvent"
        @moved="movedEvent"
      >
        <IndicadorC
          :ref="'indicador' + item.i"
          :indicador="
            $store.state.indicadores.filter(ind => {
              return ind.index == item.i;
            })[0]
          "
          :index="item.i"
          @full-screen="fullscreen($event)"
        />
      </grid-item>
    </grid-layout>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";
import axios from "axios";
import VueGridLayout from "vue-grid-layout";

import IndicadorC from "./IndicadorC.vue";

export default defineComponent({
  components: {
    IndicadorC,
    GridLayout: VueGridLayout.GridLayout,
    GridItem: VueGridLayout.GridItem
  },

  computed: {
    nameState(): boolean {
      return this.$store.state.sala.nombre.length > 0 ? true : false;
    },

    esSalaPublica(): boolean {
      return this.$store.state.token != "" && this.$store.state.idSala != "";
    }
  },

  methods: {
    movedEvent(i: string, newX: number, newY: number): void {
      console.log("MOVED i=" + i + ", X=" + newX + ", Y=" + newY);
      this.$store.state.indicadores.map((ind: any) => {
        if (ind.index == i) {
          ind.configuracion.layout.x = newX;
          ind.configuracion.layout.y = newY;
        }
      });

      console.log(this.$store.state.indicadores);
    },

    /**
     *
     * @param i the item id/index
     * @param newH new height in grid rows
     * @param newW new width in grid columns
     * @param newHPx new height in pixels
     * @param newWPx new width in pixels
     *
     */
    resizedEvent(
      i: string,
      newH: number,
      newW: number,
      newHPx: number,
      newWPx: number
    ): void {
      console.log(
        `RESIZED i= ${i}, H= ${newH}, W= ${newW}, H(px)=${newHPx}, W(px)= ${newWPx}`
      );
      this.$store.state.indicadores.map((ind: any) => {
        if (ind.index == i) {
          ind.configuracion.layout.w = newW;
          ind.configuracion.layout.h = newH;
        }
      });

      console.log(this.$store.state.indicadores);
    },

    guardarSala(tipo: string): void {
      const salaDatos =
        tipo == "guardar"
          ? this.$store.state.sala
          : { id: "", nombre: this.$store.state.sala.nombre };
      const json = {
        sala: salaDatos,
        indicadores: this.$store.state.indicadores
      };
      this.$store.state.sala_cargando = true;
      const vm = this;

      if (
        tipo == "guardar_como" &&
        this.$store.state.sala.nombre == this.$store.state.salaNombreIni
      ) {
        vm.$snotify.warning(
          vm.$t("_guardarSalaErrorNombreDiferente_") as string,
          "Error"
        );
      } else {
        axios
          .post("/api/v1/tablero/guardarSala", json)
          .then(function(response) {
            if (response.data.status == 200) {
              vm.$store.state.abrioSala = true;
              vm.$store.state.sala.id = response.data.data;
              vm.$store.state.salaNombreIni = vm.$store.state.sala.nombre;
              vm.$store.state.salasPropias.push(response.data.data);
              vm.$snotify.success(vm.$t("_salaGuardada_") as string);
            } else {
              vm.$snotify.error(
                vm.$t("_guardarSalaError_") as string,
                "Error",
                { timeout: 10000 }
              );
            }
          })
          .catch(function(error) {
            vm.$snotify.error(
              vm.$t("_guardarSalaError_") as string,
              "Error",
              {
                timeout: 10000
              }
            );
            console.log(error);
          })
          .finally(function() {
            vm.$store.state.sala_cargando = false;
          });
      }
    },

    cerrarSala(): void {
      this.$store.commit("setIndicadores", []);
      this.$store.state.indicadoresAllData = [];
      this.$store.state.sala = { nombre: "" };
      this.$store.state.abrioSala = false;
      this.$store.state.abrioIndicador = false;
    }
  }
});
</script>
