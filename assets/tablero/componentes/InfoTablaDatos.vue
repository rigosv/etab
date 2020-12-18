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
        ref="html2PdfDatos"
      >
        <section slot="pdf-content">
          <InfoTablaDatosContenido :indicador="indicador" />
        </section>
      </vue-html2pdf>
    </div>
    <div style="padding-top: 10px;">
      <b-button size="sm" variant="outline-primary" @click="exportarExcel()">
        <font-awesome-icon icon="file-excel" /> {{ $t("_exportarExcel_") }}
      </b-button>
      <b-button size="sm" variant="outline-primary" @click="exportarcsv()">
        <font-awesome-icon icon="file-csv" />{{ $t("_exportarCSV_") }}
      </b-button>
      <b-button size="sm" variant="outline-primary" @click="exportarpdf()">
        <font-awesome-icon icon="file-pdf" />{{ $t("_exportarPDF_") }}
      </b-button>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "@vue/composition-api";
import TableToExcel from "@linways/table-to-excel";
import VueHtml2pdf from "vue-html2pdf";

import InfoTablaDatosContenido from "./InfoTablaDatosContenido.vue";
import ColorMixin from "../Mixins/ColorMixin";

export default defineComponent({
  components: { InfoTablaDatosContenido, VueHtml2pdf },

  props: {
    indicador: { default: {}, type: Object },
    index: Number
  },

  mixins: [ColorMixin],

  computed: {
    pdfOptions(): object {
      return {
        filename: `${this.indicador.nombre}-tabla_datos.pdf`,
        margin: 0.5,
        image: { type: "jpeg", quality: 0.6 },
        html2canvas: { scale: 1 },
        jsPDF: { unit: "in", format: "letter", orientation: "portrait" }
      };
    }
  },

  methods: {
    getColor(valor: number): string {
      let color = "";
      for (const rango of this.indicador.informacion.rangos) {
        if (valor >= rango.limite_inf && valor <= rango.limite_sup) {
          color = rango.color;
        }
      }
      return color;
    },

    exportarExcel(): void {
      const vm = this;
      TableToExcel.convert(document.getElementById("exportar_tabla_datos"), {
        name: vm.indicador.nombre + "- tabla datos.xlsx"
      });
    },

    exportarcsv(): void {
      const arrData = this.indicador.data;
      let csvContent = "data:text/csv;charset=utf-8,";
      csvContent += [
        Object.keys(arrData[0]).join(","),
        ...arrData.map((item: any) => Object.values(item).join(","))
      ]
        .join("\n")
        .replace(/(^\[)|(\]$)/gm, "");

      const data = encodeURI(csvContent);
      const link = document.createElement("a");
      link.setAttribute("href", data);
      link.setAttribute("download", "export.csv");
      link.click();
    },

    exportarpdf(): void {
      (this.$refs.html2Pdf as Vue & { generatePdf: () => any }).generatePdf();
    },

    getColorExceljs_(v: number): string {
      const codigo = this.getColor(v);
      return this.getColorExceljs(codigo);
    }
  }
});
</script>
