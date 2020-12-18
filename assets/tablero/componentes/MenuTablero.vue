<template>
  <div>
    <section class="content-header">
      <b-navbar type="dark" variant="secondary">
        <b-navbar-brand href="/" v-if="!esSalaPublica">eTab</b-navbar-brand>
        <b-navbar-brand href="#" v-if="esSalaPublica">eTab</b-navbar-brand>
        <b-navbar-nav>
          <b-nav-item href="#" v-if="!esSalaPublica">
            <b-button
              v-b-modal.modalSalas
              variant="primary"
              style="padding: 5px;"
            >
              <font-awesome-icon icon="th" />
              {{ $t("_salas_") }}
            </b-button>
          </b-nav-item>
          <b-nav-item href="#" v-if="!esSalaPublica">
            <b-button
              v-b-modal.modalIndicadores
              variant="success"
              style="padding: 5px;"
            >
              <font-awesome-icon icon="flag" />
              {{ $t("_indicadores_") }}
            </b-button>
          </b-nav-item>
        </b-navbar-nav>
        <transition name="bounce">
          <b-navbar-nav
            class="ml-auto"
            v-if="$store.state.abrioSala || $store.state.abrioIndicador"
          >
            <b-dropdown variant="info" :text="$t('_acciones_')" right>
              <b-dropdown-item href="#" v-b-modal.modalExportar>
                <font-awesome-icon icon="file-export" /> {{ $t("_exportar_") }}
              </b-dropdown-item>
              <b-dropdown-item
                href="#"
                v-b-modal.modalAccionesSala
                v-if="$store.state.abrioSala && !esSalaPublica"
              >
                <font-awesome-icon icon="bookmark" />
                {{ $t("_accionesSalaSituacional_") }}
              </b-dropdown-item>
              <b-dropdown-item
                href="#"
                v-b-modal.modalCompartirSala
                v-if="$store.state.abrioSala && !esSalaPublica"
              >
                <font-awesome-icon icon="share" /> {{ $t("_compartirSala_") }}
              </b-dropdown-item>
              <b-dropdown-item
                href="#"
                v-b-modal.modalFiltrosGenerales
                v-if="$store.state.abrioSala && !esSalaPublica"
              >
                <font-awesome-icon icon="filter" />
                {{ $t("_filtrosGenerales_") }}
              </b-dropdown-item>
            </b-dropdown>
          </b-navbar-nav>
        </transition>
      </b-navbar>
    </section>

    <modal-salas></modal-salas>
    <modal-indicadores></modal-indicadores>
    <modal-exportar
      @convertir-graficos-sala="convertirGraficosSala"
    ></modal-exportar>
    <modal-acciones-sala></modal-acciones-sala>
    <modal-compartir-sala></modal-compartir-sala>
    <modal-filtros-generales></modal-filtros-generales>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";
import ModalSalas from "./Modal/ModalSalas.vue";
import ModalIndicadores from "./Modal/ModalIndicadores.vue";
import ModalExportar from "./Modal/ModalExportar.vue";
import ModalAccionesSala from "./Modal/ModalAccionesSala.vue";
import ModalCompartirSala from "./Modal/ModalCompartirSala.vue";
import ModalFiltrosGenerales from "./Modal/ModalFiltrosGenerales.vue";

export default defineComponent({
  components: {
    ModalIndicadores,
    ModalSalas,
    ModalExportar,
    ModalAccionesSala,
    ModalCompartirSala,
    ModalFiltrosGenerales
  },

  computed: {
    esSalaPublica(): boolean {
      return this.$store.state.token != "" && this.$store.state.idSala != "";
    }
  },

  methods: {
    convertirGraficosSala(): void {
      this.$emit("convertir-graficos-sala");
    }
  }
});
</script>
