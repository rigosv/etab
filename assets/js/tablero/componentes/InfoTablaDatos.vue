<template>
    <div>
        <div style="max-height:400px; max-width:100%; overflow:auto;"  >
            <InfoTablaDatosContenido :indicador="indicador" />
        </div>
        <div style="padding-top: 10px;">

            <b-button size="sm" variant="outline-primary" @click="exportarExcel()">
                <font-awesome-icon icon="file-excel" /> {{ $t('_exportar_excel_') }}
            </b-button>            
            <b-button size="sm" variant="outline-primary" @click="exportarcsv()">
                <font-awesome-icon icon="file-csv" />{{ $t('_exportar_csv_') }}
            </b-button>
            <b-button size="sm" variant="outline-primary" @click="exportarpdf()">
                <font-awesome-icon icon="file-pdf" />{{ $t('_exportar_pdf_') }}
            </b-button>
        </div>
    </div>
</template>

<script>
    import TableToExcel from "@linways/table-to-excel";
    import jsPDF from 'jspdf';
    import html2canvas from 'html2canvas';
    import html2pdf from 'html2pdf.js';

    import InfoTablaDatosContenido from "./InfoTablaDatosContenido";

    import ColorMixin from "../Mixins/ColorMixin";

    export default {
        props: {
              indicador: Object
        },
        components: {InfoTablaDatosContenido},
        mixins : [ColorMixin],
        methods: {
            getColor : function ( valor ){
                let color = '';
                this.indicador.informacion.rangos.forEach(rango => {
                    if (valor >= rango.limite_inf && valor <= rango.limite_sup) {
                        color = rango.color;
                    }
                });
                return color;
            },
            exportarExcel :  function () {
                let vm = this;
                TableToExcel.convert(document.getElementById("exportar_tabla_datos"), {name: vm.indicador.nombre+ "- tabla datos.xlsx"});

            },

            exportarcsv : function(){
                let arrData  = this.indicador.data;
                let csvContent = "data:text/csv;charset=utf-8,";
                csvContent += [
                    Object.keys(arrData[0]).join(","),
                    ...arrData.map(item => Object.values(item).join(","))
                ]
                    .join("\n")
                    .replace(/(^\[)|(\]$)/gm, "");

                const data = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", data);
                link.setAttribute("download", "export.csv");
                link.click();
            },

            exportarpdf: function() {
                let tabla = document.getElementById('exportar_tabla_datos');
                const opt = {
                    margin:       0.5,
                    filename:     this.indicador.nombre +'-tabla datos.pdf',
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { scale: 1 },
                    jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
                };

                html2pdf().set(opt).from(tabla).save();
            },

            getColorExceljs_ :  function ( v ) {
                let codigo = this.getColor( v );
                return this.getColorExceljs( codigo );
            }
        }

    }
</script>