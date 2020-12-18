<template>
  <div>
    <div style="max-height:400px; max-width:100%; overflow:auto;">
      <vue-html2pdf
        :show-layout="true"
        :float-layout="false"
        :enable-download="true"
        :preview-modal="false"
        :pdf-quality="1"
        :manual-pagination="true"
        :html-to-pdf-options="pdfOptions"
        pdf-content-width="100%"
        ref="html2Pdf"
      >
        <section slot="pdf-content">
          <div id="exportar_ficha_container">
            <InfoFichaTecnicaContenido
              :indicador="indicador"
            ></InfoFichaTecnicaContenido>
          </div>
        </section>
      </vue-html2pdf>
    </div>
    <div style="padding-top: 10px;">
      <b-button size="sm" variant="outline-primary" @click="exportarExcel()">
        <font-awesome-icon icon="file-excel" />{{ $t("_exportarExcel_") }}
      </b-button>
      <b-button size="sm" variant="outline-primary" @click="exportarpdf()">
        <font-awesome-icon icon="file-pdf" />{{ $t("_exportarPDF_") }}
      </b-button>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";
import VueHtml2pdf from "vue-html2pdf";
import TableToExcel from "@linways/table-to-excel";

import InfoFichaTecnicaContenido from "./InfoFichaTecnicaContenido.vue";

export default defineComponent({
  components: { InfoFichaTecnicaContenido, VueHtml2pdf },

  props: {
    indicador: { default: {}, type: Object }
  },

  computed: {
    pdfOptions(): object {
      return {
        filename: `${this.indicador.nombre}-${this.$t(
          "_fichaTecnica_" as string
        )}.pdf`,
        margin: 0.5,
        image: { type: "jpeg", quality: 0.6 },
        html2canvas: { scale: 1 },
        jsPDF: { unit: "in", format: "letter", orientation: "portrait" }
      };
    }
  },

  methods: {
    exportarExcel(): void {
      const nombreArchivo =
        this.indicador.nombre + " - " + this.$t("_fichaTecnica_");
      TableToExcel.convert(
        document.getElementById("exportar_ficha_container"),
        {
          name: nombreArchivo + ".xlsx"
        }
      );
    },

    exportarpdf(): void {
      (this.$refs.html2Pdf as Vue & { generatePdf: () => any }).generatePdf();
    }
  }
});
</script>
