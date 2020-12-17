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
        <font-awesome-icon icon="file-excel" />{{ $t("_exportar_excel_") }}
      </b-button>
      <b-button size="sm" variant="outline-primary" @click="exportarpdf()">
        <font-awesome-icon icon="file-pdf" />{{ $t("_exportar_pdf_") }}
      </b-button>
    </div>
  </div>
</template>

<script lang="ts">
import { Component, Vue, Prop, Mixins } from "vue-property-decorator";
import VueHtml2pdf from "vue-html2pdf";
import TableToExcel from "@linways/table-to-excel";

import InfoFichaTecnicaContenido from "./InfoFichaTecnicaContenido.vue";
import ColorMixin from "../Mixins/ColorMixin";

@Component({
  components: { InfoFichaTecnicaContenido, VueHtml2pdf }
})
export default class InfoFichaTecnica extends Mixins(ColorMixin) {
  @Prop({ default: {} }) indicador: any;

  get pdfOptions() {
    return {
      filename: `${this.indicador.nombre}-${this.$t(
        "_ficha_tecnica_" as string
      )}.pdf`,
      margin: 0.5,
      image: { type: "jpeg", quality: 0.6 },
      html2canvas: { scale: 1 },
      jsPDF: { unit: "in", format: "letter", orientation: "portrait" }
    };
  }

  public exportarExcel(): void {
    const nombreArchivo =
      this.indicador.nombre + " - " + this.$t("_ficha_tecnica_");
    TableToExcel.convert(document.getElementById("exportar_ficha_container"), {
      name: nombreArchivo + ".xlsx"
    });
  }
  public exportarpdf(): void {
    (this.$refs.html2Pdf as Vue & { generatePdf: () => any }).generatePdf();
  }
}
</script>
